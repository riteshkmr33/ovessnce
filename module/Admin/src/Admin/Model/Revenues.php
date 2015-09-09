<?php

namespace Admin\Model;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Revenues implements InputFilterAwareInterface
{
	public $id;
    public $invoice_id;
    public $payment_method_id;
    public $payment_instrument_no;
    public $payment_date;
    
    public $currency;
    public $transaction_charge;
    public $response_code;
    public $auth_code;
    public $transaction_id;
    public $avs_response;
    public $response_message;
    public $status_id;
    
    /* fields for dashboard data */
    public $total_subs;
    public $total_revenue;
    public $total_urevenue;
    public $total_crevenue;
    public $total_bookings;
    public $avg_commision;
    public $avg_ccommision;
    public $avg_ucommision;
    public $amount_paid;
    public $amount_cpaid;
    public $amount_upaid;
    
    /* users table field */
    public $first_name;
    public $last_name;
    
    /* booking table field */
    public $booked_date;
    public $booking_id;
    
    /* users table fields as service provider */
    public $sp_first_name;
    public $sp_last_name;
    public $sp_age;
    public $sp_gender;
    
    /* address table field */
    public $city;
    public $zip_code;
    
    /* state table field */
    public $state_name;
    
    /* country table field */
    public $country_name;
    
    /* service category table field */
    public $parent_category;
    public $category_name;
    
    /* service provider service table field */
    public $duration;
    
    /* service provider details table field */
    public $degrees;
    public $years_of_experience;
    public $prof_membership;
    
    /* banner booking table field */
    public $banner_booking;
    
    /* user subscriptions table field */
    public $subscription_start_date;
    
    /* subscription table field */
    public $subscription_name;
    
    /* invoice table field */
    public $user_id;
    public $payment_status;
    public $sale_item_details;
    public $invoice_total;
    public $invoice_status;
    public $created_date;
    
    /* lookup_status table field */
    public $status;
    
    protected $inputFilter;

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->invoice_id = (!empty($data['invoice_id'])) ? $data['invoice_id'] : null;
		$this->payment_method_id = (!empty($data['payment_method_id'])) ? $data['payment_method_id'] : null;
		$this->payment_instrument_no = (!empty($data['payment_instrument_no'])) ? $data['payment_instrument_no'] : null;
		$this->payment_date = (!empty($data['payment_date'])) ? $data['payment_date'] : null;
		
		$this->currency = (!empty($data['currency'])) ? $data['currency'] : null;
		$this->transaction_charge = (!empty($data['transaction_charge'])) ? $data['transaction_charge'] : null;
		$this->response_code = (!empty($data['response_code'])) ? $data['response_code'] : null;
		$this->auth_code = (!empty($data['auth_code'])) ? $data['auth_code'] : null;
		$this->transaction_id = (!empty($data['transaction_id'])) ? $data['transaction_id'] : null;
		$this->avs_response = (!empty($data['avs_response'])) ? $data['avs_response'] : null;
		$this->response_message = (!empty($data['response_message'])) ? $data['response_message'] : null;
		$this->status_id = (!empty($data['status_id'])) ? $data['status_id'] : null;
		
		$this->total_subs = (!empty($data['total_subs'])) ? $data['total_subs'] : 0;
		$this->total_revenue = (!empty($data['total_revenue'])) ? $data['total_revenue'] : 0;
		$this->total_crevenue = (!empty($data['total_crevenue'])) ? $data['total_crevenue'] : 0;
		$this->total_urevenue = (!empty($data['total_urevenue'])) ? $data['total_urevenue'] : 0;
		$this->total_bookings = (!empty($data['total_bookings'])) ? $data['total_bookings'] : 0;
		$this->avg_commision = (!empty($data['avg_commision'])) ? $data['avg_commision'] : 0;
		$this->avg_ccommision = (!empty($data['avg_ccommision'])) ? $data['avg_ccommision'] : 0;
		$this->avg_ucommision = (!empty($data['avg_ucommision'])) ? $data['avg_ucommision'] : 0;
		$this->amount_paid = (!empty($data['amount_paid'])) ? $data['amount_paid'] : null;
		$this->amount_cpaid = (!empty($data['amount_cpaid'])) ? $data['amount_cpaid'] : null;
		$this->amount_upaid = (!empty($data['amount_upaid'])) ? $data['amount_upaid'] : null;
		
