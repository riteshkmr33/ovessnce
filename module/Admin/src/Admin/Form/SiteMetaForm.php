<?php
namespace Admin\Form;

 use Zend\Form\Form;

 class SiteMetaForm extends Form
 {
	  
     public function __construct()
     {
		 		 
         // we want to ignore the name passed
         parent::__construct('sitemeta');

         $this->add(array(
             'name' => 'id',
             'type' => 'Hidden',
         ));
         
         $this->add(array(
             'name' => 'meta_title',
             'type' => 'Text',
             'options' => array(
                 'label' => 'Meta Title',
             ),
             'attributes' => array(
				'class' => 'form-control input-large',
			 ),
         ));
         
         $this->add(array(
             'name' => 'meta_keyword',
             'type' => 'Zend\Form\Element\Textarea',
             'options' => array(
                 'label' => 'Meta Keywords',
                 'description' => '* Enter comma seperated meta keywords',
             ),
             'attributes' => array(
				'class' => 'form-control',
			 ),
         ));
         
         $this->add(array(
             'name' => 'meta_description',
             'type' => 'Zend\Form\Element\Textarea',
             'options' => array(
                 'label' => 'Meta Description',
             ),
             'attributes' => array(
				'class' => 'form-control',
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
 
 }
