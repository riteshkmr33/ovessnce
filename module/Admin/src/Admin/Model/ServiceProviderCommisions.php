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

class ServiceProviderCommisions implements InputFilterAwareInterface
{
	public $id;
    public $user_id;
    public $commision;
    public $status_id;
    public $created_date;
    
    /* Users table fields */
    public $first_name;
    public $last_name;
    
    public $status;  // Field from lookup_status table
    
    public $adapter;  // DB adapter
    
    protected $inputFilter;

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : 0;
		$this->user_id = (!empty($data['user_id'])) ? $data['user_id'] : 0;
		$this->commision = (!empty($data['commision'])) ? $data['commision'] : 0;
		$this->status_id = (!empty($data['status_id'])) ? $data['status_id'] : 1;
		$this->created_date = (!empty($data['created_date'])) ? $data['created_date'] : null;
		
		$this->first_name = (!empty($data['first_name'])) ? $data['first_name'] : null;
		$this->last_name = (!empty($data['last_name'])) ? $data['last_name'] : null;
		
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
                'name'     => 'commision',
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
            
            
            $this->inputFilter = $inputFilter;
            
        }
        
        return $this->inputFilter;
	
	}

}
		
