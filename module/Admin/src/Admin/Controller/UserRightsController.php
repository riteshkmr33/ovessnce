<?php

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Admin\Model\Usertype;
use Admin\Model\UserRights;
use Admin\Form\UserRightsForm;
use Zend\InputFilter\InputFilter;

class UserRightsController extends AbstractActionController
{
	protected $UserRightsTable;
	protected $UsersTable;
	
	public function indexAction()
    {
        $paginator = $this->getUserRightsTable()->fetchAll();   
		$paginator->setCurrentPageNumber((int)$this->params()->fromQuery('page', 1));   
		$paginator->setItemCountPerPage(10);  
		
		return new ViewModel(array(
			'user_rights' => $paginator,
		));
        
    }
    
    public function assignAction()
    {
		$filter = new InputFilter;
		
		$id = (int) $this->params()->fromRoute('id', 0);
		
        if (!$id) {
            return $this->redirect()->toRoute('admin/users', array(
                'action' => 'index'
            ));
        }
        
        $Users = $this->getUsersTable()->getUser($id);
        if ($Users == false) {
			$this->flashMessenger()->addErrorMessage('User found..!!');
			return $this->redirect()->toRoute('admin/users');
		}
        $UserRights = $this->getUserRightsTable()->getUserRightByUsrId($id);
        
        
        $modules = $this->getServiceLocator()->get('Admin\Model\SiteModulesTable')->fetchAll(false);
        //$modules->setCurrentPageNumber((int)$this->params()->fromQuery('page', 1));   
		//$modules->setItemCountPerPage(10);
        
        $form = new UserRightsForm();
		
		$fields = array();
		$module_name = array();
		
		foreach ($modules  as $module) {
			
			$module_name[$module->id] = $module->module_name;  
			
			//echo $GroupRights["add_".$module->id];die;
			
			$form->add(array(
				'type' => 'Zend\Form\Element\Checkbox',
				'name' => 'can_add['.$module->id.']',
			));
			
			$form->add(array(
				'type' => 'Zend\Form\Element\Checkbox',
				'name' => 'can_edit['.$module->id.']',
			));
			
			$form->add(array(
				'type' => 'Zend\Form\Element\Checkbox',
				'name' => 'can_view['.$module->id.']',
			));
			
			$form->add(array(
				'type' => 'Zend\Form\Element\Checkbox',
				'name' => 'can_del['.$module->id.']',
			));
			
			$filter->add(array('name' => 'can_add['.$module->id.']','required' => false,));	
			$filter->add(array('name' => 'can_edit['.$module->id.']','required' => false,));	
			$filter->add(array('name' => 'can_view['.$module->id.']','required' => false,));	
			$filter->add(array('name' => 'can_del['.$module->id.']','required' => false,));		
			
			$fields[] = $module->id;
		}
			 
		$form->get('submit')->setValue('assignroles');
		
		$request = $this->getRequest();
		
        if ($request->isPost()) {
			
			$userrights = new UserRights();
			
			$form->setInputFilter($filter);
            $form->setData($request->getPost());
			
            if ($form->isValid()) {
				
				$userrights->exchangeArray($form->getData());
                if ($this->getUserRightsTable()->saveUserRight($userrights, $request->getPost('module_id') ,$request->getPost('can_add'),$request->getPost('can_edit'),$request->getPost('can_view'),$request->getPost('can_del'))) { 
					$this->flashMessenger()->addSuccessMessage('added successfully..!!');
				} else {
					$this->flashMessenger()->addErrorMessage('Not added..!!'); 
				}

                return $this->redirect()->toRoute('admin/users');
                 
            } else {
				
				$this->errors = $form->getMessages();
				
			}
			
        }
		
        return array(
            'id' => $id,
            'form' => $form,
            'user_id' => $Users->id,
            'user_name' => $Users->user_name,
            'fields' => $fields,
            'module_name' => $module_name,
            'UserRights' => $UserRights,
        );
	}
		
	public function getUserRightsTable()
	{
		if (!$this->UserRightsTable) {
			$sm = $this->getServiceLocator();
			$this->UserRightsTable = $sm->get('Admin\Model\UserRightsTable');
		}
	
		return $this->UserRightsTable;
	}
	
	public function getUsersTable()
	{
		if (!$this->UsersTable) {
			$sm = $this->getServiceLocator();
			$this->UsersTable = $sm->get('Admin\Model\UsersTable');
		}
	
		return $this->UsersTable;
	}
	
}
