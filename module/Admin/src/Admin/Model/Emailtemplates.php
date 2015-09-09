<?php

namespace Admin\Model;

use Zend\InputFilter\Factory as InputFactory;     // <-- Add this import
use Zend\InputFilter\InputFilter;                 // <-- Add this import
use Zend\InputFilter\InputFilterAwareInterface;   // <-- Add this import
use Zend\InputFilter\InputFilterInterface;        // <-- Add this import

class Emailtemplates implements InputFilterAwareInterface
{

    public $id;
    public $subject;
    public $content;
    public $status;
    public $fromEmail;
    public $created_date;
    public $modified_date;
    public $modified_by;
    protected $inputFilter;                       // <-- Add this variable

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->subject = (!empty($data['subject'])) ? $data['subject'] : null;
        $this->content = (!empty($data['content'])) ? $data['content'] : null;
        $this->status = (!empty($data['status'])) ? $data['status'] : null;
        $this->fromEmail = (!empty($data['fromEmail'])) ? $data['fromEmail'] : null;
        $this->created_date = (!empty($data['created_date'])) ? $data['created_date'] : null;
        $this->modified_date = (!empty($data['modified_date'])) ? $data['modified_date'] : null;
        $this->modified_by = (!empty($data['modified_by'])) ? $data['modified_by'] : null;
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
                'name' => 'subject',
                'required' => true,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
            ));

            $inputFilter->add(array(
                'name' => 'content',
                'required' => true,
            ));

            $inputFilter->add(array(
                'name' => 'status',
                'validators' => array(
                    array(
                        'name' => 'InArray',
                        'options' => array(
                            'haystack' => array(1, 2),
                            'messages' => array(
                                'notInArray' => 'Please select status !'
                            ),
                        ),
                    ),
                ),
            ));

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }

}
