<?php
namespace Admin\Form;

 use Zend\Form\Form;

 class ViewMessageForm extends Form
 {
     public function __construct($name = null)
     {
         // we want to ignore the name passed
         parent::__construct('viewmessage');

         $this->add(array(
             'name' => 'id',
             'type' => 'Hidden',
         ));
         
         $this->add(array(
             'name' => 'toUserID',
             'type' => 'Hidden',
         ));
         
         $this->add(array(
             'name' => 'subject',
             'type' => 'Hidden',
         ));
         
         $this->add(array(
             'name' => 'topLevel_id',
             'type' => 'Hidden',
         ));
         
         $this->add(array(
             'name' => 'ReplyMessage',
             'type' => 'Zend\Form\Element\Textarea',
             'options' => array(
                 'label' => 'Reply Message ',
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
