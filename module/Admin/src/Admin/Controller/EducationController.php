<?php

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Admin\Model\Educations;
use Admin\Form\EducationForm;

class EducationController extends AbstractActionController
{

    private $educationTable;

    private function getEducationTable()
    {
        if (!$this->educationTable) {
            $this->educationTable = $this->getServiceLocator()->get('Admin\Model\EducationsTable');
        }

        return $this->educationTable;
    }

    public function indexAction()
    {
        $paginator = $this->getEducationTable()->fetchAll();
        $paginator->setCurrentPageNumber((int) $this->params()->fromQuery('page', 1));
        $paginator->setItemCountPerPage(10);

        return new ViewModel(array(
            'educations' => $paginator,
            'status' => $this->getServiceLocator()->get('Admin\Model\StatusTable')->fetchAll(false, array(1, 2)),
            'successMsgs' => $this->flashMessenger()->getCurrentSuccessMessages(),
            'errors' => $this->flashMessenger()->getCurrentErrorMessages()
        ));
    }

    public function addAction()
    {
        $form = new EducationForm($this->getServiceLocator()->get('Admin\Model\StatusTable'));
        $form->get('submit')->setValue('Add');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $education = new Educations();

            // Adding already exist validation on runtime excluding the current record
            $education->getInputFilter()->get('education_label')->getValidatorChain()->attach(new \Zend\Validator\Db\NoRecordExists(array('table' => 'educations', 'field' => 'education_label', 'adapter' => $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'))));

            $form->setInputFilter($education->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $education->exchangeArray($form->getData());
                $this->getEducationTable()->saveEducation($education);
                $this->flashMessenger()->addSuccessMessage('school added successfully..!!');

                // Redirect to list of pages
                return $this->redirect()->toRoute('admin/schools');
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
            return $this->redirect()->toRoute('admin/schools', array(
                        'action' => 'add'
            ));
        }
        $education = $this->getEducationTable()->getEducation($id);

        if ($education == false) {
            $this->flashMessenger()->addErrorMessage('School not found..!!');
            return $this->redirect()->toRoute('admin/schools');
        }

        $form = new EducationForm($this->getServiceLocator()->get('Admin\Model\StatusTable'));
        $form->bind($education);
        $form->get('submit')->setAttribute('value', 'Edit');

        $request = $this->getRequest();
        if ($request->isPost()) {

            // Adding already exist validation on runtime excluding the current record
            $education->getInputFilter()->get('education_label')->getValidatorChain()->attach(new \Zend\Validator\Db\NoRecordExists(array('table' => 'educations', 'field' => 'education_label', 'adapter' => $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'), 'exclude' => array('field' => 'id', 'value' => $id))));
            $form->setInputFilter($education->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $this->getEducationTable()->saveEducation($form->getData());
                $this->flashMessenger()->addSuccessMessage('School updated successfully..!!');
                // Redirect to list of pages
                return $this->redirect()->toRoute('admin/schools');
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
            return $this->redirect()->toRoute('admin/schools');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $this->getEducationTable()->deleteEducation($id);
                $this->flashMessenger()->addSuccessMessage('school deleted successfully..!!');
            }

            // Redirect to list of pages 
            return $this->redirect()->toRoute('admin/schools');
        }

        return array(
            'id' => $id,
            'education' => $this->getEducationTable()->getEducation($id)
        );
    }

    public function changeStatusAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $id = $request->getPost('id');
            $status = $request->getPost('status', '1');
            if ($id != null && $status != null) {
                $this->getEducationTable()->changeStatus($id, $status);
                echo json_encode(array("msg" => "Status successfully changed..!!"));
            } else {
                echo json_encode(array("msg" => "Failed to change the status..!!"));
            }
            exit;
        }
    }

}
