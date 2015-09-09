<?php

namespace Admin\Model;

use Zend\InputFilter\Factory as InputFactory;     // <-- Add this import
use Zend\InputFilter\InputFilter;                 // <-- Add this import
use Zend\InputFilter\InputFilterAwareInterface;   // <-- Add this import
use Zend\InputFilter\InputFilterInterface;        // <-- Add this import

class Messages implements InputFilterAwareInterface
{
	public $id;
    public $from_user_id;
    public $from_name;
    public $to_user_id;
    public $subject;
    public $message;
    public $replyId;
    public $topLevel_id;
    public $readFlag;
    public $deleteFlag;
    public $created_date;
    protected $inputFilter;                       // <-- Add this variable
    
    public $from_user;
    public $to_user;

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
		$this->from_user_id = (!empty($data['from_user_id'])) ? $data['from_user_id'] : null;
		$this->from_name = (!empty($data['from_name'])) ? $data['from_name'] : null;
		$this->to_user_id = (!empty($data['to_user_id'])) ? $data['to_user_id'] : null;
		$this->subject = (!empty($data['subject'])) ? $data['subject'] : null;
		$this->message = (!empty($data['message'])) ? $data['message'] : null;
		$this->replyId = (!empty($data['replyId'])) ? $data['replyId'] : null;
		$this->topLevel_id = (!empty($data['topLevel_id'])) ? $data['topLevel_id'] : null;
		$this->readFlag = (!empty($data['readFlag'])) ? $data['readFlag'] : null;
		$this->deleteFlag = (!empty($data['deleteFlag'])) ? $data['deleteFlag'] : null;
		$this->created_date = (!empty($data['created_date'])) ? $data['created_date'] : null;
		
		$this->from_user = (!empty($data['from_user'])) ? $data['from_user'] : null;
		$this->to_user = (!empty($data['to_user'])) ? $data['to_user'] : null;
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
                'name'     => 'from_user_id',
                'required' => true,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
            ));
            
            $inputFilter->add(array(
                'name'     => 'from_name',
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
                            'max'      => 255,
                        ),
                    ),
                ),
            ));
            
            $inputFilter->add(array(
                'name'     => 'to_user_id',
                'required' => true,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
            ));
            
            $inputFilter->add(array(
				'name'     => 'subject',
				'required' => true,
				'filters'  => array(
					array('name' => 'StripTags'),
					array('name' => 'StringTrim'),
					
                ),
            ));
            
            $inputFilter->add(array(
				'name'     => 'message',
				'required' => true,
				'filters'  => array(
					array('name' => 'StripTags'),
					array('name' => 'StringTrim'),
					
                ),
            ));
            
            $inputFilter->add(array(
                'name'     => 'replyId',
                'required' => false,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
            ));
            
            $inputFilter->add(array(
                'name'     => 'topLevel_id',
                'required' => false,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
            ));
            
            $inputFilter->add(array(
                'name'     => 'readFlag',
                'required' => false,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
            ));
            
            $inputFilter->add(array(
                'name'     => 'deleteFlag',
                'required' => false,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
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
            
            
            
            $this->inputFilter = $inputFilter;
            
        }
        
        return $this->inputFilter;
	
	}

}
		
