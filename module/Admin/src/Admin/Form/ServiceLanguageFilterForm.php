<?php
namespace Admin\Form;

 use Zend\Form\Form;
 use Zend\Db\Adapter\AdapterInterface;

 class ServiceLanguageFilterForm extends Form
 {
		 
     public function __construct()
     {
		 	 
		 parent::__construct('ServiceLanguageFilterForm');

         
		 $this->add(array(
             'name' => 'language_name',
             'type' => 'Text',
             'options' => array(
                 'label' => 'Language Name',
             ),
             'attributes' => array(
				'class'=>'form-control input-sm'
			 )
         ));
         
         $this->add(array(
             'type' => 'Zend\Form\Element\Select',
             'name' => 'status_id',
             'options' => array(
                     'label' => 'Select Status',
                     'value_options' => array('1' => 'Active', '2' => 'Inactive'),
                     'empty_option'  => '--- Choose Status ---'
             ),
             'attributes' => array(
				'class' => 'form-control form-filter input-sm',
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
    
 }
