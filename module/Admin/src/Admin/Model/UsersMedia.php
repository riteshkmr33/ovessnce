<?php
 
namespace Admin\Model;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class UsersMedia implements InputFilterAwareInterface
{
	/* UsersMedia fields */
	public $id;
    public $user_id;
    public $media_url;
    public $media_title;
    public $media_description;
    public $media_type;
    public $created_date;
    public $created_by;
    public $updated_date;
    public $updated_by;
    public $status_id;
    
    /* users fields */
    public $first_name;
    public $last_name;
    
    /* lookup status fields */
    public $status;
    
    protected $inputFilter;

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : 0;
		$this->user_id = (!empty($data['user_id'])) ? $data['user_id'] : 0;
		$this->media_url = (!empty($data['media_url'])) ? $data['media_url'] : null;
		$this->media_title = (!empty($data['media_title'])) ? $data['media_title'] : null;
		$this->media_description = (!empty($data['media_description'])) ? $data['media_description'] : '';
		$this->media_type = (!empty($data['media_type'])) ? $data['media_type'] : 1;
		$this->created_date = (!empty($data['created_date'])) ? $data['created_date'] : null;
		$this->created_by = (!empty($data['created_by'])) ? $data['created_by'] : 0;
		$this->updated_date = (!empty($data['updated_date'])) ? $data['updated_date'] : null;
		$this->updated_by = (!empty($data['updated_by'])) ? $data['updated_by'] : 0;
		$this->status_id = (!empty($data['status_id'])) ? $data['status_id'] : 0;
		
		$this->first_name = (!empty($data['first_name'])) ? $data['first_name'] : null;
		$this->last_name = (!empty($data['last_name'])) ? $data['last_name'] : null;
		
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
                'name'     => 'media_title',
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
                'name'     => 'media_description',
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
                            'max'      => 500,
                        ),
                    )
                ),
                )
            );
            
            $inputFilter->add(array(
                'name'     => 'status_id',
                'required' => true,
                )
            );
            
            $this->inputFilter = $inputFilter;
            
        }
        
        return $this->inputFilter;
		
	}

}
		
