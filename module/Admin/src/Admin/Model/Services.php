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

class Services implements InputFilterAwareInterface
{
	public $id;
    public $service_category_id;
    public $duration;
    public $price;
    public $status_id;
    public $description;
    
    public $category_name;  // Field from service_category table
    
    public $status;  // Field from lookup_status table
    
    public $adapter;  // DB adapter
    public $default_comission;
    
    protected $inputFilter;

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
		$this->service_category_id = (!empty($data['service_category_id'])) ? $data['service_category_id'] : null;
		$this->duration = (!empty($data['duration'])) ? $data['duration'] : 0;
		$this->price = (!empty($data['price'])) ? $data['price'] : 0.00;
		$this->status_id = (!empty($data['status_id'])) ? $data['status_id'] : 1;
		$this->description = (!empty($data['description'])) ? $data['description'] : null;
		
		$this->category_name = (!empty($data['category_name'])) ? $data['category_name'] : null;
		
		$this->status = (!empty($data['status'])) ? $data['status'] : null;
		$this->default_comission = (!empty($data['default_comission']))? $data['default_comission'] : 0;
    }
    
    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
    
    public function setAdapter(Adapter $adapter)
    {
		$this->adapter = $adapter;
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
                'name'     => 'service_period',
                'required' => true,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name'    => 'Digits',
                    ),
                ),
            ));
            
            $inputFilter->add(array(
                'name'     => 'service_category_id',
                'required' => true,
            ));
            
            $inputFilter->add(array(
                'name'     => 'description',
                'required' => false,
            ));
            
            
            $this->inputFilter = $inputFilter;
            
        }
        
        return $this->inputFilter;
	
	}

}
		
