<?php

namespace Admin\Model;

use Zend\InputFilter\Factory as InputFactory;     
use Zend\InputFilter\InputFilter;                 
use Zend\InputFilter\InputFilterAwareInterface;   
use Zend\InputFilter\InputFilterInterface;        

class VideoViews implements InputFilterAwareInterface
{
	public $id;
    public $video_id;
    public $user_id;
    public $remote_ip;
    public $date_added;
    protected $inputFilter;                       

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
		$this->video_id = (!empty($data['video_id'])) ? $data['video_id'] : null;
		$this->user_id = (!empty($data['user_id'])) ? $data['user_id'] : null;
		$this->remote_ip = (!empty($data['remote_ip'])) ? $data['remote_ip'] : null;
		$this->date_added = (!empty($data['date_added'])) ? $data['date_added'] : null;
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
		
