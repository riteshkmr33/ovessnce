<?php
namespace Admin\Form;
 
 use Zend\Form\Form;
 use Admin\Model\States;
 use Admin\Model\StatesTable;
 use Admin\Model\Countries;
 use Admin\Model\CountriesTable;
 use Admin\Model\Usertype;
 use Admin\Model\UsertypeTable;
 use Admin\Model\Status;
 use Admin\Model\StatusTable;
 use Admin\Model\ServiceLanguages;
 use Admin\Model\ServiceLanguagesTable;
 use Zend\Db\Adapter\AdapterInterface;

 
 class UsersForm extends Form 
 {
	 
	 protected $selectTable;
	 private $country;
	 private $country_id;
	 private $state;
	 private $status;
	 private $servicelanguage;
     
     public function __construct(UsertypeTable $selectTable,StatesTable $state, CountriesTable $country, StatusTable $status, ServiceLanguagesTable $servicelanguage, $languages = array(), $country_id = "")
     {
		 
		 $this->setSelectTable($selectTable);
		 $this->state = $state;
		 $this->country = $country;
		 $this->country_id = $country_id;
		 $this->status = $status;
		 $this->servicelanguage = $servicelanguage;
		 $this->languages = $languages;
		 
         // we want to ignore the name passed
         parent::__construct('users');

         $this->add(array(
             'name' => 'id',
             'type' => 'Hidden',
         ));
         
         $this->add(array(
             'name' => 'first_name',
             'type' => 'Text',
             'options' => array(
                 'label' => 'First Name',
             ),
             'attributes' => array(
				'class'=>'form-control input-large'
			 )
         ));
         
         $this->add(array(
             'name' => 'last_name',
             'type' => 'Text',
             'options' => array(
                 'label' => 'Last Name',
             ),
             'attributes' => array(
				'class'=>'form-control input-large'
			 )
         ));
         
         $this->add(array(
             'name' => 'user_name',
             'type' => 'Text',
             'options' => array(
                 'label' => 'Username',
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
                'class'=>'form-control input-large',
			 )
         ));
         
         $this->add(array(
             'type' => 'Zend\Form\Element\Select',
             'name' => 'register_from',
             'options' => array(
                     'label' => 'Registered From',
                     'value_options' => array(
						'0' => 'Website',
                     ),
                     'empty_option'  => '--- Choose Type ---'
             ),
             'attributes' => array(
				'class'=>'form-control input-large select2'
			 )
		 ));
		 
		 $this->add(array(
             'type' => 'Zend\Form\Element\Select',
             'name' => 'service_language_id',
             'options' => array(
                     'label' => 'Language Spoken',
                     'value_options' => $this->getLanguages(),
             ),
             'attributes' => array(
				'class'=>'form-control input-large select2',
				'multiple' => 'multiple',
			 )
         ));
         
         $this->add(array(
             'type' => 'Zend\Form\Element\Select',
             'name' => 'user_type_id',
             'options' => array(
                     'label' => 'Select User Type',
                     'value_options' => $this->getUsertypes(),
                     'empty_option'  => '--- Choose Type ---'
             ),
             'attributes' => array(
				'class'=>'form-control input-large select2'
			 )
		 ));
		 
		 $this->add(array(
             'type' => 'Zend\Form\Element\Radio',
             'name' => 'chat',
             'options' => array(
                     'label' => 'Chat',
                      'label_attributes' => array(
						'class'  => 'radio-inline'
					 ),
                     'value_options' => array(1 => 'Enabled', 0 => 'Disabled'),
                     'attributes' => array(
						'value' => '1' //set checked to '1'
					 )
             ),
		 ));
		 
		 $this->add(array(
             'type' => 'Zend\Form\Element\Radio',
             'name' => 'sms',
             'options' => array(
                     'label' => 'Sms',
                      'label_attributes' => array(
						'class'  => 'radio-inline'
					 ),
                     'value_options' => array(1 => 'Enabled', 0 => 'Disabled'),
                     'attributes' => array(
						'value' => '1' //set checked to '1'
					 )
             ),
		 ));
		 
		 $this->add(array(
             'type' => 'Zend\Form\Element\Radio',
             'name' => 'email_status',
             'options' => array(
                     'label' => 'Email',
                      'label_attributes' => array(
						'class'  => 'radio-inline'
					 ),
                     'value_options' => array(1 => 'Enabled', 0 => 'Disabled'),
                     'attributes' => array(
						'value' => '1' //set checked to '1'
					 )
             ),
		 ));
         
         $this->add(array(
             'type' => 'Zend\Form\Element\Select',
             'name' => 'status_id',
             'options' => array(
                     'label' => 'Status',
                     'value_options' => $this->getStatus(),
                     
             ),
             'attributes' => array(
				'class'=>'form-control input-large select2'
			)
             
		 ));
		 
		  $this->add(array(
             'name' => 'age',
             'type' => 'Text',
             'options' => array(
                 'label' => 'Age',
             ),
             'attributes' => array(
				'class'=>'form-control input-large',
				'autocomplete'=> false,
			 )
         ));
         
         $this->add(array(
             'type' => 'Zend\Form\Element\Select',
             'name' => 'gender',
             'options' => array(
                     'label' => 'Select Gender',
                     'value_options' => array('M' => 'Male', 'F' => 'Female'),
             ),
             'attributes' => array(
				'class'=>'form-control input-large select2'
			 )
         ));
		 
		  $this->add(array(
             'name' => 'pass',
             'type' => 'Password',
             'options' => array(
                 'label' => 'Password',
              ),
              'attributes' => array(
				'class'=>'form-control input-large'
			 )
			));
         
		  $this->add(array(
             'name' => 'c_pass',
             'type' => 'Password',
             'options' => array(
                 'label' => 'Confirm Password',
             ),
             'attributes' => array(
				'class'=>'form-control input-large'
			 ),
             'validators' => array(
				array(
					'name' => 'Identical',
					'options' => array(
						'token' => 'password', // name of first password field
					),
				),
			),
         ));
         
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
				'id'=>'states',
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
				'data-id'=>'states'
			 )
		 ));
		 
		 $this->add(array(
             'name' => 'home_phone',
             'type' => 'Text',
             'options' => array(
                 'label' => 'Home Phone',
             ),
             'attributes' => array(
				'class'=>'form-control input-large'
			 )
         ));
         
		 $this->add(array(
             'name' => 'work_phone',
             'type' => 'Text',
             'options' => array(
                 'label' => 'Work Phone',
             ),
             'attributes' => array(
				'class'=>'form-control input-large'
			 )
         ));
         
		 $this->add(array(
             'name' => 'cell_phone',
             'type' => 'Text',
             'options' => array(
                 'label' => 'Cell Phone',
             ),
             'attributes' => array(
				'class'=>'form-control input-large'
			 )
         ));
         
		 $this->add(array(
             'name' => 'fax',
             'type' => 'Text',
             'options' => array(
                 'label' => 'Fax',
             ),
             'attributes' => array(
				'class'=>'form-control input-large'
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
     
    public function getUsertypes()
    {
        
        $table = $this->getSelectTable();
        $data  = $table->fetchAll(false);

        $selectData = array();

        foreach ($data as $selectOption) {
            $selectData[$selectOption->id] = ucwords($selectOption->user_type);
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

	public function setSelectTable($selectTable)
    {
        $this->selectTable = $selectTable;

        return $this;
    }
    
    public function getSelectTable()
    {
        return $this->selectTable;
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
        //$data  = $this->state->getStatesByCountry($this->country_id);

        $selectData = array();

        foreach ($data as $selectOption) {
            $selectData[$selectOption->id] = ucwords($selectOption->state_name);
        }

        return $selectData; 
	}
    
    public function getLanguages()
    {
        $data = $this->servicelanguage->fetchAll(false);
		$selectData = array();
		
        foreach ($data as $selectOption) {
            $selectData[] = array_key_exists($selectOption->id,$this->languages)?array('value' => $selectOption->id, 'label' => ucwords($selectOption->language_name), 'selected' => 'selected'):array('value' => $selectOption->id, 'label' => ucwords($selectOption->language_name));
        }

        return $selectData; 
	}
	
 }
