<?php

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Admin\Model\Users;
use Admin\Form\UsersForm;
use Admin\Form\UserFilterForm;
use \PHPExcel;

class UsersController extends AbstractActionController
{

    protected $usersTable;
    protected $UserRightsTable;
    public $errors = array();

    public function indexAction()
    {

        $form = new UserFilterForm($this->getServiceLocator()->get('Admin\Model\UsertypeTable'), $this->getServiceLocator()->get('Admin\Model\StatusTable'));
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
                'user_type_id' => trim($postedData['user_type_id']),
                'email' => trim($postedData['email']),
                'from_date' => ($postedData['from'] != "") ? date("Y-m-d", strtotime($postedData['from'])) : "",
                'to_date' => ($postedData['to'] != "") ? date("Y-m-d", strtotime($postedData['to'])) : "",
                'from_login_date' => ($postedData['from_login'] != "") ? date("Y-m-d", strtotime($postedData['from_login'])) : "",
                'to_login_date' => ($postedData['to_login'] != "") ? date("Y-m-d", strtotime($postedData['to_login'])) : "",
                'chat' => $postedData['chat'],
                'sms' => $postedData['sms'],
                'email_status' => $postedData['email_status'],
                'status_id' => $postedData['status_id'],
            );
            $paginator = $this->getconsumersTable()->fetchAll(true, $filter, array('sort_field' => $getData['sort_field'], 'sort_order' => $getData['sort_order']));
        } else if (count($getData) > 0) {
            $filter = array();
            $form->bind($request->getQuery());
            $postedData = $getData;
            isset($getData['name']) ? $filter['name'] = $getData['name'] : "";
            isset($getData['user_name']) ? $filter['user_name'] = $getData['user_name'] : "";
            isset($getData['user_type_id']) ? $filter['user_type_id'] = $getData['user_type_id'] : "";
            isset($getData['email']) ? $filter['email'] = $getData['email'] : "";
            (isset($getData['from']) && !empty($getData['from'])) ? $filter['from_date'] = date("Y-m-d", strtotime($getData['from'])) : $filter['from_date'] = "";
            (isset($getData['to']) && !empty($getData['to'])) ? $filter['to_date'] = date("Y-m-d", strtotime($getData['to'])) : $filter['to_date'] = "";
            (isset($getData['from_login']) && !empty($getData['from_login'])) ? $filter['from_login_date'] = date("Y-m-d", strtotime($getData['from_login'])) : $filter['from_login_date'] = "";
            (isset($getData['to_login']) && !empty($getData['to_login'])) ? $filter['to_login_date'] = date("Y-m-d", strtotime($getData['to_login'])) : $filter['to_login_date'] = "";
            isset($getData['chat']) ? $filter['chat'] = trim($getData['chat']) : "";
            isset($getData['sms']) ? $filter['sms'] = trim($getData['sms']) : "";
            isset($getData['email_status']) ? $filter['email_status'] = trim($getData['email_status']) : "";
            isset($getData['status_id']) ? $filter['status_id'] = trim($getData['status_id']) : "";

            $paginator = $this->getUsersTable()->fetchAll(true, $filter, array('sort_field' => $getData['sort_field'], 'sort_order' => $getData['sort_order']));
        } else {

            $paginator = $this->getUsersTable()->fetchAll(true, $filter);
        }

        $paginator->setCurrentPageNumber((int) $this->params()->fromQuery('page', 1));
        $paginator->setItemCountPerPage(10);

        return new ViewModel(array(
            'users' => $paginator,
            'form' => $form,
            'status' => $this->getServiceLocator()->get('Admin\Model\StatusTable')->fetchAll(false, array(9, 5, 10, 3)),
            'successMsgs' => $this->flashMessenger()->getCurrentSuccessMessages(),
            'errors' => $this->flashMessenger()->getCurrentErrorMessages()
        ));
    }

    public function addAction()
    {

        $vm = new ViewModel();
        $tableGateway = $this->getServiceLocator()->get('Admin\Model\UsertypeTable');
        $user_form = new UsersForm($tableGateway, $this->getServiceLocator()->get('Admin\Model\StatesTable'), $this->getServiceLocator()->get('Admin\Model\CountriesTable'), $this->getServiceLocator()->get('Admin\Model\StatusTable'), $this->getServiceLocator()->get('Admin\Model\ServiceLanguagesTable'));

        $user_form->get('submit')->setValue('Add');

        $request = $this->getRequest();
        if ($request->isPost()) {
            //print_r($request->getPost()); exit;
            $users = new Users();
            $users->getInputFilter()->get('user_name')->getValidatorChain()->attach(new \Zend\Validator\Db\NoRecordExists(array('table' => 'users', 'field' => 'user_name', 'adapter' => $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'))));
            $users->getInputFilter()->get('email')->getValidatorChain()->attach(new \Zend\Validator\Db\NoRecordExists(array('table' => 'users', 'field' => 'email', 'adapter' => $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'))));

            $user_form->setInputFilter($users->getInputFilter());
            $user_form->setData($request->getPost());


            if ($user_form->isValid()) {
                $users->exchangeArray($user_form->getData());
                $this->getUsersTable()->saveUser($users, '', $request->getPost('service_language_id'));
                $this->flashMessenger()->addSuccessMessage('User added successfully..!!');

                // Redirect to list of users
                return $this->redirect()->toRoute('admin/users');
            }
        }
        return array('user_form' => $user_form);
    }

    public function editAction()
    {

        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('admin/users', array(
                        'action' => 'add'
            ));
        }
        $user = $this->getUsersTable()->getUser($id);
        if ($user == false) {
            $this->flashMessenger()->addErrorMessage('User not found..!!');
            return $this->redirect()->toRoute('admin/users');
        }

        $vm = new ViewModel();
        $tableGateway = $this->getServiceLocator()->get('Admin\Model\UsertypeTable');
        $user_form = new UsersForm($tableGateway, $this->getServiceLocator()->get('Admin\Model\StatesTable'), $this->getServiceLocator()->get('Admin\Model\CountriesTable'), $this->getServiceLocator()->get('Admin\Model\StatusTable'), $this->getServiceLocator()->get('Admin\Model\ServiceLanguagesTable'), $this->getUsersTable()->getConsumerServiceLanguage($id, true), $user->country_id);

        //$form  = new UsersForm();
        $user_form->bind($user);
        $user_form->get('submit')->setAttribute('value', 'Edit');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $user->getInputFilter()->get('pass')->setRequired(false);
            if ($request->getPost('pass') == '') {
                $user->getInputFilter()->get('c_pass')->setRequired(false);
            }

            $user->getInputFilter()->get('user_name')->getValidatorChain()->attach(new \Zend\Validator\Db\NoRecordExists(array('table' => 'users', 'field' => 'user_name', 'adapter' => $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'), 'exclude' => array('field' => 'id', 'value' => $id))));
            $user->getInputFilter()->get('email')->getValidatorChain()->attach(new \Zend\Validator\Db\NoRecordExists(array('table' => 'users', 'field' => 'email', 'adapter' => $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'), 'exclude' => array('field' => 'id', 'value' => $id))));
            //echo '<pre>'; print_r($user->getInputFilter()->get('email')); exit;
            $user_form->setInputFilter($user->getInputFilter());
            $user_form->setData($request->getPost());

            if ($user_form->isValid()) {
                //echo '<pre>'; print_r($this->errors); exit;
                $this->getUsersTable()->saveUser($user_form->getData(), '', $request->getPost('service_language_id'));
                $this->flashMessenger()->addSuccessMessage('User updated successfully..!!');

                // Redirect to list of pages
                return $this->redirect()->toRoute('admin/users');
            } else {
                $this->errors = $user_form->getMessages();
            }
        }

        return array(
            'id' => $id,
            'errors' => $this->errors,
            'user_form' => $user_form,
        );
    }

    public function deleteAction()
    {
        $message = '';
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('admin/users');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                if ($id == "1") {
                    $message = "Error: you Cannot delete \"super admin\" ";
                    return array(
                        'id' => $id,
                        'user' => $this->getUsersTable()->getUser($id),
                        'message' => $message
                    );
                } else {
                    $this->getUsersTable()->deleteUser($id);
                    $this->getUserRightsTable()->deleteGroupRight($id);
                    $this->flashMessenger()->addSuccessMessage('User deleted successfully..!!');
                }
            }

            // Redirect to list of pages
            return $this->redirect()->toRoute('admin/users');
        }

        return array(
            'id' => $id,
            'user' => $this->getUsersTable()->getUser($id),
            'message' => $message
        );
    }

    public function getUsersTable()
    {
        if (!$this->usersTable) {
            $sm = $this->getServiceLocator();
            $this->usersTable = $sm->get('Admin\Model\UsersTable');
        }

        return $this->usersTable;
    }

    public function getUserRightsTable()
    {
        if (!$this->UserRightsTable) {
            $sm = $this->getServiceLocator();
            $this->UserRightsTable = $sm->get('Admin\Model\UserRightsTable');
        }

        return $this->UserRightsTable;
    }

    public function changeStatusAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $id = $request->getPost('id');
            $status = $request->getPost('status', '1');
            if ($id != null && $status != null) {
                if (is_numeric($status)) {
                    $this->getUsersTable()->changeStatus($id, $status);
                    echo json_encode(array("msg" => "Status successfully changed..!!"));
                } else {
                    switch ($status) {
                        case 'enablechat' :
                            $this->getUsersTable()->updateFeatureSetting($id, array('chat' => 1));
                            echo json_encode(array("msg" => "Chat successfully enabled..!!"));
                            break;

                        case 'disablechat' :
                            $this->getUsersTable()->updateFeatureSetting($id, array('chat' => 0));
                            echo json_encode(array("msg" => "Chat successfully disabled..!!"));
                            break;

                        case 'enablesms' :
                            $this->getUsersTable()->updateFeatureSetting($id, array('sms' => 1));
                            echo json_encode(array("msg" => "Sms successfully enabled..!!"));
                            break;

                        case 'disablesms' :
                            $this->getUsersTable()->updateFeatureSetting($id, array('sms' => 0));
                            echo json_encode(array("msg" => "Sms successfully disabled..!!"));
                            break;

                        case 'enableemail' :
                            $this->getUsersTable()->updateFeatureSetting($id, array('email' => 1));
                            echo json_encode(array("msg" => "Email successfully enabled..!!"));
                            break;

                        case 'disableemail' :
                            $this->getUsersTable()->updateFeatureSetting($id, array('email' => 0));
                            echo json_encode(array("msg" => "Email successfully disabled..!!"));
                            break;
                        case 'enablenewsletter' :
                            $this->getUsersTable()->updateFeatureSetting($id, array('newsletter' => 1));
                            echo json_encode(array("msg" => "Newsletter successfully enabled..!!"));
                            break;

                        case 'disablenewsletter' :
                            $this->getUsersTable()->updateFeatureSetting($id, array('newsletter' => 0));
                            echo json_encode(array("msg" => "Newsletter successfully disabled..!!"));
                            break;
                    }
                }
            } else {
                echo json_encode(array("msg" => "Failed to change the status..!!"));
            }
        }
        exit;
    }

    public function exportAction()
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

            $results = $this->getUsersTable()->ExportAllConsumers($filter, array(), 'All');
        } else {
            $results = $this->getUsersTable()->ExportAllConsumers(array(), array(), 'All');
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
            $xls->getActiveSheet()->getStyle('A' . $row . ':O' . $row)->getFont()->setBold(true);
            $xls->getActiveSheet()->getStyle('A' . $row . ':O' . $row)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            /* Styling code ends here */

            $xls->setActiveSheetIndex(0)
                    ->setCellValue('A' . $row, 'S.no.')
                    ->setCellValue('B' . $row, 'Name')
                    ->setCellValue('C' . $row, 'Username')
                    ->setCellValue('D' . $row, 'User Type')
                    ->setCellValue('E' . $row, 'Age')
                    ->setCellValue('F' . $row, "Gender")
                    ->setCellValue('G' . $row, 'Email')
                    ->setCellValue('H' . $row, 'Created On')
                    ->setCellValue('I' . $row, 'Last Login')
                    ->setCellValue('J' . $row, 'City')
                    ->setCellValue('K' . $row, 'State')
                    ->setCellValue('L' . $row, 'Country')
                    ->setCellValue('M' . $row, 'Chat')
                    ->setCellValue('N' . $row, 'Sms')
                    ->setCellValue('O' . $row, 'Email');
            foreach ($results as $result) {

                $row++;
                $xls->setActiveSheetIndex(0)
                        ->setCellValue('A' . $row, ($row - 1))
                        ->setCellValue('B' . $row, (trim($result->first_name . " " . $result->last_name) != "") ? $result->first_name . " " . $result->last_name : 'NA')
                        ->setCellValue('C' . $row, (isset($result->user_name) && $result->user_name != "") ? $result->user_name : 'NA')
                        ->setCellValue('D' . $row, (isset($result->user_type) && $result->user_type != "") ? $result->user_type : 'NA')
                        ->setCellValue('E' . $row, (isset($result->age) && $result->age != "") ? $result->age : 'NA')
                        ->setCellValue('F' . $row, (isset($result->gender) && $result->gender != "") ? $result->gender : 'NA')
                        ->setCellValue('G' . $row, (isset($result->email) && $result->email != "") ? $result->email : 'NA')
                        ->setCellValue('H' . $row, (isset($result->created_date) && $result->created_date != "") ? $result->created_date : 'NA')
                        ->setCellValue('I' . $row, (isset($result->last_login) && $result->last_login != "") ? $result->last_login : 'NA')
                        ->setCellValue('J' . $row, (isset($result->city) && $result->city != "") ? $result->city : 'NA')
                        ->setCellValue('K' . $row, (isset($result->state_name) && $result->state_name != "") ? $result->state_name : 'NA')
                        ->setCellValue('L' . $row, (isset($result->country_name) && $result->country_name != "") ? $result->country_name : 'NA')
                        ->setCellValue('M' . $row, (isset($result->chat) && $result->chat == "1") ? 'Enabled' : 'Disabled')
                        ->setCellValue('N' . $row, (isset($result->sms) && $result->sms == "1") ? 'Enabled' : 'Disabled')
                        ->setCellValue('O' . $row, (isset($result->email_status) && $result->email_status = "1") ? 'Enabled' : 'Disabled');
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
