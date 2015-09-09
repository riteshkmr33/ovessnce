<?php
namespace Application\Form;

 use Zend\Form\Form;
 
class RegisterForm extends Form
{
    public function __construct($name = null)
    {
        parent::__construct('Login');
        $this->setAttribute('method', 'post');
        //$this->setAttribute('enctype','multipart/form-data');
         
        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'user_type_id',
            'attributes' => array(
                'type'  => 'select',
                'name'  => 'user_type_id',
                'id'  => 'register_user_type_id',
                'options' => array(
                    '4' => 'Consumer',
                    '3' => 'Practitioner',
                ),
            ),
            'options' => array(
                'label' => 'I am a',
            ),
        ));
 
        $this->add(array(
            'name' => 'user_name',
            'attributes' => array(
                'type'  => 'text',
                'autocomplete'  => 'off',
                'name'  => 'user_name',
                'id'  => 'register_user_name',
                'placeholder'  => 'Username',
                'required' =>'required',
            ),
            'options' => array(
                'label' => 'Username',
            ),
        ));
 
        $this->add(array(
            'name' => 'first_name',
            'attributes' => array(
                'type'  => 'text',
                'autocomplete'  => 'off',
                'name'  => 'first_name',
                'id'  => 'register_first_name',
                'placeholder'  => 'First Name',
                'required' =>'required',
            ),
            'options' => array(
                'label' => 'First Name',
            ),
        ));
 
        $this->add(array(
            'name' => 'last_name',
            'attributes' => array(
                'type'  => 'text',
                'autocomplete'  => 'off',
                'name'  => 'last_name',
                'id'  => 'register_last_name',
                'placeholder'  => 'Last Name',
                'required' =>'required',
            ),
            'options' => array(
                'label' => 'Last Name',
            ),
        ));
 
        $this->add(array(
            'name' => 'email',
            'attributes' => array(
                'type'  => 'text',
                'autocomplete'  => 'off',
                'name'  => 'email',
                'id'  => 'register_email',
                'placeholder'  => 'Email Address',
                'required' =>'required',
            ),
            'options' => array(
                'label' => 'Email',
            ),
        ));
        
        $this->add(array(
            'name' => 'confirm_email',
            'attributes' => array(
                'type'  => 'text',
                'autocomplete'  => 'off',
                'name'  => 'confirm_email',
                'id'  => 'confirm_email',
                'placeholder'  => 'Confirm Email Address',
                'required' =>'required',
            ),
            'options' => array(
                'label' => 'Confirm Email',
            ),
        ));
 
        $this->add(array(
            'name' => 'Pass',
            'attributes' => array(
                'type'  => 'password',
                'autocomplete'  => 'off',
                'name'  => 'Pass',
                'id'  => 'register_password',
                'placeholder'  => 'Password',
                'required' =>'required',
            ),
            'options' => array(
                'label' => 'Password',
            ),
        )); 
        
        $this->add(array(
            'name' => 'confirm_password',
            'attributes' => array(
                'type'  => 'password',
                'autocomplete'  => 'off',
                'name'  => 'confirm_password',
                'id'  => 'register_confirm_password',
                'placeholder'  => 'Confirm Password',
                'required' =>'required',
            ),
            'options' => array(
                'label' => 'Confirm Password',
            ),
        ));        
         
        $this->add(array(
            'name' => 'register_submit',
            'attributes' => array(
                'type'  => 'submit',
                'class'  => 'black',
                'value' => 'Register'
            ),
        )); 
    }
}
