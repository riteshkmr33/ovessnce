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

class Countries implements InputFilterAwareInterface
{
	public $id;
    public $country_code;
    public $country_name;
    public $status_id;
    
    public $status; // lookup_status table field
    
    public $adapter;  // DB adapter
    
    protected $inputFilter;

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
		$this->country_code = (!empty($data['country_code'])) ? $data['country_code'] : null;
		$this->country_name = (!empty($data['country_name'])) ? $data['country_name'] : null;
		$this->status_id = (!empty($data['status_id'])) ? $data['status_id'] : null;
		
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
                'name'     => 'country_code',
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
                    ),
                    /*array(
                        'name'    => 'Db\NoRecordExists',
                        'options' => array(
                            'table' => 'country',
                            'field' => 'country_code',
                            'adapter' => $this->adapter
                        ),
                    ),*/
                ),
            ));
            
            $inputFilter->add(array(
                'name'     => 'country_name',
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
                    ),
                    /*array(
                        'name'    => 'Db\NoRecordExists',
                        'options' => array(
                            'table' => 'country',
                            'field' => 'country_name',
                            'adapter' => $this->adapter
                        ),
                    ),*/
                ),
            ));
            
            
            $this->inputFilter = $inputFilter;
            
        }
        
        return $this->inputFilter;
	
	}

}
		
