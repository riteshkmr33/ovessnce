<?php

namespace Admin\Form;

use Zend\Form\Form;
use Admin\Model\Users;
use Admin\Model\UsersTable;
use Zend\Session\Container;

class ComposeMessageForm extends Form
{

    private $users;
    private $current_user_id;
    private $user_name;

    public function __construct(UsersTable $users)
    {
        $this->users = $users;

        $user_details = new Container('user_details');
        $details = $user_details->details;

        $this->current_user_id = $details['user_id'];
        $this->user_name = $details['user_name'];

        // we want to ignore the name passed
        parent::__construct('composermessgae');

        $this->add(array(
            'name' => 'id',
            'type' => 'Hidden',
        ));

        $this->add(array(
            'name' => 'from_user_id',
            'type' => 'Hidden',
            'attributes' => array(
                'value' => $this->current_user_id,
            )
        ));

        $this->add(array(
            'name' => 'from_name',
            'type' => 'Text',
            'options' => array(
                'label' => 'From Name',
            ),
            'attributes' => array(
                'class' => 'form-control input-large',
                'value' => ucwords($this->user_name),
                'readonly' => 'true',
            )
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'to_user_id',
            'options' => array(
                'label' => 'Select User',
                'value_options' => $this->getUsers(),
            ),
            'attributes' => array(
                'class' => 'form-control input-large',
                'multiple' => 'multiple',
                'id' => 'e9',
            )
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
                'class' => 'form-control',
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

    public function getUsers()
    {

        $data = $this->users->fetchAll(false);
        $selectData = array();

        foreach ($data as $selectOption) {

            if ($selectOption->id != $this->current_user_id) {
                // not include current user into the recepie
                $selectData[$selectOption->id] = ucwords($selectOption->user_name);
            }
        }

        return $selectData;
    }

}
