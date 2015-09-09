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

class ServiceProviderServices implements InputFilterAwareInterface
{
	public $id;
    public $user_id;
    public $service_id;
    public $duration;
    public $price;
    public $status_id;
    
    public $category_name;  // Field from service_category table
    
    public $status;  // Field from lookup_status table
    
    public $adapter;  // DB adapter
    
    protected $inputFilter;

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
		$this->user_id = (!empty($data['user_id'])) ? $data['user_id'] : null;
		$this->service_id = (!empty($data['service_id'])) ? $data['service_id'] : null;
		$this->duration = (!empty($data['duration'])) ? $data['duration'] : null;
		$this->price = (!empty($data['price'])) ? $data['price'] : null;
		$this->status_id = (!empty($data['status_id'])) ? $data['status_id'] : 1;
		
		$this->category_name = (!empty($data['category_name'])) ? $data['category_name'] : null;
		
		$this->status = (!empty($data['status'])) ? $data['status'] : null;
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
                'name'     => 'user_id',
                'required' => true,
            ));
            
            $inputFilter->add(array(
                'name'     => 'service_id',
                'required' => true,
            ));
            
            $inputFilter->add(array(
                'name'     => 'duration',
                'required' => true,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
					array(
							'name' => 'Digits',
						),
                )
            ));
            
             $inputFilter->add(array(
                'name'     => 'price',
                'required' => true,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'Float',
                    ),
                ),
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
		
