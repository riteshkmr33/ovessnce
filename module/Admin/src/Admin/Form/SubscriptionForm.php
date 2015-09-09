<?php
namespace Admin\Form;

 use Zend\Form\Form;
 use Admin\Model\Users;
 use Admin\Model\UsersTable;
 use Admin\Model\Status;
 use Admin\Model\StatusTable;
 use Admin\Model\SubscriptionDurations;
 use Admin\Model\SubscriptionDurationsTable;
 use Zend\Db\Adapter\AdapterInterface;

 class SubscriptionForm extends Form
 {
	 private $users;
	 private $subscription;
	 private $status;
	 
     public function __construct(UsersTable $users, SubscriptionDurationsTable $subscription, StatusTable $status, $payment_methods)
     {
		 $this->users = $users;
		 $this->subscription = $subscription;
		 $this->status = $status;
		 
		 parent::__construct('booking');

         $this->add(array(
             'name' => 'id',
             'type' => 'Hidden',
         ));
         
         $this->add(array(
             'name' => 'invoice_id',
             'type' => 'Hidden',
         ));
         
         $this->add(array(
             'type' => 'Zend\Form\Element\Select',
             'name' => 'user_id',
             'options' => array(
                     'label' => 'Select Consumer',
                     'value_options' => $this->getUsers(),
                     'empty_option'  => '--- Choose User ---'
             ),
             'attributes' => array(
				'class'=>'form-control input-large',
			 )
		 ));
		 
         $this->add(array(
             'type' => 'Zend\Form\Element\Select',
             'name' => 'subscription_duration_id',
             'options' => array(
                     'label' => 'Select Subscription',
                     'value_options' => $this->getSubscriptions(),
                     'empty_option'  => '--- Choose Subscription ---'
             ),
             'attributes' => array(
				'class'=>'form-control input-large subscription',
				'onchange' => 'if(this.value != "") { val = $("select.subscription option:selected").text().split("@"); $("input#invoiceTotal, input#amountPaid").val(val[1])} else { $("input#invoiceTotal").val("")}'
			 )
		 ));
		 
		 
         /*$this->add(array(
             'name' => 'subscription_start_date',
             'type' => 'Text',
             'options' => array(
                 'label' => 'Start Date',
             ),
             'attributes' => array(
				'class'=>'form-control input-large',
				'readonly' => true
			 )
         ));
         
         $this->add(array(
             'name' => 'subscription_end_date',
             'type' => 'Text',
             'options' => array(
                 'label' => 'End Date',
             ),
             'attributes' => array(
				'class'=>'form-control input-large',
				'readonly' => true
			 )
         ));*/
         
         $this->add(array(
             'type' => 'Zend\Form\Element\Select',
             'name' => 'status_id',
             'options' => array(
                     'label' => 'Status',
                     'value_options' => $this->getStatus(array(1,2)),
             ),
             'attributes' => array(
				'class'=>'form-control input-large'
			 )
		 ));
		 
		 
		 /* Payment details */
		$this->add(array(
			'name' => 'payment_method_id',
			'type' => 'Zend\Form\Element\Select',
			'options' => array(
				'label' => 'Payment Method',
				'value_options' => $payment_methods,
				'empty_option'  => '--- Choose Payment Method ---'
			),
			'attributes' => array(
				'class'=>'form-control input-large',
			)
		));
		
		$this->add(array(
			'name' => 'invoice_total',
			'type' => 'Text',
			'options' => array(
				'label' => 'Invoice Total',
			),
			'attributes' => array(
				'class'=>'form-control input-large',
				'id' => 'invoiceTotal',
				'readonly' => 'true',
			)
		));
		
		$this->add(array(
			'name' => 'payment_instrument_no',
			'type' => 'Text',
			'options' => array(
				'label' => 'Instrument Number',
			),
			'attributes' => array(
				'class'=>'form-control input-large',
			)
		));
		
		$this->add(array(
			'name' => 'amount_paid',
			'type' => 'Text',
			'options' => array(
				'label' => 'Amount Paid',
			),
			'attributes' => array(
				'class'=>'form-control input-large',
				'value' => '0.00',
				'id' => 'amountPaid',
				'readonly' => 'true',
			)
		));
	 
		$this->add(array(
			'type' => 'Zend\Form\Element\Select',
			'name' => 'payment_status_id',
			'options' => array(
				'label' => 'Payment Status',
				'value_options' => $this->getStatus(array(7,8)),
				'empty_option'  => '--- Choose Payment Status ---'
			),
			'attributes' => array(
				'class'=>'form-control input-large',
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
     
    public function getSubscriptions()
    {
        $data = $this->subscription->fetchAll("", false);
        $selectData = array();
        
        foreach ($data as $value) {
			$selectData[$value->id] = $value->subscription_name." - ".$value->duration." ".$value->duration_in."@".$value->price;
		}
		
		return $selectData;
	}
	
	public function getStatus(array $options)
    {
        $data  = $this->status->fetchAll(false, $options);
        $selectData = array();
        
        foreach ($data as $value) {
			$selectData[$value->status_id] = ucwords($value->status);
		}
		
		return $selectData;
	}
	
	
	public function getUsers()
    {
        $data  = $this->users->fetchAll(false, array('user_type' => array(3,4,5,6)));

        $selectData = array();

        foreach ($data as $selectOption) {
            $selectData[$selectOption->id] = ucwords($selectOption->first_name." ".$selectOption->last_name);
        }

        return $selectData; 
	}
 }
