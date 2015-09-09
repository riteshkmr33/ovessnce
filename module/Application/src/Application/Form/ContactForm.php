<?php
namespace Application\Form;

 use Zend\Form\Form;
 use Zend\Form\Element;
 
class ContactForm extends Form
{
    public function __construct($name = null)
    {
        parent::__construct('Contact');
        $this->setAttribute('method', 'post');
 
        $this->add(array(
            'name' => 'first_name',
            'attributes' => array(
                'type'  => 'text',
                'autocomplete'  => 'off',
                'id'  => 'contact_first_name',
                'placeholder'  => 'First Name',
                'required'  => 'required',
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
                'id'  => 'contact_last_name',
                'placeholder'  => 'Last Name',
            ),
            'options' => array(
                'label' => 'Last Name',
            ),
        ));
        
        $this->add(array(
            'name' => 'email',
            'type' => 'Zend\Form\Element\Email',
            'attributes' => array(
                'autocomplete'  => 'off',
                'id'  => 'contact_email',
                'placeholder'  => 'Email Address',
                'required' => 'required',
            ),
            'options' => array(
                'label' => 'Email',
            ),
        ));
 
        $this->add(array(
            'name' => 'phone',
            'type' => 'Zend\Form\Element\Text', 
            'attributes' => array(
                'autocomplete'  => 'off',
                'id'  => 'contact_phone',
                'placeholder'  => '000 000 0000',
                'required'  => 'required',
            ),
            'options' => array(
                'label' => 'Phone',
            ),
        ));       
        
        $this->add(array( 
            'name' => 'message', 
            'type' => 'Zend\Form\Element\Textarea', 
            'attributes' => array( 
                'id' => 'contact_message', 
                'required' => 'required',
                'rows' => '4',
                'cols' =>'40',
                'placeholder' => 'Your Message', 
            ),
        ));
                  
        $this->add(array(
            'name' => 'contact_submit',
            'attributes' => array(
                'type'  => 'submit',
                'class'  => 'black',
                'value' => 'SUBMIT QUERY'
            ),
        )); 
    }
}
