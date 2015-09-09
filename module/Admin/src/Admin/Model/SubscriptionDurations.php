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

class SubscriptionDurations implements InputFilterAwareInterface
{

    public $id;
    public $subscription_id;
    public $duration;
    public $duration_in;
    public $durationin;
    public $price;
    public $currency;
    public $status_id;

    /* subscription table field */
    public $subscription_name;

    /* lookup_status table field */
    public $status;
    public $adapter;  // DB adapter
    protected $inputFilter;

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->subscription_id = (!empty($data['subscription_id'])) ? $data['subscription_id'] : null;
        $this->duration = (!empty($data['duration'])) ? $data['duration'] : null;
        $this->duration_in = (!empty($data['duration_in'])) ? $data['duration_in'] : null;
        $this->durationin = (!empty($data['durationin'])) ? $data['durationin'] : null;
        $this->price = (!empty($data['price'])) ? $data['price'] : null;
        $this->currency = (!empty($data['currency'])) ? $data['currency'] : 'CAD';
        $this->status_id = (!empty($data['status_id'])) ? $data['status_id'] : 0;

        $this->subscription_name = (!empty($data['subscription_name'])) ? $data['subscription_name'] : null;

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
                'name' => 'duration',
                'required' => true,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'Digits',
                    ),
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
                    array(
                        'name' => 'Float',
                    ),
                ),
            ));

            $inputFilter->add(array(
                'name' => 'subscription_id',
                'required' => true,
            ));

            $inputFilter->add(array(
                'name' => 'duration_in',
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
