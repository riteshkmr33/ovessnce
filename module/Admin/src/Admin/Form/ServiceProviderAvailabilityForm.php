<?php
namespace Admin\Form;

 use Zend\Form\Form;
 use Zend\Db\Adapter\AdapterInterface;
  
class ServiceProviderAvailabilityForm extends Form
{

    public function __construct()
    {
        // we want to ignore the name passed
        parent::__construct('serviceprovideravailability');
         
        $this->setAttribute('method', 'post');
        $this->setAttribute('enctype','multipart/form-data');
        
        $this->add(array(
            'name' => 'user_id',
            'type' => 'Hidden',
        ));
         
        $this->add(array(
			'type' => 'text',
			'name' => 'delay_time',
			'options' => array(
				'label' => 'Delay Between Appointments',
			),
			'attributes' => array(
				'class' => 'form-control input-small',
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
    
    function getStatus()
    {
		$results = $this->status->fetchAll(false, array(9,5,10));
		
		$selectData = array();
		
		foreach ($results as $result) {
			$selectData[$result->status_id] = ucwords($result->status);
		}
		
		return $selectData;
	}
 }
