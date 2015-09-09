<?php

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Admin\Model\Revenues;
use Admin\Form\RevenueFilterForm;
use \PHPExcel;

class RevenueController extends AbstractActionController
{
	private $getRevenueTable;
	public $errors = array();
	
	private function getRevenueTable()
	{
		if (!$this->getRevenueTable) {
			$this->getRevenueTable = $this->getServiceLocator()->get('Admin\Model\RevenuesTable');
		}
		
		return $this->getRevenueTable;
	}

    public function indexAction()
    {
		$form = new RevenueFilterForm($this->getServiceLocator()->get('Admin\Model\StatesTable'), $this->getServiceLocator()->get('Admin\Model\CountriesTable'), $this->getServiceLocator()->get('Admin\Model\SubscriptionPlansTable'), $this->getServiceLocator()->get('Admin\Model\ServiceCategoryTable'));
		$request = $this->getRequest();
        $postedData = array();
        $getData = (array)$request->getQuery();
        unset($getData['page']);
        
        if ($request->isPost()) {
			$postedData = $request->getPost();
			$form->bind($postedData);
			$filter = array(
				'name' => trim($postedData['user_name']),
				'product' => trim($postedData['product']),
				'from_date' => ($postedData['from'] != "")?date("Y-m-d",strtotime($postedData['from'])):"",
				'to_date' => ($postedData['to'] != "")?date("Y-m-d",strtotime($postedData['to'])):"",
				'status_id' => $postedData['status_id'],
				'city' => trim($postedData['city']),
				'state_id' => $postedData['state_id'],
				'country_id' => $postedData['country_id'],
				'subscription_id' => $postedData['subscription_id'],
			);
			$paginator = $this->getRevenueTable()->fetchAll(true, $filter, array('sort_field' => $getData['sort_field'], 'sort_order' => $getData['sort_order']));
		} else if (count($getData) > 0) {
			$filter = array();
			$form->bind($request->getQuery());
			$postedData = $getData;
			isset($getData['user_name'])?$filter['name'] = $getData['user_name']:"";
			isset($getData['product'])?$filter['product'] = $getData['product']:"";
			(isset($getData['from']) && !empty($getData['from']))?$filter['from_date'] = date("Y-m-d",strtotime($getData['from'])):$filter['from_date'] = "";
			(isset($getData['to']) && !empty($getData['to']))?$filter['to_date'] = date("Y-m-d",strtotime($getData['to'])):$filter['to_date'] = "";
			isset($getData['status_id'])?$filter['status_id'] = trim($getData['status_id']):"";
			isset($getData['city'])?$filter['city'] = trim($getData['city']):"";
			isset($getData['state_id'])?$filter['state_id'] = trim($getData['state_id']):"";
			isset($getData['country_id'])?$filter['country_id'] = trim($getData['country_id']):"";
			isset($getData['subscription_id'])?$filter['subscription_id'] = trim($getData['subscription_id']):"";
			
			$paginator = $this->getRevenueTable()->fetchAll(true, $filter, array('sort_field' => $getData['sort_field'], 'sort_order' => $getData['sort_order']));
		} else {
			$paginator = $this->getRevenueTable()->fetchAll();
		}
		
		$paginator->setCurrentPageNumber((int)$this->Params()->fromQuery('page',1));
		$paginator->setItemCountPerPage(10);
		
        return new ViewModel(array('revenues' => $paginator,
			'form' => $form,
			'postedData' => $postedData,
			'successMsgs' => $this->flashMessenger()->getCurrentSuccessMessages(),
			'errors' => $this->flashMessenger()->getCurrentErrorMessages()
        ));
    }
    
