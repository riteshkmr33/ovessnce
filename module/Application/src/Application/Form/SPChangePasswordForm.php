<?php
namespace Application\Form;

 use Zend\Form\Form;
 
class SPChangePasswordForm extends Form
{
    public function __construct($name = null)
    {
        parent::__construct('spchangepassword');
        $this->setAttribute('method', 'post');
         
 
        $this->add(array(
            'name' => 'old_pass',
            'attributes' => array(
                'required'  => true,
                'type'  => 'password',
                'autocomplete'  => 'off',
                'name'  => 'old_pass',
                'id'  => 'old_pass',
                'placeholder'  => 'Old Password',
            ),
            'options' => array(
                'label' => 'ENTER OLD PASSWORD',
            ),
        )); 
        
        $this->add(array(
            'name' => 'Pass',
            'attributes' => array(
				'required'  => true,
                'type'  => 'password',
                'autocomplete'  => 'off',
                'name'  => 'Pass',
                'id'  => 'Pass',
                'placeholder'  => 'Password',
            ),
            'options' => array(
                'label' => 'NEW PASSWORD',
            ),
        )); 
        
        $this->add(array(
            'name' => 'confirm_password',
            'attributes' => array(
				'required'  => true,
                'type'  => 'password',
                'autocomplete'  => 'off',
                'name'  => 'confirm_password',
                'id'  => 'confirm_password',
                'placeholder'  => 'Confirm Password',
            ),
            'options' => array(
                'label' => 'CONFIRM PASSWORD',
            ),
            'validators' => array(
				array(
					'name' => 'Identical',
					'options' => array(
						'token' => 'Pass', // name of first password field
					),
				),
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
