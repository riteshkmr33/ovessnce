<?php

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Admin\Model\PractitionerOrganizations;
use Admin\Form\PractOrgFilterForm;
use Admin\Form\PractitionerOrganizationsForm;
use Zend\Validator\File\Size;
use Zend\ImageS3;
use \PHPExcel;

class PractitionerOrganizationsController extends AbstractActionController
{
	protected $PractitionerOrganizationsTable;
	
	public function indexAction()
    {
		$form = new PractOrgFilterForm($this->getServiceLocator()->get('Admin\Model\StatesTable'),$this->getServiceLocator()->get('Admin\Model\CountriesTable'));
		//$paginator = $this->getPractitionerOrganizationsTable()->fetchAll();   
		
		$request = $this->getRequest();
        $postedData = array();
        $getData = (array)$request->getQuery();
        unset($getData['page']);
        
        if ($request->isPost()) {
			$postedData = $request->getPost();
			$form->bind($postedData);
			$filter = array(
				'organization_name' => trim($postedData['organization_name']),
				'state_id' => trim($postedData['state_id']),
				'country_id' => trim($postedData['country_id']),
				'status_id' => $postedData['status_id'],
			);
			$paginator = $this->getPractitionerOrganizationsTable()->fetchAll(true, $filter);
		} else if (count($getData) > 0) {
			$filter = array();
			$form->bind($request->getQuery());
			$postedData = $getData;
			isset($getData['organization_name'])?$filter['organization_name'] = trim($getData['organization_name']):"";
			isset($getData['state_id'])?$filter['state_id'] = trim($getData['state_id']):"";
			isset($getData['country_id'])?$filter['country_id'] = trim($getData['country_id']):"";
			isset($getData['status_id'])?$filter['status_id'] = trim($getData['status_id']):"";
			
			$paginator = $this->getPractitionerOrganizationsTable()->fetchAll(true, $filter);
		} else {
			$paginator = $this->getPractitionerOrganizationsTable()->fetchAll();
		}
		
		
		$paginator->setCurrentPageNumber((int)$this->params()->fromQuery('page', 1));   
		$paginator->setItemCountPerPage(10);
		
		return new ViewModel(array(
			'organizations' => $paginator,
			'postedData' => array_filter((array)$postedData),
			'form' => $form,
			'status' => $this->getServiceLocator()->get('Admin\Model\StatusTable')->fetchAll(false, array(1,2)), 
			'successMsgs' => $this->flashMessenger()->getCurrentSuccessMessages(),
			'errors' => $this->flashMessenger()->getCurrentErrorMessages()
		));        
    }
    
    public function addAction()
    {
		
		$form = new PractitionerOrganizationsForm($this->getServiceLocator()->get('Admin\Model\StatesTable'),$this->getServiceLocator()->get('Admin\Model\CountriesTable'));
        $form->get('submit')->setValue('Add');

        $request = $this->getRequest();
        if ($request->isPost()) {
			
			$File    = $this->params()->fromFiles('logo');
			
			// Make certain to merge the files info!
			$post = array_merge_recursive(
				$request->getPost()->toArray(),
				 array('logo'=> $File['name'])
			);
			
            $organizations = new PractitionerOrganizations();
            $form->setInputFilter($organizations->getInputFilter());
            $form->setData($post);
			
            if ($form->isValid()) {
				
				$formData = array();
				$formData = $form->getData();
									
				$size = new Size(array('min'=>200,'max'=> 2000000)); 
				
				$adapter = new \Zend\File\Transfer\Adapter\Http();
                $adapter->setValidators(array($size), $File['name']);
                
				if (!$adapter->isValid()){	
					
					$dataError = $adapter->getMessages();
					
                    $error = array();
                    foreach($dataError as $key=>$row)
                    {
                        $error[] = $row;
                    }
                    $form->setMessages(array('logo'=>$error ));
                    return array('form' => $form);
				}else{
					
					$S3 = new ImageS3;
					$data = $S3->uploadFiles($_FILES['logo'],"PractitionersOrganizationLogo",array('Small'=>25,'Medium'=>50,'Large'=>75,'Original'=>100));
					if(is_array($data) && count($data) > 0 ){
						$formData['logo'] = $data['Original'];
					}
				}
			
                $organizations->exchangeArray($formData);
                $this->getPractitionerOrganizationsTable()->savePractitionerOrganization($organizations);
                $this->flashMessenger()->addSuccessMessage('Practitioned Organization added successfully..!!');

                // Redirect to list of certifications
                return $this->redirect()->toRoute('admin/organizations');
            }
             
        }
        return array('form' => $form);
         
	}
		
