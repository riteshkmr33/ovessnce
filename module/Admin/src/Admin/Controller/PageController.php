<?php

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Admin\Model\Page;
use Admin\Form\PageForm;

class PageController extends AbstractActionController
{
	protected $pageTable; 
	
	public function indexAction() 
    {
        $paginator = $this->getPageTable()->fetchAll();   // grab the paginator from the PageTable
		$paginator->setCurrentPageNumber((int)$this->params()->fromQuery('page', 1));   // set the current page to what has been passed in query string, or to 1 if none set
		$paginator->setItemCountPerPage(10);   // set the number of items per page to 10
		
		return new ViewModel(array(
			'pages' => $paginator,
			'status' => $this->getServiceLocator()->get('Admin\Model\StatusTable')->fetchAll(false, array(1,2)),
			'successMsgs' => $this->flashMessenger()->getCurrentSuccessMessages(),
			'errors' => $this->flashMessenger()->getCurrentErrorMessages()
		));
        
    }
    
    public function addAction()
    {
		$form = new PageForm($this->getServiceLocator()->get('Admin\Model\StatusTable'),$this->getServiceLocator()->get('Admin\Model\SiteMetaTable'));
        $form->get('submit')->setValue('Add');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $page = new Page();
            $form->setInputFilter($page->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $page->exchangeArray($form->getData());
                $this->getPageTable()->savePage($page,$request->getPost('page_meta'));
                $this->flashMessenger()->addSuccessMessage('Page added successfully..!!');

                // Redirect to list of pages
                return $this->redirect()->toRoute('admin/page');
            }
        }
        return array('form' => $form);
	}
		
    public function editAction()
    {
		$id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('admin/page', array(
                'action' => 'add'
            ));
        }
        $page = $this->getPageTable()->getPage($id);
        if ($page == false) {
			$this->flashMessenger()->addErrorMessage('Page not found..!!');
			return $this->redirect()->toRoute('admin/page');
		}

        $form  = new PageForm($this->getServiceLocator()->get('Admin\Model\StatusTable'),$this->getServiceLocator()->get('Admin\Model\SiteMetaTable'),$this->getPageTable()->getPageMeta($id,true));
        $form->bind($page);
        $form->get('submit')->setAttribute('value', 'Edit');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($page->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $this->getPageTable()->savePage($form->getData(),$request->getPost('page_meta'));
				$this->flashMessenger()->addSuccessMessage('Page updated successfully..!!');
				
                // Redirect to list of pages
                return $this->redirect()->toRoute('admin/page');
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
            return $this->redirect()->toRoute('admin/page');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $this->getPageTable()->deletePage($id);
                $this->flashMessenger()->addSuccessMessage('Page deleted successfully..!!'); 
            }

            // Redirect to list of pages
            return $this->redirect()->toRoute('admin/page');
        }

        return array(
            'id'    => $id,
            'page' => $this->getPageTable()->getPage($id)
        );
	}	
	
	public function getPageTable()
	{
		if (!$this->pageTable) {
			$sm = $this->getServiceLocator();
			$this->pageTable = $sm->get('Admin\Model\PageTable');
		}
	
		return $this->pageTable;
	}
	
	public function changeStatusAction()
    {
		$request = $this->getRequest();
		if ($request->isPost()) {
			$id = $request->getPost('id');
			$status = $request->getPost('status', '1');
			if ($id != null && $status != null) {
				$this->getPageTable()->changeStatus($id, $status);
				echo json_encode(array("msg"=>"Status successfully changed..!!"));
			} else {
				echo json_encode(array("msg"=>"Failed to change the status..!!"));
			}
			exit;
		}
	}
	
}
