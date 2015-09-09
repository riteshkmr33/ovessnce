<?php
namespace Admin\Form;

 use Zend\Form\Form;
 use Admin\Model\Users;
 use Admin\Model\UsersTable;
 use Admin\Model\Status;
 use Admin\Model\StatusTable;
 use Zend\Db\Adapter\AdapterInterface;

 class TestimonialsForm extends Form
 {
	 private $usersList;
	 private $status;
	 
     public function __construct(UsersTable $usersList, StatusTable $status)
     {
		 
		 $this->usersList = $usersList;
		 $this->status = $status;
		 
         // we want to ignore the name passed
         parent::__construct('testimonials');

         $this->add(array(
             'name' => 'id',
             'type' => 'Hidden',
         ));
         
         $this->add(array(
             'name' => 'text',
             'type' => 'Zend\Form\Element\Textarea',
             'options' => array(
                 'label' => 'Testimonial Text',
             ),
             'attributes' => array(
				'class'=>'form-control'
			 )
         ));
         
         $this->add(array(
             'type' => 'Zend\Form\Element\Select',
             'name' => 'created_by',
             'options' => array(
                     'label' => 'Created By',
                     'value_options' => $this->getUsersList(),
                     'empty_option'  => '--- Select User ---'
             ),
             'attributes' => array(
				'class'=>'form-control'
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
                     'value_options' => $this->getStatus(),
                     'attributes' => array(
						'value' => '1' //set checked to '1'
					 )
             ),             
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
     
    public function getUsersList()
    {
		 $data  = $this->usersList->fetchAll(false);

        $selectData = array();

        foreach ($data as $selectOption) {
            $selectData[$selectOption->id] = ucwords($selectOption->user_name);
        }

        return $selectData; 
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
