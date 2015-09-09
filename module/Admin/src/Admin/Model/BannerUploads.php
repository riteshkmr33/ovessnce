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

class BannerUploads implements InputFilterAwareInterface
{
	public $id;
    public $user_id;
    public $booking_id;
    public $banner_type;
    public $banner_type_id;
    public $banner_title;
    public $banner_content;
    public $target_url;
    public $status_id;
    
    public $banner_type_name;  // banner_type table field
    
    public $status;  // lookup_status table field
    
    protected $inputFilter;

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : 0;
		$this->user_id = (!empty($data['user_id'])) ? $data['user_id'] : 0;
		$this->booking_id = (!empty($data['booking_id'])) ? $data['booking_id'] : 0;
		$this->banner_type = (!empty($data['banner_type'])) ? $data['banner_type'] : 0;
		$this->banner_type_id = (!empty($data['banner_type_id'])) ? $data['banner_type_id'] : 0;
		$this->banner_title = (!empty($data['banner_title'])) ? $data['banner_title'] : null;
		$this->banner_content = (!empty($data['banner_content'])) ? $data['banner_content'] : null;
		$this->target_url = (!empty($data['target_url'])) ? $data['target_url'] : '';
		$this->status_id = (!empty($data['status_id'])) ? $data['status_id'] : 0;
		
		$this->status = (!empty($data['status'])) ? $data['status'] : null;
		
		$this->banner_type_name = (!empty($data['banner_type_name'])) ? $data['banner_type_name'] : null;
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
                'name'     => 'banner_title',
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
                    )
                ),
            ));
            
            $inputFilter->add(array(
                'name'     => 'target_url',
                'required' => false,
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
                            'max'      => 255,
                        ),
                    ),
                    array(
						'name' => 'Uri',
						'options' => array(
							'allowRelative' => false
						)
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
		