    public function editAction()
    {
		
		$id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('admin/organizations', array(
                'action' => 'add'
            ));
        }
        $organization = $this->getPractitionerOrganizationsTable()->getPractitionerOrganization($id);
        
        if ($organization == false) {
			$this->flashMessenger()->addErrorMessage('Practitioned Organization not found..!!');
			return $this->redirect()->toRoute('admin/organizations');
		}

        $form  = new PractitionerOrganizationsForm($this->getServiceLocator()->get('Admin\Model\StatesTable'),$this->getServiceLocator()->get('Admin\Model\CountriesTable'), $organization->country_id);
        $old_logo = $organization->logo;
        $form->bind($organization);
        $form->get('submit')->setAttribute('value', 'Edit');

        $request = $this->getRequest();
        if ($request->isPost()) {
			
			$File    = $this->params()->fromFiles('logo');
			// Make certain to merge the files info!
			$post = array_merge_recursive(
				$request->getPost()->toArray(),
				 array('logo'=> $File['name'])
			);
			
            $form->setInputFilter($organization->getInputFilter());
            $form->setData($post);

            if ($form->isValid()) {
				
				$formData = $form->getData();
								
				$size = new Size(array('min'=>200,'max'=> 2000000)); 
				
				$adapter = new \Zend\File\Transfer\Adapter\Http();
                $adapter->setValidators(array($size), $File['name']);
                
                if (!$adapter->isValid()){	
					
					$dataError = $adapter->getMessages();
					
					if(!$dataError['fileUploadErrorNoFile']){
						
						$error = array();
						foreach($dataError as $key=>$row)
						{
							$error[] = $row;
						}
						$form->setMessages(array('logo'=>$error ));
						return array(
							'id' => $id,
							'form' => $form,
						);
					}
				}else{
					
					if(isset($old_logo) && !empty($old_logo)){
						
						$AllImages = array();	
						$S3 = new ImageS3;
						$S3Path = "https://ovessence.s3.amazonaws.com/PractitionersOrganizationLogo/Original/";
						
						$AllImages[] = str_replace($S3Path,'PractitionersOrganizationLogo/Small/',$old_logo);	
						$AllImages[] = str_replace($S3Path,'PractitionersOrganizationLogo/Medium/',$old_logo);
						$AllImages[] = str_replace($S3Path,'PractitionersOrganizationLogo/Large/',$old_logo);
						$AllImages[] = str_replace($S3Path,'PractitionersOrganizationLogo/Original/',$old_logo);
						
						if(count($AllImages) > 0 ){
							foreach($AllImages as $image){
								$S3->deleteFile($image);
							}
						}
					}
					
					$S3 = new ImageS3;
					$data = $S3->uploadFiles($_FILES['logo'],"PractitionersOrganizationLogo",array('Small'=>25,'Medium'=>50,'Large'=>75,'Original'=>100));
					
					if(is_array($data) && count($data) > 0 ){
						
						$formData->logo = $data['Original'];
					}
					
				}
                
                $this->getPractitionerOrganizationsTable()->savePractitionerOrganization($form->getData());
				
				$this->flashMessenger()->addSuccessMessage('Practitioned Organization updated successfully..!!');
				
                // Redirect to list of consumers
                return $this->redirect()->toRoute('admin/organizations');
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
            return $this->redirect()->toRoute('admin/organizations');
        }
		
        $request = $this->getRequest();
        if ($request->isPost()) {
			
            $del = $request->getPost('del', 'No');
			
            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                
                $pracOrg = $this->getPractitionerOrganizationsTable()->getPracOrg($id);
                if($pracOrg){
					$this->flashMessenger()->addErrorMessage('Practitioned Organization in use can not be deleted..!!'); 
					return $this->redirect()->toRoute('admin/organizations');
					exit;
				}
                
                /* Deleting Images from amazon - starts here */
                $organization = $this->getPractitionerOrganizationsTable()->getPractitionerOrganization($id);
                
				if($organization->logo){
					
					$AllImages = array();	
					$S3 = new ImageS3;
					$S3Path = "https://ovessence.s3.amazonaws.com/PractitionersOrganizationLogo/Original/";
						
					$AllImages[] = str_replace($S3Path,'PractitionersOrganizationLogo/Small/',$organization->logo);	
					$AllImages[] = str_replace($S3Path,'PractitionersOrganizationLogo/Medium/',$organization->logo);
					$AllImages[] = str_replace($S3Path,'PractitionersOrganizationLogo/Large/',$organization->logo);
					$AllImages[] = str_replace($S3Path,'PractitionersOrganizationLogo/Original/',$organization->logo);
					
					if(count($AllImages) > 0 ){
						foreach($AllImages as $image){
							$S3->deleteFile($image);
						}
					}
					
				}
				/* Deleting Images from amazon - ends here */
				
			$this->getPractitionerOrganizationsTable()->deletePractitionerOrganization($id); // Delete recode from database
			
			$this->flashMessenger()->addSuccessMessage('Practitioned Organization deleted successfully..!!'); 
			
            }

            // Redirect to list of certifications
            return $this->redirect()->toRoute('admin/organizations');
        }

