<?php

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Admin\Model\SiteMeta;
use Admin\Form\SiteMetaForm;

class SiteMetaController extends AbstractActionController
{
	protected $SiteMetaTable; 
	
	public function indexAction()
    {
        $paginator = $this->getSiteMetaTable()->fetchAll();  
		$paginator->setCurrentPageNumber((int)$this->params()->fromQuery('page', 1)); 
		$paginator->setItemCountPerPage(10);   
		
		return new ViewModel(array(
			'site_metas' => $paginator,
			'successMsgs' => $this->flashMessenger()->getCurrentSuccessMessages(),
			'errors' => $this->flashMessenger()->getCurrentErrorMessages()
		));
        
    }
    
    public function addAction()
    {
		$form = new SiteMetaForm();
        $form->get('submit')->setValue('Add');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $SiteMeta = new SiteMeta();
            $form->setInputFilter($SiteMeta->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
		
                $SiteMeta->exchangeArray($form->getData());
                $this->getSiteMetaTable()->saveSiteMeta($SiteMeta);
                $this->flashMessenger()->addSuccessMessage('Meta added successfully..!!');
                
                return $this->redirect()->toRoute('admin/sitemeta');
            }
        }
        return array('form' => $form);
	}
		
    public function editAction()
    {
		$id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('admin/sitemeta', array(
                'action' => 'add'
            ));
        }
        $SiteMeta = $this->getSiteMetaTable()->getSiteMeta($id);
        if ($SiteMeta == false) {
			$this->flashMessenger()->addErrorMessage('Meta not found..!!');
			return $this->redirect()->toRoute('admin/sitemeta');
		}

        $form  = new SiteMetaForm();
        $form->bind($SiteMeta);
        $form->get('submit')->setAttribute('value', 'Edit');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($SiteMeta->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $this->getSiteMetaTable()->saveSiteMeta($form->getData());
				$this->flashMessenger()->addSuccessMessage('Meta updated successfully..!!');
			
                return $this->redirect()->toRoute('admin/sitemeta');
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
            return $this->redirect()->toRoute('admin/sitemeta');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $this->getSiteMetaTable()->deleteSiteMeta($id);
                $this->flashMessenger()->addSuccessMessage('Meta deleted successfully..!!'); 
            }

            return $this->redirect()->toRoute('admin/sitemeta');
        }

        return array(
            'id'    => $id,
            'meta' => $this->getSiteMetaTable()->getSiteMeta($id)
        );
	}	
	
	public function getSiteMetaTable()
	{
		if (!$this->SiteMetaTable) {
			$sm = $this->getServiceLocator();
			$this->SiteMetaTable = $sm->get('Admin\Model\SiteMetaTable');
		}
	
		return $this->SiteMetaTable;
	}
	
}
