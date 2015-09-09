<?php
namespace Admin\Form;

 use Zend\Form\Form;

 class PageLocationForm extends Form
 {
     public function __construct($name = null)
     {
         // we want to ignore the name passed
         parent::__construct('page_location');

         $this->add(array(
             'name' => 'id',
             'type' => 'Hidden',
         ));
         $this->add(array(
             'name' => 'location_name',
             'type' => 'Text',
             'options' => array(
                 'label' => 'Location Name',
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
 }
