<?php
namespace Admin\Form; 

 use Zend\Form\Form;
 use Admin\Model\ServiceCategory;
 use Admin\Model\ServiceCategoryTable;
 use Zend\Db\Adapter\AdapterInterface;

 class ServiceForm extends Form
 {
     public function __construct(ServiceCategoryTable $selectTable)
     {
		 $this->setSelectTable($selectTable);
		 
         
         parent::__construct('services');

         $this->add(array(
             'name' => 'id',
             'type' => 'Hidden',
         ));
         $this->add(array(
             'name' => 'service_period',
             'type' => 'Text',
             'options' => array(
                 'label' => 'Service Period',
             ),
			'attributes' => array(
				'class'=>'form-control'
			 )
         ));
         $this->add(array(
             'type' => 'Zend\Form\Element\Select',
             'name' => 'service_category_id',
             'options' => array(
                     'label' => 'Select Category',
                     'value_options' => $this->getCategories(),
                     'empty_option'  => '--- Choose Service Category ---'
             ),
			'attributes' => array(
				'class'=>'form-control'
			 )
		 ));
		 $this->add(array(
             'type' => 'Zend\Form\Element\Select',
             'name' => 'status_id',
             'options' => array(
                     'label' => 'Status',
                     'value_options' => array(
                             '1' => 'Active',
                             '2' => 'Inactive',
                             '3' => 'Suspended',
                     ),
             ),
			'attributes' => array(
				'class'=>'form-control'
			 )
		));
         $this->add(array(
             'name' => 'description',
             'type' => 'Zend\Form\Element\Textarea',
             'options' => array(
                 'label' => 'Description',
             ),
			'attributes' => array(
				'class'=>'form-control'
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
        
        $table = $this->getSelectTable();
        $data  = $table->fetchAll(false, array("parent_id"=>0));

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
			$newData = $this->getSelectTable()->fetchAll(false, array("parent_id"=>$selectOption->id));
			
			if (count($newData) > 0) {
				$res = $res+$this->getChild($sep,$level+1,$newData);
			}
		}
		return $res; 
		
	}

	public function setSelectTable($selectTable)
    {
        $this->selectTable = $selectTable;

        return $this;
    }
    
     public function getSelectTable()
    {
        return $this->selectTable;
    }
 }
