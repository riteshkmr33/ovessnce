<?php

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Admin\Model\ServiceLanguages;
use Admin\Form\ServiceLanguageForm;
use Admin\Form\ServiceLanguageFilterForm;

class ServiceLanguageController extends AbstractActionController
{

    private $serviceLanguageTable;
	
	private function getServiceLanguageTable()
	{
		if (!$this->serviceLanguageTable) {
			$this->serviceLanguageTable = $this->getServiceLocator()->get('Admin\Model\ServiceLanguagesTable');
		}
		
		return $this->serviceLanguageTable;
	}

    public function indexAction()
    {
		$form = new ServiceLanguageFilterForm();
		
		$request = $this->getRequest();
        $postedData = array();
        $getData = (array)$request->getQuery();
        unset($getData['page']);
        
        if ($request->isPost()) {
			$postedData = $request->getPost();
			$form->bind($postedData);
			$filter = array(
				'language_name' => trim($postedData['language_name']),
				'status_id' => trim($postedData['status_id']),
			);
			$paginator = $this->getServiceLanguageTable()->fetchAll(true, $filter, array('sort_field' => $getData['sort_field'], 'sort_order' => $getData['sort_order']));
		} else if (count($getData) > 0) {
			$filter = array();
			$form->bind($request->getQuery());
			$postedData = $getData;
			isset($getData['language_name'])?$filter['language_name'] = trim($getData['language_name']):"";			
			isset($getData['status_id'])?$filter['status_id'] = trim($getData['status_id']):"";			
			$paginator = $this->getServiceLanguageTable()->fetchAll(true, $filter, array('sort_field' => $getData['sort_field'], 'sort_order' => $getData['sort_order']));
			
		} else {
			$paginator = $this->getServiceLanguageTable()->fetchAll();
		}
		
		$paginator->setCurrentPageNumber((int)$this->params()->fromQuery('page', 1));
		$paginator->setItemCountPerPage(10);

		return new ViewModel(array(
			'servicelanguages' => $paginator,
			'form' => $form,
			'postedData' => array_filter((array)$postedData),
			'status' => $this->getServiceLocator()->get('Admin\Model\StatusTable')->fetchAll(false, array(1,2)),
			'successMsgs' => $this->flashMessenger()->getCurrentSuccessMessages(),
			'errors' => $this->flashMessenger()->getCurrentErrorMessages()
		));
    }
    
    public function addAction()
    {
        $form = new ServiceLanguageForm($this->getServiceLocator()->get('Admin\Model\StatusTable'));
        $form->get('submit')->setValue('Add');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $sl = new ServiceLanguages();
            
            // Adding already exist validation on runtime excluding the current record
			$sl->getInputFilter()->get('language_name')->getValidatorChain()->attach(new \Zend\Validator\Db\NoRecordExists(array('table' => 'service_language','field' => 'language_name','adapter' => $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'))));
			
            $form->setInputFilter($sl->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $sl->exchangeArray($form->getData());
                $this->getServiceLanguageTable()->saveServiceLanguage($sl);
                $this->flashMessenger()->addSuccessMessage('Service Language added successfully..!!');

                // Redirect to list of pages
                return $this->redirect()->toRoute('admin/servicelanguages');
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
            return $this->redirect()->toRoute('admin/servicelanguages', array(
                'action' => 'add'
            ));
        }
        $sl = $this->getServiceLanguageTable()->getServiceLanguage($id);
        
        if ($sl == false) {
			$this->flashMessenger()->addErrorMessage('Service language not found..!!');
			return $this->redirect()->toRoute('admin/servicelanguages');
		}
		
        $form = new ServiceLanguageForm($this->getServiceLocator()->get('Admin\Model\StatusTable'));
        $form->bind($sl);
        $form->get('submit')->setAttribute('value', 'Edit');

        $request = $this->getRequest();
        if ($request->isPost()) {
			
			// Adding already exist validation on runtime excluding the current record
			$sl->getInputFilter()->get('language_name')->getValidatorChain()->attach(new \Zend\Validator\Db\NoRecordExists(array('table' => 'service_language','field' => 'language_name','adapter' => $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'), 'exclude' => array('field' => 'id','value' => $id))));
			
			$form->setInputFilter($sl->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $this->getServiceLanguageTable()->saveServiceLanguage($form->getData());
                $this->flashMessenger()->addSuccessMessage('Service Language updated successfully..!!');
                // Redirect to list of pages
                return $this->redirect()->toRoute('admin/servicelanguages');
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
            return $this->redirect()->toRoute('admin/servicelanguages');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $this->getServiceLanguageTable()->deleteServiceLanguage($id);
                $this->flashMessenger()->addSuccessMessage('Servie Language deleted successfully..!!');
            }

            // Redirect to list of pages 
            return $this->redirect()->toRoute('admin/servicelanguages');
        }

        return array(
            'id'    => $id,
            'servicelanguage' => $this->getServiceLanguageTable()->getServiceLanguage($id)
        );
    }
    
    public function changeStatusAction()
    {
		$request = $this->getRequest();
		if ($request->isPost()) {
			$id = $request->getPost('id');
			$status = $request->getPost('status', '1');
			if ($id != null && $status != null) {
				$this->getServiceLanguageTable()->changeStatus($id, $status);
				echo json_encode(array("msg"=>"Status successfully changed..!!"));
			} else {
				echo json_encode(array("msg"=>"Failed to change the status..!!"));
			}
			exit;
		}
	}


}

