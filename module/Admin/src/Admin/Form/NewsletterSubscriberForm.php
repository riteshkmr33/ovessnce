<?php
namespace Admin\Form;

 use Zend\Form\Form;
 use Admin\Model\Status;
 use Admin\Model\StatusTable;
 use Zend\Db\Adapter\AdapterInterface;

 class NewsletterSubscriberForm extends Form
 {
	 private $status;
	 
     public function __construct(StatusTable $status)
     {
		 $this->status = $status;
		 parent::__construct('newslettersubscriber');

         $this->add(array(
             'name' => 'id',
             'type' => 'Hidden',
         ));
         
         $this->add(array(
             'name' => 'email',
             'type' => 'Text',
             'options' => array(
                 'label' => 'Email',
             ),
             'attributes' => array(
				'class'=>'form-control input-large'
			 )
         ));
         
         $this->add(array(
             'type' => 'Zend\Form\Element\Select',
             'name' => 'status_id',
             'options' => array(
                     'label' => 'Status',
                     'value_options' => $this->getStatus(),
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
     
    public function getStatus()
    {
        $data  = $this->status->fetchAll(false, array(1,2,3));

        $selectData = array();

        foreach ($data as $selectOption) {
            $selectData[$selectOption->status_id] = ucwords($selectOption->status);
        }

        return $selectData; 
       
	}
 }
