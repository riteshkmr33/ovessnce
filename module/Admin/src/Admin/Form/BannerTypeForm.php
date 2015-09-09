<?php
namespace Admin\Form;

 use Zend\Form\Form;

 class BannerTypeForm extends Form
 {
     public function __construct($name = null)
     {
         // we want to ignore the name passed
         parent::__construct('activity');

         $this->add(array(
             'name' => 'id',
             'type' => 'Hidden',
         ));
         $this->add(array(
             'name' => 'banner_type_name',
             'type' => 'Text',
             'options' => array(
                 'label' => 'Banner Type',
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
