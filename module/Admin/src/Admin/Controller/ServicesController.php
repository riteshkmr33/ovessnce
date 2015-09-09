<?php

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Admin\Model\Services;
use Admin\Form\ServiceForm;

class ServicesController extends AbstractActionController
{

    private $serviceTable;
    public $errors = array();

    private function getServiceTable()
    {
        if (!$this->serviceTable) {
            $this->serviceTable = $this->getServiceLocator()->get('Admin\Model\ServicesTable');
        }

        return $this->serviceTable;
    }

    public function indexAction()
    {
        $paginator = $this->getServiceTable()->fetchAll();
        $paginator->setCurrentPageNumber((int) $this->params()->fromQuery('page', 1));
        $paginator->setItemCountPerPage(10);

        return new ViewModel(array('services' => $paginator,
            'status' => $this->getServiceLocator()->get('Admin\Model\StatusTable')->fetchAll(false),
            'successMsgs' => $this->flashMessenger()->getCurrentSuccessMessages(),
            'errors' => $this->flashMessenger()->getCurrentErrorMessages()
        ));
    }

    public function addAction()
    {
        $form = new ServiceForm($this->getServiceLocator()->get('Admin\Model\ServiceCategoryTable'));
        $form->get('submit')->setValue('Add');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $service = new Services();
            $service->setAdapter($this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'));
            $form->setInputFilter($service->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $service->exchangeArray($form->getData());
                $this->getServiceTable()->saveService($service);
                $this->flashMessenger()->addSuccessMessage('Service added successfully..!!');

                // Redirect to list of pages
                return $this->redirect()->toRoute('admin/services');
            } else {
                $this->errors = $form->getMessages();
            }
        }
        return array('form' => $form, 'errors' => $this->errors);
    }

    public function editAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('admin/services', array(
                        'action' => 'add'
            ));
        }
        $service = $this->getServiceTable()->getService($id);
        $service->setAdapter($this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'));

        $form = new ServiceForm($this->getServiceLocator()->get('Admin\Model\ServiceCategoryTable'));
        $form->bind($service);
        $form->get('submit')->setAttribute('value', 'Edit');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($service->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $this->getServiceTable()->saveService($form->getData());
                $this->flashMessenger()->addSuccessMessage('Service updated successfully..!!');

                // Redirect to list of pages
                return $this->redirect()->toRoute('admin/services');
            } else {
                $this->errors = $form->getMessages();
            }
        }

        return array(
            'id' => $id,
            'form' => $form,
            'errors' => $this->errors
        );
    }

    public function deleteAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('admin/services');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $this->getServiceTable()->deleteService($id);
                $this->flashMessenger()->addSuccessMessage('Service deleted successfully..!!');
            }

            // Redirect to list of pages 
            return $this->redirect()->toRoute('admin/services');
        }

        return array(
            'id' => $id,
            'service' => $this->getServiceTable()->getService($id)
        );
    }

    public function changeStatusAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $id = $request->getPost('id');
            $status = $request->getPost('status', '1');
            if ($id != null && $status != null) {
                $this->getServiceTable()->changeStatus($id, $status);
                echo json_encode(array("msg" => "Status successfully changed..!!"));
            } else {
                echo json_encode(array("msg" => "Failed to change the status..!!"));
            }
            exit;
        }
    }

    public function practitionerservicesAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $id = $request->getPost('id');
            $service = $request->getPost('service');
            $duration = $request->getPost('duration');
            if ($id != null) {
                $services = $this->getServiceTable()->getPractitionerServices($id, $service, $duration);
                $workAddress = $this->getServiceLocator()->get('Admin\Model\ServiceProviderTable')->getServiceProviderServiceAddress($id);
                $addresses = array();
                foreach ($workAddress as $add) {
                    $addresses[] = array('id' => $add->id, 'address' => $add->street1_address.', '.$add->city.', '.$add->state_name.' '.$add->zip_code.', '.$add->country_name);
                }
                echo json_encode(array('services' => $services, 'addresses' => $addresses));
            }
        }
        exit;
    }

}
