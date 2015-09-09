<?php

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Admin\Model\Bookings;
use Admin\Form\BookingForm;
use Zend\Session\Container;
use \PHPExcel;

class BookingController extends AbstractActionController
{

    private $bookingTable;
    public $errors = array();

    private function getBookingTable()
    {
        if (!$this->bookingTable) {
            $sm = $this->getServiceLocator();
            $this->bookingTable = $sm->get('Admin\Model\BookingsTable');
        }

        return $this->bookingTable;
    }

    public function indexAction()
    {
        $request = $this->getRequest();
        if ($request->isGet()) {
            $postedData = (array) $request->getQuery();
            unset($postedData['page']);
            $paginator = $this->getBookingTable()->fetchAll(true, array(), $postedData);
        } else {
            $paginator = $this->getBookingTable()->fetchAll(true);
        }

        $paginator->setCurrentPageNumber((int) $this->params()->fromQuery('page', 1));
        $paginator->setItemCountPerPage(10);

        return new ViewModel(array(
            'bookings' => $paginator,
            'postedData' => array_filter((array) $postedData),
            'model' => $this->getBookingTable(),
            'status' => $this->getServiceLocator()->get('Admin\Model\StatusTable')->fetchAll(false, array(4, 5, 6)),
            'successMsgs' => $this->flashMessenger()->getCurrentSuccessMessages(),
            'errors' => $this->flashMessenger()->getCurrentErrorMessages()
        ));
    }

    public function addAction()
    {
        $user_details = new Container('user_details');
        $details = $user_details->details;

        $config = $this->getServiceLocator()->get('Config');
        $payment_methods = $config['payment_methods'];
        
        $workAddress = $this->getServiceLocator()->get('Admin\Model\ServiceProviderTable')->getServiceProviderServiceAddress();
        
        $addresses = array();
        foreach ($workAddress as $add) {
            $addresses[] = array('id' => $add->id, 'address' => $add->street1_address.', '.$add->city.', '.$add->state_name.' '.$add->zip_code.', '.$add->country_name);
        }
        
        $form = new BookingForm($this->getServiceLocator()->get('Admin\Model\UsersTable'), $this->getServiceLocator()->get('Admin\Model\ServiceProviderTable'), $this->getServiceLocator()->get('Admin\Model\ServicesTable'), $this->getServiceLocator()->get('Admin\Model\BookingsTable'), $this->getServiceLocator()->get('Admin\Model\StatusTable'), $payment_methods, $addresses);

        $form->get('submit')->setValue('Add');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $booking = new Bookings();

            $form->setInputFilter($booking->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $booking->exchangeArray($form->getData());
                $this->getBookingTable()->saveBooking($booking, $details['user_id'], $this->getServiceLocator()->get('Admin\Model\ServiceProviderTable'));
                $this->flashMessenger()->addSuccessMessage('Booking added successfully..!!');

                // Redirect to list of pages
                return $this->redirect()->toRoute('admin/bookings');
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
            return $this->redirect()->toRoute('admin/bookings', array(
                        'action' => 'add'
            ));
        }

        $user_details = new Container('user_details');
        $details = $user_details->details;

        $config = $this->getServiceLocator()->get('Config');
        $payment_methods = $config['payment_methods'];

        $booking = $this->getBookingTable()->getBooking($id);

        if ($booking == false) {
            $this->flashMessenger()->addErrorMessage('Service booking not found..!!');
            return $this->redirect()->toRoute('admin/bookings');
        }

        $booking->setAdapter($this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'));
        
        $workAddress = $this->getServiceLocator()->get('Admin\Model\ServiceProviderTable')->getServiceProviderServiceAddress();
        
        $addresses = array();
        foreach ($workAddress as $add) {
            $addresses[] = array('id' => $add->id, 'address' => $add->street1_address.', '.$add->city.', '.$add->state_name.' '.$add->zip_code.', '.$add->country_name);
        }
        
        $form = new BookingForm($this->getServiceLocator()->get('Admin\Model\UsersTable'), $this->getServiceLocator()->get('Admin\Model\ServiceProviderTable'), $this->getServiceLocator()->get('Admin\Model\ServicesTable'), $this->getServiceLocator()->get('Admin\Model\BookingsTable'), $this->getServiceLocator()->get('Admin\Model\StatusTable'), $payment_methods, $addresses, $booking->service_provider_id);
        $form->bind($booking);
        $form->get('submit')->setAttribute('value', 'Edit');

        $request = $this->getRequest();
        if ($request->isPost()) {
            if ($request->getPost('booking_type') == '2') {
                $booking->getInputFilter()->get('parent_booking_id')->setRequired(true);
            }
            $form->setInputFilter($booking->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $this->getBookingTable()->saveBooking($form->getData(), $details['user_id'], $this->getServiceLocator()->get('Admin\Model\ServiceProviderTable'));
                $this->flashMessenger()->addSuccessMessage('Booking updated successfully..!!');
                // Redirect to list of pages
                return $this->redirect()->toRoute('admin/bookings');
            } else {
                $this->errors = $form->getMessages();
            }
        }

        return array('id' => $id, 'form' => $form, 'errors' => $this->errors);
    }

    public function rescheduleAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('admin/bookings', array(
                        'action' => 'add'
            ));
        }

