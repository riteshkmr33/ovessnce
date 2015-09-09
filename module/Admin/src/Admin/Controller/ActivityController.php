<?php

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Admin\Model\Activity;
use Admin\Form\ActivityForm;

class ActivityController extends AbstractActionController
{

    private $activityTable;
    public $errors;

    private function getActivityTable()
    {
        if (!$this->activityTable) {
            $sm = $this->getServiceLocator();
            $this->activityTable = $sm->get('Admin\Model\ActivityTable');
        }

        return $this->activityTable;
    }

    public function indexAction()
    {
        $paginator = $this->getActivityTable()->fetchAll();
        $paginator->setCurrentPageNumber((int) $this->params()->fromQuery('page', 1));
        $paginator->setItemCountPerPage(10);

        return new ViewModel(array(
            'activities' => $paginator,
            'successMsgs' => $this->flashMessenger()->getCurrentSuccessMessages(),
            'errors' => $this->flashMessenger()->getCurrentErrorMessages()
        ));
    }

    public function addAction()
    {
        $form = new ActivityForm($this->getServiceLocator()->get('Admin\Model\CountriesTable'));

        //$form = new ActivityForm();
        $form->get('submit')->setValue('Add');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $activity = new Activity();

            $form->setInputFilter($activity->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $activity->exchangeArray($form->getData());
                $this->getActivityTable()->saveActivity($activity);
                $this->flashMessenger()->addSuccessMessage('Activity added successfully..!!');

                // Redirect to list of pages
                return $this->redirect()->toRoute('admin/activity');
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
            return $this->redirect()->toRoute('admin/activity', array(
                        'action' => 'add'
            ));
        }
        $activity = $this->getActivityTable()->getActivity($id);

        if ($activity == false) {
            $this->flashMessenger()->addErrorMessage('Activity not found..!!');
            return $this->redirect()->toRoute('admin/activity');
        }

        $form = new ActivityForm($this->getServiceLocator()->get('Admin\Model\CountriesTable'));
        $form->bind($activity);
        $form->get('submit')->setAttribute('value', 'Edit');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($activity->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $this->getActivityTable()->saveActivity($form->getData());
                $this->flashMessenger()->addSuccessMessage('Activity updated successfully..!!');

                // Redirect to list of pages
                return $this->redirect()->toRoute('admin/activity');
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
            return $this->redirect()->toRoute('admin/activity');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $this->getActivityTable()->deleteActivity($id);
                $this->flashMessenger()->addSuccessMessage('Activity deleted successfully..!!');
            }

            // Redirect to list of pages 
            return $this->redirect()->toRoute('admin/activity');
        }

        return array(
            'id' => $id,
            'activity' => $this->getActivityTable()->getActivity($id)
        );
    }

}