        return array(
            'id'    => $id,
            'organization' => $this->getPractitionerOrganizationsTable()->getPractitionerOrganization($id)
        );
        
	}	
	
	public function getPractitionerOrganizationsTable()
	{
		
		if (!$this->PractitionerOrganizationsTable) {
			$sm = $this->getServiceLocator();
			$this->PractitionerOrganizationsTable = $sm->get('Admin\Model\PractitionerOrganizationsTable');
		}
	
		return $this->PractitionerOrganizationsTable;
		 
	}
	
	public function changeStatusAction()
    {
		$request = $this->getRequest();
		if ($request->isPost()) {
			$id = $request->getPost('id');
			$status = $request->getPost('status', '1');
			if ($id != null && $status != null) {
				$this->getPractitionerOrganizationsTable()->changeStatus($id, $status);
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
			isset($getData['organization_name'])?$filter['organization_name'] = trim($getData['organization_name']):"";
			isset($getData['state_id'])?$filter['state_id'] = trim($getData['state_id']):"";
			isset($getData['country_id'])?$filter['country_id'] = trim($getData['country_id']):"";
			isset($getData['status_id'])?$filter['status_id'] = trim($getData['status_id']):"";
			
			$results = $this->getPractitionerOrganizationsTable()->ExportAll($filter);
			
		} else {
			$results = $this->getPractitionerOrganizationsTable()->ExportAll();
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
						->setCellValue('B'.$row, 'Organization name')
						->setCellValue('C'.$row, 'Address')
						->setCellValue('D'.$row, 'City')
						->setCellValue('E'.$row, "State")
						->setCellValue('F'.$row, 'Country')
						->setCellValue('G'.$row, 'Postal code')
						->setCellValue('H'.$row, 'Email')
						->setCellValue('I'.$row, 'Phone');
			foreach ($results as $result) {
				
				$row++;
				$xls->setActiveSheetIndex(0)
						->setCellValue('A'.$row, ($row-1))
						->setCellValue('B'.$row, (isset($result->organization_name) && $result->organization_name != "")?$result->organization_name:'NA')
						->setCellValue('C'.$row, (trim($result->street1_address.", ".$result->street2_address) != "")?$result->street1_address.", ".$result->street2_address:'NA')
						->setCellValue('D'.$row, (isset($result->city) && $result->city != "")?$result->city:'NA')
						->setCellValue('E'.$row, (isset($result->state_name) && $result->state_name != "")?$result->state_name:'NA')
						->setCellValue('F'.$row, (isset($result->country_name) && $result->country_name != "")?$result->country_name:'NA')
						->setCellValue('G'.$row, (isset($result->zip_code) && $result->zip_code != "")?$result->zip_code:'NA')
						->setCellValue('H'.$row, (isset($result->email) && $result->email != "")?$result->email:'NA')
						->setCellValue('I'.$row, (isset($result->phone_no) && $result->phone_no != "")?$result->phone_no:'NA');
				
			}
			
			require_once './vendor/phpexcel/phpexcel/Classes/PHPExcel/IOFactory.php';
		
			$objWriter = \PHPExcel_IOFactory::createWriter($xls, 'Excel2007');
			
			// If you want to output e.g. a PDF file, simply do:
			//$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'PDF');
			$objWriter->save('./public/uploads/MyExcel.xlsx');
			header("Content-disposition: attachment; filename=Ovessence_practitioner_organizations(".date('d-M-Y').").xlsx");
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
