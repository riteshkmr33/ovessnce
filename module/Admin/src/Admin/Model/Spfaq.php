<?php

namespace Admin\Model;

use Zend\InputFilter\Factory as InputFactory;     // <-- Add this import
use Zend\InputFilter\InputFilter;                 // <-- Add this import
use Zend\InputFilter\InputFilterAwareInterface;   // <-- Add this import
use Zend\InputFilter\InputFilterInterface;        // <-- Add this import

class Spfaq implements InputFilterAwareInterface
{
	public $id;
    public $from_user_id;
    public $to_user_id;
    public $question;
    public $answer;
    public $answered_by_id;
    public $asked_on;
    public $answered_on;
    public $from_user_name;
    public $to_user_name;
    public $answered_by_user;
    public $status;
    public $status_id;
    protected $inputFilter;                       // <-- Add this variable

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
		$this->from_user_id = (!empty($data['from_user_id'])) ? $data['from_user_id'] : null;
		$this->to_user_id = (!empty($data['to_user_id'])) ? $data['to_user_id'] : null;
		$this->question = (!empty($data['question'])) ? $data['question'] : null;
		$this->answer = (!empty($data['answer'])) ? $data['answer'] : null;
		$this->answered_by_id = (!empty($data['answered_by_id'])) ? $data['answered_by_id'] : null;
		$this->asked_on = (!empty($data['asked_on'])) ? $data['asked_on'] : null;
		$this->answered_on = (!empty($data['answered_on'])) ? $data['answered_on'] : null;
		$this->from_user_name = (!empty($data['from_user_name'])) ? $data['from_user_name'] : null;
		$this->to_user_name = (!empty($data['to_user_name'])) ? $data['to_user_name'] : null;
		$this->answered_by_user = (!empty($data['answered_by_user'])) ? $data['answered_by_user'] : null;
		$this->status = (!empty($data['status'])) ? $data['status'] : null;
		$this->status_id = (!empty($data['status_id'])) ? $data['status_id'] : null;
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
                'name' => 'from_user_id',
                'required' => true,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
            )); 
            
            $inputFilter->add(array(
                'name' => 'to_user_id',
                'required' => true,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
            )); 
            
            $inputFilter->add(array(
				'name'     => 'question',
				'required' => true,
            )); 
            
            $inputFilter->add(array(
				'name'     => 'answer',
				'required' => false,
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
		
