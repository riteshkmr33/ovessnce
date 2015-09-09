<?php

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Admin\Model\Sms; 
use Admin\Form\SmsForm;

class SmsController extends AbstractActionController
{
	protected $SmsTable;
	
	public function indexAction()
    {
        $paginator = $this->getSmsTable()->fetchAll();    
		$paginator->setCurrentPageNumber((int)$this->params()->fromQuery('page', 1)); 
		$paginator->setItemCountPerPage(10);  
		
		return new ViewModel(array(
			'smss' => $paginator,
			'status' => $this->getServiceLocator()->get('Admin\Model\StatusTable')->fetchAll(false, array(1,2)),
			'successMsgs' => $this->flashMessenger()->getCurrentSuccessMessages(),
			'errors' => $this->flashMessenger()->getCurrentErrorMessages()
		));
        
    }
    
    public function addAction()
    {
		$form = new SmsForm($this->getServiceLocator()->get('Admin\Model\StatusTable'));
        $form->get('submit')->setValue('Add');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $Sms = new Sms();
            $form->setInputFilter($Sms->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $Sms->exchangeArray($form->getData());
                $this->getSmsTable()->saveSms($Sms);
				$this->flashMessenger()->addSuccessMessage('Sms template added successfully..!!');
				
                // Redirect to list of sms
                return $this->redirect()->toRoute('admin/sms');
            }
        }
        return array('form' => $form);
	}
		
    public function editAction()
    {
		$id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('admin/sms', array(
                'action' => 'add'
            ));
        }
        $Sms = $this->getSmsTable()->getSms($id);
        if ($Sms == false) {
			$this->flashMessenger()->addErrorMessage('Sms template not found..!!');
			return $this->redirect()->toRoute('admin/sms');
		}

        $form  = new SmsForm($this->getServiceLocator()->get('Admin\Model\StatusTable'));
        $form->bind($Sms);
        $form->get('submit')->setAttribute('value', 'Edit');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($Sms->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $this->getSmsTable()->saveSms($form->getData());
                $this->flashMessenger()->addSuccessMessage('Sms template updated successfully..!!');

                // Redirect to list of pages
                return $this->redirect()->toRoute('admin/sms');
            }
        }

        return array(
            'id' => $id,
            'form' => $form,
        );
	}
		
    public function deleteAction()
    {
		$id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('admin/sms');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $this->getSmsTable()->deleteSms($id);
                $this->flashMessenger()->addSuccessMessage('Sms template deleted successfully..!!');
            }

            // Redirect to list of pages
            return $this->redirect()->toRoute('admin/sms');
        }

        return array(
            'id'    => $id,
            'sms' => $this->getSmsTable()->getSms($id)
        );
	}	
	
	public function getSmsTable()
	{
		if (!$this->SmsTable) {
			$sm = $this->getServiceLocator();
			$this->SmsTable = $sm->get('Admin\Model\SmsTable');
		}
	
		return $this->SmsTable;
	}
	
	public function changeStatusAction()
    {
		$request = $this->getRequest();
		if ($request->isPost()) {
			$id = $request->getPost('id');
			$status = $request->getPost('status', '1');
			if ($id != null && $status != null) {
				$this->getSmsTable()->changeStatus($id, $status);
				echo json_encode(array("msg"=>"Status successfully changed..!!"));
			} else {
				echo json_encode(array("msg"=>"Failed to change the status..!!"));
			}
			exit;
		}
	}
	
}
