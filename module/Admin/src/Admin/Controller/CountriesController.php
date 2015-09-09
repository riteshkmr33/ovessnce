<?php

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Admin\Model\Countries;
use Admin\Form\CountryForm;
use Admin\Form\CountryFilterForm;

class CountriesController extends AbstractActionController
{

    private $countryTable;
    public $errors = array();

    private function getCountryTable()
    {
        if (!$this->countryTable) {
            $sm = $this->getServiceLocator();
            $this->countryTable = $sm->get('Admin\Model\CountriesTable');
        }

        return $this->countryTable;
    }

    public function indexAction()
    {
        $form = new CountryFilterForm($this->getServiceLocator()->get('Admin\Model\StatusTable'));
        $request = $this->getRequest();
        $postedData = array();
        $getData = (array) $request->getQuery();
        unset($getData['page']);

        if ($request->isPost()) {
            $postedData = $request->getPost();
            $form->bind($postedData);
            $filter = array(
                'country_code' => trim($postedData['country_code']),
                'country_name' => trim($postedData['country_name']),
                'status_id' => $postedData['status_id'],
            );
            $paginator = $this->getCountryTable()->fetchAll(true, $filter, array('sort_field' => $getData['sort_field'], 'sort_order' => $getData['sort_order']));
        } else if (count($getData) > 0) {
            $filter = array();
            $form->bind($request->getQuery());
            $postedData = $getData;
            isset($getData['country_code']) ? $filter['country_code'] = trim($getData['country_code']) : "";
            isset($getData['country_name']) ? $filter['country_name'] = trim($getData['country_name']) : "";
            isset($getData['status_id']) ? $filter['status_id'] = trim($getData['status_id']) : "";

            $paginator = $this->getCountryTable()->fetchAll(true, $filter, array('sort_field' => $getData['sort_field'], 'sort_order' => $getData['sort_order']));
        } else {
            $paginator = $this->getCountryTable()->fetchAll();
        }

        $paginator->setCurrentPageNumber((int) $this->params()->fromQuery('page', 1));   // set the current page to what has been passed in query string, or to 1 if none set
        $paginator->setItemCountPerPage(10);   // set the number of items per page to 10

        return new ViewModel(array(
            'countries' => $paginator,
            'form' => $form,
            'postedData' => array_filter((array) $postedData),
            'status' => $this->getServiceLocator()->get('Admin\Model\StatusTable')->fetchAll(false, array(1, 2)),
            'successMsgs' => $this->flashMessenger()->getCurrentSuccessMessages(),
            'errors' => $this->flashMessenger()->getCurrentErrorMessages()
        ));
    }

    public function addAction()
    {
        $form = new CountryForm();
        $form->get('submit')->setValue('Add');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $country = new Countries();
            $country->setAdapter($this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'));

            // Adding already exist validation on runtime excluding the current record
            $country->getInputFilter()->get('country_code')->getValidatorChain()->attach(new \Zend\Validator\Db\NoRecordExists(array('table' => 'country', 'field' => 'country_code', 'adapter' => $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'))));
            $country->getInputFilter()->get('country_name')->getValidatorChain()->attach(new \Zend\Validator\Db\NoRecordExists(array('table' => 'country', 'field' => 'country_name', 'adapter' => $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'))));

            $form->setInputFilter($country->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {

                $country->exchangeArray($form->getData());
                $this->getCountryTable()->saveCountry($country);
                $this->flashMessenger()->addSuccessMessage('Country added successfully..!!');

                // Redirect to list of pages
                return $this->redirect()->toRoute('admin/countries');
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
            return $this->redirect()->toRoute('admin/countries', array(
                        'action' => 'add'
            ));
        }
        $country = $this->getCountryTable()->getCountry($id);
        $country->setAdapter($this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'));

        $form = new CountryForm();
        $form->bind($country);
        $form->get('submit')->setAttribute('value', 'Edit');

        $request = $this->getRequest();
        if ($request->isPost()) {

            // Adding already exist validation on runtime excluding the current record
            $country->getInputFilter()->get('country_code')->getValidatorChain()->attach(new \Zend\Validator\Db\NoRecordExists(array('table' => 'country', 'field' => 'country_code', 'adapter' => $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'), 'exclude' => array('field' => 'id', 'value' => $id))));
            $country->getInputFilter()->get('country_name')->getValidatorChain()->attach(new \Zend\Validator\Db\NoRecordExists(array('table' => 'country', 'field' => 'country_name', 'adapter' => $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'), 'exclude' => array('field' => 'id', 'value' => $id))));

            $form->setInputFilter($country->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $this->getCountryTable()->saveCountry($form->getData());

                $this->flashMessenger()->addSuccessMessage('Country updated successfully..!!');

                // Redirect to list of pages
                return $this->redirect()->toRoute('admin/countries');
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
            return $this->redirect()->toRoute('admin/countries');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $this->getCountryTable()->deletePage($id);

                $this->flashMessenger()->addSuccessMessage('Country deleted successfully..!!');
            }

            // Redirect to list of pages 
            return $this->redirect()->toRoute('admin/countries');
        }

        return array(
            'id' => $id,
            'country' => $this->getCountryTable()->getCountry($id)
        );
    }

    public function changeStatusAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $id = $request->getPost('id');
            $status = $request->getPost('status', '1');
            if ($id != null && $status != null) {
                $this->getCountryTable()->changeStatus($id, $status);
                echo json_encode(array("msg" => "Status successfully changed..!!"));
            } else {
                echo json_encode(array("msg" => "Failed to change the status..!!"));
            }
        }
        exit;
    }

}
