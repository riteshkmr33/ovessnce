<?php
namespace Application\Form;

 use Zend\Form\Form;
 
class LoginForm extends Form
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
                'id'  => 'user_type_id',
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
                'id'  => 'user_name',
                'placeholder'  => 'Username',
                'required' =>'required',
            ),
            'options' => array(
                'label' => 'I am a',
            ),
        ));
 
        $this->add(array(
            'name' => 'Pass',
            'attributes' => array(
                'type'  => 'password',
                'autocomplete'  => 'off',
                'name'  => 'Pass',
                'id'  => 'Pass',
                'placeholder'  => 'Password',
                'required' =>'required',
            ),
            'options' => array(
                'label' => 'Password',
            ),
        ));
 
        $this->add(array(
            'type' => 'Zend\Form\Element\Checkbox',
            'name' => 'rememberme',
            'attributes' => array(
                'type'  => 'checkbox',
                'name'  => 'rememberme',
                'id'  => 'remember-me',
            ),
            'options' => array(
                'label' => 'Remember me',
                'checked_value' => 'yes',
                'unchecked_value' => 'no',
            ),
            
        )); 
         
        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type'  => 'submit',
                'class'  => 'black',
                'value' => 'Login'
            ),
        )); 
    }
}
