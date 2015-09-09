<?php
namespace Admin\Form;

 use Zend\Form\Form;
 use Admin\Model\States;
 use Admin\Model\StatesTable;
 use Admin\Model\Countries;
 use Admin\Model\CountriesTable;
 use Admin\Model\Status;
 use Admin\Model\StatusTable;

 class ConsumerFilterForm extends Form
 {
	 private $country;
	 private $state;
	 private $status;
	 	 
     public function __construct(StatesTable $state, CountriesTable $country, StatusTable $status)
     {
		 $this->state = $state;
		 $this->country = $country;
		 $this->status = $status;
		  
         parent::__construct('consumerFilter');

         
         $this->add(array(
             'name' => 'name',
             'type' => 'Text',
             'options' => array(
                 'label' => 'Name',
             ),
             'attributes' => array(
				'class' => 'form-control form-filter input-sm',
             ),
         ));
         
         $this->add(array(
             'name' => 'user_name',
             'type' => 'Text',
             'options' => array(
                 'label' => 'User Name',
             ),
             'attributes' => array(
				'class' => 'form-control form-filter input-sm',
             ),
         ));
         
         $this->add(array(
             'name' => 'age',
             'type' => 'Text',
             'options' => array(
                 'label' => 'Age',
             ),
             'attributes' => array(
				'class'=>'form-control input-sm',
				'autocomplete'=> false,
			 )
         ));
         
         $this->add(array(
             'type' => 'Zend\Form\Element\Select',
             'name' => 'gender',
             'options' => array(
                     'label' => 'Select Gender',
                     'value_options' => array('' => '-- Select Gender --', 'M' => 'Male', 'F' => 'Female'),
             ),
             'attributes' => array(
				'class'=>'form-control form-filter input-sm select2',
			 )
         ));
         
         $this->add(array(
             'name' => 'email',
             'type' => 'Text',
             'options' => array(
                 'label' => 'Email',
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
             'name' => 'created_on',
             'type' => 'Text',
             'options' => array(
                 'label' => 'Created On',
             ),
             'attributes' => array(
				'class' => 'date-picker form-control form-filter input-sm',
				'data-date-format' => 'yyyy-mm-dd',
             ),
         ));
         
         $this->add(array(
             'type' => 'Zend\Form\Element\Select',
             'name' => 'state_id',
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
             'name' => 'country_id',
             'options' => array(
                     'label' => 'Select Country',
                     'value_options' => $this->getCountries(),
                     'empty_option'  => '--- Choose Country ---'
             ),
             'attributes' => array(
				'class' => 'form-control form-filter input-sm select2 getStates',
				'data-id' => 'states'
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
	
	public function getStatus()
    {
        $data  = $this->status->fetchAll(false, array(9,5,10,3));

        $selectData = array();

        foreach ($data as $selectOption) {
            $selectData[$selectOption->status_id] = ucwords($selectOption->status);
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
	
	public function getServices()
    {
        $data  = $data  = $this->service->fetchAll(false);

        $selectData = array();

        foreach ($data as $selectOption) {
            $selectData[$selectOption->id] = ucwords($selectOption->service_name);
        }

        return $selectData; 
	}
 }
