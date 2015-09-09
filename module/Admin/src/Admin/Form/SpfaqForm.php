<?php
namespace Admin\Form;

 use Zend\Form\Form;
 use Admin\Model\Status;
 use Admin\Model\StatusTable;
 use Admin\Model\Users;
 use Admin\Model\UsersTable;

 class SpfaqForm extends Form
 {
	private $status;
	private $users;
	 	 
    public function __construct(StatusTable $status,UsersTable $users)
    {
		$this->status = $status;
		$this->users = $users;
		 
        // we want to ignore the name passed
        parent::__construct('sp_faq');

        $this->add(array(
            'name' => 'id', 
            'type' => 'Hidden',
        ));
        
        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'from_user_id',
            'options' => array(
                    'label' => 'From',
                    'value_options' => $this->getUsers(),
                    'empty_option'  => '--- Select User ---'
            ),
            'attributes' => array(
				'class'=>'form-control input-large select2'
			)
		));
		
        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'to_user_id',
            'options' => array(
                    'label' => 'From',
                    'value_options' => $this->getUsers(),
                    'empty_option'  => '--- Select User ---'
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
                    'value_options' => $this->getStatus(),      
                    'empty_option'  => '--- Select Status ---'   
            ),
            'attributes' => array(
				'class'=>'form-control input-large select2'
			)
        ));
                          
        $this->add(array(
            'name' => 'question',
            'type' => 'Zend\Form\Element\Textarea',
            'options' => array(
                'label' => 'Question',
            ),
            'attributes' => array(
				'class' => 'form-control',
			),
        ));
         
        $this->add(array(
            'name' => 'answer',
            'type' => 'Zend\Form\Element\Textarea',
            'options' => array(
                'label' => 'Answer',
            ),
            'attributes' => array(
				'class' => 'form-control',
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
    
    public function getStatus()
    {
        $data  = $this->status->fetchAll(false, array(5,9,10));

        $selectData = array();

        foreach ($data as $selectOption) {
            $selectData[$selectOption->status_id] = ucwords($selectOption->status);
        }

        return $selectData; 
	}
	
    public function getUsers()
    {
        $data  = $this->users->fetchAll(false);

        $selectData = array();

        foreach ($data as $selectOption) {
            $selectData[$selectOption->id] = ucwords($selectOption->user_name);
        }

        return $selectData; 
	}
 }
