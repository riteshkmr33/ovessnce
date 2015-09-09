<?php 
namespace Admin\Form;

use Zend\Form\Form;

class TestForm extends Form
{
	public function __construct($name = null){
		 // we want to ignore the name passed
         parent::__construct('test');
			
			 $this->add(array(
             'name' => 'id',
             'type' => 'Hidden',
			));
			
			$this->add(array(
				'name'=>'name',
				'type'=>'text',
				'options'=>array(
							'label'=>'Name',
							),
			));
			$this->add(array(
				'name'=>'contactNumber',
				'type'=>'text',
				'options'=>array(
								'label'=>'Contact Number',
							),
			));
			$this ->add(array(
					'name'=>'status',
					 'type' => 'Radio',
					 'options'=> array(
									'label'=>'Status',
									'label_attributes'=> array(
															'class'=> 'radio-inline'
															),
									'value_options'=> array(
															array('value'=>1, 'label'=>'Active','selected'=>'selected'),
															array('value'=>2, 'label'=>'Inactive'),
														),
									),
					  'attributes' => array(
											'class'=>'fieldToggle',
										) 

					));
			$this->add(array(
					'name'=>'language',
					'type'=>'multi-checkbox',
					'options'=>array(
								'label'=>'Select Language',
								'value_options'=>array(
													array('value'=>'english','label'=>'English'),
													array('value'=>'hindi','label'=>'Hindi'),
													array('value'=>'french','label'=>'French'),
													)
								)
					));
			$this->add(array(
					'name'=>'country',
					'type'=>'select',
					'required'=>true,
					'options'=>array(
								'label'=>'Countries',
								'value_options'=>array(
													array('label'=>'Select','value'=>''),
													array('label'=>'India','value'=>'IN'),
													array('label'=>'U.S.A','value'=>'USA'),
												)
								)
					));
			$this->add(array(
					'name'=>'document',
					'type'=>'File',
					'options'=>array(
								'label'=>'Upload Document',
								
								)
					));
					
			$this->add(array(
				'name'=>'submit',
				'type'=>'submit',
				'attributes'=>array(
								'id'=>'submitForm',
								'value'=>'submit',
								)
			));
		}
		
		
	}
?>
