<?php

namespace Admin\Form;

use Zend\Form\Form;
use Admin\Model\Status;
use Admin\Model\StatusTable;
use Admin\Model\FaqIndex;
use Admin\Model\FaqIndexTable;
use Admin\Model\Usertype;
use Admin\Model\UsertypeTable;

class FaqsForm extends Form
{
    private $faq_index;
    private $user_type;
    private $status;

    public function __construct(FaqIndexTable $faq_index, UsertypeTable $user_type, StatusTable $status)
    {
        $this->faq_index = $faq_index;
        $this->user_type = $user_type;
        $this->status = $status;

        // we want to ignore the name passed
        parent::__construct('faqs');

        $this->add(array(
            'name' => 'id',
            'type' => 'Hidden',
        ));

        $this->add(array(
            'type' => 'Text',
            'name' => 'question',
            'options' => array(
                'label' => 'Question',
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
            'type' => 'Zend\Form\Element\Textarea',
            'name' => 'answer',
            'options' => array(
                'label' => 'Answer',
            ),
            'attributes' => array(
                'class' => 'form-control input-large ckeditor'
            )
        ));
        
        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'user_type_id',
            'options' => array(
                'label' => 'User Type',
                'value_options' => $this->getUserType(),
                'empty_option' => '--- Select User Type ---'
            ),
            'attributes' => array(
                'class' => 'form-control input-large select2'
            )
        ));
        
        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'index_id',
            'options' => array(
                'label' => 'Index',
                'value_options' => $this->getFaqIndex(),
                'empty_option' => '--- Select Index ---'
            ),
            'attributes' => array(
                'class' => 'form-control input-large select2'
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

    public function getFaqIndex()
    {
        $data = $this->faq_index->fetchAll(false);

        $selectData = array();

        foreach ($data as $selectOption) {
            $selectData[$selectOption->id] = ucwords($selectOption->index_name);
        }

        return $selectData;
    }
    
    public function getUserType()
    {
        $data = $this->user_type->fetchAll(false, array(3,4));

        $selectData = array();

        foreach ($data as $selectOption) {
            $selectData[$selectOption->id] = ucwords($selectOption->user_type);
        }

        return $selectData;
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
