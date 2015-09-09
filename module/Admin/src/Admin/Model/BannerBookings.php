<?php

namespace Admin\Model;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class BannerBookings implements InputFilterAwareInterface
{

    public $id;
    public $user_id;
    public $advertisement_plan_id;
    public $start_date;
    public $end_date;
    public $booking_date;
    public $invoice_id;
    public $status_id;

    /* advertisement plan table fields */
    public $page_name;
    public $duration;
    public $duration_in;
    public $price;

    /* Users table fields */
    public $first_name;
    public $last_name;

    /* Invoice table fields */
    public $invoice_total;
    public $payment_status;
    public $created_date;
    public $invoice_status;

    /* Invoice details table fields */
    public $sale_item_details;

    /* Payment history table fields */
    public $payment_method_id;
    public $payment_instrument_no;
    public $amount_paid;
    public $currency;

    /* lookup_status table field */
    public $status;
    protected $inputFilter;

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->user_id = (!empty($data['user_id'])) ? $data['user_id'] : null;
        $this->advertisement_plan_id = (!empty($data['advertisement_plan_id'])) ? $data['advertisement_plan_id'] : null;
        $this->start_date = (!empty($data['start_date'])) ? $data['start_date'] : null;
        $this->end_date = (!empty($data['end_date'])) ? $data['end_date'] : null;
        $this->booking_date = (!empty($data['booking_date'])) ? $data['booking_date'] : null;
        $this->invoice_id = (!empty($data['invoice_id'])) ? $data['invoice_id'] : null;
        $this->status_id = (!empty($data['status_id'])) ? $data['status_id'] : null;

        $this->plan_name = (!empty($data['plan_name'])) ? $data['plan_name'] : null;
        $this->duration = (!empty($data['duration'])) ? $data['duration'] : null;
        $this->duration_in = (!empty($data['duration_in'])) ? $data['duration_in'] : null;
        $this->price = (!empty($data['price'])) ? $data['price'] : null;

        $this->first_name = (!empty($data['first_name'])) ? $data['first_name'] : null;
        $this->last_name = (!empty($data['last_name'])) ? $data['last_name'] : null;

        $this->invoice_total = (!empty($data['invoice_total'])) ? $data['invoice_total'] : null;
        $this->payment_status = (!empty($data['payment_status'])) ? $data['payment_status'] : null;
        $this->created_date = (!empty($data['created_date'])) ? $data['created_date'] : null;
        $this->invoice_status = (!empty($data['invoice_status'])) ? $data['invoice_status'] : null;

        $this->sale_item_details = (!empty($data['sale_item_details'])) ? $data['sale_item_details'] : null;

        $this->payment_method_id = (!empty($data['payment_method_id'])) ? $data['payment_method_id'] : null;
        $this->payment_instrument_no = (!empty($data['payment_instrument_no'])) ? $data['payment_instrument_no'] : null;
        $this->amount_paid = (!empty($data['amount_paid'])) ? $data['amount_paid'] : null;
        $this->currency = (!empty($data['currency'])) ? $data['currency'] : null;

        $this->status = (!empty($data['status'])) ? $data['status'] : null;
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
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

            /* $inputFilter->add(array(
              'name'     => 'plan_name',
              'required' => true,
              'filters'  => array(
              array('name' => 'StripTags'),
              array('name' => 'StringTrim'),
              ),
              'validators' => array(
              array(
              'name'    => 'StringLength',
              'options' => array(
              'encoding' => 'UTF-8',
              'min'      => 1,
              'max'      => 100,
              ),
              ),
              ),
              )); */

            $inputFilter->add(array(
                'name' => 'user_id',
                'required' => true
            ));

            $inputFilter->add(array(
                'name' => 'advertisement_plan_id',
                'required' => true
            ));

            $inputFilter->add(array(
                'name' => 'invoice_total',
                'required' => true,
                'validators' => array(
                    array('name' => 'Float'),
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
                'name' => 'payment_instrument_no',
                'required' => true,
                'validators' => array(
                    array('name' => 'Digits'),
                ),
            ));

            $inputFilter->add(array(
                'name' => 'status_id',
                'required' => true
            ));

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }

}
