<?php

namespace Admin\Form;

use Zend\Form\Form;
use Admin\Model\Users;
use Admin\Model\UsersTable;
use Admin\Model\Status;
use Admin\Model\StatusTable;
use Admin\Model\AdvertisementPlan;
use Admin\Model\AdvertisementPlanTable;

class BannerBookingForm extends Form
{
    private $status;
    private $user;
    private $ap;

    public function __construct(AdvertisementPlanTable $ap, UsersTable $user, StatusTable $status, $payment_methods = array())
    {
        $this->status = $status;
        $this->user = $user;
        $this->ap = $ap;

        // we want to ignore the name passed
        parent::__construct('bannerplan');

        $this->add(array(
            'name' => 'id',
            'type' => 'Hidden',
        ));

        $this->add(array(
            'name' => 'invoice_id',
            'type' => 'Hidden',
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'user_id',
            'options' => array(
                'label' => 'Users',
                'value_options' => $this->getUsers(),
                'empty_option' => '--- Choose User ---'
            ),
            'attributes' => array(
                'class' => 'form-control input-large',
            )
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'advertisement_plan_id',
            'options' => array(
                'label' => 'Advertisement Plans',
                'value_options' => $this->getPlans(),
                'empty_option' => '--- Choose Advertisement Plans ---'
            ),
            'attributes' => array(
                'class' => 'form-control input-large',
                'id' => 'bannerPlans',
                'onchange' => 'if(this.value != "") { val = $("select#bannerPlans option:selected").text().split("@"); $("input#invoiceTotal, input#amountPaid").val(val[1])} else { $("input#invoiceTotal").val("")}'
            )
        ));

        /* Payment details */
        $this->add(array(
            'name' => 'payment_method_id',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'Payment Method',
                'value_options' => $payment_methods,
                'empty_option' => '--- Choose Payment Method ---'
            ),
            'attributes' => array(
                'class' => 'form-control input-large',
            )
        ));

        $this->add(array(
            'name' => 'invoice_total',
            'type' => 'Text',
            'options' => array(
                'label' => 'Invoice Total',
            ),
            'attributes' => array(
                'class' => 'form-control input-large',
                'id' => 'invoiceTotal',
                'readonly' => 'true'
            )
        ));

        $this->add(array(
            'name' => 'payment_instrument_no',
            'type' => 'Text',
            'options' => array(
                'label' => 'Instrument number',
            ),
            'attributes' => array(
                'class' => 'form-control input-large',
            )
        ));

        $this->add(array(
            'name' => 'amount_paid',
            'type' => 'Text',
            'options' => array(
                'label' => 'Amount Paid',
            ),
            'attributes' => array(
                'class' => 'form-control input-large',
                'id' => 'amountPaid',
                'readonly' => 'true',
                'value' => '0.00',
            )
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'status_id',
            'options' => array(
                'label' => 'Payment Status',
                'value_options' => $this->getStatus(),
                'empty_option' => '--- Choose Payment Status ---'
            ),
            'attributes' => array(
                'class' => 'form-control input-large',
            )
        ));
        
        $this->add(array(
            'name' => 'currency',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'Currency',
                'value_options' => array('CAD' => 'CAD', 'USD' => 'USD'),
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

    private function getPlans()
    {
        $data = $this->ap->fetchAll(false);

        $selectData = array();

        foreach ($data as $selectOption) {
            $selectData[$selectOption->id] = ucwords($selectOption->plan_name . " - " . $selectOption->duration . " " . $selectOption->duration_in . "@" . $selectOption->price);
        }

        return $selectData;
    }

    private function getUsers()
    {
        $data = $this->user->fetchAll(false, array('user_type' => array(1,6)));

        $selectData = array();

        foreach ($data as $selectOption) {
            $selectData[$selectOption->id] = ucwords($selectOption->first_name . " " . $selectOption->last_name);
        }

        return $selectData;
    }

    public function getStatus()
    {
        $data = $this->status->fetchAll(false, array(7, 8));

        $selectData = array();

        foreach ($data as $selectOption) {
            $selectData[$selectOption->status_id] = ucwords($selectOption->status);
        }

        return $selectData;
    }

}
