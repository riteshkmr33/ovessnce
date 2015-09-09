<?php

namespace Application\Form;

use Zend\Form\Form;

class BookingForm extends Form
{

    public function __construct($addresses = array(), $service_list = array())
    {
        parent::__construct('Booking');
        $this->setAttribute('method', 'post');

        $this->add(array(
            'type' => 'text',
            'name' => 'service_date',
            'options' => array(
                'label' => 'Service Date',
            ),
            'attributes' => array(
                'required' => 'required',
                'class' => 'datetimepicker',
                'id' => 'service_date',
                'readonly' => 'true',
                'style' => 'height: 35px !important; color: inherit; font-size: 12px;',
                'onfocus' => 'if (this.value == "- Select date -") this.value = ""',
                'onblur' => 'if (this.value == "") this.value = "- Select date -"',
                'value' => '- Select date -',
            ),
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'service_location',
            'options' => array(
                'label' => '',
                'value_options' => $this->getAddresses($addresses),
                'empty_option' => '- Select service location -',
            ),
            'attributes' => array(
                'required' => 'required',
            ),
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'service_id',
            'options' => array(
                'label' => '',
                'value_options' => $this->getServices($service_list),
                'empty_option' => '- Select service -'
            ),
            'attributes' => array(
                'required' => 'required',
                'id' => 'services',
                'onchange' => 'getServiceduration($("#sp_id").data("spid"), this.value)',
            ),
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'duration',
            'options' => array(
                'label' => '',
                'value_options' => $this->getDuration($service_list),
                'empty_option' => '- Select duration -'
            ),
            'attributes' => array(
                'required' => 'required',
                'id' => 'duration_list',
                'onchange' => 'getprice(this.value, this);',
                'disabled' => 'true',
            ),
        ));

        $this->add(array(
            'type' => 'hidden',
            'name' => 'priceDel',
            'attributes' => array(
                'id' => 'priceDel',
            ),
        ));


        $this->add(array(
            'name' => 'time',
            'type' => 'Submit',
            'attributes' => array(
                'value' => 'submit',
                'id' => 'submitbutton',
            ),
        ));
    }

    public function getServices($service_list)
    {
        //echo '<pre>'; print_r($service_list); exit;
        $selectData = array();

        foreach ($service_list as $service) {
            if (!isset($service['status_id']) || $service['status_id'] == 1) {
                $selectData[$service['service_id']] = ucwords($service['service_name']);
            }
        }

        return $selectData;
    }

    public function getAddresses($addresses)
    {
        $selectData = array();

        foreach ($addresses as $address) {
            $selectData[$address['id']] = ucwords($address['street1_address'] . ', ' . $address['city'] . ', ' . $address['state_name'] . ' ' . $address['zip_code'] . ', ' . $address['country_name']);
        }

        return $selectData;
    }

    public function getDuration($service_list)
    {
        $selectData = array();
        foreach ($service_list as $service) {
            $selectData[$service['id']] = ucwords($service['duration'] . ' mins');
        }

        return $selectData;
    }

}
