<?php

namespace Admin\Model;

use Zend\InputFilter\Factory as InputFactory;     
use Zend\InputFilter\InputFilter;                 
use Zend\InputFilter\InputFilterAwareInterface;   
use Zend\InputFilter\InputFilterInterface;        

class Partners implements InputFilterAwareInterface
{
	public $id;
    public $title;
    public $desc;
    public $url;
    public $logo;
    public $status_id;   
    protected $inputFilter;                  

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
		$this->title = (!empty($data['title'])) ? $data['title'] : null;
		$this->desc = (!empty($data['desc'])) ? $data['desc'] : null;
		$this->url = (!empty($data['url'])) ? $data['url'] : null;
		$this->logo = (!empty($data['logo'])) ? $data['logo'] : null;
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
                'name'     => 'title',
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
                            'max'      => 50,
                        ),
                    ),
                ),
            )); 	
            
			$inputFilter->add(array(
                'name'     => 'desc',
                'required' => true,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
            )); 	
            
			$inputFilter->add(array(
                'name'     => 'url',
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
                'name'     => 'logo',
                'required' => false,
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
		
