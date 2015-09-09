<?php

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Admin\Model\ServiceProvider;
use Admin\Form\ServiceProviderForm;
use Admin\Form\ServiceProviderFilterForm;
use \PHPExcel;
use Zend\Mail\Message;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;

class ServiceProviderController extends AbstractActionController
{

    private $serviceProvider;
    public $errors = array();

    private function getServiceProviderTable()
    {
        if (!$this->serviceProvider) {
            $this->serviceProvider = $this->getServiceLocator()->get('Admin\Model\ServiceProviderTable');
        }

        return $this->serviceProvider;
    }

    public function indexAction()
    {
        $form = new ServiceProviderFilterForm($this->getServiceLocator()->get('Admin\Model\StatesTable'), $this->getServiceLocator()->get('Admin\Model\CountriesTable'), $this->getServiceLocator()->get('Admin\Model\ServicesTable'), $this->getServiceLocator()->get('Admin\Model\StatusTable'));
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
                'country_id' => $postedData['country'],
                'state_id' => $postedData['state'],
                'city' => trim($postedData['city']),
                'status_id' => $postedData['status_id'],
            );
            $paginator = $this->getServiceProviderTable()->fetchAll(true, $filter, array('sort_field' => $getData['sort_field'], 'sort_order' => $getData['sort_order']));
        } else if (count($getData) > 0) {
            $filter = array();
            $form->bind($request->getQuery());
            $postedData = $getData;
            isset($getData['provider_name']) ? $filter['name'] = $getData['provider_name'] : "";
            (isset($getData['from']) && $getData['from'] != "") ? $filter['from_date'] = date("Y-m-d", strtotime($getData['from'])) : "";
            (isset($getData['to']) && $getData['to'] != "") ? $filter['to_date'] = date("Y-m-d", strtotime($getData['to'])) : "";
            isset($getData['serviceType']) ? $filter['service_id'] = trim($getData['serviceType']) : "";
            isset($getData['country']) ? $filter['country_id'] = trim($getData['country']) : "";
            isset($getData['state']) ? $filter['state_id'] = trim($getData['state']) : "";
            isset($getData['city']) ? $filter['city'] = trim($getData['city']) : "";
            isset($getData['status_id']) ? $filter['status_id'] = trim($getData['status_id']) : "";

            $paginator = $this->getServiceProviderTable()->fetchAll(true, $filter, array('sort_field' => $getData['sort_field'], 'sort_order' => $getData['sort_order']));
        } else {
            $paginator = $this->getServiceProviderTable()->fetchAll();
        }

        $paginator->setCurrentPageNumber((int) $this->params()->fromQuery('page', 1));
        $paginator->setItemCountPerPage(10);

        return new ViewModel(array(
            'serviceproviders' => $paginator,
            'form' => $form,
            'postedData' => array_filter((array) $postedData),
            'model' => $this->getServiceProviderTable(),
            'status' => $this->getServiceLocator()->get('Admin\Model\StatusTable')->fetchAll(false, array(9, 5, 10, 3)),
            'successMsgs' => $this->flashMessenger()->getCurrentSuccessMessages(),
            'errors' => $this->flashMessenger()->getCurrentErrorMessages()
        ));
    }

    public function addAction()
    {
        $form = new ServiceProviderForm($this->getServiceLocator()->get('Admin\Model\StatesTable'), $this->getServiceLocator()->get('Admin\Model\CountriesTable'), $this->getServiceLocator()->get('Admin\Model\StatusTable'), $this->getServiceLocator()->get('Admin\Model\ServicesTable'), $this->getServiceLocator()->get('Admin\Model\EducationsTable'), $this->getServiceLocator()->get('Admin\Model\ServiceLanguagesTable'), $this->getServiceLocator()->get('Admin\Model\PractitionerOrganizationsTable')
        );

        //$form = new StateForm();
        $form->get('submit')->setValue('Add');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $sp = new ServiceProvider();

            $sp->getInputFilter()->get('user_name')->getValidatorChain()->attach(new \Zend\Validator\Db\NoRecordExists(array('table' => 'users', 'field' => 'user_name', 'adapter' => $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'))));
            $sp->getInputFilter()->get('email')->getValidatorChain()->attach(new \Zend\Validator\Db\NoRecordExists(array('table' => 'users', 'field' => 'email', 'adapter' => $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'))));

            $form->setInputFilter($sp->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {

                $serviceRenderAddresses = array('street1_address' => $request->getPost('s_street1_address'),
                    'street2_address' => $request->getPost('s_street2_address'),
                    'city' => $request->getPost('s_city'),
                    'zip_code' => $request->getPost('s_zip_code'),
                    'state_id' => $request->getPost('s_state_id'),
                    'country_id' => $request->getPost('s_country_id'),
                );

                $sp->exchangeArray($form->getData());
                $this->getServiceProviderTable()->saveServiceProvider($sp, $serviceRenderAddresses, $request->getPost('service_language_id'), $request->getPost('education_id'), $request->getPost('prac_org'));
                $this->flashMessenger()->addSuccessMessage('Service Provider added successfully..!!');

                // Redirect to list of pages
                return $this->redirect()->toRoute('admin/serviceproviders');
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
            return $this->redirect()->toRoute('admin/serviceproviders', array(
                        'action' => 'add'
            ));
        }
        $sp = $this->getServiceProviderTable()->getServiceProvider($id);
        if ($sp == false) {
            $this->flashMessenger()->addErrorMessage('Service provider not found..!!');
            return $this->redirect()->toRoute('admin/serviceproviders');
        }
        $sp->setAdapter($this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'));

        $form = new ServiceProviderForm($this->getServiceLocator()->get('Admin\Model\StatesTable'), $this->getServiceLocator()->get('Admin\Model\CountriesTable'), $this->getServiceLocator()->get('Admin\Model\StatusTable'), $this->getServiceLocator()->get('Admin\Model\ServicesTable'), $this->getServiceLocator()->get('Admin\Model\EducationsTable'), $this->getServiceLocator()->get('Admin\Model\ServiceLanguagesTable'), $this->getServiceLocator()->get('Admin\Model\PractitionerOrganizationsTable'), $this->getServiceProviderTable()->getServiceProviderServiceLanguage($sp->id, true), $this->getServiceProviderTable()->getServiceProviderServiceEducation($sp->id, true), $this->getServiceProviderTable()->getServiceProviderOrganization($sp->id, true)
        );
        $form->bind($sp);
        $form->get('submit')->setAttribute('value', 'Edit');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $sp->getInputFilter()->get('pass')->setRequired(false);
            $sp->getInputFilter()->get('user_name')->getValidatorChain()->attach(new \Zend\Validator\Db\NoRecordExists(array('table' => 'users', 'field' => 'user_name', 'adapter' => $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'), 'exclude' => array('field' => 'id', 'value' => $sp->id))));
            $sp->getInputFilter()->get('email')->getValidatorChain()->attach(new \Zend\Validator\Db\NoRecordExists(array('table' => 'users', 'field' => 'email', 'adapter' => $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'), 'exclude' => array('field' => 'id', 'value' => $sp->id))));

            $form->setInputFilter($sp->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $serviceRenderAddresses = array('id' => $request->getPost('s_id'),
                    'street1_address' => $request->getPost('s_street1_address'),
                    'street2_address' => $request->getPost('s_street2_address'),
                    'city' => $request->getPost('s_city'),
                    'zip_code' => $request->getPost('s_zip_code'),
                    'state_id' => $request->getPost('s_state_id'),
                    'country_id' => $request->getPost('s_country_id'),
                );
                $this->getServiceProviderTable()->saveServiceProvider($form->getData(), $serviceRenderAddresses, $request->getPost('service_language_id'), $request->getPost('education_id'));
                $this->flashMessenger()->addSuccessMessage('Service Provider updated successfully..!!');

                // Redirect to list of pages
                return $this->redirect()->toRoute('admin/serviceproviders');
            } else {
                $this->errors = $form->getMessages();
            }
        }

        return array(
            'id' => $id,
            'form' => $form,
            'errors' => $this->errors,
            'addresses' => $this->getServiceProviderTable()->getServiceProviderServiceAddress($sp->id)
        );
    }

    public function deleteAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('admin/serviceproviders');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $this->getServiceProviderTable()->deleteServiceProvider($id);
                $this->flashMessenger()->addSuccessMessage('Service Provider deleted successfully..!!');
            }

            // Redirect to list of pages 
            return $this->redirect()->toRoute('admin/serviceproviders');
        }

        return array(
            'id' => $id,
            'serviceprovider' => $this->getServiceProviderTable()->getServiceProvider($id)
        );
    }

    public function changeStatusAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $id = $request->getPost('id');
            $status = $request->getPost('status', '1');
            if ($id != null && $status != null) {
                $this->getServiceProviderTable()->changeStatus($id, $status);

                if ($status == 9) {
                    if ($emailTemplate = $this->getServiceLocator()->get('Admin\Model\EmailtemplatesTable')->getEmailtemplate(4)) {

                        $mail = new Message();
                        $transport = new \Zend\Mail\Transport\Sendmail();

                        foreach ($id as $user_id) {
                            $user_details = $this->getServiceProviderTable()->getUserDetails($user_id);

                            $html = new MimePart(preg_replace('/{{user_name}}/i', '<strong>' . $user_details->first_name . ' ' . $user_details->last_name . '</strong>', $emailTemplate->content));
                            $html->type = "text/html";

                            $body = new MimeMessage();
                            $body->setParts(array($html));

                            $mail->setBody($body)
                                    ->setFrom($emailTemplate->fromEmail, 'Ovessence')
                                    ->addTo($user_details->email, $user_details->first_name . ' ' . $user_details->last_name)
                                    ->setSubject($emailTemplate->subject);
                            $transport->send($mail);
                        }
                    }
                }
                echo json_encode(array("msg" => "Status successfully changed..!!"));
            } else {
                echo json_encode(array("msg" => "Failed to change the status..!!"));
            }
            exit;
        }
    }

    public function exportAction()
    {
        $request = $this->getRequest();
        $getData = (array) $request->getQuery();

        if (count($getData) > 0) {
            $filter = array();
            $postedData = $getData;
            isset($getData['provider_name']) ? $filter['name'] = $getData['provider_name'] : "";
            (isset($getData['from']) && $getData['from'] != "") ? $filter['from_date'] = date("Y-m-d", strtotime($getData['from'])) : "";
            (isset($getData['to']) && $getData['to'] != "") ? $filter['to_date'] = date("Y-m-d", strtotime($getData['to'])) : "";
            isset($getData['serviceType']) ? $filter['service_id'] = trim($getData['serviceType']) : "";
            isset($getData['country']) ? $filter['country_id'] = trim($getData['country']) : "";
            isset($getData['state']) ? $filter['state_id'] = trim($getData['state']) : "";
            isset($getData['city']) ? $filter['city'] = trim($getData['city']) : "";
            isset($getData['status_id']) ? $filter['status_id'] = trim($getData['status_id']) : "";

            $results = $this->getServiceProviderTable()->ExportAll($filter, $postedData);
        } else {
            $results = $this->getServiceProviderTable()->ExportAll();
        }

        //echo '<pre>'.count($results)."<br />"; print_r($results->current()); exit;

        if (count($results) > 0) {
            $row = 1;

            $xls = new PHPExcel();
            $xls->getProperties()->setCreator("Ovessence")
                    ->setLastModifiedBy("Ovessence Admin")
                    ->setTitle("Ovessence Service Providers")
                    ->setSubject("Ovessence Service Providers")
                    ->setDescription("")
                    ->setKeywords("ovessence Service Providers")
                    ->setCategory("Sale report file");
            $xls->getActiveSheet()->setTitle("Ovessence Service Providers");

            /* Styling code starts here */
            $xls->getActiveSheet()->getStyle('A' . $row . ':G' . $row)->getFont()->setBold(true);
            $xls->getActiveSheet()->getStyle('A' . $row . ':G' . $row)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            /* Styling code ends here */

            $xls->setActiveSheetIndex(0)
                    ->setCellValue('A' . $row, 'S.no.')
                    ->setCellValue('B' . $row, 'Name')
                    ->setCellValue('C' . $row, 'Date of Registration')
                    ->setCellValue('D' . $row, 'Services')
                    ->setCellValue('E' . $row, 'City')
                    ->setCellValue('F' . $row, "State")
                    ->setCellValue('G' . $row, 'Country');
            foreach ($results as $result) {

                $services = $this->getServiceProviderTable()->getServicesByName($result->id, true);

                $row++;
                $xls->setActiveSheetIndex(0)
                        ->setCellValue('A' . $row, ($row - 1))
                        ->setCellValue('B' . $row, (trim($result->first_name . " " . $result->last_name) != "") ? $result->first_name . " " . $result->last_name : 'NA')
                        ->setCellValue('C' . $row, (isset($result->created_date) && $result->created_date != "") ? $result->created_date : 'NA')
                        ->setCellValue('D' . $row, (isset($services) && $services != "") ? $services : 'NA')
                        ->setCellValue('E' . $row, (isset($result->city) && $result->city != "") ? $result->city : 'NA')
                        ->setCellValue('F' . $row, (isset($result->state_name) && $result->state_name != "") ? $result->state_name : 'NA')
                        ->setCellValue('G' . $row, (isset($result->country_name) && $result->country_name != "") ? $result->country_name : 'NA');
            }

            require_once './vendor/phpexcel/phpexcel/Classes/PHPExcel/IOFactory.php';

            $objWriter = \PHPExcel_IOFactory::createWriter($xls, 'Excel2007');

            // If you want to output e.g. a PDF file, simply do:
            //$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'PDF');
            $objWriter->save('./public/uploads/MyExcel.xlsx');
            header("Content-disposition: attachment; filename=Ovessence_practitioner_organizations(" . date('d-M-Y') . ").xlsx");
            header("Content-type: application/vnd.ms-excel");
            readfile("./public/uploads/MyExcel.xlsx");
            unlink("./public/MyExcel.xlsx");
            exit;
        } else {
            $this->flashMessenger()->addErrorMessage('No records found to export..!!');
            return $this->redirect()->toRoute('admin/organizations');
        }
    }

}
