<?php
namespace Admin\Form;

 use Zend\Form\Form;
 use Admin\Model\Status;
 use Admin\Model\StatusTable;

 class SmsForm extends Form
 {
	 private $status;
	 
     public function __construct(StatusTable $status)
     {
		 $this->status = $status;
		 
         // we want to ignore the name passed
         parent::__construct('sms');

         $this->add(array(
             'name' => 'id',
             'type' => 'Hidden',
         ));
         $this->add(array(
             'name' => 'subject',
             'type' => 'Text',
             'options' => array(
                 'label' => 'Subject',
             ),
             'attributes' => array(
				'class' => 'form-control input-large',
			 ),
         ));
         
         $this->add(array(
             'name' => 'message',
             'type' => 'Zend\Form\Element\Textarea',
             'options' => array(
                 'label' => 'Message',
             ),
             'attributes' => array(
				'class' => 'form-control',
				'onkeyup' => 'limiter()',
			 ),
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
						'value' => '1' 
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
