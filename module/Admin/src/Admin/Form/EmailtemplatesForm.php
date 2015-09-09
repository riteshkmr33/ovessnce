<?php

namespace Admin\Form;

use Zend\Form\Form;
use Admin\Model\Status;
use Admin\Model\StatusTable;

class EmailtemplatesForm extends Form
{

    private $status;

    public function __construct(StatusTable $status)
    {
        $this->status = $status;

        // we want to ignore the name passed
        parent::__construct('emailtemplates');

        $this->add(array(
            'name' => 'id',
            'type' => 'Hidden',
        ));

        $this->add(array(
            'name' => 'subject',
            'type' => 'Text',
            'options' => array(
                'label' => 'Subject',
            ),
            'attributes' => array(
                'class' => 'form-control input-large',
            ),
        ));

        $this->add(array(
            'name' => 'fromEmail',
            'type' => 'Zend\Form\Element\Email',
            'options' => array(
                'label' => 'From',
            ),
            'attributes' => array(
                'placeholder' => 'you@domain.com',
                'class' => 'form-control input-large'
            )
        ));

        $this->add(array(
            'name' => 'content',
            'type' => 'Zend\Form\Element\Textarea',
            'options' => array(
                'label' => 'Content',
            ),
            'attributes' => array(
                'class' => 'ckeditor form-control',
            ),
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Radio',
            'name' => 'status',
            'options' => array(
                'label_attributes' => array(
                    'class' => 'radio-inline'
                ),
                'label' => 'Status',
                'value_options' => $this->getStatus(),
                'attributes' => array(
                    'value' => '1'
                )
            ),
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
