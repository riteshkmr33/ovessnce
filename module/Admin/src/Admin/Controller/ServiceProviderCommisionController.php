<?php

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Admin\Model\ServiceProviderCommisions;
use Admin\Form\ServiceProviderCommisionForm;

class ServiceProviderCommisionController extends AbstractActionController
{
	private $getServiceProviderCommisionTable;
	
	private function getSPCTable()
	{
		if (!$this->getServiceProviderCommisionTable) {
			$this->getServiceProviderCommisionTable = $this->getServiceLocator()->get('Admin\Model\ServiceProviderCommisionsTable');
		}
		
		return $this->getServiceProviderCommisionTable;
	}

    public function indexAction()
    {
		$id = (int) $this->params()->fromRoute('user_id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('admin/serviceproviders');
        }
        
		$paginator = $this->getSPCTable()->fetchAll($id);
		$paginator->setCurrentPageNumber((int)$this->Params()->fromQuery('page',1));
		$paginator->setItemCountPerPage(10);
		
		$service_provider = $this->getServiceLocator()->get('Admin\Model\ServiceProviderTable')->getServiceProvider($id);
		
        return new ViewModel(array('serviceprovidercommisions' => $paginator,
			'user_id' => $id,
			'name' => ucfirst($service_provider->first_name),
			'successMsgs' => $this->flashMessenger()->getCurrentSuccessMessages(),
			'errors' => $this->flashMessenger()->getCurrentErrorMessages()
        ));
    }
    
    public function addAction()
    {
		$id = (int) $this->params()->fromRoute('user_id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('admin/serviceproviders');
        }
        
        $form = new ServiceProviderCommisionForm();
        $form->get('submit')->setValue('Add');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $commision = new ServiceProviderCommisions();
            
            $form->setInputFilter($commision->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $commision->exchangeArray($form->getData());
                $this->getSPCTable()->saveServiceProviderCommision($commision);
                $this->flashMessenger()->addSuccessMessage("Service Provider's Commision added successfully..!!");

                // Redirect to list of pages
                return $this->redirect()->toRoute('admin/serviceprovidercommisions', array('user_id' => $id));
            } else {
				$this->errors = $form->getMessages();
			}
        }
        return array(
			'user_id' => $id,
			'form' => $form,
			'errors' => $this->errors);
    }
    
    public function changeStatusAction()
    {
		$request = $this->getRequest();
		if ($request->isPost()) {
			$id = $request->getPost('id');
			$status = $request->getPost('status', '1');
			if ($id != null && $status != null) {
				$this->getSPCTable()->changeStatus($id, $status);
				echo json_encode(array("msg"=>"Status successfully changed..!!"));
			} else {
				echo json_encode(array("msg"=>"Failed to change the status..!!"));
			}
			exit;
		}
	}


}

