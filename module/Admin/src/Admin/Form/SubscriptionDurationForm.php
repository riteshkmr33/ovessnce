<?php

namespace Admin\Form;

use Zend\Form\Form;
use Admin\Model\Status;
use Admin\Model\StatusTable;

class SubscriptionDurationForm extends Form
{

    private $status;

    public function __construct(StatusTable $status)
    {
        $this->status = $status;

        parent::__construct('SubscriptionDuration');

        $this->add(array(
            'name' => 'id',
            'type' => 'Hidden',
        ));

        $this->add(array(
            'name' => 'subscription_id',
            'type' => 'Hidden',
        ));

        $this->add(array(
            'name' => 'duration',
            'type' => 'Text',
            'options' => array(
                'label' => 'Duration',
            ),
            'attributes' => array(
                'class' => 'form-control input-large'
            )
        ));

        $this->add(array(
            'name' => 'price',
            'type' => 'Text',
            'options' => array(
                'label' => 'Price',
            ),
            'attributes' => array(
                'class' => 'form-control input-medium'
            )
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'duration_in',
            'options' => array(
                'label' => 'Duration In',
                'value_options' => array(1 => 'Years', 2 => 'Months', 3 => 'Days', 4 => 'Lifetime'),
            ),
            'attributes' => array(
                'class' => 'form-control input-large'
            )
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'status_id',
            'options' => array(
                'label' => 'Status',
                'value_options' => $this->getStatus(),
                'empty_option' => '--- Choose Status ---'
            ),
            'attributes' => array(
                'class' => 'form-control input-large'
            )
        ));

        $this->add(array(
            'name' => 'submit',
            'type' => 'Submit',
            'attributes' => array(
                'value' => 'Go',
                'id' => 'submitbutton',
            ),
        ));
    }

    public function getStatus()
    {
        $data = $this->status->fetchAll(false, array(1, 2));

        $selectData = array();

        foreach ($data as $selectOption) {
            $selectData[$selectOption->status_id] = ucwords($selectOption->status);
        }

        return $selectData;
    }

}
