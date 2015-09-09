<?php

namespace Admin\Model;

use Zend\InputFilter\Factory as InputFactory;     // <-- Add this import
use Zend\InputFilter\InputFilter;                 // <-- Add this import
use Zend\InputFilter\InputFilterAwareInterface;   // <-- Add this import
use Zend\InputFilter\InputFilterInterface;        // <-- Add this import

class Page implements InputFilterAwareInterface
{
	public $page_id;
    public $title;
    public $slug;
    public $content;
    public $page_status;
    public $created_date;
    public $created_by;
    public $updated_date;
    public $updated_by;
    protected $inputFilter;                       // <-- Add this variable

    public function exchangeArray($data)
    {
        $this->page_id = (!empty($data['page_id'])) ? $data['page_id'] : null;
		$this->title = (!empty($data['title'])) ? $data['title'] : null;
		$this->slug = (!empty($data['slug'])) ? $data['slug'] : null;
		$this->content = (!empty($data['content'])) ? $data['content'] : null;
		$this->page_status = (!empty($data['page_status'])) ? $data['page_status'] : null;
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
                            'max'      => 100,
                        ),
                    ),
                ),
            ));
            
            $inputFilter->add(array(
				'name'     => 'content',
				'required' => true,
            )); 
            
             $inputFilter->add(array(
                'name'     => 'page_status',
                'validators' => array(
                    array(
                        'name'    => 'InArray',
                        'options' => array(
                            'haystack' => array(1,2),
                            'messages' => array(
                                'notInArray' => 'Please select User status !' 
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
		
