<?php

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Admin\Model\Newsletters;
use Admin\Form\NewsletterForm;

class NewsletterController extends AbstractActionController
{

    private $newsletterTable;
    public $errors = array();

    private function getNewsletterTable()
    {
        if (!$this->newsletterTable) {
            $this->newsletterTable = $this->getServiceLocator()->get('Admin\Model\NewslettersTable');
        }

        return $this->newsletterTable;
    }

    public function indexAction()
    {
        $paginator = $this->getNewsletterTable()->fetchAll();
        $paginator->setCurrentPageNumber((int) $this->params()->fromQuery('page', 1));
        $paginator->setItemCountPerPage(10);

        return new ViewModel(array(
            'newsletters' => $paginator,
            'status' => $this->getServiceLocator()->get('Admin\Model\StatusTable')->fetchAll(false, array(1, 2, 3)),
            'successMsgs' => $this->flashMessenger()->getCurrentSuccessMessages(),
            'errors' => $this->flashMessenger()->getCurrentErrorMessages()
        ));
        ;
    }

    public function addAction()
    {
        $form = new NewsletterForm($this->getServiceLocator()->get('Admin\Model\StatusTable'), $this->getServiceLocator()->get('Admin\Model\UserTypeTable'));

        //$form = new StateForm();
        $form->get('submit')->setValue('Add');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $newsletter = new Newsletters();

            $form->setInputFilter($newsletter->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $newsletter->exchangeArray($form->getData());
                $this->getNewsletterTable()->saveNewsletter($newsletter);
                $this->flashMessenger()->addSuccessMessage('Newsletter added successfully..!!');

                // Redirect to list of pages
                return $this->redirect()->toRoute('admin/newsletters');
            }
        }
        return array('form' => $form);
    }

    public function editAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('admin/newsletters', array(
                        'action' => 'add'
            ));
        }
        $newsletter = $this->getNewsletterTable()->getNewsletter($id);
        if ($newsletter == false) {
            $this->flashMessenger()->addErrorMessage('Newsletter not found..!!');
            return $this->redirect()->toRoute('admin/newsletters');
        }

        $newsletter->setAdapter($this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'));

        $form = new NewsletterForm($this->getServiceLocator()->get('Admin\Model\StatusTable'), $this->getServiceLocator()->get('Admin\Model\UserTypeTable'));
        $form->bind($newsletter);
        $form->get('submit')->setAttribute('value', 'Edit');

        $request = $this->getRequest();
        if ($request->isPost()) {

            $form->setInputFilter($newsletter->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $this->getNewsletterTable()->saveNewsletter($form->getData());
                $this->flashMessenger()->addSuccessMessage('Newsletter updated successfully..!!');

                // Redirect to list of pages
                return $this->redirect()->toRoute('admin/newsletters');
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
            return $this->redirect()->toRoute('admin/newsletters');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $this->getNewsletterTable()->deleteNewsletter($id);
                $this->flashMessenger()->addSuccessMessage('Newsletter deleted successfully..!!');
            }

            // Redirect to list of pages 
            return $this->redirect()->toRoute('admin/newsletters');
        }

        return array(
            'id' => $id,
            'newsletter' => $this->getNewsletterTable()->getNewsletter($id)
        );
    }

    public function changeStatusAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $id = $request->getPost('id');
            $status = $request->getPost('status', '1');
            if ($id != null && $status != null) {
                $this->getNewsletterTable()->changeStatus($id, $status);
                echo json_encode(array("msg" => "Status successfully changed..!!"));
            } else {
                echo json_encode(array("msg" => "Failed to change the status..!!"));
            }
            exit;
        }
    }

}
