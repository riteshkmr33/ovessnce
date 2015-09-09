<?php

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Admin\Model\NewsletterSubscribers;
use Admin\Form\NewsletterSubscriberForm;

class NewsletterSubscriberController extends AbstractActionController
{

    private $subscriberTable;
    public $errors = array();

    private function getSubscriberTable()
    {
        if (!$this->subscriberTable) {
            $this->subscriberTable = $this->getServiceLocator()->get('Admin\Model\NewsletterSubscribersTable');
        }

        return $this->subscriberTable;
    }

    public function indexAction()
    {
        $paginator = $this->getSubscriberTable()->fetchAll(true, $this->getRequest()->getQuery('usertype'));
        $paginator->setCurrentPageNumber((int) $this->params()->fromQuery('page', 1));
        $paginator->setItemCountPerPage(10);

        return new ViewModel(array(
            'newslettersubscribers' => $paginator,
            'status' => $this->getServiceLocator()->get('Admin\Model\StatusTable')->fetchAll(false, array(1, 2, 3)),
            'successMsgs' => $this->flashMessenger()->getCurrentSuccessMessages(),
            'errors' => $this->flashMessenger()->getCurrentErrorMessages(),
            'postedData' => $this->getRequest()->getQuery()
        ));
    }

    public function addAction()
    {
        $form = new NewsletterSubscriberForm($this->getServiceLocator()->get('Admin\Model\StatusTable'));

        //$form = new NewsletterSubscriberForm();
        $form->get('submit')->setValue('Add');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $subscriber = new NewsletterSubscribers();
            $subscriber->setAdapter($this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'));
            $form->setInputFilter($subscriber->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $subscriber->exchangeArray($form->getData());
                $this->getSubscriberTable()->saveSubscriber($subscriber);
                $this->flashMessenger()->addSuccessMessage('Newsletter Subscriber added successfully..!!');

                // Redirect to list of pages
                return $this->redirect()->toRoute('admin/newslettersubscribers');
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
            return $this->redirect()->toRoute('admin/newslettersubscribers', array(
                        'action' => 'add'
            ));
        }
        $subscriber = $this->getSubscriberTable()->getSubscriber($id);
        if ($subscriber == false) {
            $this->flashMessenger()->addErrorMessage('Newsletter subscriber not found..!!');
            return $this->redirect()->toRoute('admin/newslettersubscribers');
        }

        $subscriber->setAdapter($this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'));

        $form = new NewsletterSubscriberForm($this->getServiceLocator()->get('Admin\Model\StatusTable'));
        $form->bind($subscriber);
        $form->get('submit')->setAttribute('value', 'Edit');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($subscriber->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $this->getSubscriberTable()->saveSubscriber($form->getData());
                $this->flashMessenger()->addSuccessMessage('Newsletter Subscriber updated successfully..!!');

                // Redirect to list of pages
                return $this->redirect()->toRoute('admin/newslettersubscribers');
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
            return $this->redirect()->toRoute('admin/newslettersubscribers');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $this->getSubscriberTable()->deleteSubscriber($id);
                $this->flashMessenger()->addSuccessMessage('Newsletter Subscriber deleted successfully..!!');
            }

            // Redirect to list of pages 
            return $this->redirect()->toRoute('admin/newslettersubscribers');
        }

        return array(
            'id' => $id,
            'newslettersubscriber' => $this->getSubscriberTable()->getSubscriber($id)
        );
    }

    public function changeStatusAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $id = $request->getPost('id');
            $status = $request->getPost('status', '1');
            if ($id != null && $status != null) {
                $this->getSubscriberTable()->changeStatus($id, $status);
                echo json_encode(array("msg" => "Status successfully changed..!!"));
            } else {
                echo json_encode(array("msg" => "Failed to change the status..!!"));
            }
            exit;
        }
    }

}
