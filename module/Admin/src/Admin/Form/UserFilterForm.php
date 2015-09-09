<?php
namespace Admin\Form;

 use Zend\Form\Form;
 use Admin\Model\Usertype;
 use Admin\Model\UsertypeTable;
 use Admin\Model\Status;
 use Admin\Model\StatusTable;

 class UserFilterForm extends Form
 {
	 private $usertype;
	 private $status;
	 	 
     public function __construct(UsertypeTable $usertype, StatusTable $status)
     {
		 $this->usertype = $usertype;
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
             'type' => 'Zend\Form\Element\Select',
             'name' => 'user_type_id',
             'options' => array(
                     'label' => 'User Type',
                     'value_options' => $this->getUsertypes(),
                     'empty_option'  => 'Select'
             ),
             'attributes' => array(
				'class' => 'form-control form-filter input-sm select2',
             ),
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
			'name' => 'from_login',
			'type' => 'Text',
			'options' => array(
				'label' => 'From',
			),
			'attributes' => array(
				'id' => 'from_login',
				'class' => 'form-control form-filter input-sm',
				'readonly' => true,
				'placeholder' => 'From'
			),
		));
		
		$this->add(array(
			'name' => 'to_login',
			'type' => 'Text',
			'options' => array(
				'label' => 'To',
			),
			'attributes' => array(
				'id' => 'to_login',
			),
			'attributes' => array(
				'id' => 'to_login',
				'class' => 'form-control form-filter input-sm',
				'readonly' => true,
				'placeholder' => 'To'
			),
		));
		
		$this->add(array(
             'type' => 'Zend\Form\Element\Select',
             'name' => 'chat',
             'options' => array(
                     'label' => 'Chat',
                     'value_options' => array(1 => 'Enabled', 0 => 'Disabled'),
                     'empty_option'  => 'Chat Status',
                     'attributes' => array(
						'value' => '1' //set checked to '1'
					 )
             ),
             'attributes' => array(
             'class' => 'form-control form-filter input-sm select2',
             )
		 ));
		 
		 $this->add(array(
             'type' => 'Zend\Form\Element\Select',
             'name' => 'sms',
             'options' => array(
                     'label' => 'Sms',
                     'value_options' => array(1 => 'Enabled', 0 => 'Disabled'),
                     'empty_option'  => 'Sms Status',
                     'attributes' => array(
						'value' => '1' //set checked to '1'
					 )
             ),
             'attributes' => array(
             'class' => 'form-control form-filter input-sm select2',
             )
		 ));
		 
		 $this->add(array(
             'type' => 'Zend\Form\Element\Select',
             'name' => 'email_status',
             'options' => array(
                     'label' => 'Email',
                     'value_options' => array(1 => 'Enabled', 0 => 'Disabled'),
                     'empty_option'  => 'Email Status',
                     'attributes' => array(
						'value' => '1' //set checked to '1'
					 )
             ),
             'attributes' => array(
             'class' => 'form-control form-filter input-sm select2',
             )
		 ));
         
         
         $this->add(array(
             'type' => 'Zend\Form\Element\Select',
             'name' => 'status_id',
             'options' => array(
                     'label' => 'Select Status',
                     'value_options' => $this->getStatus(),
                     'empty_option'  => 'Choose Status'
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
     
    public function getUsertypes()
    {
        $data  = $this->usertype->fetchAll(false);

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
	
 }
