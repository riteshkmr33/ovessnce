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

class SubscriptionPlans implements InputFilterAwareInterface
{
	public $id;
	public $subscription_name;
	public $status_id;
    
    /* feature_video_limit table field */
    public $subscription_plan_id;
    public $feature_id;
    public $limit;
    
    /* lookup_status table field */
    public $status;
    
	public $adapter;  // DB adapter
    
    protected $inputFilter;

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
		$this->subscription_name = (!empty($data['subscription_name'])) ? $data['subscription_name'] : null;
		$this->status_id = (!empty($data['status_id'])) ? $data['status_id'] : 0;
		
		$this->subscription_plan_id = (!empty($data['subscription_plan_id'])) ? $data['subscription_plan_id'] : 0;
		$this->feature_id = (!empty($data['feature_id'])) ? $data['feature_id'] : 0;
		$this->limit = (!empty($data['limit'])) ? $data['limit'] : 0;
		
		$this->status = (!empty($data['status'])) ? $data['status'] : null;
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
                'name'     => 'subscription_name',
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
                'name'     => 'limit',
                'required' => false,
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
		
