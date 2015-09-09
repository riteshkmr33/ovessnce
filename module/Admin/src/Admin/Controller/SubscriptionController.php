<?php

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Admin\Model\Subscriptions;
use Admin\Form\SubscriptionForm;
use Zend\Session\Container;
use \PHPExcel;

class SubscriptionController extends AbstractActionController
{
	private $subscriptionTable;
	public $errors = array();
	
	private function getSubscriptionTable()
	{
		if (!$this->subscriptionTable) {
			$this->subscriptionTable = $this->getServiceLocator()->get('Admin\Model\SubscriptionsTable');
		}
		
		return $this->subscriptionTable;
	}

    public function indexAction()
    {
		$request = $this->getRequest();
        if ($request->isGet()) {
			$postedData = (array)$request->getQuery();
			$paginator = $this->getSubscriptionTable()->fetchAll(true, $postedData);
		} else {
			$paginator = $this->getSubscriptionTable()->fetchAll();
		}
       
        $paginator->setCurrentPageNumber((int)$this->params()->fromQuery('page', 1));
		$paginator->setItemCountPerPage(10);

		return new ViewModel(array(
			'subscriptions' => $paginator,
			'postedData' => $postedData,
			'status' => $this->getServiceLocator()->get('Admin\Model\StatusTable')->fetchAll(false, array(1,2)),
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
		
        $form = new SubscriptionForm($this->getServiceLocator()->get('Admin\Model\UsersTable'), $this->getServiceLocator()->get('Admin\Model\SubscriptionDurationsTable'), $this->getServiceLocator()->get('Admin\Model\StatusTable'), $payment_methods);
        $form->get('submit')->setValue('Add');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $sb = new Subscriptions();
            
            $form->setInputFilter($sb->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $sb->exchangeArray($form->getData());
                $this->getSubscriptionTable()->saveSubscription($sb, $details['user_id'], $this->getServiceLocator()->get('Admin\Model\SubscriptionDurationsTable'));
                $this->flashMessenger()->addSuccessMessage('Subscription added successfully..!!');

                // Redirect to list of pages
                return $this->redirect()->toRoute('admin/subscriptions');
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
            return $this->redirect()->toRoute('admin/subscriptions', array(
                'action' => 'add'
            ));
        }
        
        $user_details = new Container('user_details');
		$details = $user_details->details;
		
		$config = $this->getServiceLocator()->get('Config');
		$payment_methods = $config['payment_methods'];
		
        $sb = $this->getSubscriptionTable()->getSubscription($id);
        if ($sb == false) {
			$this->flashMessenger()->addErrorMessage('Subscription not found..!!');
			return $this->redirect()->toRoute('admin/subscriptions');
		}
		
        $form = new SubscriptionForm($this->getServiceLocator()->get('Admin\Model\UsersTable'), $this->getServiceLocator()->get('Admin\Model\SubscriptionDurationsTable'), $this->getServiceLocator()->get('Admin\Model\StatusTable'), $payment_methods);
        $form->bind($sb);
        $form->get('submit')->setAttribute('value', 'Edit');

        $request = $this->getRequest();
        if ($request->isPost()) {
			
            $form->setInputFilter($sb->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
				
                $this->getSubscriptionTable()->saveSubscription($form->getData(), $details['user_id'], $this->getServiceLocator()->get('Admin\Model\SubscriptionDurationsTable'));
                $this->flashMessenger()->addSuccessMessage('Subscription updated successfully..!!');
                
                // Redirect to list of pages
                return $this->redirect()->toRoute('admin/subscriptions');
            } else {
				$this->errors = $form->getMessages();
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
            return $this->redirect()->toRoute('admin/subscriptions');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $this->getSubscriptionTable()->deleteSubscription($id);
                $this->flashMessenger()->addSuccessMessage('Subscription deleted successfully..!!');
            }

            // Redirect to list of pages 
            return $this->redirect()->toRoute('admin/subscriptions');
        }

        return array(
            'id'    => $id,
            'subscription' => $this->getSubscriptionTable()->getSubscription($id)
        );
    }
    
    public function changeStatusAction()
    {
		$request = $this->getRequest();
		if ($request->isPost()) {
			$id = $request->getPost('id');
			$status = $request->getPost('status', '1');
			if ($id != null && $status != null) {
				$this->getSubscriptionTable()->changeStatus($id, $status);
				echo json_encode(array("msg"=>"Status successfully changed..!!"));
			} else {
				echo json_encode(array("msg"=>"Failed to change the status..!!"));
			}
			exit;
		}
	}
	
	public function exportAction()
    {
		$request = $this->getRequest();
        $getData = (array)$request->getQuery();
        
        if (count($getData) > 0) {
			$filter = array();
			$postedData = $getData;
			
			// Filter fields goes here
			
			$results = $this->getSubscriptionTable()->ExportAll($filter, $postedData);
			
		} else {
			$results = $this->getSubscriptionTable()->ExportAll();
		}
		
		//echo '<pre>'.count($results)."<br />"; print_r($results->current()); exit;
		
		if (count($results) > 0) {
			$row = 1;
			
			$xls = new PHPExcel();
			$xls->getProperties()->setCreator("Ovessence")
						->setLastModifiedBy("Ovessence Admin")
						->setTitle("Ovessence Practitioner's Organizations")
						->setSubject("Ovessence Practitioner's Organizations")
						->setDescription("")
						->setKeywords("ovessence Practitioner's Organizations")
						->setCategory("Sale report file");
			$xls->getActiveSheet()->setTitle("Ovessence Organizations List");
			
			/* Styling code starts here */
			$xls->getActiveSheet()->getStyle('A'.$row.':I'.$row)->getFont()->setBold(true);
			$xls->getActiveSheet()->getStyle('A'.$row.':I'.$row)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			
			/* Styling code ends here */
			
			$xls->setActiveSheetIndex(0)
						->setCellValue('A'.$row, 'S.no.')
						->setCellValue('B'.$row, 'User')
						->setCellValue('C'.$row, 'Subscription')
						->setCellValue('D'.$row, 'Start Date')
						->setCellValue('E'.$row, "End Date")
						->setCellValue('F'.$row, 'Amount')
						->setCellValue('G'.$row, 'Payment Status');
			foreach ($results as $result) {
				
				$row++;
				$xls->setActiveSheetIndex(0)
						->setCellValue('A'.$row, ($row-1))
						->setCellValue('B'.$row, (trim($result->first_name." ".$result->last_name) != "")?$result->first_name." ".$result->last_name:'NA')
						->setCellValue('C'.$row, (isset($result->subscription_name) && $result->subscription_name != "")?$result->subscription_name." - ".$result->duration." ".$result->duration_in:'NA')
						->setCellValue('D'.$row, (isset($result->subscription_start_date) && $result->subscription_start_date != "")?$result->subscription_start_date:'NA')
						->setCellValue('E'.$row, (isset($result->subscription_end_date) && $result->subscription_end_date != "")?$result->subscription_end_date:'NA')
						->setCellValue('F'.$row, (isset($result->invoice_total) && $result->invoice_total != "")?$result->invoice_total:'NA')
						->setCellValue('G'.$row, (isset($result->payment_status) && $result->payment_status != "")?$result->payment_status:'NA');
				
			}
			
			require_once './vendor/phpexcel/phpexcel/Classes/PHPExcel/IOFactory.php';
		
			$objWriter = \PHPExcel_IOFactory::createWriter($xls, 'Excel2007');
			
			// If you want to output e.g. a PDF file, simply do:
			//$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'PDF');
			$objWriter->save('./public/uploads/MyExcel.xlsx');
			header("Content-disposition: attachment; filename=Ovessence_subscriptions(".date('d-M-Y').").xlsx");
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

