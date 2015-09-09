<?php

namespace Application\Form;

use Zend\Form\Form;

class CheckoutForm extends Form
{

    public function __construct($cardTypes = array())
    {
        parent::__construct();
        $this->setAttribute('method', 'post');

        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'card_type',
            'options' => array(
                'label' => 'Card Type',
                'value_options' => $cardTypes,
                'empty_option' => '- Select Card Type-'
            ),
            'attributes' => array(
                'required' => 'required',
                'id' => 'card_type',
                'style' => 'width: 100%',
            ),
        ));

        $this->add(array(
            'name' => 'name_on_card',
            'type' => 'text',
            'options' => array(
                'label' => 'Name on card',
            ),
            'attributes' => array(
                'required' => 'required',
                'id' => 'name_on_card',
            ),
        ));
        $this->add(array(
            'type' => 'text',
            'name' => 'card_no',
            'options' => array(
                'label' => 'Card Number',
            ),
            'attributes' => array(
                'required' => 'required',
                'id' => 'card_no',
                'autocomplete' => 'off',
                'maxlength' => '16'
            ),
        ));
        $this->add(array(
            'type' => 'text',
            'name' => 'emailid',
            'options' => array(
                'label' => 'Email id',
            ),
            'attributes' => array(
                'required' => 'required',
                'id' => 'emailid',
            ),
        ));
        $this->add(array(
            'type' => 'password',
            'name' => 'cvv_no',
            'options' => array(
                'label' => 'CVV Number',
            ),
            'attributes' => array(
                'required' => 'required',
                'id' => 'cvv_no',
                'autocomplete' => 'off',
                'maxlength' => '4'
            ),
        ));
        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'month',
            'options' => array(
                'label' => '',
                'value_options' => $this->getMonth(),
                'empty_option' => '- Month-'
            ),
            'attributes' => array(
                'required' => 'required',
                'id' => 'month',
            ),
        ));
        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'year',
            'options' => array(
                'label' => '',
                'value_options' => $this->getYear(),
                'empty_option' => '- Year -'
            ),
            'attributes' => array(
                'required' => 'required',
                'id' => 'year',
            ),
        ));
        $this->add(array(
            'type' => 'Zend\Form\Element\Radio',
            'name' => 'rememberme',
            'options' => array(
                'label' => '',
                'value_options' => array(
                    array('value' => '1', 'label' => 'Yes'),
                    array('value' => '0', 'label' => 'No', 'selected' => 'selected'),
                ),
            ),
            'attributes' => array(
                'class' => 'rememberme',
            ),
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Radio',
            'name' => 'useforrenew',
            'options' => array(
                'label' => '',
                'value_options' => array(
                    array('value' => '1', 'label' => 'Yes'),
                    array('value' => '0', 'label' => 'No', 'selected' => 'selected'),
                ),
            ),
            'attributes' => array(
                'class' => 'useforrenew',
            ),
        ));
        
        $this->add(array(
            'type' => 'Zend\Form\Element\Checkbox',
            'name' => 'termCondition',
            'options' => array(
                'label' => '',
                'value_options' => array(
                    array('value' => '1', 'label' => '')
                ),
            ),
            'attributes' => array(
                'id' => 'termCondition',
            ),
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Radio',
            'name' => 'cardtermCondition',
            'options' => array(
                'label' => '',
                'value_options' => array(
                    array('value' => '1', 'label' => 'Yes'),
                    array('value' => '0', 'label' => 'No', 'selected' => 'selected'),
                ),
            ),
            'attributes' => array(
                'id' => 'cardtermCondition',
            ),
        ));
        $this->add(array(
            'type' => 'Zend\Form\Element\Radio',
            'name' => 'autorenewtermCondition',
            'options' => array(
                'label' => '',
                'value_options' => array(
                    array('value' => '1', 'label' => 'Yes', 'selected' => 'selected'),
                    array('value' => '0', 'label' => 'No'),
                ),
            ),
            'attributes' => array(
                'id' => 'autorenewtermCondition',
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

    public function getMonth()
    {
        $getMonthVal = array();
        for ($i = 1; $i < 13; $i++) {
            $getMonthVal[$i] = $i;
        }
        return $getMonthVal;
    }

    public function getYear()
    {
        $getYearVal = array();
        for ($i = date('Y'); $i <= (date('Y') + 20); $i++) {
            $getYearVal[$i] = $i;
        }
        return $getYearVal;
    }

}

?>
