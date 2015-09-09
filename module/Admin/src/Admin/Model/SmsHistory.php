<?php

namespace Admin\Model;

use Zend\InputFilter\Factory as InputFactory;     
use Zend\InputFilter\InputFilter;                 
use Zend\InputFilter\InputFilterAwareInterface;   
use Zend\InputFilter\InputFilterInterface;        

class SmsHistory implements InputFilterAwareInterface
{
	public $id;
    public $to_user_id;
    public $from_user_id;
    public $subject;
    public $message;
    public $sent_date;
    public $status;
    public $total;
    
    protected $inputFilter;                       

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
		$this->to_user_id = (!empty($data['to_user_id'])) ? $data['to_user_id'] : null;
		$this->from_user_id = (!empty($data['from_user_id'])) ? $data['from_user_id'] : null;
		$this->subject = (!empty($data['subject'])) ? $data['subject'] : null;
		$this->message = (!empty($data['message'])) ? $data['message'] : null;
		$this->sent_date = (!empty($data['sent_date'])) ? $data['sent_date'] : null;
		$this->status = (!empty($data['status'])) ? $data['status'] : null;
		$this->total = (!empty($data['total'])) ? $data['total'] : 0;
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
                                   
            $this->inputFilter = $inputFilter;
            
        }
        
        return $this->inputFilter;
	
	}

}
		
