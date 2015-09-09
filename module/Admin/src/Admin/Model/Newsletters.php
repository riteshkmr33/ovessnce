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

class Newsletters implements InputFilterAwareInterface
{

    public $id;
    public $subject;
    public $message;
    public $attachment;
    public $date_created;
    public $send_date;
    public $status_id;
    public $user_type_id;
    
    public $status; // lookup_status table field
    public $user_type; // lookup_user_type table field
    public $adapter;  // DB adapter
    protected $inputFilter;

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->subject = (!empty($data['subject'])) ? $data['subject'] : null;
        $this->message = (!empty($data['message'])) ? $data['message'] : null;
        $this->attachment = (!empty($data['attachment'])) ? $data['attachment'] : null;
        $this->date_created = (!empty($data['date_created'])) ? $data['date_created'] : null;
        $this->send_date = (!empty($data['send_date'])) ? $data['send_date'] : null;
        $this->status_id = (!empty($data['status_id'])) ? $data['status_id'] : null;
        $this->user_type_id = (!empty($data['user_type_id'])) ? $data['user_type_id'] : null;

        $this->status = (!empty($data['status'])) ? $data['status'] : null;
        
        $this->user_type = (!empty($data['user_type'])) ? $data['user_type'] : null;
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

            $inputFilter->add(array(
                'name' => 'subject',
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
                            'max' => 255,
                        ),
                    ),
                ),
            ));
            
            $inputFilter->add(array(
                'name' => 'user_type_id',
                'required' => true,
            ));

            $inputFilter->add(array(
                'name' => 'message',
                'required' => true,
            ));

            $inputFilter->add(array(
                'name' => 'send_date',
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
