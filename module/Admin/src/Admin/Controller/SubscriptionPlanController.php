<?php

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Admin\Model\SubscriptionPlans;
use Admin\Form\SubscriptionPlanForm;
use Zend\InputFilter\InputFilter;

class SubscriptionPlanController extends AbstractActionController
{

    private $subscriptionTable;
    public $errors = array();
	
	private function getSubscriptionTable()
	{
		if (!$this->subscriptionTable) {
			$this->subscriptionTable = $this->getServiceLocator()->get('Admin\Model\SubscriptionPlansTable');
		}
		
		return $this->subscriptionTable;
	}
	
    public function indexAction()
    {
        $paginator = $this->getSubscriptionTable()->fetchAll();
        $paginator->setCurrentPageNumber((int)$this->Params()->fromQuery('page',1));
        $paginator->setItemCountPerPage(10);

		return new ViewModel(array(
			'subscriptionplans' => $paginator,
			'status' => $this->getServiceLocator()->get('Admin\Model\StatusTable')->fetchAll(false, array(1,2)),
			'successMsgs' => $this->flashMessenger()->getCurrentSuccessMessages(),
			'errors' => $this->flashMessenger()->getCurrentErrorMessages()
		));
    }
    
    public function addAction()
    {
        $form = new SubscriptionPlanForm($this->getServiceLocator()->get('Admin\Model\StatusTable'));
        $form->get('submit')->setValue('Add');
        
        $filter = new InputFilter;
        $filter->add(array('name' => 'subscription_name','required' => true));
        $fields = array();
        $features = $this->getSubscriptionTable()->getFeatures();
        
        foreach ($features as $feature) {
			$form->add(array(
				'type' => 'Zend\Form\Element\Checkbox',
				'name' => 'features['.$feature->id.']',
				'options' => array(
					'label' => $feature->feature_name,
				),
				'attributes' => array(
					'class' => 'checkboxes'
				)
			));
			
			// adding validation rule at run time
			$filter->add(array('name' => 'features['.$feature->id.']','required' => false,));
			
			$fields[] = $feature->id;
		}
		
		$filter->add(array('name' => 'limit','required' => false));
		
        $request = $this->getRequest();
        if ($request->isPost()) {
            $plan = new SubscriptionPlans();
            
            $ftrs = $request->getPost('features');
			if (isset($ftrs[2]) && $ftrs[2] == '1') {
				$filter->add(array('name' => 'limit','required' => true));
			}
            
            $form->setInputFilter($filter);
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $plan->exchangeArray($form->getData());
                $this->getSubscriptionTable()->saveSubscriptionPlan($plan, $request->getPost('features'));
                $this->flashMessenger()->addSuccessMessage('Subscription Plan added successfully..!!');

                // Redirect to list of pages
                return $this->redirect()->toRoute('admin/subscriptionplans');
            } else {
				$this->errors = $form->getMessages();
			}
        }
        return array('form' => $form, 'errors' => $this->errors, 'fields' => $fields);
    }
    
    public function editAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('admin/subscriptionplans', array(
                'action' => 'add'
            ));
        }
        $plan = $this->getSubscriptionTable()->getSubscriptionPlan($id);
        if ($plan == false) {
			$this->flashMessenger()->addErrorMessage('Subscription plan not found..!!');
			return $this->redirect()->toRoute('admin/subscriptionplans');
		}
		$subs_features = $this->getSubscriptionTable()->getSubscriptionFeatures($id);
		$limit = $this->getSubscriptionTable()->getFeatureVideoLimit($id, 2);
		$form = new SubscriptionPlanForm($this->getServiceLocator()->get('Admin\Model\StatusTable'));
        //$form->bind($plan);
        $form->get('submit')->setAttribute('value', 'Edit');
        
        $filter = new InputFilter;
        $filter->add(array('name' => 'subscription_name','required' => true));
        $fields = array();
        $features = $this->getSubscriptionTable()->getFeatures();
        foreach ($features as $feature) {
			$form->add(array(
				'type' => 'Zend\Form\Element\Checkbox',
				'name' => 'features['.$feature->id.']',
				'options' => array(
					'label' => $feature->feature_name,
				),
				'attributes' => array(
					'class' => 'checkboxes'
				)
			));
			
			// adding validation rule at run time
			$filter->add(array('name' => 'features['.$feature->id.']','required' => false));
			
			$fields[] = $feature->id;
		}
		
		$filter->add(array('name' => 'limit','required' => false));
		
        $request = $this->getRequest();
        if ($request->isPost()) {
			$ftrs = $request->getPost('features');
			if (isset($ftrs[2]) && $ftrs[2] == '1') {
				$filter->add(array('name' => 'limit','required' => true));
			}
            $form->setInputFilter($filter);
            $form->setData($request->getPost());

            if ($form->isValid()) {
				$plan->exchangeArray($form->getData());
                $this->getSubscriptionTable()->saveSubscriptionPlan($plan, $request->getPost('features'));
                $this->flashMessenger()->addSuccessMessage('Subscription Plan updated successfully..!!');

                // Redirect to list of pages
                return $this->redirect()->toRoute('admin/subscriptionplans');
            } else {
				$this->errors = $form->getMessages();
			}
        }

        return array(
            'id' => $id,
            'form' => $form,
            'errors' => $this->errors,
            'fields' => $fields,
            'plan' => $plan,
            'subs_features' => $subs_features,
            'limit' => $limit,
        );
    }
    
    public function deleteAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('admin/subscriptionplans');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $this->getSubscriptionTable()->deleteSubscriptionPlan($id);
                $this->flashMessenger()->addSuccessMessage('Subscription duration deleted successfully..!!');
            }

            // Redirect to list of pages 
            return $this->redirect()->toRoute('admin/subscriptionplans');
        }

        return array(
            'id'    => $id,
            'model' => $this->getSubscriptionTable(),
            'subscriptionplan' => $this->getSubscriptionTable()->getSubscriptionPlan($id)
        );
    }
    
    public function changeStatusAction()
    {
		$request = $this->getRequest();
		if ($request->isPost()) {
			$id = $request->getPost('id');
			$status = $request->getPost('status', '1');
			if ($id != null && $status != null) {
				$this->getSubscriptionTable()->changeStatus($id, $status);
				echo json_encode(array("msg"=>"Status successfully changed..!!"));
			} else {
				echo json_encode(array("msg"=>"Failed to change the status..!!"));
			}
			exit;
		}
	}
    
}

