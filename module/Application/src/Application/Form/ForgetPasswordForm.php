<?php
namespace Application\Form;

 use Zend\Form\Form;
 
class ForgetPasswordForm extends Form
{
    public function __construct($name = null)
    {
        parent::__construct('ForgetPassword');
        $this->setAttribute('method', 'post');
        //$this->setAttribute('enctype','multipart/form-data');
        
        $this->add(array(
            'name' => 'email',
            'attributes' => array(
                'type'  => 'text',
                'autocomplete'  => 'off',
                'name'  => 'email',
                'id'  => 'register_email',
                'placeholder'  => 'Email Address',
            ),
            'options' => array(
                'label' => 'Email',
            ),
        ));
 
        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type'  => 'submit',
                'class'  => 'black',
                'value' => 'Submit'
            ),
        )); 
    }
}