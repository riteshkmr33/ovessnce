<?php

namespace Admin\Model;

use Zend\InputFilter\Factory as InputFactory;     
use Zend\InputFilter\InputFilter;                 
use Zend\InputFilter\InputFilterAwareInterface;   
use Zend\InputFilter\InputFilterInterface;        

class UserFeatureSetting implements InputFilterAwareInterface
{
	public $id;
    public $email;
    public $chat;
    public $sms;
    public $newsletter;
    
    public function exchangeArray($data)
    {
        $this->id 	 = (!empty($data['id'])) ? $data['id'] : null;
		$this->email = (!empty($data['email'])) ? $data['email'] : null;
		$this->chat  = (!empty($data['chat'])) ? $data['chat'] : null;
		$this->sms   = (!empty($data['sms'])) ? $data['sms'] : null;
		$this->newsletter   = (!empty($data['newsletter'])) ? $data['newsletter'] : null;
	}
   
   public function setAdapter(Adapter $adapter)
    {
		$this->adapter = $adapter;
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
		
