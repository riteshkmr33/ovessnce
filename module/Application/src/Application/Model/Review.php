<?php
/**
 * Review.php
 * @author <piyush@clavax.us>
 * @package Model
 */
namespace Application\Model;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\Db\Adapter\Adapter;

class Review implements InputFilterAwareInterface
{
    public $service_id;
    public $comment;
    public $captcha;
  
    protected $inputFilter;

    public function exchangeArray($data)
    {
        $this->service_id = (!empty($data['service_id'])) ? $data['service_id'] : null;
		$this->comment = (!empty($data['comment'])) ? $data['comment'] : null;
		$this->captcha = (!empty($data['captcha'])) ? $data['captcha'] : null;
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
                'name'     => 'service_id',
                'required' => true,
            ));
            
            $inputFilter->add(array(
                'name'     => 'comment',
                'required' => true,
            ));
                      
            $inputFilter->add(array(
                'name'     => 'captcha',
                'required' => true,
            ));
            
            
            $this->inputFilter = $inputFilter;
            
        }
        
        return $this->inputFilter;
	
	}

}
		
