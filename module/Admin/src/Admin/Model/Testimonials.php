<?php

namespace Admin\Model;

use Zend\InputFilter\Factory as InputFactory;     
use Zend\InputFilter\InputFilter;                 
use Zend\InputFilter\InputFilterAwareInterface;   
use Zend\InputFilter\InputFilterInterface;        

class Testimonials implements InputFilterAwareInterface
{
	public $id;
    public $text;
    public $created_by;
    public $created_on;
    public $status_id;
    public $user_name;
    protected $inputFilter;                       

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
		$this->text = (!empty($data['text'])) ? $data['text'] : null;
		$this->created_by = (!empty($data['created_by'])) ? $data['created_by'] : null;
		$this->created_on = (!empty($data['created_on'])) ? $data['created_on'] : null;
		$this->status_id = (!empty($data['status_id'])) ? $data['status_id'] : null;
		$this->user_name = (!empty($data['user_name'])) ? $data['user_name'] : null;
		
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
                'name'     => 'text',
                'required' => true,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
            ));
            
            $inputFilter->add(array(
				'name'     => 'created_by',
				'required' => true,
				'filters'  => array(
					array('name' => 'StripTags'),
					array('name' => 'StringTrim'),
					
                ),
            ));
            
            $inputFilter->add(array(
				'name'     => 'created_on',
				'required' => false,
				'filters'  => array(
					array('name' => 'StripTags'),
					array('name' => 'StringTrim'),
					
                ),
            ));
            
            $inputFilter->add(array(
                'name'     => 'status_id',
                'validators' => array(
                    array(
                        'name'    => 'InArray',
                        'options' => array(
                            'haystack' => array(1,2),
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
		
