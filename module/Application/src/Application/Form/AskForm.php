<?php
namespace Application\Form;

 use Zend\Captcha;
 use Zend\Form\Form;
  
class AskForm extends Form
{
    public function __construct() 
    {
        parent::__construct();
        $this->setAttribute('method', 'post');
       		 
		$this->add(array(
            'name' => 'question',
            'type' => 'Zend\Form\Element\Textarea',
            'options' => array(
                'label' => 'question',
            ),
            'attributes' => array(
				'required' => 'required',
				'placeholder' => 'Ask your question',
			),
        ));
        
        $this->add(array(
			'type' => 'Zend\Form\Element\Captcha',
			'name' => 'captcha',
			'options' => array(
				'label' => 'Please verify you are human',
				'captcha' => new Captcha\Dumb(),
			),
		));
         
		$this->add(array(
            'name' => 'submit',
            'type' => 'Submit',
            'attributes' => array(
                'value' => 'submit',
                'id' => 'submitbutton',
             ),
        ));
    }
}
