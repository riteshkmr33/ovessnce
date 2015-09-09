<?php

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Admin\Form\FeedbackFilterForm;

class FeedbackController extends AbstractActionController
{

    private $feedbackTable;

    private function getFeedbackTable()
    {
        if (!$this->feedbackTable) {
            $sm = $this->getServiceLocator();
            $this->feedbackTable = $sm->get('Admin\Model\FeedbacksTable');
        }

        return $this->feedbackTable;
    }

    public function indexAction()
    {
        $form = new FeedbackFilterForm($this->getServiceLocator()->get('Admin\Model\ServiceProviderServicesTable'), $this->getServiceLocator()->get('Admin\Model\StatusTable'));
        $request = $this->getRequest();
        $postedData = array();
        $getData = (array) $request->getQuery();
        unset($getData['page']);

        if ($request->isPost()) {
            $postedData = $request->getPost();
            $form->bind($postedData);
            $filter = array(
                'name' => trim($postedData['provider_name']),
                'from_date' => ($postedData['from'] != "") ? date("Y-m-d", strtotime($postedData['from'])) : "",
                'to_date' => ($postedData['to'] != "") ? date("Y-m-d", strtotime($postedData['to'])) : "",
                'service_id' => $postedData['serviceType'],
                'status_id' => $postedData['status_id'],
            );
            $paginator = $this->getFeedbackTable()->fetchAll(true, $filter, array('sort_field' => $getData['sort_field'], 'sort_order' => $getData['sort_order']));
        } else if (count($getData) > 0) {
            $filter = array();
            $form->bind($request->getQuery());
            $postedData = $getData;

            isset($getData['provider_name']) ? $filter['name'] = $getData['provider_name'] : "";
            (isset($getData['from']) && $getData['from'] != "") ? $filter['from_date'] = date("Y-m-d", strtotime($getData['from'])) : "";
            (isset($getData['to']) && $getData['to'] != "") ? $filter['to_date'] = date("Y-m-d", strtotime($getData['to'])) : "";
            isset($getData['serviceType']) ? $filter['service_id'] = trim($getData['serviceType']) : "";
            isset($getData['status_id']) ? $filter['status_id'] = trim($getData['status_id']) : "";

            $paginator = $this->getFeedbackTable()->fetchAll(true, $filter, array('sort_field' => $getData['sort_field'], 'sort_order' => $getData['sort_order']));
        } else {
            $paginator = $this->getFeedbackTable()->fetchAll();
        }

        $paginator->setCurrentPageNumber((int) $this->params()->fromQuery('page', 1));
        $paginator->setItemCountPerPage(10);

        return new ViewModel(array(
            'feedbacks' => $paginator,
            'form' => $form,
            'status' => $this->getServiceLocator()->get('Admin\Model\StatusTable')->fetchAll(false, array(9, 5, 10)),
            'postedData' => array_filter((array) $postedData),
            'successMsgs' => $this->flashMessenger()->getCurrentSuccessMessages(),
            'errors' => $this->flashMessenger()->getCurrentErrorMessages()
        ));
    }

    public function deleteAction()
    {
        $user = (int) $this->params()->fromRoute('user', 0);
        $service = (int) $this->params()->fromRoute('service', 0);
        if (!$user || !$service) {
            return $this->redirect()->toRoute('admin/feedback');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $user = (int) $request->getPost('user');
                $service = (int) $request->getPost('service');
                $this->getFeedbackTable()->deleteFeedback($user, $service);
                $this->flashMessenger()->addSuccessMessage('Feedback deleted successfully..!!');
            }

            // Redirect to list of pages 
            return $this->redirect()->toRoute('admin/feedback');
        }

        return array(
            'user' => $user,
            'service' => $service,
            'feedback' => $this->getFeedbackTable()->getFeedback($user, $service)
        );
    }

    public function changeStatusAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $id = $request->getPost('id');
            $services = $request->getPost('services');
            $status = $request->getPost('status', '1');
            if ($id != null && $status != null && $services != null) {
                $this->getFeedbackTable()->changeStatus($id, $services, $status);
                echo json_encode(array("msg" => "Status successfully changed..!!"));
            } else {
                echo json_encode(array("msg" => "Failed to change the status..!!"));
            }
        }
        exit;
    }

}
