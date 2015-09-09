<?php
namespace Admin\Form;

 use Zend\Form\Form;

 class SiteModulesForm extends Form
 {
     public function __construct($name = null)
     {
         // we want to ignore the name passed
         parent::__construct('sitemodules');

         $this->add(array(
             'name' => 'id',
             'type' => 'Hidden',
         ));
         $this->add(array(
             'name' => 'module_name',
             'type' => 'Text',
             'options' => array(
                 'label' => 'Module Name',
             ),
             'attributes' => array(
				'class' => 'form-control input-large',
			 ),
         ));
         
         $this->add(array(
             'name' => 'frontFlag',
             'type' => 'Zend\Form\Element\Checkbox',
             'options' => array(
                     'label' => 'Front',
                     'use_hidden_element' => true,
                     'checked_value' => '1',
                     'unchecked_value' => '0'
             )
         ));
         
         $this->add(array(
             'name' => 'adminFlag',
             'type' => 'Zend\Form\Element\Checkbox',
             'options' => array(
                     'label' => 'Admin',
                     'use_hidden_element' => true,
                     'checked_value' => '1',
                     'unchecked_value' => '0'
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
