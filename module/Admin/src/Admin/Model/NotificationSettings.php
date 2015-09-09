<?php

namespace Admin\Model;

use Zend\InputFilter\Factory as InputFactory;     
use Zend\InputFilter\InputFilter;                 
use Zend\InputFilter\InputFilterAwareInterface;   
use Zend\InputFilter\InputFilterInterface;        

class NotificationSettings implements InputFilterAwareInterface
{
	public $id;
    public $user_id;
    public $module_id;
    public $sms_flag;
    public $email_flag;
    public $page_alert_flag;
    public $user_name;
    public $module_name;
    protected $inputFilter; 

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
		$this->user_id = (!empty($data['user_id'])) ? $data['user_id'] : null;
		$this->module_id = (!empty($data['module_id'])) ? $data['module_id'] : null;
		$this->sms_flag = (!empty($data['sms_flag'])) ? $data['sms_flag'] : null;
		$this->email_flag = (!empty($data['email_flag'])) ? $data['email_flag'] : null;
		$this->page_alert_flag = (!empty($data['page_alert_flag'])) ? $data['page_alert_flag'] : null;
		$this->user_name = (!empty($data['user_name'])) ? $data['user_name'] : null;
		$this->module_name = (!empty($data['module_name'])) ? $data['module_name'] : null;
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
                'name' => 'user_id',
                'required' => true,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
            )); 
            
            $inputFilter->add(array(
                'name' => 'module_id',
                'required' => true,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
            )); 
            
            $inputFilter->add(array(
                'name' => 'sms_flag',
                'required' => false,
            )); 
            
            $inputFilter->add(array(
                'name' => 'email_flag',
                'required' => false,
            )); 
            
            $inputFilter->add(array(
                'name' => 'page_alert_flag',
                'required' => false,
            )); 
            
            $this->inputFilter = $inputFilter;
            
        }
        
        return $this->inputFilter;
	
	}

}
		
