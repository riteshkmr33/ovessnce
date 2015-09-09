<?php
/*
 * For more validation refer to 
 * http://framework.zend.com/manual/2.0/en/modules/zend.validator.set.html 
 * */
namespace Admin\Model;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\Db\Adapter\Adapter;

class PageBannerLocations implements InputFilterAwareInterface
{
	public $id;
    public $location_id;
    public $banner_id;
    public $page_name;
    
    public $banner_name;  // banner table field
    
    public $location_name;  // page_location table field
    
    protected $inputFilter;

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
		$this->location_id = (!empty($data['location_id'])) ? $data['location_id'] : null;
		$this->banner_id = (!empty($data['banner_id'])) ? $data['banner_id'] : null;
		$this->page_name = (!empty($data['page_name'])) ? $data['page_name'] : null;
		
		$this->banner_name = (!empty($data['banner_name'])) ? $data['banner_name'] : null;
		
		$this->location_name = (!empty($data['location_name'])) ? $data['location_name'] : null;
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
                'name'     => 'page_name',
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
                    )
                ),
            ));
            
            $inputFilter->add(array(
                'name'     => 'location_id',
                'required' => true,
            ));
            
            $inputFilter->add(array(
                'name'     => 'banner_id',
                'required' => true,
            ));
            
            $this->inputFilter = $inputFilter;
            
        }
        
        return $this->inputFilter;
	
	}

}
		
