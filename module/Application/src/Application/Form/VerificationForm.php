<?php 
namespace Application\Form;

use Zend\Form\Form;

class VerificationForm extends Form
{
	public function __construct() 
    {
        parent::__construct();
        $this->setAttribute('method', 'post');
		
		$this ->add(array(
					'name'=>'verification_type',
					 'type' => 'Radio',
					 'options'=> array( 
					 'label'=>'Select verification Type',
					 'value_options'=> array(
							array('value'=>'1','label'=>'Email','checked'=>'checked', 'attributes' => array(
						'class' => 'verification_type',
					 )),
							array('value'=>'2','label'=>'SMS', 'attributes' => array(
						'class' => 'verification_type',
					 )),
						),
					 ),
					
					 
					));
		
		$this->add(array(
				'type'=>'text',
				'name'=>'mobile_no',
				'options'=>array(
							'label'=>'Mobile No.',
							),
				'attributes'=>array(
								'id'=>'mobile_no',
								),
				));
				
		$this->add(array(
				'type'=>'text',
				'name'=>'emailid',
				'options'=>array(
							'label'=>'Email id',
							),
				'attributes'=>array(
								'id'=>'emailid',
								'readonly'=>true,
								
								),
				));
		$this->add(array(
				'type'=>'text',
				'name'=>'verifycodeval',
				'options'=>array(
							'label'=>'verify code',
							),
				'attributes'=>array(
								'id'=>'verifycodeval',
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
?>