    public function exportAction()
    {
		$request = $this->getRequest();
        $getData = (array)$request->getQuery();
        
        $config = $this->getServiceLocator()->get('Config');
		$payment_methods = $config['payment_methods'];
        
        if (count($getData) > 0) {
			$filter = array();
			$postedData = $getData;
			isset($getData['user_name'])?$filter['name'] = $getData['user_name']:"";
			isset($getData['product'])?$filter['product'] = $getData['product']:"";
			(isset($getData['from']) && !empty($getData['from']))?$filter['from_date'] = date("Y-m-d",strtotime($getData['from'])):$filter['from_date'] = "";
			(isset($getData['to']) && !empty($getData['to']))?$filter['to_date'] = date("Y-m-d",strtotime($getData['to'])):$filter['to_date'] = "";
			isset($getData['state_id'])?$filter['state_id'] = trim($getData['state_id']):"";
			isset($getData['country_id'])?$filter['country_id'] = trim($getData['country_id']):"";
			isset($getData['subscription_id'])?$filter['subscription_id'] = trim($getData['subscription_id']):"";
			isset($getData['status_id'])?$filter['status_id'] = trim($getData['status_id']):"";
			
			$results = $this->getRevenueTable()->ExportAll($filter);
			
		} else {
			$results = $this->getRevenueTable()->ExportAll();
		}
		
		//echo '<pre>'.count($results)."<br />"; print_r($results->current()); exit;
		
		if (count($results) > 0) {
			$row = 1;
			
			$xls = new PHPExcel();
			$xls->getProperties()->setCreator("Ovessence")
						->setLastModifiedBy("Ovessence Admin")
						->setTitle("Ovessence Revenue Report")
						->setSubject("Ovessence Revenue Report")
						->setDescription("")
						->setKeywords("ovessence revenue report")
						->setCategory("Sale report file");
			$xls->getActiveSheet()->setTitle('Ovessence Revenue Report');
			
			/* Styling code starts here */
			$xls->getActiveSheet()->getStyle('A'.$row.':AN'.($row+2))->getFont()->setBold(true);
			$xls->getActiveSheet()->getStyle('A'.$row.':AN'.($row+2))->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			
			$xls->getActiveSheet()->getStyle('B'.$row.':AN'.($row+1))->getBorders()->getTop()->setBorderStyle(\PHPExcel_Style_Border::BORDER_THICK);
			$xls->getActiveSheet()->getStyle('B'.$row.':AN'.($row+1))->getBorders()->getLeft()->setBorderStyle(\PHPExcel_Style_Border::BORDER_THIN);
			$xls->getActiveSheet()->getStyle('B'.$row.':AN'.($row+1))->getBorders()->getBottom()->setBorderStyle(\PHPExcel_Style_Border::BORDER_THIN);
			$xls->getActiveSheet()->getStyle('B'.$row.':AN'.($row+1))->getBorders()->getRight()->setBorderStyle(\PHPExcel_Style_Border::BORDER_THIN);
			
			$xls->getActiveSheet()->getStyle('J'.$row.':J'.($row+1))->getBorders()->getRight()->setBorderStyle(\PHPExcel_Style_Border::BORDER_THIN);
			$xls->getActiveSheet()->getStyle('M'.$row.':M'.($row+1))->getBorders()->getRight()->setBorderStyle(\PHPExcel_Style_Border::BORDER_THIN);
			$xls->getActiveSheet()->getStyle('AG'.$row.':AG'.($row+1))->getBorders()->getRight()->setBorderStyle(\PHPExcel_Style_Border::BORDER_THIN);
			$xls->getActiveSheet()->getStyle('AL'.$row.':AL'.($row+1))->getBorders()->getRight()->setBorderStyle(\PHPExcel_Style_Border::BORDER_THIN);
			$xls->getActiveSheet()->getStyle('AM'.$row.':AM'.($row+1))->getBorders()->getRight()->setBorderStyle(\PHPExcel_Style_Border::BORDER_THIN);
			
			$xls->getActiveSheet()->getStyle('B'.$row.':J'.($row+1))->getFill()->applyFromArray(array('type' => \PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => '9ACD32')));
			$xls->getActiveSheet()->getStyle('K'.$row.':M'.($row+1))->getFill()->applyFromArray(array('type' => \PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => 'FF0000')));
			$xls->getActiveSheet()->getStyle('N'.$row.':AG'.($row+1))->getFill()->applyFromArray(array('type' => \PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => 'DAA520')));
			$xls->getActiveSheet()->getStyle('AH'.$row.':AL'.($row+1))->getFill()->applyFromArray(array('type' => \PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => 'ADD8E6')));
			$xls->getActiveSheet()->getStyle('AM'.$row.':AN'.($row+1))->getFill()->applyFromArray(array('type' => \PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => 'FFB6C1')));
			/* Styling code ends here */
			
			/* Setting headers for sections start */
			$xls->setActiveSheetIndex(0)->setCellValue('B'.$row, 'Customers')->mergeCells('B'.$row.':J'.$row)
										->setCellValue('K'.$row, 'Services ordered')->mergeCells('K'.$row.':M'.$row)
										->setCellValue('N'.$row, "Practitioner's Profile")->mergeCells('N'.$row.':AG'.$row)
										->setCellValue('AE'.$row, "Booking or subscription information")->mergeCells('AH'.$row.':AL'.$row)
										->setCellValue('AM'.$row, "Type of payment")
										->setCellValue('AN'.$row, "Status of payment");
			$row++;
			
			$xls->setActiveSheetIndex(0)->mergeCells('B'.$row.':J'.$row)
										->mergeCells('K'.$row.':M'.$row)
										->mergeCells('N'.$row.':AG'.$row)
										->mergeCells('AH'.$row.':AL'.$row);
			
			$row++;
			/* Setting headers for sections end */
			
			$xls->setActiveSheetIndex(0)
						->setCellValue('A'.$row, 'S.no.')
						
						/* Customer details start */
						->setCellValue('B'.$row, 'Customer name')
						->setCellValue('C'.$row, 'Age of Customer')
						->setCellValue('D'.$row, 'Gender Of Customer')
						->setCellValue('E'.$row, "Languages spoken")
						->setCellValue('F'.$row, 'Postal Code')
						->setCellValue('G'.$row, 'City')
						->setCellValue('H'.$row, 'State')
						->setCellValue('I'.$row, 'Country')
						->setCellValue('J'.$row, 'Continent')
						/* Customer details end */
						
						/* Service details start */
						->setCellValue('K'.$row, 'Category')
						->setCellValue('L'.$row, 'Sub Category')
						->setCellValue('M'.$row, 'Duration of treatment')
						/* Service details end */
						
						/* Practitioners details start */
						->setCellValue('N'.$row, "Practitioner's name")
						->setCellValue('O'.$row, "Practitioner's age")
						->setCellValue('P'.$row, "Gender")
						->setCellValue('Q'.$row, "Languages spoken")
						->setCellValue('R'.$row, "Neighborhood")
						->setCellValue('S'.$row, "Postal code")
						->setCellValue('T'.$row, "City")
						->setCellValue('U'.$row, "State")
						->setCellValue('V'.$row, "Country")
						->setCellValue('W'.$row, "Continent")
						->setCellValue('X'.$row, "Schools")
						->setCellValue('Y'.$row, "Degrees")
						->setCellValue('Z'.$row, "Years of experience")
						->setCellValue('AA'.$row, "Professional membership")
						->setCellValue('AB'.$row, "Workdays")
						->setCellValue('AC'.$row, "Auth to provide insurance receipt")
						->setCellValue('AD'.$row, "Treatment for physically disabled person")
						->setCellValue('AE'.$row, "Number of video uploaded")
						->setCellValue('AF'.$row, "Number of image uploaded")
						->setCellValue('AG'.$row, "Subscription selected by practitioner")
						/* Practitioners details end */
						
						/* Booking details start */
						->setCellValue('AH'.$row, "Date and time of registration of booking")
						->setCellValue('AI'.$row, "Total amount of booking")
						->setCellValue('AJ'.$row, "Commission of booking")
						->setCellValue('AK'.$row, "Date and time of booking")
						->setCellValue('AL'.$row, "Number of reschedulation of booking")
						/* Booking details end */
						
						->setCellValue('AM'.$row, "Visa, Mastercard or Amex")
						->setCellValue('AN'.$row, "Paid or Unpaid");
			foreach ($results as $result) {
				
				/* User details starts */
				$user_address = $this->getRevenueTable()->getUserAddress($result->user_id);
				$user_langs = implode(', ', $this->getServiceLocator()->get('Admin\Model\UsersTable')->getConsumerServiceLanguage($result->user_id,true));
				$user_langs = rtrim($user_langs, ', ');
				/* User details ends */
				
				/* Fetching service provider details starts */
				$sp_address = $this->getRevenueTable()->getUserAddress($result->service_provider_id);
				$sp_langs = implode(', ', $this->getServiceLocator()->get('Admin\Model\ServiceProviderTable')->getServiceProviderServiceLanguage($result->service_provider_id,true));
				$sp_langs = rtrim($sp_langs, ', ');
				$sp_schools = implode(', ', $this->getServiceLocator()->get('Admin\Model\ServiceProviderTable')->getServiceProviderServiceEducation($result->service_provider_id,true));
				$sp_schools = rtrim($sp_schools, ', ');
				$subscription = $this->getServiceLocator()->get('Admin\Model\SubscriptionsTable')->getUserSubscription($result->service_provider_id);
				$total_images = $this->getServiceLocator()->get('Admin\Model\MediaTable')->getUserMedia($result->service_provider_id, 1, 'count');
				$total_videos = $this->getServiceLocator()->get('Admin\Model\MediaTable')->getUserMedia($result->service_provider_id, 2, 'count');
				$work_days = implode(',', $this->getServiceLocator()->get('Admin\Model\ServiceProviderAvailabilityTable')->getUserWorkdays($result->service_provider_id, true));
				
				/* Fetching service provider details ends */
				
				/* Booking details start */
				$rescheduled = $this->getServiceLocator()->get('Admin\Model\BookingsTable')->getChildBookings($result->booking_id);
				/* Booking details end */
				
				switch ($result->sale_type)
				{
					case '1' :
						$booking_date = ($result->subscription_start_date != '')?date('d-m-Y', strtotime($result->subscription_start_date)):'';
						break;
						
					case '2' :
						$booking_date = ($result->booking_date != '')?date('d-m-Y', strtotime($result->booking_date)):'';
						break;
					
					case '3' :
						$booking_date = ($result->booked_date != '')?date('d-m-Y h:i:s', strtotime($result->booked_date)):'';
						break;
				}
				
				$row++;
				$xls->setActiveSheetIndex(0)
						->setCellValue('A'.$row, ($row-3))
						
						/* Customer details start */
						->setCellValue('B'.$row, (trim($result->first_name." ".$result->last_name) != "")?$result->first_name." ".$result->last_name:'NA')
						->setCellValue('C'.$row, (isset($result->age) && $result->age != "")?$result->age:'NA')
						->setCellValue('D'.$row, (isset($result->gender) && $result->gender != "")?$result->gender:'NA')
						->setCellValue('E'.$row, (isset($user_langs) && $user_langs != "")?$user_langs:'NA')
						->setCellValue('F'.$row, (isset($user_address->zip_code) && $user_address->zip_code != "")?$user_address->zip_code:'NA')
						->setCellValue('G'.$row, (isset($user_address->city) && $user_address->city != "")?$user_address->city:'NA')
						->setCellValue('H'.$row, (isset($user_address->state_name) && $user_address->state_name != "")?$user_address->state_name:'NA')
						->setCellValue('I'.$row, (isset($user_address->country_name) && $user_address->country_name != "")?$user_address->country_name:'NA')
						->setCellValue('J'.$row, (isset($user_address->continent) && $user_address->continent != "")?$user_address->continent:'NA')
						/* Customer details end */
						
						/* Service details start */
						->setCellValue('K'.$row, (isset($result->parent_category) && $result->parent_category != "")?$result->parent_category:'NA')
						->setCellValue('L'.$row, (isset($result->category_name) && $result->category_name != "")?$result->category_name:'NA')
						->setCellValue('M'.$row, (isset($result->duration) && $result->duration != "")?$result->duration.' Mins':'NA')
						/* Service details end */
						
						/* Practitioners details start */
						->setCellValue('N'.$row, (trim($result->sp_first_name." ".$result->sp_last_name) != "")?$result->sp_first_name." ".$result->sp_last_name:'NA')
						->setCellValue('O'.$row, (isset($result->sp_age) && $result->sp_age != "")?$result->sp_age:'NA')
						->setCellValue('P'.$row, (isset($result->sp_gender) && $result->sp_gender != "")?$result->sp_gender:'NA')
						->setCellValue('Q'.$row, (isset($sp_langs) && $sp_langs != "")?$sp_langs:'NA')
						->setCellValue('R'.$row, "NA")   // Neightborhood
						->setCellValue('S'.$row, (isset($sp_address->zip_code) && $sp_address->zip_code != "")?$sp_address->zip_code:'NA')
						->setCellValue('T'.$row, (isset($sp_address->city) && $sp_address->city != "")?$sp_address->city:'NA')
						->setCellValue('U'.$row, (isset($sp_address->state_name) && $sp_address->state_name != "")?$sp_address->state_name:'NA')
						->setCellValue('V'.$row, (isset($sp_address->country_name) && $sp_address->country_name != "")?$sp_address->country_name:'NA')
						->setCellValue('W'.$row, (isset($sp_address->continent) && $sp_address->continent != "")?$sp_address->continent:'NA')
						->setCellValue('X'.$row, (isset($sp_schools) && $sp_schools != "")?$sp_schools:'NA')
						->setCellValue('Y'.$row, (isset($result->degrees) && $result->degrees != "")?$result->degrees:'NA')
						->setCellValue('Z'.$row, (isset($result->years_of_experience) && $result->years_of_experience != "")?$result->years_of_experience:'NA')
						->setCellValue('AA'.$row, (isset($result->prof_membership) && $result->prof_membership != "")?$result->prof_membership:'NA')
						->setCellValue('AB'.$row, (isset($work_days) && $work_days != "")?$work_days:'NA')
						->setCellValue('AC'.$row, (isset($result->auth_to_issue_insurence_rem_receipt) && $result->auth_to_issue_insurence_rem_receipt != "")?$result->auth_to_issue_insurence_rem_receipt:'NA')
						->setCellValue('AD'.$row, (isset($result->treatment_for_physically_disabled_person) && $result->treatment_for_physically_disabled_person != "")?$result->treatment_for_physically_disabled_person:'NA')
						->setCellValue('AE'.$row, (isset($total_videos) && $total_videos != "")?$total_videos:'0')
						->setCellValue('AF'.$row, (isset($total_images) && $total_images != "")?$total_images:'0')
						->setCellValue('AG'.$row, ($subscription != false && isset($subscription->subscription_name))?$subscription->subscription_name:"NA")
						/* Practitioners details end */
						
						/* Booking details start */
						->setCellValue('AH'.$row, (isset($result->created_date) && $result->created_date != "")?$result->created_date:'NA')
						->setCellValue('AI'.$row, (isset($result->invoice_total) && $result->invoice_total != "")?'$'.$result->invoice_total:'NA')
						->setCellValue('AJ'.$row, (isset($result->amount_paid) && $result->amount_paid != "")?'$'.$result->amount_paid:'NA')
						->setCellValue('AK'.$row, (isset($booking_date) && $booking_date != "")?$booking_date:'NA')
						->setCellValue('AL'.$row, ($rescheduled != false)?$rescheduled->count():"0")
						/* Booking details end */
						
						->setCellValue('AM'.$row, (isset($result->payment_method_id) && array_key_exists($result->payment_method_id,$payment_methods))?$payment_methods[$result->payment_method_id]:'NA')
						->setCellValue('AN'.$row, (isset($result->status) && $result->status != "")?$result->status:'NA');
				
			}
			
			require_once './vendor/phpexcel/phpexcel/Classes/PHPExcel/IOFactory.php';
		
			$objWriter = \PHPExcel_IOFactory::createWriter($xls, 'Excel2007');
			
			// If you want to output e.g. a PDF file, simply do:
			//$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'PDF');
			$objWriter->save('./vendor/MyExcel.xlsx');
			header("Content-disposition: attachment; filename=Ovessence_revenue_report(".date('d-M-Y').").xlsx");
			header("Content-type: application/vnd.ms-excel");
			readfile("./vendor/MyExcel.xlsx");
			unlink("./vendor/MyExcel.xlsx");
			exit;
		} else {
			$this->flashMessenger()->addErrorMessage('No records found to export..!!');
			return $this->redirect()->toRoute('admin/revenues');
		}
        
	}
    
    public function deleteAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('admin/revenues');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $this->getRevenueTable()->deleteRevenue($id);
                $this->flashMessenger()->addSuccessMessage('Payment Record deleted successfully..!!');
            }

            // Redirect to list of pages 
            return $this->redirect()->toRoute('admin/revenues');
        }

        return array(
            'id'    => $id,
            'revenue' => $this->getRevenueTable()->getRevenue($id)
        );
    }
    
    public function changeStatusAction()
    {
		$request = $this->getRequest();
		if ($request->isPost()) {
			$id = $request->getPost('id');
			$status = $request->getPost('status', '1');
			if ($id != null && $status != null) {
				$this->getRevenueTable()->changeStatus($id, $status);
				echo json_encode(array("msg"=>"Status successfully changed..!!"));
			} else {
				echo json_encode(array("msg"=>"Failed to change the status..!!"));
			}
			exit;
		}
	}


}

