<?php

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Admin\Model\SiteModules;
use Admin\Form\SiteModulesForm;

class SiteModulesController extends AbstractActionController
{
	protected $SiteModulesTable;
	
	public function indexAction()
    {
        $paginator = $this->getSiteModulesTable()->fetchAll();   
		$paginator->setCurrentPageNumber((int)$this->params()->fromQuery('page', 1));   
		$paginator->setItemCountPerPage(10);   
		
		return new ViewModel(array(
			'modules' => $paginator,
			'successMsgs' => $this->flashMessenger()->getCurrentSuccessMessages(),
			'errors' => $this->flashMessenger()->getCurrentErrorMessages()
		));
        
    }
    
    public function addAction()
    {
		
		$form = new SiteModulesForm();
        $form->get('submit')->setValue('Add');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $sitemodules = new SiteModules();
            $form->setInputFilter($sitemodules->getInputFilter());
            $form->setData($request->getPost());
            
            if ($form->isValid()) {
                
                $sitemodules->exchangeArray($form->getData());
         
                if($_POST['frontFlag'] == '0' && $_POST['adminFlag'] == '0' ) {
					$form->get('frontFlag')->setMessages(array('Select atleast one of the options'));
					$form->get('adminFlag')->setMessages(array('Select atleast one of the options'));
					return array('form' => $form);
					exit;
				}
                
                $this->getSiteModulesTable()->saveSiteModule($sitemodules);
                $this->flashMessenger()->addSuccessMessage('Site Module added successfully..!!');

                return $this->redirect()->toRoute('admin/sitemodules');
            } else {
				if($_POST['frontFlag'] == '0' && $_POST['adminFlag'] == '0' ) {
					$form->get('frontFlag')->setMessages(array('Select atleast one of the options'));
					$form->get('adminFlag')->setMessages(array('Select atleast one of the options'));
				}
			}
        }
        return array('form' => $form);
         
	}
		
    public function editAction()
    {

		$id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('admin/sitemodules', array(
                'action' => 'add'
            ));
        }
        $sitemodules = $this->getSiteModulesTable()->getSiteModule($id);
        
        if ($sitemodules == false) {
			$this->flashMessenger()->addErrorMessage('Site Module not found..!!');
			return $this->redirect()->toRoute('admin/sitemodules');
		}

        $form  = new SiteModulesForm();
        $form->bind($sitemodules);
        $form->get('submit')->setAttribute('value', 'Edit');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($sitemodules->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
				
				if($_POST['frontFlag'] == '0' && $_POST['adminFlag'] == '0' ) {
					$form->get('frontFlag')->setMessages(array('Select atleast one of the options'));
					$form->get('adminFlag')->setMessages(array('Select atleast one of the options'));
					return array(
						'id' => $id,
						'form' => $form,
					);
					exit;
				}
				
                $this->getSiteModulesTable()->saveSiteModule($form->getData());
				$this->flashMessenger()->addSuccessMessage('Site Module Updated successfully..!!');
				
                return $this->redirect()->toRoute('admin/sitemodules');
            }
        }

        return array(
            'id' => $id,
            'form' => $form,
        );
         
	}
		
    public function deleteAction()
    {
		return $this->redirect()->toRoute('admin/sitemodules');  // delete functionality is disabled for the time being
		$id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('admin/sitemodules');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $this->getSiteModulesTable()->deleteSiteModule($id);
                $this->flashMessenger()->addSuccessMessage('Site Module Deleted successfully..!!');
            }

            return $this->redirect()->toRoute('admin/sitemodules');
        }

        return array(
            'id'    => $id,
            'sitemodule' => $this->getSiteModulesTable()->getSiteModule($id)
        );
         
	}	
	
	public function getSiteModulesTable()
	{
		if (!$this->SiteModulesTable) {
			$sm = $this->getServiceLocator();
			$this->SiteModulesTable = $sm->get('Admin\Model\SiteModulesTable');
		}
	
		return $this->SiteModulesTable;
	}
	
}
