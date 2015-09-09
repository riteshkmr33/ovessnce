<?php

namespace Application\Form;

use Zend\Form\Form;

class SPaddservicesFrom extends Form
{

    public function __construct($service_list = array())
    {
        parent::__construct('addservices');
        $this->setAttribute('method', 'post');

        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'service_id',
            'options' => array(
                'label' => 'SERVICE',
                'value_options' => $this->getServices($service_list),
                'empty_option' => '- Select service -'
            ),
            'attributes' => array(
                'required' => 'required',
                'id' => 'service_id',
            ),
        ));

        $this->add(array(
            'name' => 'duration',
            'attributes' => array(
                'type' => 'text',
                'name' => 'duration',
                'id' => 'duration',
                'required' => 'required',
            ),
            'options' => array(
                'label' => 'DURATION',
            ),
        ));

        $this->add(array(
            'name' => 'price',
            'attributes' => array(
                'type' => 'text',
                'name' => 'price',
                'id' => 'price',
                'required' => 'required',
            ),
            'options' => array(
                'label' => 'PRICE',
            ),
        ));

        $this->add(array(
            'name' => 'submit',
            'type' => 'Submit',
            'attributes' => array(
                'value' => 'submit',
                'id' => 'submitbutton',
            ),
        ));
    }

    public function getServices($service_list)
    {
        $selectData = array();

        if (count($service_list) > 0) {
            foreach ($service_list as $key => $value) {
                $selectData[$key] = $value;
            }
        } else {
            $selectData[''] = "No services available";
        }

        return $selectData;
    }

}
