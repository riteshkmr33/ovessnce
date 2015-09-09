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

class SiteBanners implements InputFilterAwareInterface
{

    public $id;
    public $banner_url;
    public $title;
    public $page_location_id;
    public $page_name;
    public $status_id;
    public $status;  // lookup_status table field
    protected $inputFilter;

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->banner_url = (!empty($data['banner_url'])) ? $data['banner_url'] : null;
        $this->title = (!empty($data['title'])) ? $data['title'] : null;
        $this->page_location_id = (!empty($data['page_location_id'])) ? $data['page_location_id'] : null;
        $this->page_name = (!empty($data['page_name'])) ? $data['page_name'] : null;
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
                'name' => 'banner_url',
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
                            'max' => 150,
                        ),
                    )
                ),
            ));
            
            $inputFilter->add(array(
                'name' => 'title',
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
                'name' => 'page_location_id',
                'required' => true,
            ));

            $inputFilter->add(array(
                'name' => 'status_id',
                'required' => true,
            ));


            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }

}

