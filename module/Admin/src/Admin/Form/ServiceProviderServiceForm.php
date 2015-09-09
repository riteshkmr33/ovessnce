<?php
namespace Admin\Form; 

 use Zend\Form\Form;
 use Admin\Model\ServiceCategory;
 use Admin\Model\ServiceCategoryTable;
 use Admin\Model\Status;
 use Admin\Model\StatusTable;
 use Zend\Db\Adapter\AdapterInterface;

 class ServiceProviderServiceForm extends Form
 {
	 private $service;
	 private $status;
	 
     public function __construct(StatusTable $status, ServiceCategoryTable $service)
     {
		 $this->service = $service;
		 $this->status = $status;
		 
		 parent::__construct('serviceproviderservice');

         $this->add(array(
             'name' => 'id',
             'type' => 'Hidden',
         ));
         
         $this->add(array(
             'name' => 'user_id',
             'type' => 'Hidden',
         ));
         
         $this->add(array(
             'name' => 'duration',
             'type' => 'Text',
             'options' => array(
                 'label' => 'Duration',
             ),
			'attributes' => array(
				'class'=>'form-control input-large'
			 )
         ));
         
         $this->add(array(
             'name' => 'price',
             'type' => 'Text',
             'options' => array(
                 'label' => 'Fees',
             ),
			'attributes' => array(
				'class'=>'form-control input-large'
			 )
         ));
         
         $this->add(array(
             'type' => 'Zend\Form\Element\Select',
             'name' => 'service_id',
             'options' => array(
                     'label' => 'Select Category',
                     'value_options' => $this->getCategories(),
                     'empty_option'  => '--- Choose Service ---'
             ),
			'attributes' => array(
				'class'=>'form-control input-large select2'
			 )
		 ));
		 
		 $this->add(array(
             'type' => 'Zend\Form\Element\Select',
             'name' => 'status_id',
             'options' => array(
                     'label' => 'Status',
                     'value_options' => $this->getStatus()
             ),
			'attributes' => array(
				'class'=>'form-control input-large select2'
			 )
		));
		
         $this->add(array(
             'name' => 'submit',
             'type' => 'Submit',
             'attributes' => array(
                 'value' => 'Go',
                 'id' => 'submitbutton',
             ),
         ));
     }
     
    public function getCategories()
    {
        $data  = $this->service->fetchAll(false, array("parent_id"=>0));
        
        $selectData = array();
		$selectData = $this->getChild('',0,$data);
			
        return $selectData; 
       
		
	}
	
	public function getChild($sep,$level,$data){
		
		$res = array();
		
		if ($level > 0 && count($data) > 0) {
			$sep = "|";
			
			for ($i=1; $i<=$level; $i++) {
				$sep .= "_";
			}
			
		}
		
		foreach($data as $selectOption){
			$res[$selectOption->id] = $sep.ucwords($selectOption->category_name);
			$newData = $this->service->fetchAll(false, array("parent_id"=>$selectOption->id));
			
			if (count($newData) > 0) {
				$res = $res+$this->getChild($sep,$level+1,$newData);
			}
		}
		return $res; 
		
	}
	
	public function getStatus()
    {
        $data  = $this->status->fetchAll(false, array(1,2));

        $selectData = array();

        foreach ($data as $selectOption) {
            $selectData[$selectOption->status_id] = ucwords($selectOption->status);
        }

        return $selectData; 
	}
 }
