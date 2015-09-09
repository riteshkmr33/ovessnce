<?php

namespace Admin\Model;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Faqs implements InputFilterAwareInterface
{
    public $id;
    public $index_id;
    public $user_type_id;
    public $question;
    public $answer;
    public $order_by;
    public $created_on;
    public $status_id;
    
    public $index_name;
    
    public $user_type;
    
    public $status;
    
    protected $inputFilter;                       // <-- Add this variable

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->index_id = (!empty($data['index_id'])) ? $data['index_id'] : null;
        $this->user_type_id = (!empty($data['user_type_id'])) ? $data['user_type_id'] : null;
        $this->question = (!empty($data['question'])) ? $data['question'] : null;
        $this->answer = (!empty($data['answer'])) ? $data['answer'] : null;
        $this->order_by = (!empty($data['order_by'])) ? $data['order_by'] : null;
        $this->created_on = (!empty($data['created_on'])) ? $data['created_on'] : null;
        $this->status_id = (!empty($data['status_id'])) ? $data['status_id'] : null;
        
        $this->index_name = (!empty($data['index_name'])) ? $data['index_name'] : null;
        
        $this->user_type = (!empty($data['user_type'])) ? $data['user_type'] : null;
        
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
                'name' => 'index_id',
                'required' => true,
            ));
            
            $inputFilter->add(array(
                'name' => 'user_type_id',
                'required' => true,
            ));

            $inputFilter->add(array(
                'name'     => 'question',
                'required' => true,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
            ));
            
            $inputFilter->add(array(
                'name'     => 'answer',
                'required' => true,
                'filters'  => array(
                    array('name' => 'StringTrim'),
                ),
            ));
            
            $inputFilter->add(array(
                'name' => 'order_by',
                'required' => true,
                'validators' => array(
                    array(
                        'name' => 'Digits',
                    ),
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