		$this->first_name = (!empty($data['first_name'])) ? $data['first_name'] : null;
		$this->last_name = (!empty($data['last_name'])) ? $data['last_name'] : null;
		$this->age = (!empty($data['age'])) ? $data['age'] : null;
		$this->gender = (!empty($data['gender'])) ? $data['gender'] : null;
		
		$this->city = (!empty($data['city'])) ? $data['city'] : null;
		$this->sp_city = (!empty($data['sp_city'])) ? $data['sp_city'] : null;
		$this->zip_code = (!empty($data['zip_code'])) ? $data['zip_code'] : null;
		$this->sp_zip_code = (!empty($data['sp_zip_code'])) ? $data['sp_zip_code'] : null;
		
		$this->state_name = (!empty($data['state_name'])) ? $data['state_name'] : null;
		$this->sp_state_name = (!empty($data['sp_state_name'])) ? $data['sp_state_name'] : null;
		
		$this->country_name = (!empty($data['country_name'])) ? $data['country_name'] : null;
		$this->sp_country_name = (!empty($data['sp_country_name'])) ? $data['sp_country_name'] : null;
		
		$this->service_provider_id = (!empty($data['service_provider_id'])) ? $data['service_provider_id'] : 0;
		$this->booked_date = (!empty($data['booked_date'])) ? $data['booked_date'] : null;
		$this->booking_id = (!empty($data['booking_id'])) ? $data['booking_id'] : null;
		
		$this->sp_first_name = (!empty($data['sp_first_name'])) ? $data['sp_first_name'] : null;
		$this->sp_last_name = (!empty($data['sp_last_name'])) ? $data['sp_last_name'] : null;
		$this->sp_age = (!empty($data['sp_age'])) ? $data['sp_age'] : null;
		$this->sp_gender = (!empty($data['sp_gender'])) ? $data['sp_gender'] : null;
		
		$this->duration = (!empty($data['duration'])) ? $data['duration'] : 0;
		
		$this->degrees = (!empty($data['degrees'])) ? $data['degrees'] : null;
		$this->years_of_experience = (!empty($data['years_of_experience'])) ? $data['years_of_experience'] : 0;
		$this->prof_membership = (!empty($data['prof_membership'])) ? $data['prof_membership'] : null;
		$this->auth_to_issue_insurence_rem_receipt = (!empty($data['auth_to_issue_insurence_rem_receipt'])) ? $data['auth_to_issue_insurence_rem_receipt'] : null;
		$this->treatment_for_physically_disabled_person = (!empty($data['treatment_for_physically_disabled_person'])) ? $data['treatment_for_physically_disabled_person'] : null;
		
		$this->parent_category = (!empty($data['parent_category'])) ? $data['parent_category'] : null;
		$this->category_name = (!empty($data['category_name'])) ? $data['category_name'] : null;
		
		$this->booking_date = (!empty($data['booking_date'])) ? $data['booking_date'] : null;
		
		$this->subscription_start_date = (!empty($data['subscription_start_date'])) ? $data['subscription_start_date'] : null;
		$this->subscription_name = (!empty($data['subscription_name'])) ? $data['subscription_name'] : null;
		
		$this->user_id = (!empty($data['user_id'])) ? $data['user_id'] : 0;
		$this->payment_status = (!empty($data['payment_status'])) ? $data['payment_status'] : null;
		$this->sale_type = (!empty($data['sale_type'])) ? $data['sale_type'] : null;
		$this->sale_item_details = (!empty($data['sale_item_details'])) ? $data['sale_item_details'] : null;
		$this->invoice_total = (!empty($data['invoice_total'])) ? $data['invoice_total'] : null;
		$this->invoice_status = (!empty($data['invoice_status'])) ? $data['invoice_status'] : 0;
		$this->created_date = (!empty($data['created_date'])) ? $data['created_date'] : null;
		
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
            
            $this->inputFilter = $inputFilter;
            
        }
        
        return $this->inputFilter;
	
	}

}
		
