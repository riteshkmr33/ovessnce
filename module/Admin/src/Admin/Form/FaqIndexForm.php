<?php

namespace Admin\Form;

use Zend\Form\Form;
use Admin\Model\Status;
use Admin\Model\StatusTable;

class FaqIndexForm extends Form
{
    private $status;

    public function __construct(StatusTable $status)
    {
        $this->status = $status;

        // we want to ignore the name passed
        parent::__construct('faq_index');

        $this->add(array(
            'name' => 'id',
            'type' => 'Hidden',
        ));

        $this->add(array(
            'type' => 'Text',
            'name' => 'index_name',
            'options' => array(
                'label' => 'Index Name',
            ),
            'attributes' => array(
                'class' => 'form-control input-large'
            )
        ));
        
        $this->add(array(
            'type' => 'Text',
            'name' => 'order_by',
            'options' => array(
                'label' => 'Display Order',
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
                'empty_option' => '--- Select Status ---'
            ),
            'attributes' => array(
                'class' => 'form-control input-large select2'
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
        $data = $this->status->fetchAll(false, array(1,2));

        $selectData = array();

        foreach ($data as $selectOption) {
            $selectData[$selectOption->status_id] = ucwords($selectOption->status);
        }

        return $selectData;
    }

}
