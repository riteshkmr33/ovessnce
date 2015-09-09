<?php

namespace Application\Form;

use Zend\Form\Form;

class SearchForm extends Form
{

    public function __construct($treatment = array(), $location = array())
    {
        parent::__construct();
        $this->setAttribute('method', 'post');

        $this->add(array(
            'name' => 'idorname',
            'type' => 'text',
            'options' => array(
                'label' => 'ID or Full Name',
            ),
            'attributes' => array(
                'id' => 'idorname',
                'placeholder' => 'ID or Full Name',
            ),
        ));

        $this->add(array(
            'type' => 'text',
            'name' => 'location',
            'options' => array(
                'label' => 'Location',
            ),
            'attributes' => array(
                'class' => 'address-autofill',
                'placeholder' => 'ZIP code, City',
                'id' => 'search_location',
            ),
        ));
        
        /*$this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'location',
            'options' => array(
                'label' => 'Location',
                'value_options' => $location,
                'empty_option' => '-Select state-'
            ),
            'attributes' => array(
                'id' => 'search_location',
            ),
        ));*/

        $this->add(array(
            'name' => 'treatment',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'Treatment',
                'value_options' => $treatment,
                'empty_option' => '- Select Treatment -'
            ),
            'attributes' => array(
                'id' => 'treatment',
            ),
        ));

        $this->add(array(
            'name' => 'datetime',
            'type' => 'text',
            'options' => array(
                'label' => 'Time',
            ),
            'attributes' => array(
                'id' => 'datetime',
                'placeholder' => 'When ?',
                'readonly' => 'readonly',
            ),
        ));
    }

}

?>