        $user_details = new Container('user_details');
        $details = $user_details->details;

        $config = $this->getServiceLocator()->get('Config');
        $payment_methods = $config['payment_methods'];

        $booking = $this->getBookingTable()->getBooking($id);

        if ($booking == false) {
            $this->flashMessenger()->addErrorMessage('Service booking not found..!!');
            return $this->redirect()->toRoute('admin/bookings');
        }

        $booking->setAdapter($this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'));
        
        $workAddress = $this->getServiceLocator()->get('Admin\Model\ServiceProviderTable')->getServiceProviderServiceAddress();
        
        $addresses = array();
        foreach ($workAddress as $add) {
            $addresses[] = array('id' => $add->id, 'address' => $add->street1_address.', '.$add->city.', '.$add->state_name.' '.$add->zip_code.', '.$add->country_name);
        }
        
        $form = new BookingForm($this->getServiceLocator()->get('Admin\Model\UsersTable'), $this->getServiceLocator()->get('Admin\Model\ServiceProviderTable'), $this->getServiceLocator()->get('Admin\Model\ServicesTable'), $this->getServiceLocator()->get('Admin\Model\BookingsTable'), $this->getServiceLocator()->get('Admin\Model\StatusTable'), $payment_methods, $addresses, $booking->service_provider_id);

        $form->bind($booking);

        $form->get('submit')->setAttribute('value', 'Edit');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $postedData = $request->getPost();
            $postedData->booking_time = str_replace("/","-",$request->getPost('booking_time'));
            
            $form->setInputFilter($booking->getInputFilter());
            $form->setData($postedData);

            if ($form->isValid()) {
                $confirmations = $this->getBookingTable()->getConfirmations($id);

                if ($confirmations < 2) {
                    $data = $this->getBookingTable()->getBooking($id);

                    $pattern = array('/{{user_name}}/i', '/{{user_type}}/i', '/{{booking_id}}/i', '/{{new_date_time}}/i');
                    $c_replace = array('<strong>' . $data->first_name . " " . $data->last_name . '</strong>', 'Admin', '<strong>#' . $data->id . '</strong>', '<strong>' . date('l d/m/Y h:i A', strtotime($form->getData()->booking_time)) . '</strong>');
                    $sp_replace = array('<strong>' . $data->sp_first_name . " " . $data->sp_last_name . '</strong>', 'Admin', '<strong>#' . $data->id . '</strong>', '<strong>' . date('l d/m/Y h:i A', strtotime($form->getData()->booking_time)) . '</strong>');
                    $common = $this->getServiceLocator()->get('Application\Model\Common');
                    $this->getBookingTable()->reschedule($form->getData());

                    $common->sendMail($this->getServiceLocator()->get('config')['api_url']['value'], $data->email, '', 12, '', $pattern, $c_replace);
                    $common->sendMail($this->getServiceLocator()->get('config')['api_url']['value'], $data->sp_email, '', 12, '', $pattern, $sp_replace);

                    $this->flashMessenger()->addSuccessMessage('Booking rescheduled successfully..!!');
                    // Redirect to list of pages
                    return $this->redirect()->toRoute('admin/bookings');
                } else {
                    $this->errors = array('booking_time' => array('error. Booking can not be rescheduled more than 2 times for a single entry..!!'));
                }
            } else {
                $this->errors = $form->getMessages();
            }
        }

