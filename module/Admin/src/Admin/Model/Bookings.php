<?php

/*
 * For more validation refer to
 * http://framework.zend.com/manual/2.0/en/modules/zend.validator.set.html 
 * */

namespace Admin\Model;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\Db\Adapter\Adapter;

class Bookings implements InputFilterAwareInterface
{

    public $id;
    public $user_id;
    public $service_provider_id;
    public $service_address_id;
    public $service_provider_service_id;
    public $invoice_id;
    public $created_date;
    public $modified_date;
    public $modified_by;
    public $parent_booking_id;
    public $booked_date;
    public $status_id;
    public $payment_status;
    public $PaymentStatus;

    /* Dashboard data fields */
    public $total;
    public $state_id;
    public $state_name;
    public $month;
    
    /* Booking suggestion history */
    public $suggestion_id;
    public $booking_time;
    public $booking_status;


    /* Users table fields */
    public $first_name;
    public $last_name;
    public $email;
    public $currency;

    /* Service provider contact table fields */
    public $sp_first_name;
    public $sp_last_name;
    public $sp_email;

    /* Service provider service table fields */
    public $service_id;
    public $duration;
    public $price;

    /* Service category table fields */
    public $category_name;

    /* Invoice table fields */
    public $invoice_total;
    public $site_commision;
    public $invoice_status;

    /* Invoice details table fields */
    public $sale_item_details;

    /* Payment history table fields */
    public $payment_method_id;
    public $payment_instrument_no;
    public $amount_paid;
    public $payment_status_id;

    /* Status table fields */
    public $status;
    public $adapter;  // DB adapter
    protected $inputFilter;

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->user_id = (!empty($data['user_id'])) ? $data['user_id'] : null;
        $this->service_provider_id = (!empty($data['service_provider_id'])) ? $data['service_provider_id'] : null;
        $this->service_address_id = (!empty($data['service_address_id'])) ? $data['service_address_id'] : null;
        $this->service_provider_service_id = (!empty($data['service_provider_service_id'])) ? $data['service_provider_service_id'] : null;
        $this->invoice_id = (!empty($data['invoice_id'])) ? $data['invoice_id'] : 0;
        $this->created_date = (!empty($data['created_date'])) ? $data['created_date'] : null;
        $this->modified_date = (!empty($data['modified_date'])) ? $data['modified_date'] : null;
        $this->modified_by = (!empty($data['modified_by'])) ? $data['modified_by'] : null;
        $this->parent_booking_id = (!empty($data['parent_booking_id'])) ? $data['parent_booking_id'] : null;
        $this->booked_date = (!empty($data['booked_date'])) ? $data['booked_date'] : null;
        $this->status_id = (!empty($data['status_id'])) ? $data['status_id'] : 0;
        $this->payment_status = (!empty($data['payment_status'])) ? $data['payment_status'] : 0;
        $this->PaymentStatus = (!empty($data['PaymentStatus'])) ? $data['PaymentStatus'] : null;
        
        $this->suggestion_id = (!empty($data['suggestion_id'])) ? $data['suggestion_id'] : 0;
        $this->booking_time = (!empty($data['booking_time'])) ? $data['booking_time'] : null;
        $this->booking_status = (!empty($data['booking_status'])) ? $data['booking_status'] : 0;

        $this->total = (!empty($data['total'])) ? $data['total'] : 0;
        $this->state_id = (!empty($data['state_id'])) ? $data['state_id'] : 0;
        $this->state_name = (!empty($data['state_name'])) ? $data['state_name'] : null;
        $this->month = (!empty($data['month'])) ? $data['month'] : 0;

        $this->sp_first_name = (!empty($data['sp_first_name'])) ? $data['sp_first_name'] : null;
        $this->sp_last_name = (!empty($data['sp_last_name'])) ? $data['sp_last_name'] : null;
        $this->sp_email = (!empty($data['sp_email'])) ? $data['sp_email'] : null;

        $this->first_name = (!empty($data['first_name'])) ? $data['first_name'] : null;
        $this->last_name = (!empty($data['last_name'])) ? $data['last_name'] : null;
        $this->email = (!empty($data['email'])) ? $data['email'] : null;
        $this->currency = (!empty($data['currency'])) ? $data['currency'] : 'CAD';

        $this->service_id = (!empty($data['service_id'])) ? $data['service_id'] : null;
        $this->duration = (!empty($data['duration'])) ? $data['duration'] : null;
        $this->price = (!empty($data['price'])) ? $data['price'] : null;

        $this->category_name = (!empty($data['category_name'])) ? $data['category_name'] : null;

        $this->invoice_total = (!empty($data['invoice_total'])) ? $data['invoice_total'] : 0.00;
        $this->site_commision = (!empty($data['site_commision'])) ? $data['site_commision'] : 0.00;
        $this->invoice_status = (!empty($data['invoice_status'])) ? $data['invoice_status'] : 0;

        $this->sale_item_details = (!empty($data['sale_item_details'])) ? $data['sale_item_details'] : null;

        $this->payment_method_id = (!empty($data['payment_method_id'])) ? $data['payment_method_id'] : null;
        $this->payment_instrument_no = (!empty($data['payment_instrument_no'])) ? $data['payment_instrument_no'] : null;
        $this->amount_paid = (!empty($data['amount_paid'])) ? $data['amount_paid'] : null;
        $this->payment_status_id = (!empty($data['payment_status_id'])) ? $data['payment_status_id'] : null;

        $this->status = (!empty($data['status'])) ? $data['status'] : null;
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }

    public function setAdapter(Adapter $adapter)
    {
        $this->adapter = $adapter;
    }

    // Add content to this method:
    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }

    public function getInputFilter()
    {

        if (!$this->inputFilter) {

            $inputFilter = new InputFilter();

            $inputFilter->add(array(
                'name' => 'user_id',
                'required' => true,
            ));

            $inputFilter->add(array(
                'name' => 'service_provider_service_id',
                'required' => false,
            ));

            $inputFilter->add(array(
                'name' => 'duration',
                'required' => true,
            ));

            $inputFilter->add(array(
                'name' => 'booking_time',
                'required' => true,
            ));

            $inputFilter->add(array(
                'name' => 'booking_status',
                'required' => true,
            ));

            $inputFilter->add(array(
                'name' => 'invoice_total',
                'required' => true,
                'validators' => array(
                    array('name' => 'Float'),
                ),
            ));

            $inputFilter->add(array(
                'name' => 'site_commision',
                'required' => true,
                'validators' => array(
                    array('name' => 'Float'),
                ),
            ));

            $inputFilter->add(array(
                'name' => 'payment_instrument_no',
                'required' => true,
                'validators' => array(
                    array('name' => 'Digits'),
                ),
            ));

            $inputFilter->add(array(
                'name' => 'amount_paid',
                'required' => true,
                'validators' => array(
                    array('name' => 'Float'),
                ),
            ));

            $inputFilter->add(array(
                'name' => 'payment_status_id',
                'required' => true,
            ));

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }

}
