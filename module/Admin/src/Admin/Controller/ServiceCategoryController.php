<?php

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Admin\Model\ServiceCategory;
use Admin\Form\ServiceCategoryForm;

class ServiceCategoryController extends AbstractActionController
{

    private $servCatTable;
    public $errors = array();

    private function getServCatTable()
    {
        if (!$this->servCatTable) {
            $this->servCatTable = $this->getServiceLocator()->get('Admin\Model\ServiceCategoryTable');
        }
        return $this->servCatTable;
    }

    public function indexAction()
    {
        if (!$this->getServiceLocator()->get('AuthService')->hasIdentity()) {
            return $this->redirect()->toRoute('admin/login');
        }

        $paginator = $this->getServCatTable()->fetchAll();
        $paginator->setCurrentPageNumber((int) $this->params()->fromQuery('page', 1));
        $paginator->setItemCountPerPage(10);

        return new ViewModel(array('servicecats' => $paginator,
            'model' => $this->getServiceLocator()->get('Admin\Model\BookingsTable'),
            'successMsgs' => $this->flashMessenger()->getCurrentSuccessMessages(),
            'errors' => $this->flashMessenger()->getCurrentErrorMessages()));
    }

    public function addAction()
    {
        $form = new ServiceCategoryForm($this->getServiceLocator()->get('Admin\Model\ServiceCategoryTable'));
        $form->get('submit')->setValue('Add');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $servCat = new ServiceCategory();
            $servCat->setAdapter($this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'));
            $form->setInputFilter($servCat->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $servCat->exchangeArray($form->getData());
                $this->getServCatTable()->saveServiceCategory($servCat);
                $this->flashMessenger()->addSuccessMessage('Service Category added successfully..!!');

                // Redirect to list of pages
                return $this->redirect()->toRoute('admin/servicecategory');
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
            return $this->redirect()->toRoute('admin/servicecategory', array(
                        'action' => 'add'
            ));
        }
        $servCat = $this->getServCatTable()->getServiceCategory($id);
        if ($servCat == false) {
            $this->flashMessenger()->addErrorMessage('Service category not found..!!');
            return $this->redirect()->toRoute('admin/servicecategory');
        }
        $servCat->setAdapter($this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'));

        $form = new ServiceCategoryForm($this->getServiceLocator()->get('Admin\Model\ServiceCategoryTable'));
        $form->bind($servCat);
        $form->get('submit')->setAttribute('value', 'Edit');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($servCat->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                if ($request->getPost('parent_id') != $id) {
                    $this->getServCatTable()->saveServiceCategory($form->getData());
                    $this->flashMessenger()->addSuccessMessage('Service Category updated successfully..!!');

                    // Redirect to list of pages
                    return $this->redirect()->toRoute('admin/servicecategory');
                } else {
                    $form->get('parent_id')->setMessages(array('Category can not be its own parent category..!!'));
                }
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
            return $this->redirect()->toRoute('admin/servicecategory');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $msg = 'Service Category';
                if ($this->getServCatTable()->checkUsedStatus($id, $msg)) {

                    $this->getServCatTable()->deleteServiceCategory($id);
                    $this->flashMessenger()->addSuccessMessage('Service Category deleted successfully..!!');
                } else {
                    $this->flashMessenger()->addErrorMessage($msg);
                }
            }

            // Redirect to list of pages 
            return $this->redirect()->toRoute('admin/servicecategory');
        }

        return array(
            'id' => $id,
            'category' => $this->getServCatTable()->getServiceCategory($id)
        );
    }

}
