<?php

namespace Admin\Form;

use Zend\Form\Form;
use Admin\Model\Status;
use Admin\Model\StatusTable;
use Zend\Db\Adapter\AdapterInterface;

class AdvertisementForm extends Form
{
    private $status;

    public function __construct(StatusTable $status)
    {
        $this->status = $status;

        parent::__construct('advertisement');

        $this->add(array(
            'name' => 'id',
            'type' => 'Hidden',
        ));
        
        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'status_id',
            'options' => array(
                'label' => 'Select Status',
                'value_options' => $this->getStatus(),
                'empty_option' => '--- Choose Status ---'
            ),
            'attributes' => array(
                'class' => 'form-control input-large'
            )
        ));
        $this->add(array(
            'name' => 'banner_name',
            'type' => 'Text',
            'options' => array(
                'label' => 'Banner Name',
            ),
            'attributes' => array(
                'class' => 'form-control input-large'
            )
        ));
        $this->add(array(
            'name' => 'banner_height',
            'type' => 'Text',
            'options' => array(
                'label' => 'Banner Height',
            ),
            'attributes' => array(
                'class' => 'form-control input-large'
            )
        ));

        $this->add(array(
            'name' => 'banner_width',
            'type' => 'Text',
            'options' => array(
                'label' => 'Banner Width',
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
