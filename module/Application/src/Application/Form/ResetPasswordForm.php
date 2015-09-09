<?php
namespace Application\Form;

 use Zend\Form\Form;
 
class ResetPasswordForm extends Form
{
    public function __construct($name = null)
    {
        parent::__construct('ResetPassword');
        $this->setAttribute('method', 'post');
        //$this->setAttribute('enctype','multipart/form-data');
         
        $this->add(array(
            'name' => 'resettoken',
            'attributes' => array(
                //'type'  => 'hidden',
                'type'  => 'text',
                'autocomplete'  => 'off',
                'name'  => 'resettoken',
                'id'  => 'resettoken',
            ),
            'options' => array(
                'label' => 'Reset token',
            ),
        )); 
        
        $this->add(array(
            'name' => 'password',
            'attributes' => array(
                'type'  => 'password',
                'autocomplete'  => 'off',
                'name'  => 'password',
                'id'  => 'password',
                'placeholder'  => 'Password',
            ),
            'options' => array(
                'label' => 'Password',
            ),
        )); 
        
        $this->add(array(
            'name' => 'repassword',
            'attributes' => array(
                'type'  => 'password',
                'autocomplete'  => 'off',
                'name'  => 'repassword',
                'id'  => 'repassword',
                'placeholder'  => 'Confirm Password',
            ),
            'options' => array(
                'label' => 'Confirm Password',
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