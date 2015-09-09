<?php

namespace Admin\Form;

use Zend\Form\Form;

class SubscriptionFeaturesForm extends Form
{

    public function __construct($name = null)
    {
        // we want to ignore the name passed
        parent::__construct('countries');

        $this->add(array(
            'name' => 'id',
            'type' => 'Hidden',
        ));

        $this->add(array(
            'name' => 'feature_name',
            'type' => 'Text',
            'options' => array(
                'label' => 'Feature Name',
            ),
            'attributes' => array(
                'class' => 'form-control input-large',
                'disabled' => 'disabled'
            )
        ));
        $this->add(array(
            'name' => 'description',
            'type' => 'Text',
            'options' => array(
                'label' => 'Description',
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

}
