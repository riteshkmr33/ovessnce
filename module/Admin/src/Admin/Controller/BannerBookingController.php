<?php

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Admin\Model\BannerBookings;
use Admin\Form\BannerBookingForm;
use Zend\Session\Container;
use \PHPExcel;

class BannerBookingController extends AbstractActionController
{

    private $getBannerBookingTable;
    public $errors = array();

    private function getBannerBookingTable()
    {
        if (!$this->getBannerBookingTable) {
            $this->getBannerBookingTable = $this->getServiceLocator()->get('Admin\Model\BannerBookingsTable');
        }
        return $this->getBannerBookingTable;
    }

    public function indexAction()
    {
        $request = $this->getRequest();
        if ($request->isGet()) {
            $postedData = (array) $request->getQuery();
            unset($postedData['page']);
            $paginator = $this->getBannerBookingTable()->fetchAll(true, $postedData);
        } else {
            $paginator = $this->getBannerBookingTable()->fetchAll();
        }

        $paginator->setCurrentPageNumber((int) $this->Params()->fromQuery('page', 1));
        $paginator->setItemCountPerPage(10);

        return new ViewModel(array(
            'bannerbookings' => $paginator,
            'postedData' => $postedData,
            'status' => $this->getServiceLocator()->get('Admin\Model\StatusTable')->fetchAll(false, array(1, 2)),
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

        $form = new BannerBookingForm($this->getServiceLocator()->get('Admin\Model\AdvertisementPlanTable'), $this->getServiceLocator()->get('Admin\Model\UsersTable'), $this->getServiceLocator()->get('Admin\Model\StatusTable'), $payment_methods);
        $form->get('submit')->setValue('Add');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $bb = new BannerBookings();

            $form->setInputFilter($bb->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $bb->exchangeArray($form->getData());
                $this->getBannerBookingTable()->saveBannerBooking($bb, $details['user_id'], $this->getServiceLocator()->get('Admin\Model\AdvertisementPlanTable'));
                $this->flashMessenger()->addSuccessMessage('Banner Booking added successfully..!!');

                // Redirect to list of pages
                return $this->redirect()->toRoute('admin/bannerbookings');
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
            return $this->redirect()->toRoute('admin/bannerbookings', array(
                        'action' => 'add'
            ));
        }
        $user_details = new Container('user_details');
        $details = $user_details->details;

        $config = $this->getServiceLocator()->get('Config');
        $payment_methods = $config['payment_methods'];

        $bb = $this->getBannerBookingTable()->getBannerBooking($id);
        if ($bb == false) {
            $this->flashMessenger()->addErrorMessage('Banner booking not found..!!');
            return $this->redirect()->toRoute('admin/bannerbookings');
        }

        $form = new BannerBookingForm($this->getServiceLocator()->get('Admin\Model\AdvertisementPlanTable'), $this->getServiceLocator()->get('Admin\Model\UsersTable'), $this->getServiceLocator()->get('Admin\Model\StatusTable'), $payment_methods);
        $form->bind($bb);
        $form->get('submit')->setAttribute('value', 'Edit');

        $request = $this->getRequest();
        if ($request->isPost()) {

            $form->setInputFilter($bb->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $this->getBannerBookingTable()->saveBannerBooking($form->getData(), $details['user_id'], $this->getServiceLocator()->get('Admin\Model\AdvertisementPlanTable'));
                $this->flashMessenger()->addSuccessMessage('Banner Booking updated successfully..!!');
                // Redirect to list of pages
                return $this->redirect()->toRoute('admin/bannerbookings');
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
            return $this->redirect()->toRoute('admin/bannerbookings');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $this->getBannerBookingTable()->deleteBannerBooking($id);
                $this->flashMessenger()->addSuccessMessage('Banner Plan deleted successfully..!!');
            }

            // Redirect to list of pages 
            return $this->redirect()->toRoute('admin/bannerbookings');
        }

        return array(
            'id' => $id,
            'bannerbooking' => $this->getBannerBookingTable()->getBannerBooking($id)
        );
    }

    public function changeStatusAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $id = $request->getPost('id');
            $status = $request->getPost('status', '1');
            if ($id != null && $status != null) {
                $this->getBannerBookingTable()->changeStatus($id, $status);
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

            $results = $this->getBannerBookingTable()->ExportAll($filter, $postedData);
        } else {
            $results = $this->getBannerBookingTable()->ExportAll();
        }

        //echo '<pre>'.count($results)."<br />"; print_r($results->current()); exit;

        if (count($results) > 0) {
            $row = 1;

            $xls = new PHPExcel();
            $xls->getProperties()->setCreator("Ovessence")
                    ->setLastModifiedBy("Ovessence Admin")
                    ->setTitle("Ovessence Banner bookings")
                    ->setSubject("Ovessence Banner bookings")
                    ->setDescription("")
                    ->setKeywords("ovessence Banner bookings")
                    ->setCategory("Sale report file");
            $xls->getActiveSheet()->setTitle("Ovessence Banner bookings");

            /* Styling code starts here */
            $xls->getActiveSheet()->getStyle('A' . $row . ':F' . $row)->getFont()->setBold(true);
            $xls->getActiveSheet()->getStyle('A' . $row . ':F' . $row)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            /* Styling code ends here */

            $xls->setActiveSheetIndex(0)
                    ->setCellValue('A' . $row, 'S.no.')
                    ->setCellValue('B' . $row, 'Customer Name')
                    ->setCellValue('C' . $row, 'Banner Plan')
                    ->setCellValue('D' . $row, 'Order Amount')
                    ->setCellValue('E' . $row, "Order Date")
                    ->setCellValue('F' . $row, 'Payment Status');
            foreach ($results as $result) {

                $row++;
                $xls->setActiveSheetIndex(0)
                        ->setCellValue('A' . $row, ($row - 1))
                        ->setCellValue('B' . $row, (trim($result->first_name . " " . $result->last_name) != "") ? $result->first_name . " " . $result->last_name : 'NA')
                        ->setCellValue('C' . $row, (isset($result->sale_item_details) && $result->sale_item_details != "") ? $result->sale_item_details : 'NA')
                        ->setCellValue('D' . $row, (isset($result->invoice_total) && $result->invoice_total != "") ? '$' . $result->invoice_total : 'NA')
                        ->setCellValue('E' . $row, (isset($result->created_date) && $result->created_date != "") ? $result->created_date : 'NA')
                        ->setCellValue('F' . $row, (isset($result->payment_status) && $result->payment_status != "") ? $result->payment_status : 'NA');
            }

            require_once './vendor/phpexcel/phpexcel/Classes/PHPExcel/IOFactory.php';

            $objWriter = \PHPExcel_IOFactory::createWriter($xls, 'Excel2007');

            // If you want to output e.g. a PDF file, simply do:
            //$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'PDF');
            $objWriter->save('./public/uploads/MyExcel.xlsx');
            header("Content-disposition: attachment; filename=Ovessence_banner_bookings(" . date('d-M-Y') . ").xlsx");
            header("Content-type: application/vnd.ms-excel");
            readfile("./public/uploads/MyExcel.xlsx");
            unlink("./public/MyExcel.xlsx");
            exit;
        } else {
            $this->flashMessenger()->addErrorMessage('No records found to export..!!');
            return $this->redirect()->toRoute('admin/bannerbookings');
        }
    }

}
