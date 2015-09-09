<?php

namespace Admin\Model;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class RatingType implements InputFilterAwareInterface
{
	public $id;
    public $rating_type;
    public $created_by;
    public $status_id;
    
    /* Lookup_status table field */
    public $status;
    
    /* Users table field */
    public $first_name;
    public $last_name;
    
    protected $inputFilter;

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
		$this->rating_type = (!empty($data['rating_type'])) ? $data['rating_type'] : null;
		$this->created_by = (!empty($data['created_by'])) ? $data['created_by'] : null;
		$this->status_id = (!empty($data['status_id'])) ? $data['status_id'] : null;
		
		$this->status = (!empty($data['status'])) ? $data['status'] : null;
		
		$this->first_name = (!empty($data['first_name'])) ? $data['first_name'] : null;
		$this->last_name = (!empty($data['last_name'])) ? $data['last_name'] : null;
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
                'name'     => 'rating_type',
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
                            'max'      => 100,
                        ),
                    ),
                ),
            ));
            
            $inputFilter->add(array(
                'name'     => 'status_id',
                'required' => true,
            ));
            
            $this->inputFilter = $inputFilter;
            
        }
        
        return $this->inputFilter;
	
	}

}
