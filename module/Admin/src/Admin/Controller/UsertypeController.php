<?php

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Admin\Model\Usertype;
use Admin\Form\UsertypeForm;

class UsertypeController extends AbstractActionController
{
	protected $UsertypeTable;
	protected $GroupRightsTable;
	
	public function indexAction()
    {

        $paginator = $this->getUsertypeTable()->fetchAll();   
		$paginator->setCurrentPageNumber((int)$this->params()->fromQuery('page', 1));   
		$paginator->setItemCountPerPage(10);   
			
		return new ViewModel(array(
			'usertypes' => $paginator,
			'successMsgs' => $this->flashMessenger()->getCurrentSuccessMessages(),
			'errors' => $this->flashMessenger()->getCurrentErrorMessages()
		));

    }
    
    public function addAction()
    {
		
		$form = new UsertypeForm();
        $form->get('submit')->setValue('Add');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $Usertype = new Usertype();
            $form->setInputFilter($Usertype->getInputFilter());
            $form->setData($request->getPost());
			
			try {

				if ($form->isValid()) {
					$Usertype->exchangeArray($form->getData());
					$this->getUsertypeTable()->saveUsertype($Usertype);
					$this->flashMessenger()->addSuccessMessage('Usertype added successfully..!!');

					// Redirect to list of user types
					return $this->redirect()->toRoute('admin/usertype');
				}
			} catch (\Exception $e) {
				
				$form->setMessages(array(
					'user_type' => array($e->getMessage())
				));
				return new ViewModel(array(
					'form' => $form
				));
				
			}
        }
        return array('form' => $form);
         
	}
		
    public function editAction()
    {

		$id = (int) $this->params()->fromRoute('id', 0);
		
        if (!$id) {
            return $this->redirect()->toRoute('admin/usertype', array(
                'action' => 'add'
            ));
        }
        $Usertype = $this->getUsertypeTable()->getUsertype($id);
        if ($Usertype == false) {
			$this->flashMessenger()->addErrorMessage('User type not found..!!');
			return $this->redirect()->toRoute('admin/usertype');
		}
		
        $form  = new UsertypeForm();
        $form->bind($Usertype);
        $form->get('submit')->setAttribute('value', 'Edit');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($Usertype->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $this->getUsertypeTable()->saveUsertype($form->getData());
                $this->flashMessenger()->addSuccessMessage('Usertype updated successfully..!!');

                // Redirect to list of user types
                return $this->redirect()->toRoute('admin/usertype');
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
            return $this->redirect()->toRoute('admin/usertype');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $this->getUsertypeTable()->deleteUsertype($id);
                $this->getGroupRightsTable()->deleteGroupRight($id);
                $this->flashMessenger()->addSuccessMessage('Usertype deleted successfully..!!');
            }

            // Redirect to list of user types
            return $this->redirect()->toRoute('admin/usertype');
        }

        return array(
            'id'    => $id,
            'usertypes' => $this->getUsertypeTable()->getUsertype($id)
        );
         
	}	
	
	public function getUsertypeTable()
	{
		if (!$this->UsertypeTable) {
			$sm = $this->getServiceLocator();
			$this->UsertypeTable = $sm->get('Admin\Model\UsertypeTable');
		}
	
		return $this->UsertypeTable;
	}
	
	public function getGroupRightsTable()
	{
		if (!$this->GroupRightsTable) {
			$sm = $this->getServiceLocator();
			$this->GroupRightsTable = $sm->get('Admin\Model\GroupRightsTable');
		}
	
		return $this->GroupRightsTable;
	}
	
}
