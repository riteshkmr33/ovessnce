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

class ServiceProviderAvailability implements InputFilterAwareInterface
{
    /* UsersMedia fields */

    public $id;
    public $user_id;
    public $days_id;
    public $start_time;
    public $end_time;
    public $lunch_start_time;
    public $lunch_end_time;
    public $address_id;

    /* Availability table fields */
    public $day;

    /* Service provider availability delay table fields */
    public $delay_time;

    /* users fields */
    public $first_name;
    public $last_name;
    protected $inputFilter;

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : 0;
        $this->user_id = (!empty($data['user_id'])) ? $data['user_id'] : 0;
        $this->days_id = (!empty($data['days_id'])) ? $data['days_id'] : 0;
        $this->start_time = (!empty($data['start_time'])) ? $data['start_time'] : null;
        $this->end_time = (!empty($data['end_time'])) ? $data['end_time'] : null;
        $this->lunch_start_time = (!empty($data['lunch_start_time'])) ? $data['lunch_start_time'] : null;
        $this->lunch_end_time = (!empty($data['lunch_end_time'])) ? $data['lunch_end_time'] : null;
        $this->address_id = (!empty($data['address_id'])) ? $data['address_id'] : null;

        $this->day = (!empty($data['day'])) ? $data['day'] : null;

        $this->delay_time = (!empty($data['delay_time'])) ? $data['delay_time'] : null;

        $this->first_name = (!empty($data['first_name'])) ? $data['first_name'] : null;
        $this->last_name = (!empty($data['last_name'])) ? $data['last_name'] : null;
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
                    )
            );

            $inputFilter->add(array(
                'name' => 'delay_time',
                'required' => false,
                'validators' => array(
                    array('name' => 'Digits'),
                ),
                    )
            );

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }

}