        return array('id' => $id, 'form' => $form, 'errors' => $this->errors);
    }

    public function deleteAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('admin/bookings');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $this->getBookingTable()->deleteBooking($id);
                $this->flashMessenger()->addSuccessMessage('Booking deleted successfully..!!');
            }

            // Redirect to list of pages 
            return $this->redirect()->toRoute('admin/bookings');
        }

        return array(
            'id' => $id,
            'booking' => $this->getBookingTable()->getBooking($id)
        );
    }

    public function changeStatusAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $id = $request->getPost('id');
            $status = $request->getPost('status', '1');
            if ($id != null && $status != null) {
                $this->getBookingTable()->changeStatus($id, $status);
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

            // Filtering fields goes here

            $results = $this->getBookingTable()->ExportAll($filter, $postedData);
        } else {
            $results = $this->getBookingTable()->ExportAll();
        }

        //echo '<pre>'.count($results)."<br />"; print_r($results->current()); exit;

        if (count($results) > 0) {
            $row = 1;

            $xls = new PHPExcel();
            $xls->getProperties()->setCreator("Ovessence")
                    ->setLastModifiedBy("Ovessence Admin")
                    ->setTitle("Ovessence Service bookings")
                    ->setSubject("Ovessence Service bookings")
                    ->setDescription("")
                    ->setKeywords("ovessence Service bookings")
                    ->setCategory("Sale report file");
            $xls->getActiveSheet()->setTitle("Ovessence Service bookings");

            /* Styling code starts here */
            $xls->getActiveSheet()->getStyle('A' . $row . ':J' . $row)->getFont()->setBold(true);
            $xls->getActiveSheet()->getStyle('A' . $row . ':J' . $row)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            /* Styling code ends here */

            $xls->setActiveSheetIndex(0)
                    ->setCellValue('A' . $row, 'S.no.')
                    ->setCellValue('B' . $row, 'Consumer Name')
                    ->setCellValue('C' . $row, 'Practitioner Name')
                    ->setCellValue('D' . $row, 'Service')
                    ->setCellValue('E' . $row, "Duration")
                    ->setCellValue('F' . $row, 'Price')
                    ->setCellValue('G' . $row, 'Site Commision')
                    ->setCellValue('H' . $row, 'Appointment Date')
                    ->setCellValue('I' . $row, 'Date of Booking')
                    ->setCellValue('J' . $row, 'Payment Status');
            foreach ($results as $result) {

                $row++;
                $xls->setActiveSheetIndex(0)
                        ->setCellValue('A' . $row, ($row - 1))
                        ->setCellValue('B' . $row, (trim($result->first_name . " " . $result->last_name) != "") ? $result->first_name . " " . $result->last_name : 'NA')
                        ->setCellValue('C' . $row, (trim($result->sp_first_name . " " . $result->sp_last_name) != "") ? $result->sp_first_name . " " . $result->sp_last_name : 'NA')
                        ->setCellValue('D' . $row, (isset($result->category_name) && $result->category_name != "") ? $result->category_name : 'NA')
                        ->setCellValue('E' . $row, (isset($result->duration) && $result->duration != "") ? $result->duration . ' mins' : 'NA')
                        ->setCellValue('F' . $row, (isset($result->price) && $result->price != "") ? '$' . $result->price : 'NA')
                        ->setCellValue('G' . $row, (isset($result->site_commision) && $result->site_commision != "") ? '$' . $result->site_commision : 'NA')
                        ->setCellValue('H' . $row, (isset($result->booked_date) && $result->booked_date != "") ? $result->booked_date : 'NA')
                        ->setCellValue('I' . $row, (isset($result->created_date) && $result->created_date != "") ? $result->created_date : 'NA')
                        ->setCellValue('J' . $row, (isset($result->PaymentStatus) && $result->PaymentStatus != "") ? $result->PaymentStatus : 'NA');
            }

            require_once './vendor/phpexcel/phpexcel/Classes/PHPExcel/IOFactory.php';

            $objWriter = \PHPExcel_IOFactory::createWriter($xls, 'Excel2007');

            // If you want to output e.g. a PDF file, simply do:
            //$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'PDF');
            $objWriter->save('./public/uploads/MyExcel.xlsx');
            header("Content-disposition: attachment; filename=Ovessence_service_bookings(" . date('d-M-Y') . ").xlsx");
            header("Content-type: application/vnd.ms-excel");
            readfile("./public/uploads/MyExcel.xlsx");
            unlink("./public/MyExcel.xlsx");
            exit;
        } else {
            $this->flashMessenger()->addErrorMessage('No records found to export..!!');
            return $this->redirect()->toRoute('admin/bookings');
        }
    }

}
