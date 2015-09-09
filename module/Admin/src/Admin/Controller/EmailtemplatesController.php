<?php

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Admin\Model\Emailtemplates;
use Admin\Form\EmailtemplatesForm;

class EmailtemplatesController extends AbstractActionController
{

    protected $EmailtemplatesTable;

    public function indexAction()
    {
        $paginator = $this->getEmailtemplatesTable()->fetchAll();
        $paginator->setCurrentPageNumber((int) $this->params()->fromQuery('page', 1));
        $paginator->setItemCountPerPage(10);

        return new ViewModel(array(
            'emailtemplates' => $paginator,
            'status' => $this->getServiceLocator()->get('Admin\Model\StatusTable')->fetchAll(false, array(1, 2)),
            'successMsgs' => $this->flashMessenger()->getCurrentSuccessMessages(),
            'errors' => $this->flashMessenger()->getCurrentErrorMessages()
        ));
    }

    public function addAction()
    {
        //return $this->redirect()->toRoute('admin/emailtemplates'); // add functionality is disabled for the time being
        $form = new EmailtemplatesForm($this->getServiceLocator()->get('Admin\Model\StatusTable'));
        $form->get('submit')->setValue('Add');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $Emailtemplates = new Emailtemplates();
            $form->setInputFilter($Emailtemplates->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $Emailtemplates->exchangeArray($form->getData());
                $this->getEmailtemplatesTable()->saveEmailtemplate($Emailtemplates);

                // Redirect to list of pages
                return $this->redirect()->toRoute('admin/emailtemplates');
                $this->flashMessenger()->addSuccessMessage('Email template added successfully..!!');
            }
        } else {
            $form->get('status')->setValue('1');
        }
        return array('form' => $form);
    }

    public function editAction()
    {

        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('admin/emailtemplates', array(
                        'action' => 'add'
            ));
        }
        $Emailtemplate = $this->getEmailtemplatesTable()->getEmailtemplate($id);
        if ($Emailtemplate == false) {
            $this->flashMessenger()->addErrorMessage('Email template not found..!!');
            return $this->redirect()->toRoute('admin/emailtemplates');
        }
        
        $Emailtemplate->content = stripslashes($Emailtemplate->content);
        
        $form = new EmailtemplatesForm($this->getServiceLocator()->get('Admin\Model\StatusTable'));
        $form->bind($Emailtemplate);
        $form->get('submit')->setAttribute('value', 'Edit');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($Emailtemplate->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $this->getEmailtemplatesTable()->saveEmailtemplate($form->getData());
                $this->flashMessenger()->addSuccessMessage('Email template updated successfully..!!');

                // Redirect to list of pages
                return $this->redirect()->toRoute('admin/emailtemplates');
            }
        }

        return array(
            'id' => $id,
            'form' => $form,
        );
    }

    public function deleteAction()
    {
        return $this->redirect()->toRoute('admin/emailtemplates'); // delete functionality is disabled for the time being
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('admin/emailtemplates');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $this->getEmailtemplatesTable()->deleteEmailtemplate($id);
                $this->flashMessenger()->addSuccessMessage('Email template deleted successfully..!!');
            }

            // Redirect to list of pages
            return $this->redirect()->toRoute('admin/emailtemplates');
        }

        return array(
            'id' => $id,
            'emailtemplates' => $this->getEmailtemplatesTable()->getEmailtemplate($id)
        );
    }

    public function getEmailtemplatesTable()
    {
        if (!$this->EmailtemplatesTable) {
            $sm = $this->getServiceLocator();
            $this->EmailtemplatesTable = $sm->get('Admin\Model\EmailtemplatesTable');
        }

        return $this->EmailtemplatesTable;
    }

    public function changeStatusAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $id = $request->getPost('id');
            $status = $request->getPost('status', '1');
            if ($id != null && $status != null) {
                $this->getEmailtemplatesTable()->changeStatus($id, $status);
                echo json_encode(array("msg" => "Status successfully changed..!!"));
            } else {
                echo json_encode(array("msg" => "Failed to change the status..!!"));
            }
            exit;
        }
    }

}
