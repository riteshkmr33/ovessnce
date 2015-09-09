<?php

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Admin\Model\SubscriptionFeatures;
use Admin\Form\SubscriptionFeaturesForm;

class SubscriptionFeaturesController extends AbstractActionController
{

    private $featureTable;
    public $errors = array();

    private function getFeatureTable()
    {
        if (!$this->featureTable) {
            $sm = $this->getServiceLocator();
            $this->featureTable = $sm->get('Admin\Model\SubscriptionFeaturesTable');
        }

        return $this->featureTable;
    }

    public function indexAction()
    {
        $paginator = $this->getFeatureTable()->fetchAll();
        $paginator->setCurrentPageNumber((int) $this->params()->fromQuery('page', 1));
        $paginator->setItemCountPerPage(10);

        return new ViewModel(array(
            'features' => $paginator,
            'successMsgs' => $this->flashMessenger()->getCurrentSuccessMessages(),
            'errors' => $this->flashMessenger()->getCurrentErrorMessages()
        ));
    }
    
    public function editAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('admin/subscriptionfeatures');
        }
        $sf = $this->getFeatureTable()->getFeature($id);

        $form = new SubscriptionFeaturesForm();
        $form->bind($sf);
        $form->get('submit')->setAttribute('value', 'Edit');

        $request = $this->getRequest();
        if ($request->isPost()) {
            
            $form->setInputFilter($sf->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $this->getFeatureTable()->saveFeature($form->getData());

                $this->flashMessenger()->addSuccessMessage('Feature updated successfully..!!');

                // Redirect to list of pages
                return $this->redirect()->toRoute('admin/subscriptionfeatures');
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

    public function changeStatusAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $id = $request->getPost('id');
            $status = $request->getPost('status', '1');
            if ($id != null && $status != null) {
                $this->getFeatureTable()->changeStatus($id, $status);
                echo json_encode(array("msg" => "Status successfully changed..!!"));
            } else {
                echo json_encode(array("msg" => "Failed to change the status..!!"));
            }
        }
        exit;
    }

}
