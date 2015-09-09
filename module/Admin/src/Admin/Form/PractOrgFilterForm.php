<?php
namespace Admin\Form;

 use Zend\Form\Form;
 use Admin\Model\States;
 use Admin\Model\StatesTable;
 use Admin\Model\Countries;
 use Admin\Model\CountriesTable;
 use Zend\Db\Adapter\AdapterInterface;

 class PractOrgFilterForm extends Form
 {
	 
	 private $country;
	 private $state;
	 
     public function __construct(StatesTable $state, CountriesTable $country)
     {
		 
		 $this->state = $state;
		 $this->country = $country;
		 
		 parent::__construct('PractOrgFilterForm');

         
		 $this->add(array(
             'name' => 'organization_name',
             'type' => 'Text',
             'options' => array(
                 'label' => 'Organization Name',
             ),
             'attributes' => array(
				'class'=>'form-control input-sm'
			 )
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
				'class'=>'form-control select2',
				'id' => 'states',
			 )
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
				'class'=>'form-control select2 getStates',
				'data-id' => 'states',
			 )
		 ));
		 
		 $this->add(array(
             'type' => 'Zend\Form\Element\Select',
             'name' => 'status_id',
             'options' => array(
                     'label' => 'Select Status',
                     'value_options' => array('1' => 'Active', '2' => 'Inactive'),
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
        $data  = $this->state->fetchAll(false);

        $selectData = array();

        foreach ($data as $selectOption) {
            $selectData[$selectOption->id] = ucwords($selectOption->state_name);
        }

        return $selectData; 
	}
     
 }
