<?php
namespace Admin\Form;

 use Zend\Form\Form;
 use Admin\Model\ServiceProviderServices;
 use Admin\Model\ServiceProviderServicesTable;
 use Admin\Model\Status;
 use Admin\Model\StatusTable;

 class FeedbackFilterForm extends Form
 {
	 private $service;
	 private $status;
	 
     public function __construct(ServiceProviderServicesTable $service, StatusTable $status)
     {
		 $this->service = $service;
		 $this->status = $status;
		 
         
         parent::__construct('feedbackfilter');

         
         $this->add(array(
             'name' => 'provider_name',
             'type' => 'Text',
             'options' => array(
                 'label' => 'Provider Name',
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
				'class' => 'form-control form-filter input-sm',
				'readonly' => true,
				'placeholder' => 'To'
             ),
         ));
         $this->add(array(
             'type' => 'Zend\Form\Element\Select',
             'name' => 'serviceType',
             'options' => array(
                     'label' => 'Select Service',
                     'value_options' => $this->getServices(),
                     'empty_option'  => '--- Choose Service ---'
             ),
             'attributes' => array(
				'class' => 'form-control form-filter input-sm select2',
             ),
		 ));
         $this->add(array(
             'type' => 'Zend\Form\Element\Select',
             'name' => 'status_id',
             'options' => array(
                     'label' => 'Select Status',
                     'value_options' => $this->getStatus(),
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
	
	public function getStatus()
    {
        $data  =  $this->status->fetchAll(false, array(9,5,10));

        $selectData = array();

        foreach ($data as $selectOption) {
            $selectData[$selectOption->status_id] = ucwords($selectOption->status);
        }

        return $selectData; 
	}
	
	public function getServices()
    {
        $data  =  $this->service->fetchAll("",false);

        $selectData = array();

        foreach ($data as $selectOption) {
            $selectData[$selectOption->id] = $selectOption->category_name." - ".$selectOption->duration." mins";
        }

        return $selectData; 
	}
 }
