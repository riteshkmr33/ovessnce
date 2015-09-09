<?php
namespace Admin\Form;

 use Zend\Form\Form;
 use Admin\Model\States;
 use Admin\Model\StatesTable;
 use Admin\Model\Countries;
 use Admin\Model\CountriesTable;
 use Admin\Model\Services;
 use Admin\Model\ServicesTable;
 use Admin\Model\Status;
 use Admin\Model\StatusTable;

 class ServiceProviderFilterForm extends Form
 {
	 private $country;
	 private $state;
	 private $service;
	 private $status;
	 
     public function __construct(StatesTable $state, CountriesTable $country, ServicesTable $service, StatusTable $status)
     {
		 $this->state = $state;
		 $this->country = $country;
		 $this->service = $service;
		 $this->status = $status;
		 
         
         parent::__construct('serviceproviderfilter');

         
         $this->add(array(
             'name' => 'provider_name',
             'type' => 'Text',
             'options' => array(
                 'label' => 'Provider Name',
             ),
             'attributes' => array(
				'class' => 'form-control form-filter input-sm',
             ),
         ));
         $this->add(array(
             'name' => 'from',
             'type' => 'Text',
             'options' => array(
                 'label' => 'From',
             ),
             'attributes' => array(
				'id' => 'from',
				'class' => 'form-control form-filter input-sm',
				'readonly' => true,
				'placeholder' => 'From'
             ),
         ));
         $this->add(array(
             'name' => 'to',
             'type' => 'Text',
             'options' => array(
                 'label' => 'To',
             ),
             'attributes' => array(
				'id' => 'to',
             ),
             'attributes' => array(
				'id' => 'to',
				'class' => 'form-control form-filter input-sm',
				'readonly' => true,
				'placeholder' => 'To'
             ),
         ));
         $this->add(array(
             'type' => 'Zend\Form\Element\Select',
             'name' => 'serviceType',
             'options' => array(
                     'label' => 'Select Service',
                     'value_options' => $this->getServices(),
                     'empty_option'  => '--- Choose Service ---'
             ),
             'attributes' => array(
				'class' => 'form-control form-filter input-sm select2',
             ),
		 ));
         $this->add(array(
             'type' => 'Zend\Form\Element\Select',
             'name' => 'state',
             'options' => array(
                     'label' => 'Select State',
                     'value_options' => array(),
                     'empty_option'  => '--- Choose State ---'
             ),
             'attributes' => array(
				'class' => 'form-control form-filter input-sm select2',
				'id' => 'states'
             ),
		 ));
		 $this->add(array(
             'type' => 'Zend\Form\Element\Select',
             'name' => 'country',
             'options' => array(
                     'label' => 'Select Country',
                     'value_options' => $this->getCountries(),
                     'empty_option'  => '--- Choose Country ---'
             ),
             'attributes' => array(
				'class' => 'form-control form-filter input-sm select2 getStates',
				'data-id'=>'states'
             ),
		 ));
		 $this->add(array(
             'name' => 'city',
             'type' => 'Text',
             'options' => array(
                 'label' => 'City',
             ),
             'attributes' => array(
				'class' => 'form-control form-filter input-sm',
             ),
         ));
         $this->add(array(
             'type' => 'Zend\Form\Element\Select',
             'name' => 'status_id',
             'options' => array(
                     'label' => 'Select Status',
                     'value_options' => $this->getStatus(),
                     'empty_option'  => '--- Choose Status ---'
             ),
             'attributes' => array(
				'class' => 'form-control form-filter input-sm select2',
             ),
		 ));
         $this->add(array(
             'name' => 'submit',
             'type' => 'Submit',
             'attributes' => array(
                 'value' => 'Filter',
                 'id' => 'submitbutton',
             ),
         ));
     }
     
    public function getCountries()
    {
        $data  = $this->country->fetchAll(false);

        $selectData = array();

        foreach ($data as $selectOption) {
            $selectData[$selectOption->id] = ucwords($selectOption->country_name);
        }

        return $selectData; 
	}
	
	public function getStates()
    {
        $data  = $data  = $this->state->fetchAll(false);

        $selectData = array();

        foreach ($data as $selectOption) {
            $selectData[$selectOption->id] = ucwords($selectOption->state_name);
        }

        return $selectData; 
	}
	
	public function getStatus()
    {
        $data  = $this->status->fetchAll(false, array(9,5,10,3));

        $selectData = array();

        foreach ($data as $selectOption) {
            $selectData[$selectOption->status_id] = ucwords($selectOption->status);
        }

        return $selectData; 
	}
	
	public function getServices()
    {
        $data  = $data  = $this->service->fetchAll(false);

        $selectData = array();

        foreach ($data as $selectOption) {
            $selectData[$selectOption->id] = $selectOption->category_name." - ".$selectOption->duration." mins";
        }

        return $selectData; 
	}
 }
