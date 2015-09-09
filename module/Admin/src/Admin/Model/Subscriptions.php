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

class Subscriptions implements InputFilterAwareInterface
{
	public $id;
	public $user_id;
	public $subscription_duration_id;
	public $subscription_start_date;
	public $subscription_end_date;
	public $currency;
	public $invoice_id;
	public $status_id;
    
    /* Dashboard data fields */
    public $total;
    public $state_id;
    public $state_name;
    public $month;
    
    /* users table field */
    public $first_name;
    public $last_name;
    
    /* subscription table field */
    public $susbcription_name;
    
    /* subscription duration table field */
    public $duration;
    public $duration_in;
    public $price;
    
    /* invoice table field */
    public $invoice_status;
    public $payment_status;
    public $invoice_total;
    public $created_by;
    
    /* payment history table field */
    public $payment_method_id;
    public $payment_instrument_no;
    public $amount_paid;
    public $payment_status_id;
    
    /* lookup_status table field */
    public $status;
    
	public $adapter;  // DB adapter
    
    protected $inputFilter;

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
		$this->user_id = (!empty($data['user_id'])) ? $data['user_id'] : null;
		$this->subscription_duration_id = (!empty($data['subscription_duration_id'])) ? $data['subscription_duration_id'] : null;
		$this->subscription_start_date = (!empty($data['subscription_start_date'])) ? $data['subscription_start_date'] : null;
		$this->subscription_end_date = (!empty($data['subscription_end_date'])) ? $data['subscription_end_date'] : null;
		$this->invoice_id = (!empty($data['invoice_id'])) ? $data['invoice_id'] : null;
		$this->status_id = (!empty($data['status_id'])) ? $data['status_id'] : 0;
		$this->currency = (!empty($data['currency'])) ? $data['currency'] : 0;
		
		$this->total = (!empty($data['total'])) ? $data['total'] : 0;
		$this->state_id = (!empty($data['state_id'])) ? $data['state_id'] : null;
		$this->state_name = (!empty($data['state_name'])) ? $data['state_name'] : null;
		$this->month = (!empty($data['month'])) ? $data['month'] : 0;
		
		$this->duration = (!empty($data['duration'])) ? $data['duration'] : 0;
		$this->duration_in = (!empty($data['duration_in'])) ? $data['duration_in'] : null;
		$this->price = (!empty($data['price'])) ? $data['price'] : 0;
		
		$this->first_name = (!empty($data['first_name'])) ? $data['first_name'] : null;
		$this->last_name = (!empty($data['last_name'])) ? $data['last_name'] : null;
		
		$this->invoice_status = (!empty($data['invoice_status'])) ? $data['invoice_status'] : 0;
		$this->payment_status = (!empty($data['payment_status'])) ? $data['payment_status'] : null;
		$this->invoice_total = (!empty($data['invoice_total'])) ? $data['invoice_total'] : null;
		$this->created_by = (!empty($data['created_by'])) ? $data['created_by'] : null;
		
		$this->payment_method_id = (!empty($data['payment_method_id'])) ? $data['payment_method_id'] : null;
		$this->payment_instrument_no = (!empty($data['payment_instrument_no'])) ? $data['payment_instrument_no'] : null;
		$this->amount_paid = (!empty($data['amount_paid'])) ? $data['amount_paid'] : null;
		$this->payment_status_id = (!empty($data['payment_status_id'])) ? $data['payment_status_id'] : null;
		
		$this->subscription_name = (!empty($data['subscription_name'])) ? $data['subscription_name'] : null;
		
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
            
            $inputFilter->add(array(
                'name'     => 'user_id',
                'required' => true,
            ));
            
            $inputFilter->add(array(
                'name'     => 'subscription_duration_id',
                'required' => true,
            ));
            
            /*$inputFilter->add(array(
                'name'     => 'subscription_start_date',
                'required' => true,
            ));
            
            $inputFilter->add(array(
                'name'     => 'subscription_end_date',
                'required' => true,
            ));
            */
            
            $inputFilter->add(array(
                'name'     => 'status_id',
                'required' => true,
            ));
            
            $inputFilter->add(array(
                'name'     => 'payment_instrument_no',
                'required' => true,
            ));
            
            
            $this->inputFilter = $inputFilter;
            
        }
        
        return $this->inputFilter;
	
	}

}
		
