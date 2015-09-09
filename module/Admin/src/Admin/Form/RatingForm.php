<?php
namespace Admin\Form;

 use Zend\Form\Form;
 use Admin\Model\Services;
 use Admin\Model\ServicesTable;
 use Admin\Model\ServiceProvider;
 use Admin\Model\ServiceProviderTable;
 use Zend\Db\Adapter\AdapterInterface;

 class RatingForm extends Form
 {
	 private $practitioners;
	 private $services;
	 private $id;
	 
     public function __construct(ServiceProviderTable $practitioners, ServicesTable $services, $id="")
     {
		 $this->practitioners = $practitioners;
		 $this->services = $services;
		 $this->id = $id;
		 
		 parent::__construct('booking');

         $this->add(array(
             'name' => 'id',
             'type' => 'Hidden',
         ));
         $this->add(array(
             'type' => 'Zend\Form\Element\Select',
             'name' => 'users_id',
             'options' => array(
                     'label' => 'Select Practitioner',
                     'value_options' => $this->getPractitioners(),
                     'empty_option'  => '--- Choose Practitioner ---'
             ),
             'attributes' => array(
				'class'=>'form-control input-large getServices'
			 )
		 ));
		/* $this->add(array(
             'type' => 'Zend\Form\Element\Select',
             'name' => 'service_id',
             'options' => array(
                     'label' => 'Select Service',
                     'value_options' => $this->getServices(),
                     'empty_option'  => '--- Choose Service ---',
             ),
             'attributes' => array(
				'class'=>'form-control input-large services',
				'disabled' => true
			 )
		 ));*/
		 
         $this->add(array(
             'name' => 'submit',
             'type' => 'Submit',
             'attributes' => array(
                 'value' => 'Go',
                 'id' => 'submitbutton',
             ),
         ));
     }
     
  /*  public function getServices()
    {
        $data  = ($this->id != "")?$this->services->getPractitionerServices($this->id):$this->services->getPractitionerServices();
        $selectData = array();
        
        foreach ($data as $value) {
			$selectData[$value['id']] = ucwords($value['service']);
		}
		
		return $selectData;
	}*/
	
	public function getPractitioners()
    {
        $data  = $this->practitioners->fetchAll(false);

        $selectData = array();

        foreach ($data as $selectOption) {
            $selectData[$selectOption->id] = ucwords($selectOption->first_name." ".$selectOption->last_name);
        }

        return $selectData; 
       
		
	}
	
 }
