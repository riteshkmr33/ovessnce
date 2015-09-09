<?php

namespace Admin\Model;

use Zend\InputFilter\Factory as InputFactory;     
use Zend\InputFilter\InputFilter;                 
use Zend\InputFilter\InputFilterAwareInterface;   
use Zend\InputFilter\InputFilterInterface;        

class PractitionerOrganizations implements InputFilterAwareInterface
{
	public $organization_id;
    public $organization_name;
    public $logo;
    public $phone_no;
    public $email;
    public $status_id;
    
    /* user_address table fields */
    public $street1_address;
	public $street2_address;
	public $city;
	public $zip_code;
	public $state_id;
	public $country_id;
	
	/* state table field */
    public $state_name;
    
    /* country table field */
    public $country_name;
	
    protected $inputFilter;                       

    public function exchangeArray($data)
    {
        $this->organization_id = (!empty($data['organization_id'])) ? $data['organization_id'] : null;
		$this->organization_name = (!empty($data['organization_name'])) ? $data['organization_name'] : null;
		$this->logo = (!empty($data['logo'])) ? $data['logo'] : null;
		$this->phone_no = (!empty($data['phone_no'])) ? $data['phone_no'] : null;
		$this->email = (!empty($data['email'])) ? $data['email'] : null;
		$this->status_id = (!empty($data['status_id'])) ? $data['status_id'] : null;
		
		$this->street1_address = (!empty($data['street1_address'])) ? $data['street1_address'] : null;
		$this->street2_address = (!empty($data['street2_address'])) ? $data['street2_address'] : null;
		$this->city = (!empty($data['city'])) ? $data['city'] : null;
		$this->zip_code = (!empty($data['zip_code'])) ? $data['zip_code'] : null;
		$this->state_id = (!empty($data['state_id'])) ? $data['state_id'] : null;
		$this->country_id = (!empty($data['country_id'])) ? $data['country_id'] : null;
		
		$this->state_name = (!empty($data['state_name'])) ? $data['state_name'] : null;
		$this->country_name = (!empty($data['country_name'])) ? $data['country_name'] : null;
		
		
    }
    
    public function getArrayCopy()
    {
        return get_object_vars($this);
    }

    // Add content to this method:
    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }
    
    public function getInputFilter()
    {
		
		if (!$this->inputFilter) {
			
            $inputFilter = new InputFilter();
			
			$inputFilter->add(array(
                'name'     => 'organization_name',
                'required' => true,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name'    => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min'      => 1,
                            'max'      => 150,
                        ),
                    ),
                ),
            )); 	
            
            
			$inputFilter->add(array(
                'name'     => 'logo',
                'required' => false,
            )); 	
            
            $inputFilter->add(array(
                'name'     => 'phone_no',
                'required' => true,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    
                    array(
                        'name'    => 'Digits',
                    ),
                ),
            ));
            
            $inputFilter->add(array(
                'name' => 'email',
                'validators' => array(
                    array(
                        'name' => 'EmailAddress',
                    ),
                ),
            )); 
            
			$inputFilter->add(array(
                'name'     => 'status_id',
                'validators' => array(
                    array(
                        'name'    => 'InArray',
                        'options' => array(
                            'haystack' => array(1,2,3),
                            'messages' => array(
                                'notInArray' => 'Please select status !' 
                            ),
                        ),
                    ),
                ),
            ));	
            
            $this->inputFilter = $inputFilter;
            
        }
        
        return $this->inputFilter;
	
	}

}
		
