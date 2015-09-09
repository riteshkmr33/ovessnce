<?php

namespace Admin\Model;

use Zend\InputFilter\Factory as InputFactory;     
use Zend\InputFilter\InputFilter;                 
use Zend\InputFilter\InputFilterAwareInterface;   
use Zend\InputFilter\InputFilterInterface;        

class Sms implements InputFilterAwareInterface
{
	public $id;
    public $subject;
    public $message;
    public $status_id;
    public $created_date;
    public $created_by;
    public $updated_date;
    public $updated_by;
    protected $inputFilter;                       

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
		$this->subject = (!empty($data['subject'])) ? $data['subject'] : null;
		$this->message = (!empty($data['message'])) ? $data['message'] : null;
		$this->status_id = (!empty($data['status_id'])) ? $data['status_id'] : null;
		$this->created_date = (!empty($data['created_date'])) ? $data['created_date'] : null;
		$this->created_by = (!empty($data['created_by'])) ? $data['created_by'] : null;
		$this->updated_date = (!empty($data['updated_date'])) ? $data['updated_date'] : null;
		$this->updated_by = (!empty($data['updated_by'])) ? $data['updated_by'] : null;
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
                'name'     => 'subject',
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
                'name'     => 'message',
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
                            'max'      => 170,
                        ),
                    ),
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
            
            $inputFilter->add(array(
                'name'     => 'created_date',
                'required' => false,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
            ));
            
            $inputFilter->add(array(
                'name'     => 'created_by',
                'required' => false,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
            ));
            
            $inputFilter->add(array(
                'name'     => 'updated_date',
                'required' => false,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
            ));
            
            $inputFilter->add(array(
                'name'     => 'updated_by',
                'required' => false,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
            ));
            
            $this->inputFilter = $inputFilter;
            
        }
        
        return $this->inputFilter;
	
	}

}
		
