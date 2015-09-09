<?php

namespace Admin\Form;

use Zend\Form\Form;
use Admin\Model\Status;
use Admin\Model\StatusTable;
use Admin\Model\UserType;
use Admin\Model\UserTypeTable;
use Zend\Db\Adapter\AdapterInterface;

class NewsletterForm extends Form
{

    private $status;
    private $user_type;

    public function __construct(StatusTable $status, UserTypeTable $user_type)
    {
        $this->status = $status;
        $this->user_type = $user_type;
        parent::__construct('newsletters');

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
                'class' => 'form-control input-large'
            )
        ));

        $this->add(array(
            'name' => 'message',
            'type' => 'Zend\Form\Element\Textarea',
            'options' => array(
                'label' => 'Message',
            ),
            'attributes' => array(
                'class' => 'form-control input-large ckeditor'
            )
        ));

        $this->add(array(
            'name' => 'send_date',
            'type' => 'Text',
            'options' => array(
                'label' => 'Send Date',
            ),
            'attributes' => array(
                'class' => 'form-control'
            )
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'status_id',
            'options' => array(
                'label' => 'Status',
                'value_options' => $this->getStatus(),
            ),
            'attributes' => array(
                'class' => 'form-control input-large'
            )
        ));
        
        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'user_type_id',
            'options' => array(
                'label' => 'Send To',
                'value_options' => $this->getUserTypes(),
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
        $data = $this->status->fetchAll(false, array(1, 2, 3));

        $selectData = array();

        foreach ($data as $selectOption) {
            $selectData[$selectOption->status_id] = ucwords($selectOption->status);
        }

        return $selectData;
    }
    
    public function getUserTypes()
    {
        $data = $this->user_type->fetchAll(false, array(3,4,8));

        $selectData = array();
        $selectData[1] = 'ALL';

        foreach ($data as $selectOption) {
            $selectData[$selectOption->id] = ucwords($selectOption->user_type);
        }

        return $selectData;
    }

}
