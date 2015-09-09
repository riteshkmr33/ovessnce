<?php

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Admin\Model\ServiceProviderServices;
use Admin\Form\ServiceProviderServiceForm;

class ServiceProviderServiceController extends AbstractActionController
{
	private $getServiceProviderServicesTable;
	
	private function getSPSTable()
	{
		if (!$this->getServiceProviderServicesTable) {
			$this->getServiceProviderServicesTable = $this->getServiceLocator()->get('Admin\Model\ServiceProviderServicesTable');
		}
		
		return $this->getServiceProviderServicesTable;
	}

    public function indexAction()
    {
		$id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('admin/serviceproviders');
        }
        
        $paginator = $this->getSPSTable()->fetchAll($id);
		$paginator->setCurrentPageNumber((int)$this->params()->fromQuery('page', 1));
		$paginator->setItemCountPerPage(10);
		
		$service_provider = $this->getServiceLocator()->get('Admin\Model\ServiceProviderTable')->getServiceProvider($id);
		
        return new ViewModel(array('services'=>$paginator,
        'id' => $id,
        'name' => ucfirst($service_provider->first_name),
        'status' => $this->getServiceLocator()->get('Admin\Model\StatusTable')->fetchAll(false),
        'successMsgs' => $this->flashMessenger()->getCurrentSuccessMessages(),
		'errors' => $this->flashMessenger()->getCurrentErrorMessages()
        ));
    }
    
    public function addAction()
    {
		$id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('admin/serviceproviders');
        }
        
        $form = new ServiceProviderServiceForm($this->getServiceLocator()->get('Admin\Model\StatusTable'), $this->getServiceLocator()->get('Admin\Model\ServiceCategoryTable'));
        $form->get('submit')->setValue('Add');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $service = new ServiceProviderServices();
            
            $form->setInputFilter($service->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $service->exchangeArray($form->getData());
                $this->getSPSTable()->saveService($service);
                $this->flashMessenger()->addSuccessMessage("Service Provider's Service added successfully..!!");

                // Redirect to list of pages
                return $this->redirect()->toRoute('admin/serviceproviderservices', array('id' => $id));
            } else {
				$this->errors = $form->getMessages();
			}
        }
        return array(
			'id' => $id,
			'form' => $form,
			'errors' => $this->errors);
    }
    
    public function editAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        $service_id = (int) $this->params()->fromRoute('service_id', 0);
        if (!$id || !$service_id) {
            return $this->redirect()->toRoute('admin/serviceproviders');
        }
        
        $service = $this->getSPSTable()->getService($service_id);
        if ($service == false) {
			$this->flashMessenger()->addErrorMessage('Service provider service not found..!!');
			return $this->redirect()->toRoute('admin/serviceproviderservices', array('id' => $id));
		}
		$service->setAdapter($this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'));
		
        $form = new ServiceProviderServiceForm($this->getServiceLocator()->get('Admin\Model\StatusTable'), $this->getServiceLocator()->get('Admin\Model\ServiceCategoryTable'));
        $form->bind($service);
        $form->get('submit')->setAttribute('value', 'Edit');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($service->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $this->getSPSTable()->saveService($form->getData());
                $this->flashMessenger()->addSuccessMessage("Service Provider's Service added successfully..!!");

                // Redirect to list of pages
                return $this->redirect()->toRoute('admin/serviceproviderservices', array('id' => $id));
            } else {
				$this->errors = $form->getMessages();
			}
        }

        return array(
            'id' => $id,
            'service_id' => $service_id,
            'form' => $form,
            'errors' => $this->errors
        );
    }
    
    public function deleteAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        $service_id = (int) $this->params()->fromRoute('service_id', 0);
        if (!$id || !$service_id) {
            return $this->redirect()->toRoute('admin/serviceproviders');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $service_id = (int) $request->getPost('service_id');
                $this->getSPSTable()->deleteService($service_id);
                $this->flashMessenger()->addSuccessMessage("Service Provider's Service added successfully..!!");
            }

            // Redirect to list of pages 
            return $this->redirect()->toRoute('admin/serviceproviderservices', array('id' => $id));
        }

        return array(
            'id'    => $id,
            'service_id'    => $service_id,
            'service' => $this->getSPSTable()->getService($service_id)
        );
    }
    
    public function changeStatusAction()
    {
		$request = $this->getRequest();
		if ($request->isPost()) {
			$id = $request->getPost('id');
			$status = $request->getPost('status', '1');
			if ($id != null && $status != null) {
				$this->getSPSTable()->changeStatus($id, $status);
				echo json_encode(array("msg"=>"Status successfully changed..!!"));
			} else {
				echo json_encode(array("msg"=>"Failed to change the status..!!"));
			}
			exit;
		}
	}


}

