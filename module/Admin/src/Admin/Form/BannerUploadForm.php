<?php

namespace Admin\Form;

use Zend\Form\Form;
use Admin\Model\Status;
use Admin\Model\StatusTable;
use Zend\Db\Adapter\AdapterInterface;

class BannerUploadForm extends Form
{

    private $status;

    public function __construct(StatusTable $status)
    {
        $this->status = $status;

        // we want to ignore the name passed
        parent::__construct('bannerupload');

        $this->setAttribute('method', 'post');
        $this->setAttribute('enctype', 'multipart/form-data');


        $this->add(array(
            'name' => 'id',
            'type' => 'Hidden',
        ));

        $this->add(array(
            'name' => 'booking_id',
            'type' => 'Hidden',
        ));

        $this->add(array(
            'name' => 'user_id',
            'type' => 'Hidden',
        ));

        $this->add(array(
            'name' => 'banner_type_id',
            'type' => 'Hidden',
        ));

        $this->add(array(
            'name' => 'banner_title',
            'type' => 'Text',
            'options' => array(
                'label' => 'Banner Title',
            ),
            'attributes' => array(
                'class' => 'form-control input-large'
            )
        ));

        $this->add(array(
            'name' => 'banner_content',
            'attributes' => array(
                'type' => 'file',
            ),
            'options' => array(
                'label' => 'Banner',
            )
        ));

        $this->add(array(
            'name' => 'banner_content',
            'type' => 'Zend\Form\Element\Textarea',
            'options' => array(
                'label' => 'Banner',
            ),
            'attributes' => array(
                'class' => 'ckeditor form-control',
            ),
        ));

        $this->add(array(
            'name' => 'target_url',
            'type' => 'Text',
            'options' => array(
                'label' => 'Target Url',
            ),
            'attributes' => array(
                'class' => 'form-control input-large'
            )
        ));

        $this->add(array(
            'name' => 'banner_type',
            'type' => 'Radio',
            'options' => array(
                'label' => 'Banner Type',
                'label_attributes' => array(
                    'class' => 'radio-inline'
                ),
                'value_options' => array(array('value' => 1, 'label' => 'Image', 'selected' => 'selected'),
                    //array('value' => 2, 'label'  => 'Video'), 
                    //array('value' => 3, 'label' => 'Text')
                    ),
            ),
            'attributes' => array(
                'class' => 'fieldToggle',
            )
        ));

        $this->add(array(
            'name' => 'status_id',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'Status',
                'value_options' => $this->getStatus(),
                'empty_option' => '--- Select Status ---'
            ),
            'attributes' => array(
                'class' => 'form-control input-large',
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

    function getStatus()
    {
        $results = $this->status->fetchAll(false, array(1, 2));

        $selectData = array();

        foreach ($results as $result) {
            $selectData[$result->status_id] = ucwords($result->status);
        }

        return $selectData;
    }

}
