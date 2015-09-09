<?php

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Admin\Model\Advertisement;
use Admin\Form\AdvertisementForm;

class AdvertisementController extends AbstractActionController
{

    private $getAdvertisementTable;

    private function getAdvertisementTable()
    {
        if (!$this->getAdvertisementTable) {
            $this->getAdvertisementTable = $this->getServiceLocator()->get('Admin\Model\AdvertisementTable');
        }

        return $this->getAdvertisementTable;
    }

    public function indexAction()
    {
        $paginator = $this->getAdvertisementTable()->fetchAll();
        $paginator->setCurrentPageNumber((int) $this->Params()->fromQuery('page', 1));
        $paginator->setItemCountPerPage(10);

        return new ViewModel(array('advertisements' => $paginator,
            'status' => $this->getServiceLocator()->get('Admin\Model\StatusTable')->fetchAll(false, array(1, 2)),
            'successMsgs' => $this->flashMessenger()->getCurrentSuccessMessages(),
            'errors' => $this->flashMessenger()->getCurrentErrorMessages()
        ));
    }

    public function addAction()
    {
        $form = new AdvertisementForm($this->getServiceLocator()->get('Admin\Model\StatusTable'));
        //$form = new AdvertisementForm();
        $form->get('submit')->setValue('Add');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $advertisement = new Advertisement();

            $form->setInputFilter($advertisement->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $advertisement->exchangeArray($form->getData());
                $this->getAdvertisementTable()->saveAdvertisement($advertisement);
                $this->flashMessenger()->addSuccessMessage('Advertisement added successfully..!!');

                // Redirect to list of pages
                return $this->redirect()->toRoute('admin/advertisement');
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
            return $this->redirect()->toRoute('admin/advertisement', array(
                        'action' => 'add'
            ));
        }
        $advertisement = $this->getAdvertisementTable()->getAdvertisement($id);

        if ($advertisement == false) {
            $this->flashMessenger()->addErrorMessage('Advertisement not found..!!');
            return $this->redirect()->toRoute('admin/advertisement');
        }

        $form = new AdvertisementForm($this->getServiceLocator()->get('Admin\Model\StatusTable'));
        $form->bind($advertisement);
        $form->get('submit')->setAttribute('value', 'Edit');

        $request = $this->getRequest();
        if ($request->isPost()) {

            $form->setInputFilter($advertisement->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $this->getAdvertisementTable()->saveAdvertisement($form->getData());
                $this->flashMessenger()->addSuccessMessage('Advertisement updated successfully..!!');
                // Redirect to list of pages
                return $this->redirect()->toRoute('admin/advertisement');
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
            return $this->redirect()->toRoute('admin/advertisement');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $this->getAdvertisementTable()->deleteAdvertisement($id);
                $this->flashMessenger()->addSuccessMessage('Advertisment deleted successfully..!!');
            }

            // Redirect to list of pages 
            return $this->redirect()->toRoute('admin/advertisement');
        }

        return array(
            'id' => $id,
            'advertisement' => $this->getAdvertisementTable()->getAdvertisement($id)
        );
    }

    public function changeStatusAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $id = $request->getPost('id');
            $status = $request->getPost('status', '1');
            if ($id != null && $status != null) {
                $this->getAdvertisementTable()->changeStatus($id, $status);
                echo json_encode(array("msg" => "Status successfully changed..!!"));
            } else {
                echo json_encode(array("msg" => "Failed to change the status..!!"));
            }
            exit;
        }
    }

}
