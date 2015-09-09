<?php

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Admin\Model\Users;
use Admin\Form\ConsumersForm;
use Admin\Form\ConsumerFilterForm;
use \PHPExcel;

class ConsumersController extends AbstractActionController
{

    protected $ConsumersTable;

    public function indexAction()
    {

        $form = new ConsumerFilterForm($this->getServiceLocator()->get('Admin\Model\StatesTable'), $this->getServiceLocator()->get('Admin\Model\CountriesTable'), $this->getServiceLocator()->get('Admin\Model\StatusTable'));
        $request = $this->getRequest();
        $postedData = array();
        $getData = (array) $request->getQuery();
        unset($getData['page']);

        if ($request->isPost()) {
            $postedData = $request->getPost();
            $form->bind($postedData);
            $filter = array(
                'name' => trim($postedData['name']),
                'user_name' => trim($postedData['user_name']),
                'age' => trim($postedData['age']),
                'gender' => trim($postedData['gender']),
                'email' => trim($postedData['email']),
                'created_on' => ($postedData['created_on']),
                'from_date' => ($postedData['from'] != "") ? date("Y-m-d", strtotime($postedData['from'])) : "",
                'to_date' => ($postedData['to'] != "") ? date("Y-m-d", strtotime($postedData['to'])) : "",
                'city' => trim($postedData['city']),
                'state_id' => $postedData['state'],
                'country_id' => $postedData['country'],
                'status_id' => $postedData['status_id'],
                'user_type_id' => '4',
            );
            $paginator = $this->getconsumersTable()->fetchAll(true, $filter, array('sort_field' => $getData['sort_field'], 'sort_order' => $getData['sort_order']));
        } else if (count($getData) > 0) {
            $filter = array();
            $form->bind($request->getQuery());
            $postedData = $getData;
            isset($getData['name']) ? $filter['name'] = $getData['name'] : "";
            isset($getData['user_name']) ? $filter['user_name'] = $getData['user_name'] : "";
            isset($getData['age']) ? $filter['age'] = $getData['age'] : "";
            isset($getData['gender']) ? $filter['gender'] = $getData['gender'] : "";
            isset($getData['email']) ? $filter['email'] = $getData['email'] : "";
            isset($getData['created_on']) ? $filter['created_on'] = $getData['created_on'] : "";
            (isset($getData['from']) && !empty($getData['from'])) ? $filter['from_date'] = date("Y-m-d", strtotime($getData['from'])) : $filter['from_date'] = "";
            (isset($getData['to']) && !empty($getData['to'])) ? $filter['to_date'] = date("Y-m-d", strtotime($getData['to'])) : $filter['to_date'] = "";
            isset($getData['city']) ? $filter['city'] = trim($getData['city']) : "";
            isset($getData['state_id']) ? $filter['state_id'] = trim($getData['state_id']) : "";
            isset($getData['country_id']) ? $filter['country_id'] = trim($getData['country_id']) : "";
            isset($getData['status_id']) ? $filter['status_id'] = trim($getData['status_id']) : "";
            $filter['user_type_id'] = 4;

            $paginator = $this->getconsumersTable()->fetchAll(true, $filter, array('sort_field' => $getData['sort_field'], 'sort_order' => $getData['sort_order']));
        } else {
            $filter['user_type_id'] = 4;
            $paginator = $this->getconsumersTable()->fetchAll(true, $filter);
        }

        $paginator->setCurrentPageNumber((int) $this->params()->fromQuery('page', 1));
        $paginator->setItemCountPerPage(10);

        return new ViewModel(array(
            'consumers' => $paginator,
            'form' => $form,
            'status' => $this->getServiceLocator()->get('Admin\Model\StatusTable')->fetchAll(false, array(9, 5, 10, 3)),
            'successMsgs' => $this->flashMessenger()->getCurrentSuccessMessages(),
            'errors' => $this->flashMessenger()->getCurrentErrorMessages()
        ));
    }

