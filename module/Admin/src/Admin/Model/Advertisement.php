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

class Advertisement implements InputFilterAwareInterface
{

    public $id;
    public $banner_name;
    public $banner_height;
    public $banner_width;
    public $status_id;
    
    public $status;  // lookup_status table field
    protected $inputFilter;

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->banner_name = (!empty($data['banner_name'])) ? $data['banner_name'] : null;
        $this->banner_height = (!empty($data['banner_height'])) ? $data['banner_height'] : null;
        $this->banner_width = (!empty($data['banner_width'])) ? $data['banner_width'] : null;
        $this->status_id = (!empty($data['status_id'])) ? $data['status_id'] : null;

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
                'name' => 'banner_name',
                'required' => true,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min' => 1,
                            'max' => 100,
                        ),
                    )
                ),
            ));

            $inputFilter->add(array(
                'name' => 'status_id',
                'required' => true,
            ));

            $inputFilter->add(array(
                'name' => 'banner_height',
                'required' => true,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'Digits'
                    )
                ),
            ));

            $inputFilter->add(array(
                'name' => 'banner_width',
                'required' => true,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'Digits'
                    )
                ),
            ));


            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }

}
