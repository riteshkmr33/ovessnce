<?php

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Admin\Model\SiteActions;
use Admin\Form\SiteActionsForm;

class SiteActionsController extends AbstractActionController
{
	protected $SiteActionsTable; 
	
	public function indexAction() 
    {
        $paginator = $this->getSiteActionsTable()->fetchAll();  
		$paginator->setCurrentPageNumber((int)$this->params()->fromQuery('page', 1)); 
		$paginator->setItemCountPerPage(10);  
		
		return new ViewModel(array(
			'siteactions' => $paginator,
			'successMsgs' => $this->flashMessenger()->getCurrentSuccessMessages(),
			'errors' => $this->flashMessenger()->getCurrentErrorMessages()
		));
        
    }
    
    public function addAction()
    {
		$form = new SiteActionsForm($this->getServiceLocator()->get('Admin\Model\SiteMetaTable'));
        $form->get('submit')->setValue('Add');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $SiteActions = new SiteActions();
            $form->setInputFilter($SiteActions->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $SiteActions->exchangeArray($form->getData());
                $this->getSiteActionsTable()->saveSiteAction($SiteActions,$request->getPost('action_meta'));
                $this->flashMessenger()->addSuccessMessage('Site Action added successfully..!!');

                // Redirect to list of actions
                return $this->redirect()->toRoute('admin/siteactions');
            }
        }
        return array('form' => $form);
	}
		
    public function editAction()
    {
		$id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('admin/siteactions', array(
                'action' => 'add'
            ));
        }
        $SiteActions = $this->getSiteActionsTable()->getSiteAction($id);
       
        if ($SiteActions == false) {
			$this->flashMessenger()->addErrorMessage('Action not found..!!');
			return $this->redirect()->toRoute('admin/siteactions');
		}

        $form  = new SiteActionsForm($this->getServiceLocator()->get('Admin\Model\SiteMetaTable'),$this->getSiteActionsTable()->getActionMeta($id,true));
        $form->bind($SiteActions);
        $form->get('submit')->setAttribute('value', 'Edit');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($SiteActions->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $this->getSiteActionsTable()->saveSiteAction($form->getData(),$request->getPost('action_meta'));
				$this->flashMessenger()->addSuccessMessage('Site Action updated successfully..!!');
				
                // Redirect to list of actions
                return $this->redirect()->toRoute('admin/siteactions');
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
            return $this->redirect()->toRoute('admin/siteactions');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $this->getSiteActionsTable()->deleteSiteAction($id);
                $this->flashMessenger()->addSuccessMessage('Action deleted successfully..!!'); 
            }

            // Redirect to list of Actions
            return $this->redirect()->toRoute('admin/siteactions');
        }

        return array(
            'id'    => $id,
            'siteactions' => $this->getSiteActionsTable()->getSiteAction($id)
        );
	}	
	
	public function getSiteActionsTable()
	{
		if (!$this->SiteActionsTable) {
			$sm = $this->getServiceLocator();
			$this->SiteActionsTable = $sm->get('Admin\Model\SiteActionsTable');
		}
	
		return $this->SiteActionsTable;
	}
	
}
