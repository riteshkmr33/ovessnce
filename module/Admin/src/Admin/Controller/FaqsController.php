<?php

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Admin\Model\Faqs;
use Admin\Form\FaqsForm;

class FaqsController extends AbstractActionController
{

    protected $FaqsTable;

    public function getFaqsTable()
    {
        if (!$this->FaqsTable) {
            $sm = $this->getServiceLocator();
            $this->FaqsTable = $sm->get('Admin\Model\FaqsTable');
        }

        return $this->FaqsTable;
    }

    public function indexAction()
    {
        $paginator = $this->getFaqsTable()->fetchAll();
        $paginator->setCurrentPageNumber((int) $this->params()->fromQuery('page', 1));
        $paginator->setItemCountPerPage(10);

        return new ViewModel(array(
            'faqs' => $paginator,
            'status' => $this->getServiceLocator()->get('Admin\Model\StatusTable')->fetchAll(false, array(1, 2)),
            'successMsgs' => $this->flashMessenger()->getCurrentSuccessMessages(),
            'errors' => $this->flashMessenger()->getCurrentErrorMessages()
        ));
    }

    public function addAction()
    {

        $form = new FaqsForm($this->getServiceLocator()->get('Admin\Model\FaqIndexTable'), $this->getServiceLocator()->get('Admin\Model\UserTypeTable'), $this->getServiceLocator()->get('Admin\Model\StatusTable'));
        $form->get('submit')->setValue('Add');

        $request = $this->getRequest();

        if ($request->isPost()) {

            $faqs = new Faqs();
            $faqs->getInputFilter()->get('order_by')->getValidatorChain()->attach(new \Zend\Validator\Db\NoRecordExists(array('table' => 'faqs', 'field' => 'order_by', 'message' => 'order number already assigned', 'adapter' => $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'))));
            $form->setInputFilter($faqs->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {

                $faqs->exchangeArray($form->getData());
                $this->getFaqsTable()->saveFaq($faqs);
                $this->flashMessenger()->addSuccessMessage('Faq added successfully..!!');

                // Redirect to list 
                return $this->redirect()->toRoute('admin/faqs');
            }
        }
        return array('form' => $form);
    }

    public function editAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('admin/faqs', array('action' => 'add'));
        }

        $faq = $this->getFaqsTable()->getFaq($id);
        if ($faq == false) {
            $this->flashMessenger()->addErrorMessage('Faq not found..!!');
            return $this->redirect()->toRoute('admin/faqindex');
        }
        
        $faq->answer = stripslashes($faq->answer);

        $form = new FaqsForm($this->getServiceLocator()->get('Admin\Model\FaqIndexTable'), $this->getServiceLocator()->get('Admin\Model\UserTypeTable'), $this->getServiceLocator()->get('Admin\Model\StatusTable'));
        $form->bind($faq);
        $form->get('submit')->setAttribute('value', 'Edit');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $faq->getInputFilter()->get('order_by')->getValidatorChain()->attach(new \Zend\Validator\Db\NoRecordExists(array('table' => 'faqs', 'field' => 'order_by', 'adapter' => $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'), 'exclude' => array('field' => 'id', 'value' => $id), 'message' => 'order number already assigned')));
            $form->setInputFilter($faq->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $this->getFaqsTable()->saveFaq($form->getData());
                $this->flashMessenger()->addSuccessMessage('Faq updated successfully..!!');

                // Redirect to list 
                return $this->redirect()->toRoute('admin/faqs');
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
            return $this->redirect()->toRoute('admin/faqs');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $this->getFaqsTable()->deleteFaq($id);
                $this->flashMessenger()->addSuccessMessage('Faq deleted successfully..!!');
            }

            // Redirect to list 
            return $this->redirect()->toRoute('admin/faqs');
        }

        return array(
            'id' => $id,
            'faq' => $this->getFaqsTable()->getFaq($id)
        );
    }

    public function changeStatusAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $id = $request->getPost('id');
            $status = $request->getPost('status', '1');
            if ($id != null && $status != null) {
                $this->getFaqsTable()->changeStatus($id, $status);
                echo json_encode(array("msg" => "Status successfully changed..!!"));
            } else {
                echo json_encode(array("msg" => "Failed to change the status..!!"));
            }
        }
        exit;
    }

}
