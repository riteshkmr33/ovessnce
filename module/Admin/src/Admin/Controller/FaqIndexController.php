<?php

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Admin\Model\FaqIndex;
use Admin\Form\FaqIndexForm;

class FaqIndexController extends AbstractActionController
{

    protected $FaqIndexTable;

    public function getFaqIndexTable()
    {
        if (!$this->FaqIndexTable) {
            $sm = $this->getServiceLocator();
            $this->FaqIndexTable = $sm->get('Admin\Model\FaqIndexTable');
        }

        return $this->FaqIndexTable;
    }

    public function indexAction()
    {
        $paginator = $this->getFaqIndexTable()->fetchAll();
        $paginator->setCurrentPageNumber((int) $this->params()->fromQuery('page', 1));
        $paginator->setItemCountPerPage(10);

        return new ViewModel(array(
            'faqindex' => $paginator,
            'status' => $this->getServiceLocator()->get('Admin\Model\StatusTable')->fetchAll(false, array(1, 2)),
            'successMsgs' => $this->flashMessenger()->getCurrentSuccessMessages(),
            'errors' => $this->flashMessenger()->getCurrentErrorMessages()
        ));
    }

    public function addAction()
    {

        $form = new FaqIndexForm($this->getServiceLocator()->get('Admin\Model\StatusTable'));
        $form->get('submit')->setValue('Add');

        $request = $this->getRequest();

        if ($request->isPost()) {

            $fi = new FaqIndex();
            $fi->getInputFilter()->get('order_by')->getValidatorChain()->attach(new \Zend\Validator\Db\NoRecordExists(array('table' => 'faq_index', 'field' => 'order_by', 'message' => 'order number already assigned', 'adapter' => $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'))));
            $form->setInputFilter($fi->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {

                $fi->exchangeArray($form->getData());
                $this->getFaqIndexTable()->saveFaqIndex($fi);
                $this->flashMessenger()->addSuccessMessage('Faq Index added successfully..!!');

                // Redirect to list 
                return $this->redirect()->toRoute('admin/faqindex');
            }
        }
        return array('form' => $form);
    }

    public function editAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('admin/faqindex', array('action' => 'add'));
        }

        $fi = $this->getFaqIndexTable()->getFaqIndex($id);
        if ($fi == false) {
            $this->flashMessenger()->addErrorMessage('Faq Index not found..!!');
            return $this->redirect()->toRoute('admin/faqindex');
        }

        $form = new FaqIndexForm($this->getServiceLocator()->get('Admin\Model\StatusTable'));
        $form->bind($fi);
        $form->get('submit')->setAttribute('value', 'Edit');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $fi->getInputFilter()->get('order_by')->getValidatorChain()->attach(new \Zend\Validator\Db\NoRecordExists(array('table' => 'faq_index', 'field' => 'order_by', 'adapter' => $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'), 'exclude' => array('field' => 'id', 'value' => $id), 'message' => 'order number already assigned')));
            $form->setInputFilter($fi->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $this->getFaqIndexTable()->saveFaqIndex($form->getData());
                $this->flashMessenger()->addSuccessMessage('Faq Index updated successfully..!!');

                // Redirect to list 
                return $this->redirect()->toRoute('admin/faqindex');
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
            return $this->redirect()->toRoute('admin/faqindex');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $this->getFaqIndexTable()->deleteFaqIndex($id);
                $this->flashMessenger()->addSuccessMessage('Faq Index deleted successfully..!!');
            }

            // Redirect to list 
            return $this->redirect()->toRoute('admin/faqindex');
        }

        return array(
            'id' => $id,
            'faqindex' => $this->getFaqIndexTable()->getFaqIndex($id)
        );
    }

    public function changeStatusAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $id = $request->getPost('id');
            $status = $request->getPost('status', '1');
            if ($id != null && $status != null) {
                $this->getFaqIndexTable()->changeStatus($id, $status);
                echo json_encode(array("msg" => "Status successfully changed..!!"));
            } else {
                echo json_encode(array("msg" => "Failed to change the status..!!"));
            }
        }
        exit;
    }

}
