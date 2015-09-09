<?php

namespace Admin\Form;

use Zend\Form\Form;
use Admin\Model\Users;
use Admin\Model\UsersTable;
use Admin\Model\Services;
use Admin\Model\ServicesTable;
use Admin\Model\Bookings;
use Admin\Model\BookingsTable;
use Admin\Model\Status;
use Admin\Model\StatusTable;
use Admin\Model\ServiceProvider;
use Admin\Model\ServiceProviderTable;
use Zend\Db\Adapter\AdapterInterface;

class BookingForm extends Form
{

    private $users;
    private $addresses;
    private $practitioners;
    private $services;
    private $status;
    private $booking;
    private $id;

    public function __construct(UsersTable $users, ServiceProviderTable $practitioners, ServicesTable $services, BookingsTable $booking, StatusTable $status, $payment_methods, $addresses, $id = "")
    {
        $this->users = $users;
        $this->practitioners = $practitioners;
        $this->services = $services;
        $this->addresses = $addresses;
        $this->status = $status;
        $this->booking = $booking;
        $addresses;
        $this->id = $id;

        parent::__construct('booking');

        $this->add(array(
            'name' => 'id',
            'type' => 'Hidden',
        ));
        $this->add(array(
            'name' => 'suggestion_id',
            'type' => 'Hidden',
        ));
        $this->add(array(
            'name' => 'service_provider_service_id',
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
                'label' => 'Select Consumer',
                'value_options' => $this->getUsers(),
                'empty_option' => '--- Choose Consumer ---'
            ),
            'attributes' => array(
                'class' => 'form-control input-large',
            )
        ));
        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'service_provider_id',
            'options' => array(
                'label' => 'Select Practitioner',
                'value_options' => $this->getPractitioners(),
                'empty_option' => '--- Choose Practitioner ---'
            ),
            'attributes' => array(
                'class' => 'form-control input-large getServices'
            )
        ));
        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'service_id',
            'options' => array(
                'label' => 'Select Service',
                'value_options' => $this->getServices(),
                'empty_option' => '--- Choose Service ---',
            ),
            'attributes' => array(
                'class' => 'form-control input-large services getDuration',
                'disabled' => 'disabled'
            )
        ));
        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'service_address_id',
            'options' => array(
                'label' => 'Select Adress',
                'value_options' => $this->getServiceAddress(),
                'empty_option' => '--- Choose Address ---',
            ),
            'attributes' => array(
                'class' => 'form-control input-large address',
                'disabled' => 'disabled'
            )
        ));
        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'duration',
            'options' => array(
                'label' => 'Duration',
                'value_options' => $this->getDurations(),
                'empty_option' => '--- Choose Duration ---',
            ),
            'attributes' => array(
                'class' => 'form-control input-large duration getPrice',
                'disabled' => 'disabled'
            )
        ));

        $this->add(array(
            'name' => 'booking_time',
            'type' => 'Text',
            'options' => array(
                'label' => 'Booking Date',
            ),
            'attributes' => array(
                'class' => 'form-control input-large',
                'readonly' => 'true',
                'id' => 'booking_time'
            )
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'booking_status',
            'options' => array(
                'label' => 'Status',
                'value_options' => $this->getStatus(),
            ),
            'attributes' => array(
                'class' => 'form-control input-large'
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
                'readonly' => 'true',
            )
        ));

        $this->add(array(
            'name' => 'site_commision',
            'type' => 'Text',
            'options' => array(
                'label' => 'Site Commission',
            ),
            'attributes' => array(
                'class' => 'form-control input-large',
                'id' => 'siteCommision',
                'readonly' => 'true'
            )
        ));

        $this->add(array(
            'name' => 'payment_instrument_no',
            'type' => 'Text',
            'options' => array(
                'label' => 'Instrument Number',
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
                'value' => '0.00',
                'id' => 'siteCommision',
                'readonly' => 'true',
            )
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'payment_status_id',
            'options' => array(
                'label' => 'Payment Status',
                'value_options' => array(7 => 'Paid', 8 => 'Unpaid'),
                'empty_option' => '--- Choose Payment Status ---'
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

    public function getServices()
    {
        //$data = ($this->id != "") ? $this->services->getPractitionerServices($this->id) : $this->services->getPractitionerServices();
        $data = $this->services->getPractitionerServices();
        $selectData = array();

        foreach ($data as $value) {
            $selectData[$value['id']] = ucwords($value['service']);
        }

        return $selectData;
    }
    
    public function getServiceAddress()
    {
        //$data = ($this->id != "") ? $this->services->getPractitionerServices($this->id) : $this->services->getPractitionerServices();
        $data = $this->addresses;
        $selectData = array();
        
        foreach ($data as $value) {
            $selectData[$value['id']] = $value['address'];
        }

        return $selectData;
    }

    public function getStatus()
    {
        $data = $this->status->fetchAll(false, array(4, 5, 6));

        $selectData = array();

        foreach ($data as $selectOption) {
            $selectData[$selectOption->status_id] = ucwords($selectOption->status);
        }

        return $selectData;
    }

    public function getDurations()
    {
        $data = ($this->id != "") ? $this->services->getPractitionerServices($this->id, 'All') : $this->services->getPractitionerServices("", "All");
        $data = $this->services->getPractitionerServices("", "All");
        $selectData = array();

        foreach ($data as $value) {
            $selectData[$value['id']] = ucwords($value['duration']);
        }

        return $selectData;
    }

    public function getPractitioners()
    {
        $data = $this->practitioners->fetchAll(false);

        $selectData = array();

        foreach ($data as $selectOption) {
            $selectData[$selectOption->id] = ucwords($selectOption->first_name . " " . $selectOption->last_name);
        }

        return $selectData;
    }

    public function getUsers()
    {
        $data = $this->users->fetchAll(false, array('user_type' => 4));

        $selectData = array();

        foreach ($data as $selectOption) {
            $selectData[$selectOption->id] = ucwords($selectOption->first_name . " " . $selectOption->last_name);
        }

        return $selectData;
    }

    public function getBookings()
    {
        $data = $this->booking->fetchAll(false, array('payment_status' => 1, 'booking.status_id' => 4, 'parent_booking_id' => 0));

        $selectData = array();

        foreach ($data as $selectOption) {
            $selectData[$selectOption->id] = ucwords($selectOption->first_name . " " . $selectOption->last_name . " -> " . $selectOption->category_name . " - " . $selectOption->duration . " mins" . " (" . $selectOption->sp_first_name . " " . $selectOption->sp_last_name . ") : " . $selectOption->booked_date);
        }

        return $selectData;
    }

}
