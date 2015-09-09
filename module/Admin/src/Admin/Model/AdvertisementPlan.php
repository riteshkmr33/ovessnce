<?php

namespace Admin\Model;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class AdvertisementPlan implements InputFilterAwareInterface
{

    public $id;
    public $advertisement_id;
    public $advertisement_page_id;
    public $plan_name;
    public $duration;
    public $duration_unit;
    public $duration_in;
    public $price;

    /* advertisement table field */
    public $banner_name;
    public $banner_height;
    public $banner_width;
    
    /* advertisement page table field*/
    public $page_name;

    protected $inputFilter;

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->advertisement_id = (!empty($data['advertisement_id'])) ? $data['advertisement_id'] : null;
        $this->advertisement_page_id = (!empty($data['advertisement_page_id'])) ? $data['advertisement_page_id'] : null;
        $this->plan_name = (!empty($data['plan_name'])) ? $data['plan_name'] : 0;
        $this->duration = (!empty($data['duration'])) ? $data['duration'] : 0;
        $this->duration_in = (!empty($data['duration_in'])) ? $data['duration_in'] : null;
        $this->duration_unit = (!empty($data['duration_unit'])) ? $data['duration_unit'] : null;
        $this->price = (!empty($data['price'])) ? $data['price'] : null;
        
        $this->banner_name = (!empty($data['banner_name'])) ? $data['banner_name'] : null;
        $this->banner_height = (!empty($data['banner_height'])) ? $data['banner_height'] : 0;
        $this->banner_width = (!empty($data['banner_width'])) ? $data['banner_width'] : 0;
        
        $this->page_name = (!empty($data['page_name'])) ? $data['page_name'] : null;
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
                'name' => 'advertisement_id',
                'required' => true
            ));
            
            $inputFilter->add(array(
                'name' => 'advertisement_page_id',
                'required' => true
            ));
            
            $inputFilter->add(array(
                'name' => 'plan_name',
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
                'name' => 'duration',
                'required' => true,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array('name' => 'Digits'),
                ),
            ));

            $inputFilter->add(array(
                'name' => 'price',
                'required' => true,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array('name' => 'Float'),
                ),
            ));

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }

}
