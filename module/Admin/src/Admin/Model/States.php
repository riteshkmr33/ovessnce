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

class States implements InputFilterAwareInterface
{
	public $id;
    public $state_code;
    public $country_id;
    public $state_name;
    public $status_id;
    
    public $country_name;  // country table field
    
    public $status;  // lookup_status table field
    
    public $adapter;  // DB adapter
    
    protected $inputFilter;

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
		$this->state_code = (!empty($data['state_code'])) ? $data['state_code'] : null;
		$this->country_id = (!empty($data['country_id'])) ? $data['country_id'] : null;
		$this->state_name = (!empty($data['state_name'])) ? $data['state_name'] : null;
		$this->status_id = (!empty($data['status_id'])) ? $data['status_id'] : null;
		
		$this->status = (!empty($data['status'])) ? $data['status'] : null;
		
		$this->country_name = (!empty($data['country_name'])) ? $data['country_name'] : null;
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
                'name'     => 'state_code',
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
                            'max'      => 4,
                        ),
                    )
                ),
            ));
            
            $inputFilter->add(array(
                'name'     => 'country_id',
                'required' => true,
            ));
            
            $inputFilter->add(array(
                'name'     => 'state_name',
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
                            'max'      => 70,
                        ),
                    )
                ),
            ));
            
            
            $this->inputFilter = $inputFilter;
            
        }
        
        return $this->inputFilter;
	
	}

}
		
