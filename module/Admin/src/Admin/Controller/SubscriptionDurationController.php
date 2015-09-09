<?php

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Admin\Model\SubscriptionDurations;
use Admin\Form\SubscriptionDurationForm;

class SubscriptionDurationController extends AbstractActionController
{

    private $getSubscriptionDurationTable;
    public $errors = array();

    private function getSDTable()
    {
        if (!$this->getSubscriptionDurationTable) {
            $this->getSubscriptionDurationTable = $this->getServiceLocator()->get('Admin\Model\SubscriptionDurationsTable');
        }
        return $this->getSubscriptionDurationTable;
    }

    public function indexAction()
    {
        $subscription_id = (int) $this->params()->fromRoute('subscription_id', 0);
        if (!$subscription_id) {
            return $this->redirect()->toRoute('admin/subscriptionplans');
        }

        $paginator = $this->getSDTable()->fetchAll($subscription_id);
        $paginator->setCurrentPageNumber((int) $this->Params()->fromQuery('page', 1));
        $paginator->setItemCountPerPage(10);

        return new ViewModel(array('subscriptiondurations' => $paginator,
            'subscription_id' => $subscription_id,
            'status' => $this->getServiceLocator()->get('Admin\Model\StatusTable')->fetchAll(false, array(1, 2)),
            'successMsgs' => $this->flashMessenger()->getCurrentSuccessMessages(),
            'errors' => $this->flashMessenger()->getCurrentErrorMessages()
        ));
    }

    public function addAction()
    {
        $subscription_id = (int) $this->params()->fromRoute('subscription_id', 0);
        if (!$subscription_id) {
            return $this->redirect()->toRoute('admin/subscriptionplans');
        }

        $form = new SubscriptionDurationForm($this->getServiceLocator()->get('Admin\Model\StatusTable'));
        $form->get('submit')->setValue('Add');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $sd = new SubscriptionDurations();

            $form->setInputFilter($sd->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $sd->exchangeArray($form->getData());
                $this->getSDTable()->saveSubscriptionDuration($sd);
                $this->flashMessenger()->addSuccessMessage('Subscription Duration added successfully..!!');

                // Redirect to list of pages
                return $this->redirect()->toRoute('admin/subscriptiondurations', array('subscription_id' => $subscription_id));
            } else {
                $this->errors = $form->getMessages();
            }
        }
        return array('form' => $form, 'errors' => $this->errors, 'fields' => $fields, 'subscription_id' => $subscription_id);
    }

    public function editAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        $subscription_id = (int) $this->params()->fromRoute('subscription_id', 0);
        if (!$id || !$subscription_id) {
            return $this->redirect()->toRoute('admin/subscriptiondurations', array(
                        'action' => 'add'
            ));
        }
        $sd = $this->getSDTable()->getSubscriptionDuration($id);
        //echo '<pre>'; print_r($sd); exit;
        if ($sd == false) {
            $this->flashMessenger()->addErrorMessage('Subscription duration not found..!!');
            return $this->redirect()->toRoute('admin/subscriptiondurations', array('subscription_id' => $subscription_id));
        }

        $form = new SubscriptionDurationForm($this->getServiceLocator()->get('Admin\Model\StatusTable'));
        $form->bind($sd);
        $form->get('submit')->setAttribute('value', 'Edit');

        $request = $this->getRequest();
        if ($request->isPost()) {

            $form->setInputFilter($sd->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $this->getSDTable()->saveSubscriptionDuration($form->getData());
                $this->flashMessenger()->addSuccessMessage('Subscription Duration updated successfully..!!');

                // Redirect to list of pages
                return $this->redirect()->toRoute('admin/subscriptiondurations', array('subscription_id' => $subscription_id));
            } else {
                $this->errors = $form->getMessages();
            }
        }

        return array(
            'id' => $id,
            'form' => $form,
            'errors' => $this->errors,
            'subscription_id' => $subscription_id
        );
    }

    public function deleteAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        $subscription_id = (int) $this->params()->fromRoute('subscription_id', 0);
        if (!$id || !$subscription_id) {
            return $this->redirect()->toRoute('admin/subscriptionplans');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $this->getSDTable()->deleteSubscriptionDuration($id);
                $this->flashMessenger()->addSuccessMessage('Subscription duration deleted successfully..!!');
            }

            // Redirect to list of pages 
            return $this->redirect()->toRoute('admin/subscriptiondurations', array('subscription_id' => $subscription_id));
        }

        return array(
            'id' => $id,
            'subscription_id' => $subscription_id,
            'subscriptionduration' => $this->getSDTable()->getSubscriptionDuration($id)
        );
    }

    public function changeStatusAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $id = $request->getPost('id');
            $status = $request->getPost('status', '1');
            if ($id != null && $status != null) {
                $this->getSDTable()->changeStatus($id, $status);
                echo json_encode(array("msg" => "Status successfully changed..!!"));
            } else {
                echo json_encode(array("msg" => "Failed to change the status..!!"));
            }
            exit;
        }
    }

}