    public function addAction()
    {

        $form = new ConsumersForm($this->getServiceLocator()->get('Admin\Model\UsertypeTable'), $this->getServiceLocator()->get('Admin\Model\StatesTable'), $this->getServiceLocator()->get('Admin\Model\CountriesTable'), $this->getServiceLocator()->get('Admin\Model\StatusTable'), $this->getServiceLocator()->get('Admin\Model\ServiceLanguagesTable'));
        $form->get('submit')->setValue('Add');

        $request = $this->getRequest();
        if ($request->isPost()) {

            $consumers = new Users();
            $consumers->getInputFilter()->get('user_name')->getValidatorChain()->attach(new \Zend\Validator\Db\NoRecordExists(array('table' => 'users', 'field' => 'user_name', 'adapter' => $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'))));
            $consumers->getInputFilter()->get('email')->getValidatorChain()->attach(new \Zend\Validator\Db\NoRecordExists(array('table' => 'users', 'field' => 'email', 'adapter' => $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'))));
            $form->setInputFilter($consumers->getInputFilter());
            $request->getPost()->user_type_id = '4'; // set user type to consumer 
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $consumers->exchangeArray($form->getData());
                $this->getconsumersTable()->saveUser($consumers, '', $request->getPost('service_language_id'));
                $this->flashMessenger()->addSuccessMessage('Consumer added successfully..!!');

                // Redirect to list of pages
                return $this->redirect()->toRoute('admin/consumers');
            }
        }
        return array('form' => $form);
    }

    public function editAction()
    {

        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('admin/consumers', array(
                        'action' => 'add'
            ));
        }
        $consumer = $this->getconsumersTable()->getUser($id);
        if ($consumer == false) {
            $this->flashMessenger()->addErrorMessage('Consumer not found..!!');
            return $this->redirect()->toRoute('admin/consumers');
        }

        $form = new ConsumersForm($this->getServiceLocator()->get('Admin\Model\UsertypeTable'), $this->getServiceLocator()->get('Admin\Model\StatesTable'), $this->getServiceLocator()->get('Admin\Model\CountriesTable'), $this->getServiceLocator()->get('Admin\Model\StatusTable'), $this->getServiceLocator()->get('Admin\Model\ServiceLanguagesTable'), $this->getconsumersTable()->getConsumerServiceLanguage($id, true), $consumer->country_id
        );
        $form->bind($consumer);
        $form->get('submit')->setAttribute('value', 'Edit');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $consumer->getInputFilter()->get('user_type_id')->setRequired(false);

            if (empty($_POST['pass'])) {
                $consumer->getInputFilter()->get('pass')->setRequired(false);
                $consumer->getInputFilter()->get('c_pass')->setRequired(false);
            }
            $consumer->getInputFilter()->get('user_name')->getValidatorChain()->attach(new \Zend\Validator\Db\NoRecordExists(array('table' => 'users', 'field' => 'user_name', 'adapter' => $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'), 'exclude' => array('field' => 'id', 'value' => $id))));
            $consumer->getInputFilter()->get('email')->getValidatorChain()->attach(new \Zend\Validator\Db\NoRecordExists(array('table' => 'users', 'field' => 'email', 'adapter' => $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'), 'exclude' => array('field' => 'id', 'value' => $id))));
            $form->setInputFilter($consumer->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $this->getconsumersTable()->saveUser($form->getData(), '', $request->getPost('service_language_id'));
                $this->flashMessenger()->addSuccessMessage('Consumer updated successfully..!!');

                // Redirect to list of consumers
                return $this->redirect()->toRoute('admin/consumers');
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
            return $this->redirect()->toRoute('admin/consumers');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $this->getconsumersTable()->deleteUser($id);
                $this->flashMessenger()->addSuccessMessage('Consumer deleted successfully..!!');
            }

            // Redirect to list of Consumers
            return $this->redirect()->toRoute('admin/consumers');
        }

        return array(
            'id' => $id,
            'consumer' => $this->getconsumersTable()->getUser($id)
        );
    }

    public function getconsumersTable()
    {
        if (!$this->ConsumersTable) {
            $sm = $this->getServiceLocator();
            $this->ConsumersTable = $sm->get('Admin\Model\UsersTable');
        }

        return $this->ConsumersTable;
    }

    public function changeStatusAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $id = $request->getPost('id');
            $status = $request->getPost('status', '1');
            if ($id != null && $status != null) {
                $this->getconsumersTable()->changeStatus($id, $status);
                echo json_encode(array("msg" => "Status successfully changed..!!"));
            } else {
                echo json_encode(array("msg" => "Failed to change the status..!!"));
            }
            exit;
        }
    }

    public function exportconsumersAction()
    {
        $request = $this->getRequest();
        $getData = (array) $request->getQuery();

        if (count($getData) > 0) {
            $filter = array();
            $postedData = $getData;
            isset($getData['name']) ? $filter['name'] = $getData['name'] : "";
            isset($getData['user_name']) ? $filter['user_name'] = $getData['user_name'] : "";
            isset($getData['age']) ? $filter['age'] = $getData['age'] : "";
            isset($getData['gender']) ? $filter['gender'] = $getData['gender'] : "";
            isset($getData['email']) ? $filter['email'] = $getData['email'] : "";
            isset($getData['created_on']) ? $filter['created_on'] = $getData['created_on'] : "";
            (isset($getData['from']) && !empty($getData['from'])) ? $filter['from_date'] = date("Y-m-d", strtotime($getData['from'])) : $filter['from_date'] = "";
            (isset($getData['to']) && !empty($getData['to'])) ? $filter['to_date'] = date("Y-m-d", strtotime($getData['to'])) : $filter['to_date'] = "";
            isset($getData['city']) ? $filter['city'] = trim($getData['city']) : "";
            isset($getData['state_id']) ? $filter['state_id'] = trim($getData['state_id']) : "";
            isset($getData['country_id']) ? $filter['country_id'] = trim($getData['country_id']) : "";
            isset($getData['status_id']) ? $filter['status_id'] = trim($getData['status_id']) : "";
            $filter['user_type_id'] = 4;

            $results = $this->getconsumersTable()->ExportAllConsumers($filter);
        } else {
            $results = $this->getconsumersTable()->ExportAllConsumers();
        }

        //echo '<pre>'.count($results)."<br />"; print_r($results->current()); exit;

        if (count($results) > 0) {
            $row = 1;

            $xls = new PHPExcel();
            $xls->getProperties()->setCreator("Ovessence")
                    ->setLastModifiedBy("Ovessence Admin")
                    ->setTitle("Ovessence Consumers")
                    ->setSubject("Ovessence Consumers")
                    ->setDescription("")
                    ->setKeywords("ovessence Consumers")
                    ->setCategory("Sale report file");
            $xls->getActiveSheet()->setTitle("Ovessence Consumers");

            /* Styling code starts here */
            $xls->getActiveSheet()->getStyle('A' . $row . ':J' . $row)->getFont()->setBold(true);
            $xls->getActiveSheet()->getStyle('A' . $row . ':J' . $row)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            /* Styling code ends here */

            $xls->setActiveSheetIndex(0)
                    ->setCellValue('A' . $row, 'S.no.')
                    ->setCellValue('B' . $row, 'Name')
                    ->setCellValue('C' . $row, 'Username')
                    ->setCellValue('D' . $row, 'Age')
                    ->setCellValue('E' . $row, "Gender")
                    ->setCellValue('F' . $row, 'Email')
                    ->setCellValue('G' . $row, 'Created On')
                    ->setCellValue('H' . $row, 'City')
                    ->setCellValue('I' . $row, 'State')
                    ->setCellValue('J' . $row, 'Country');
            foreach ($results as $result) {

                $row++;
                $xls->setActiveSheetIndex(0)
                        ->setCellValue('A' . $row, ($row - 1))
                        ->setCellValue('B' . $row, (trim($result->first_name . " " . $result->last_name) != "") ? $result->first_name . " " . $result->last_name : 'NA')
                        ->setCellValue('C' . $row, (isset($result->user_name) && $result->user_name != "") ? $result->user_name : 'NA')
                        ->setCellValue('D' . $row, (isset($result->age) && $result->age != "") ? $result->age : 'NA')
                        ->setCellValue('E' . $row, (isset($result->gender) && $result->gender != "") ? $result->gender : 'NA')
                        ->setCellValue('F' . $row, (isset($result->email) && $result->email != "") ? $result->email : 'NA')
                        ->setCellValue('G' . $row, (isset($result->created_date) && $result->created_date != "") ? $result->created_date : 'NA')
                        ->setCellValue('H' . $row, (isset($result->city) && $result->city != "") ? $result->city : 'NA')
                        ->setCellValue('I' . $row, (isset($result->state_name) && $result->state_name != "") ? $result->state_name : 'NA')
                        ->setCellValue('J' . $row, (isset($result->country_name) && $result->country_name != "") ? $result->country_name : 'NA');
            }

            require_once './vendor/phpexcel/phpexcel/Classes/PHPExcel/IOFactory.php';

            $objWriter = \PHPExcel_IOFactory::createWriter($xls, 'Excel2007');

            // If you want to output e.g. a PDF file, simply do:
            //$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'PDF');
            $objWriter->save('./public/uploads/MyExcel.xlsx');
            header("Content-disposition: attachment; filename=Ovessence_consumers(" . date('d-M-Y') . ").xlsx");
            header("Content-type: application/vnd.ms-excel");
            readfile("./public/uploads/MyExcel.xlsx");
            unlink("./public/MyExcel.xlsx");
            exit;
        } else {
            $this->flashMessenger()->addErrorMessage('No records found to export..!!');
            return $this->redirect()->toRoute('admin/consumers');
        }
    }

}
