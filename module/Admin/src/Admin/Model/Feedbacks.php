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

class Feedbacks implements InputFilterAwareInterface
{
	public $users_id;
    public $service_id;
    public $comments;
    public $created_date;
    public $created_by;
    public $status_id;
    
    /* Users table fields */
    public $first_name;
    public $last_name;
    
    /* Service provider contact table fields */
    public $sp_first_name;
    public $sp_last_name;
    
    /* Service provider service table fields */
    public $duration;
    
    /* Service category table fields */
    public $category_name;
    
    /* Status table fields */
    public $status;
    
    public $adapter;  // DB adapter
    
    protected $inputFilter;

    public function exchangeArray($data)
    {
        $this->users_id = (!empty($data['users_id'])) ? $data['users_id'] : null;
		$this->service_id = (!empty($data['service_id'])) ? $data['service_id'] : null;
		$this->comments = (!empty($data['comments'])) ? $data['comments'] : null;
		$this->created_date = (!empty($data['created_date'])) ? $data['created_date'] : null;
		$this->created_by = (!empty($data['created_by'])) ? $data['created_by'] : null;
		$this->status_id = (!empty($data['status_id'])) ? $data['status_id'] : 0;
		
		$this->first_name = (!empty($data['first_name'])) ? $data['first_name'] : null;
		$this->last_name = (!empty($data['last_name'])) ? $data['last_name'] : null;
		
		$this->sp_first_name = (!empty($data['sp_first_name'])) ? $data['sp_first_name'] : null;
		$this->sp_last_name = (!empty($data['sp_last_name'])) ? $data['sp_last_name'] : null;
		
		$this->duration = (!empty($data['duration'])) ? $data['duration'] : null;
		
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
            
            $this->inputFilter = $inputFilter;
            
        }
        
        return $this->inputFilter;
	
	}

}
		
