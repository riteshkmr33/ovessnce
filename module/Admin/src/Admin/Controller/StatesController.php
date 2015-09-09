<?php

namespace Admin\Controller; 

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Admin\Model\States;
use Admin\Form\StateForm;
use Admin\Form\StateFilterForm;

class StatesController extends AbstractActionController
{
	private $stateTable;
	public $errors = array();
	
	private function getStateTable()
	{
		if (!$this->stateTable) {
			$sm = $this->getServiceLocator();
			$this->stateTable = $sm->get('Admin\Model\StatesTable');
		}
	
		return $this->stateTable;
	}
	
    public function indexAction()
    {
		$form = new StateFilterForm($this->getServiceLocator()->get('Admin\Model\CountriesTable'), $this->getServiceLocator()->get('Admin\Model\StatusTable'));
		$request = $this->getRequest();
        $postedData = array();
        $getData = (array)$request->getQuery();
        unset($getData['page']);
        
		$request = $this->getRequest();
        if ($request->isPost()) {
			$postedData = $request->getPost();
			$form->bind($postedData);
			$filter = array(
				'state_code' => trim($postedData['state_code']),
				'state_name' => trim($postedData['state_name']),
				'country_id' => $postedData['country_id'],
				'status_id' => $postedData['status_id'],
			);
			$paginator = $this->getStateTable()->fetchAll(true, $filter, array('sort_field' => $getData['sort_field'], 'sort_order' => $getData['sort_order']));
		} else if (count($getData) > 0) {
			$filter = array();
			$form->bind($request->getQuery());
			$postedData = $getData;
			isset($getData['state_code'])?$filter['state_code'] = trim($getData['state_code']):"";
			isset($getData['state_name'])?$filter['state_name'] = trim($getData['state_name']):"";
			isset($getData['country_id'])?$filter['country_id'] = trim($getData['country_id']):"";
			isset($getData['status_id'])?$filter['status_id'] = trim($getData['status_id']):"";
			
			$paginator = $this->getStateTable()->fetchAll(true, $filter, array('sort_field' => $getData['sort_field'], 'sort_order' => $getData['sort_order']));
		} else {
			$paginator = $this->getStateTable()->fetchAll();
		}
		
		$paginator->setCurrentPageNumber((int)$this->params()->fromQuery('page', 1));
		$paginator->setItemCountPerPage(10);

		return new ViewModel(array(
			'states' => $paginator,
			'form' => $form,
			'postedData' => array_filter((array)$postedData),
			'status' => $this->getServiceLocator()->get('Admin\Model\StatusTable')->fetchAll(false, array(1,2)),
			'successMsgs' => $this->flashMessenger()->getCurrentSuccessMessages(),
			'errors' => $this->flashMessenger()->getCurrentErrorMessages()
		));
    }
    
    public function addAction()
    {
        $form = new StateForm($this->getServiceLocator()->get('Admin\Model\CountriesTable'));
        //$form = new StateForm();
        $form->get('submit')->setValue('Add');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $state = new States();
            $state->setAdapter($this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'));
            
            // Adding already exist validation on runtime excluding the current record
			$state->getInputFilter()->get('state_code')->getValidatorChain()->attach(new \Zend\Validator\Db\NoRecordExists(array('table' => 'state','field' => 'state_code','adapter' => $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'))));
			$state->getInputFilter()->get('state_name')->getValidatorChain()->attach(new \Zend\Validator\Db\NoRecordExists(array('table' => 'state','field' => 'state_name','adapter' => $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'))));
            
            $form->setInputFilter($state->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $state->exchangeArray($form->getData());
                $this->getStateTable()->saveState($state);
                $this->flashMessenger()->addSuccessMessage('State added successfully..!!');

                // Redirect to list of pages
                return $this->redirect()->toRoute('admin/states');
            } else {
				$this->errors = $form->getMessages();
			}
        }
        return array('form' => $form,  'errors' => $this->errors);
    }
    
    public function editAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('admin/states', array(
                'action' => 'add'
            ));
        }
        $state = $this->getStateTable()->getState($id);
		$state->setAdapter($this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'));
		
        $form = new StateForm($this->getServiceLocator()->get('Admin\Model\CountriesTable'));
        $form->bind($state);
        $form->get('submit')->setAttribute('value', 'Edit');

        $request = $this->getRequest();
        if ($request->isPost()) {
			// Adding already exist validation on runtime excluding the current record
			$state->getInputFilter()->get('state_code')->getValidatorChain()->attach(new \Zend\Validator\Db\NoRecordExists(array('table' => 'state','field' => 'state_code','adapter' => $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'), 'exclude' => array('field' => 'id','value' => $id))));
			$state->getInputFilter()->get('state_name')->getValidatorChain()->attach(new \Zend\Validator\Db\NoRecordExists(array('table' => 'state','field' => 'state_name','adapter' => $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'), 'exclude' => array('field' => 'id','value' => $id))));
			
            $form->setInputFilter($state->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $this->getStateTable()->saveState($form->getData());
                $this->flashMessenger()->addSuccessMessage('State updated successfully..!!');
                // Redirect to list of pages
                return $this->redirect()->toRoute('admin/states');
            } else {
				$this->errors = $form->getMessages();
			}
        }

        return array(
            'id' => $id,
            'form' => $form,
            'errors' => $this->errors
        );
    }
    
    public function deleteAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('admin/states');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $this->getStateTable()->deleteState($id);
                $this->flashMessenger()->addSuccessMessage('State deleted successfully..!!');
            }

            // Redirect to list of pages 
            return $this->redirect()->toRoute('admin/states');
        }

        return array(
            'id'    => $id,
            'state' => $this->getStateTable()->getState($id)
        );
    }
    
    public function changeStatusAction()
    {
		$request = $this->getRequest();
		if ($request->isPost()) {
			$id = $request->getPost('id');
			$status = $request->getPost('status', '1');
			if ($id != null && $status != null) {
				$this->getStateTable()->changeStatus($id, $status);
				echo json_encode(array("msg"=>"Status successfully changed..!!"));
			} else {
				echo json_encode(array("msg"=>"Failed to change the status..!!"));
			}
		}
		exit;
	}
	
	public function getstatesAction()
    {
		$request = $this->getRequest();
		if ($request->isPost()) {
			$country = $request->getPost('country');
			if ($country != null) {
				$results = $this->getStateTable()->getStatesByCountry($country);
				$states = array();
				
				foreach ($results as $result) {
					$states[] = array('id' => $result->id, 'name' => $result->state_name);
				}
				
				echo json_encode($states);
			} else {
				echo json_encode(array());
			}
			
		}
		exit;
	}
}
