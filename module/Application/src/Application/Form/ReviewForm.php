<?php

namespace Application\Form;

use Zend\Captcha;
use Zend\Form\Form;

class ReviewForm extends Form
{

    public function __construct($service_list = array())
    {
        parent::__construct('Review');
        $this->setAttribute('method', 'post');

        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'service_id',
            'options' => array(
                'label' => '',
                'value_options' => $this->getServices($service_list),
                'empty_option' => '- Select service to review -',
                'disable_inarray_validator' => true,
            ),
            'attributes' => array(
                'required' => 'required',
                'id' => 'service_id',
            ),
        ));

        $this->add(array(
            'name' => 'comment',
            'type' => 'Zend\Form\Element\Textarea',
            'options' => array(
                'label' => 'comment',
            ),
            'attributes' => array(
                'required' => 'required',
                'placeholder' => 'Your Review',
            ),
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Captcha',
            'name' => 'captcha',
            'options' => array(
                'label' => 'Please verify you are human',
                'captcha' => new Captcha\Dumb(),
            ),
            'attributes' => array(
                'required' => 'required',
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
            foreach ($service_list as $service) {
                $selectData[$service['id']] = ucwords($service['service_name']);
            }
        } else {
            $selectData[''] = "No services available to review";
        }

        return $selectData;
    }

}
