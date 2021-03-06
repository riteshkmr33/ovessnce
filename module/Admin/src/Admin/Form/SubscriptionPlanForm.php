<?php
namespace Admin\Form;

 use Zend\Form\Form;
 use Admin\Model\Status;
 use Admin\Model\StatusTable;

 class SubscriptionPlanForm extends Form
 {
	 private $status;
	 
     public function __construct(StatusTable $status)
     {
		 $this->status = $status;
		 
         parent::__construct('subscriptionplan');

         $this->add(array(
             'name' => 'id',
             'type' => 'Hidden',
         ));
         
         $this->add(array(
             'name' => 'subscription_name',
             'type' => 'Text',
             'options' => array(
                 'label' => 'Subscription Name',
             ),
             'attributes' => array(
				'class'=>'form-control input-large'
			 )
         ));
         
         $this->add(array(
             'name' => 'limit',
             'type' => 'Zend\Form\Element\Select',
             'options' => array(
                 'label' => 'Feature video upload limit',
                 'value_options' => array(1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5, 6 => 6, 7 => 7, 8 => 8, 9 => 9, 10 => 10),
                 'empty_option'  => '0'
             ),
             'attributes' => array(
				'class'=>'input-small select2',
				'style'=>'margin-left:20px'
			 )
         ));
         
		 $this->add(array(
             'type' => 'Zend\Form\Element\Select',
             'name' => 'status_id',
             'options' => array(
                     'label' => 'Status',
                     'value_options' => $this->getStatus(),
                     'empty_option'  => '--- Choose Status ---'
             ),
             'attributes' => array(
				'class'=>'form-control input-large select2'
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
