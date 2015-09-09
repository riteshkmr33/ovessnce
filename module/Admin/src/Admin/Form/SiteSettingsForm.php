<?php
namespace Admin\Form;

 use Zend\Form\Form;

 class SiteSettingsForm extends Form
 {
     public function __construct($name = null)
     {
         // we want to ignore the name passed
         parent::__construct('sitesettings');

         $this->add(array(
             'name' => 'id',
             'type' => 'Hidden',
         ));
         $this->add(array(
             'name' => 'setting_key',
             'type' => 'Text',
             'options' => array(
                 'label' => 'Setting',
             ),
             'attributes' => array(
				'class'=>'form-control input-large',
				'disabled' => 'true',
			 )
         ));
         
         $this->add(array(
             'name' => 'setting_value',
             'type' => 'Text',
             'options' => array(
                 'label' => 'Value',
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
