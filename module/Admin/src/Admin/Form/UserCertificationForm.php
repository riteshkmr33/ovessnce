<?php

namespace Admin\Form;

use Zend\Form\Form;
use Admin\Model\Users;
use Admin\Model\UsersTable;
use Admin\Model\Status;
use Admin\Model\StatusTable;
use Zend\Db\Adapter\AdapterInterface;

class UserCertificationForm extends Form
{

    private $usersList;
    private $status;

    public function __construct(UsersTable $usersList, StatusTable $status)
    {

        $this->usersList = $usersList;
        $this->status = $status;

        // we want to ignore the name passed
        parent::__construct('user-certifications');

        $this->setAttribute('method', 'post');
        $this->setAttribute('enctype', 'multipart/form-data');


        $this->add(array(
            'name' => 'id',
            'type' => 'Hidden',
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'user_id',
            'options' => array(
                'label' => 'Select User',
                'value_options' => $this->getUsersList(),
                'empty_option' => '--- Select User ---'
            ),
            'attributes' => array(
                'class' => 'form-control'
            )
        ));

        $this->add(array(
            'name' => 'title',
            'type' => 'Text',
            'options' => array(
                'label' => 'Title',
            ),
            'attributes' => array(
                'class' => 'form-control'
            )
        ));
        
        $this->add(array(
            'name' => 'professional_licence_number',
            'type' => 'Text',
            'options' => array(
                'label' => 'Professional Licence Number',
            ),
            'attributes' => array(
                'class' => 'form-control'
            )
        ));

        $this->add(array(
            'name' => 'logo',
            'attributes' => array(
                'type' => 'file',
            ),
            'options' => array(
                'label' => 'Logo',
            ),
        ));

        $this->add(array(
            'name' => 'organization_name',
            'type' => 'Text',
            'options' => array(
                'label' => 'Organization Name',
            ),
            'attributes' => array(
                'class' => 'form-control'
            )
        ));

        $this->add(array(
            'name' => 'certification_date',
            'type' => 'Zend\Form\Element\Date',
            'options' => array(
                'label' => 'Certification Date',
            ),
            'attributes' => array(
                'class' => 'form-control date-picker',
                'data-date-format' => 'yyyy-mm-dd',
            )
        ));

        $this->add(array(
            'name' => 'validity',
            'type' => 'Zend\Form\Element\Date',
            'options' => array(
                'label' => 'Expiration Date',
            ),
            'attributes' => array(
                'class' => 'form-control date-picker',
                'data-date-format' => 'yyyy-mm-dd',
            )
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
                'class' => 'form-control',
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

    public function getUsersList()
    {
        $data = $this->usersList->fetchAll(false);

        $selectData = array();

        foreach ($data as $selectOption) {
            $selectData[$selectOption->id] = ucwords($selectOption->user_name);
        }

        return $selectData;
    }
    
    public function getStatus()
    {
        $data = $this->status->fetchAll(false, array(12,13,14));

        $selectData = array();

        foreach ($data as $selectOption) {
            $selectData[$selectOption->status_id] = ucwords($selectOption->status);
        }

        return $selectData;
    }

}
