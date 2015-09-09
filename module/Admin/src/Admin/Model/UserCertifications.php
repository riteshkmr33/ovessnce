<?php

namespace Admin\Model;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class UserCertifications implements InputFilterAwareInterface
{

    public $id;
    public $user_id;
    public $title;
    public $logo;
    public $professional_licence_number;
    public $organization_name;
    public $certification_date;
    public $validity;
    public $status_id;
    
    public $status;
    
    public $user_name;
    
    protected $inputFilter;

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->user_id = (!empty($data['user_id'])) ? $data['user_id'] : null;
        $this->title = (!empty($data['title'])) ? $data['title'] : null;
        $this->logo = (!empty($data['logo'])) ? $data['logo'] : null;
        $this->professional_licence_number = (!empty($data['professional_licence_number'])) ? $data['professional_licence_number'] : null;
        $this->organization_name = (!empty($data['organization_name'])) ? $data['organization_name'] : null;
        $this->certification_date = (!empty($data['certification_date'])) ? $data['certification_date'] : null;
        $this->validity = (!empty($data['validity'])) ? $data['validity'] : null;
        $this->status_id = (!empty($data['status_id'])) ? $data['status_id'] : null;
        
        $this->status = (!empty($data['status'])) ? $data['status'] : null;
        
        $this->user_name = (!empty($data['user_name'])) ? $data['user_name'] : null;
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
                'name' => 'user_id',
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
                            'max' => 10,
                        ),
                    ),
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
                    ),
                ),
            ));

            $inputFilter->add(array(
                'name' => 'logo',
                'required' => false,
            ));
            
            $inputFilter->add(array(
                'name' => 'professional_licence_number',
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
                            'max' => 50,
                        ),
                    ),
                ),
            ));

            $inputFilter->add(array(
                'name' => 'organization_name',
                'required' => true,
            ));

            $inputFilter->add(array(
                'name' => 'certification_date',
                'required' => true,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
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
