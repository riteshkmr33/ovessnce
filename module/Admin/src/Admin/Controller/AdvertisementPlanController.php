<?php

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Admin\Model\AdvertisementPlan;
use Admin\Form\AdvertisementPlanForm;

class AdvertisementPlanController extends AbstractActionController
{

    private $getAdvertisementPlanTable;
    public $errors = array();

    private function getAdvertisementPlanTable()
    {
        if (!$this->getAdvertisementPlanTable) {
            $this->getAdvertisementPlanTable = $this->getServiceLocator()->get('Admin\Model\AdvertisementPlanTable');
        }

        return $this->getAdvertisementPlanTable;
    }

    public function indexAction()
    {
        $paginator = $this->getAdvertisementPlanTable()->fetchAll();
        $paginator->setCurrentPageNumber((int) $this->Params()->fromQuery('page', 1));
        $paginator->setItemCountPerPage(10);

        return new ViewModel(array('advertisementplans' => $paginator,
            'successMsgs' => $this->flashMessenger()->getCurrentSuccessMessages(),
            'errors' => $this->flashMessenger()->getCurrentErrorMessages()
        ));
    }

    public function addAction()
    {
        $form = new AdvertisementPlanForm($this->getServiceLocator()->get('Admin\Model\AdvertisementTable'), $this->getServiceLocator()->get('Admin\Model\AdvertisementPageTable'));
        $form->get('submit')->setValue('Add');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $ap = new AdvertisementPlan();

            $form->setInputFilter($ap->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $ap->exchangeArray($form->getData());
                $this->getAdvertisementPlanTable()->saveAdvertisementPlan($ap);
                $this->flashMessenger()->addSuccessMessage('Advertisement Plan added successfully..!!');

                // Redirect to list of pages
                return $this->redirect()->toRoute('admin/advertisementplan');
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
            return $this->redirect()->toRoute('admin/advertisementplan');
        }
        
        $ap = $this->getAdvertisementPlanTable()->getAdvertisementPlan($id);
        
        if ($ap == false) {
            $this->flashMessenger()->addErrorMessage('Advertisement plan not found..!!');
            return $this->redirect()->toRoute('admin/advertisementplan');
        }

        $form = new AdvertisementPlanForm($this->getServiceLocator()->get('Admin\Model\AdvertisementTable'), $this->getServiceLocator()->get('Admin\Model\AdvertisementPageTable'));
        $form->bind($ap);
        $form->get('submit')->setAttribute('value', 'Edit');

        $request = $this->getRequest();
        if ($request->isPost()) {

            $form->setInputFilter($ap->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $this->getAdvertisementPlanTable()->saveAdvertisementPlan($form->getData());
                $this->flashMessenger()->addSuccessMessage('Advertisement Plan updated successfully..!!');
                // Redirect to list of pages
                return $this->redirect()->toRoute('admin/advertisementplan');
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
            return $this->redirect()->toRoute('admin/advertisementplan');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $this->getAdvertisementPlanTable()->deleteAdvertisementPlan($id);
                $this->flashMessenger()->addSuccessMessage('Advertisement Plan deleted successfully..!!');
            }

            // Redirect to list of pages 
            return $this->redirect()->toRoute('admin/advertisementplan');
        }

        return array(
            'id' => $id,
            'advertisementplan' => $this->getAdvertisementPlanTable()->getAdvertisementPlan($id),
        );
    }

}
