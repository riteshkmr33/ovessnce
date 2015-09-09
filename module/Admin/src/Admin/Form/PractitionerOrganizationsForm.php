<?php
namespace Admin\Form;

 use Zend\Form\Form;
 use Admin\Model\Users;
 use Admin\Model\UsersTable;
 use Admin\Model\States;
 use Admin\Model\StatesTable;
 use Admin\Model\Countries;
 use Admin\Model\CountriesTable;
 use Zend\Db\Adapter\AdapterInterface;
  
 class PractitionerOrganizationsForm extends Form
 {
	  
	 private $country;
	 private $state;
	 private $country_id;
	 	 
     public function __construct(StatesTable $state, CountriesTable $country, $country_id = "")
     {
		 
		 $this->state = $state;
		 $this->country = $country;
		 $this->country_id = $country_id;
		 
         // we want to ignore the name passed
         parent::__construct('practitioners-organisations');
         
         $this->setAttribute('method', 'post');
         $this->setAttribute('enctype','multipart/form-data');
         

         $this->add(array(
             'name' => 'organization_id',
             'type' => 'Hidden',
         ));
         
         $this->add(array(
             'name' => 'organization_name',
             'type' => 'Text',
             'options' => array(
                 'label' => 'Organization Name',
             ),
             'attributes' => array(
				'class'=>'form-control input-large'
			 )
         ));
                  
         $this->add(array(
            'name' => 'logo',
            'attributes' => array(
                'type'  => 'file',
            ),
            'options' => array(
                'label' => 'Logo',
            ),
        )); 
         
         $this->add(array(
             'name' => 'address',
             'type' => 'Text',
             'options' => array(
                 'label' => 'Address',
             ),
             'attributes' => array(
				'class'=>'form-control input-large'
			 )
         ));
         
         $this->add(array(
             'name' => 'phone_no',
             'type' => 'Text',
             'options' => array(
                 'label' => 'Phone#',
             ),
             'attributes' => array(
				'class'=>'form-control input-large'
			 )
         ));
         
         $this->add(array(
             'type' => 'Zend\Form\Element\Email',
             'name' => 'email',
             'options' => array(
                'label' => 'Email'
			 ),
			 'attributes' => array(
                'placeholder' => 'you@domain.com',
                'class'=>'form-control input-large'
			 )
         ));
         
         $this->add(array(
             'type' => 'Zend\Form\Element\Radio',
             'name' => 'status_id',
             'options' => array(
					 'label_attributes' => array(
						'class'  => 'radio-inline'
     				 ),
                     'label' => 'Status',
                     'value_options' => array(
                             '1' => 'Active',
                             '2' => 'Inactive',
                     ),
                     'attributes' => array(
						'value' => '1' //set checked to '1'
					 )
             ),
             
		 ));
		 
		 /* Address  fields - starts here*/
		 $this->add(array(
             'name' => 'street1_address',
             'type' => 'Text',
             'options' => array(
                 'label' => 'Street Address',
             ),
             'attributes' => array(
				'class'=>'form-control input-large'
			 )
         ));
         
         $this->add(array(
             'name' => 'street2_address',
             'type' => 'Text',
             'options' => array(
                 'label' => 'Street Address 2',
             ),
             'attributes' => array(
				'class'=>'form-control input-large'
			 )
         ));
         
         $this->add(array(
             'name' => 'city',
             'type' => 'Text',
             'options' => array(
                 'label' => 'City',
             ),
             'attributes' => array(
				'class'=>'form-control input-large'
			 )
         ));
         
         $this->add(array(
             'name' => 'zip_code',
             'type' => 'Text',
             'options' => array(
                 'label' => 'Zip Code',
             ),
             'attributes' => array(
				'class'=>'form-control input-large'
			 )
         ));
         
         $this->add(array(
             'type' => 'Zend\Form\Element\Select',
             'name' => 'state_id',
             'options' => array(
                     'label' => 'Select State',
                     'value_options' => $this->getStates(),
                     'empty_option'  => '--- Choose State ---'
             ),
             'attributes' => array(
				'class'=>'form-control input-large select2',
				'id' => 'states'
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
				'class'=>'form-control input-large select2 getStates',
				'data-id'=>'states',
			 )
		 ));
		 /* Address  fields - ends here */
                     
         $this->add(array(
             'name' => 'submit',
             'type' => 'Submit',
             'attributes' => array(
                 'value' => 'Go',
                 'id' => 'submitbutton',
             ),
         ));
     }
     
     
    public function getUsersList()
    {
		 $data  = $this->usersList->fetchAll(false);

        $selectData = array();

        foreach ($data as $selectOption) {
            $selectData[$selectOption->id] = ucwords($selectOption->user_name);
        }

        return $selectData; 
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
        //$data  = $this->state->fetchAll(false);
        $data  = $this->state->getStatesByCountry("");

        $selectData = array();

        foreach ($data as $selectOption) {
            $selectData[$selectOption->id] = ucwords($selectOption->state_name);
        }

        return $selectData; 
	}
	 
 }
