<?php
namespace Admin\Form; 

use Zend\Form\Form;
use Zend\Db\Adapter\AdapterInterface;

class ServiceProviderCommisionForm extends Form
{
	public function __construct()
    {
		parent::__construct('serviceprovidercommision');

        $this->add(array(
            'name' => 'id',
            'type' => 'Hidden',
        ));
         
        $this->add(array(
            'name' => 'user_id',
            'type' => 'Hidden',
        ));
         
        $this->add(array(
            'name' => 'commision',
            'type' => 'Text',
            'options' => array(
                'label' => 'Commission',
            ),
			'attributes' => array(
				'class'=>'form-control input-small'
			)
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
