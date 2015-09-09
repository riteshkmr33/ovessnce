<?php

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Admin\Model\Usertype;
use Admin\Model\GroupRights;
use Admin\Form\GroupRightsForm;
use Zend\InputFilter\InputFilter;

class GroupRightsController extends AbstractActionController
{
	protected $GroupRightsTable;
	protected $UsertypeTable;
	
	public function indexAction()
    {
        $paginator = $this->getGroupRightsTable()->fetchAll();   
		$paginator->setCurrentPageNumber((int)$this->params()->fromQuery('page', 1));   
		$paginator->setItemCountPerPage(10);  
		
		return new ViewModel(array(
			'grouprights' => $paginator,
		));
        
    }
    
    public function assignAction()
    {
		$filter = new InputFilter;
		
		$id = (int) $this->params()->fromRoute('id', 0);
		
        if (!$id) {
            return $this->redirect()->toRoute('admin/usertype', array(
                'action' => 'index'
            ));
        }
        
        $Usertype = $this->getUsertypeTable()->getUsertype($id);
                
        $GroupRights = $this->getGroupRightsTable()->getGroupRightByGrpId($id);
        
        
        $modules = $this->getServiceLocator()->get('Admin\Model\SiteModulesTable')->fetchAll(false);
        //$modules->setCurrentPageNumber((int)$this->params()->fromQuery('page', 1));   
		//$modules->setItemCountPerPage(10);   
        
        $form = new GroupRightsForm();
		
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
			
			$grouprights = new GroupRights();
			
			$form->setInputFilter($filter);
            $form->setData($request->getPost());
			
            if ($form->isValid()) {
				
				$grouprights->exchangeArray($form->getData());
                if ($this->getGroupRightsTable()->saveGroupRight($grouprights, $request->getPost('module_id') ,$request->getPost('can_add'),$request->getPost('can_edit'),$request->getPost('can_view'),$request->getPost('can_del'))) {
					$this->flashMessenger()->addSuccessMessage('Group rights added successfully..!!');
				} else {
					$this->flashMessenger()->addErrorMessage('Not added..!!'); 
				}
				
                return $this->redirect()->toRoute('admin/usertype', array(
					'successMsgs' => $this->flashMessenger()->getCurrentSuccessMessages(),
					'errors' => $this->flashMessenger()->getCurrentErrorMessages()
				));
                 
            } else {
				
				$this->errors = $form->getMessages();
				
			}
			
        }
		
        return array(
            'id' => $id,
            'form' => $form,
            'group_id' => $Usertype->id,
            'group_name' => $Usertype->user_type,
            'fields' => $fields,
            'module_name' => $module_name,
            'GroupRights' => $GroupRights,
        );
	}
		
	public function getGroupRightsTable()
	{
		if (!$this->GroupRightsTable) {
			$sm = $this->getServiceLocator();
			$this->GroupRightsTable = $sm->get('Admin\Model\GroupRightsTable');
		}
	
		return $this->GroupRightsTable;
	}
	
	public function getUsertypeTable()
	{
		if (!$this->UsertypeTable) {
			$sm = $this->getServiceLocator();
			$this->UsertypeTable = $sm->get('Admin\Model\UsertypeTable');
		}
	
		return $this->UsertypeTable;
	}
	
}
