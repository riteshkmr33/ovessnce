<?php 

namespace Admin\Model;

use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\DataCache;
use Zend\Db\Sql\Select;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;
use Zend\Db\Sql\Expression;

class RevenuesTable
{
    protected $tableGateway;
    private $CacheKey = 'revenue';
    private $invoice;
    private $invoice_details;
    private $booking;
    private $banner_booking;
    private $user_subscriptions;
    private $user_address;
    
    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
        $adapter=$this->tableGateway->getAdapter();
        $this->invoice = new TableGateway('invoice', $adapter);
        $this->invoice_details = new TableGateway('invoice_details', $adapter);
        $this->booking = new TableGateway('booking', $adapter);
        $this->banner_booking = new TableGateway('banner_booking', $adapter);
        $this->user_subscriptions = new TableGateway('user_subscriptions', $adapter);
        $this->user_address = new TableGateway('user_address', $adapter);
    }
    
    public function fetchAll($paginate=true, $filter = array(), $orderBy=array())
    {
		if ($paginate) {
			
			$select = new Select('payment_history');
			$select->columns(array(new Expression("payment_history.id,payment_history.currency, payment_history.amount_paid, invoice.status_id AS invoice_status, 
				CASE invoice.status_id WHEN 0 THEN 'Unpaid' WHEN 1 THEN 'Paid' WHEN 2 THEN 'Partially Paid' END AS payment_status")));
			$select->join('invoice', 'invoice.id = payment_history.invoice_id', array('invoice_total', 'created_date', 'sale_type'), 'inner');
			$select->join('invoice_details', 'invoice_details.invoice_id = invoice.id', array('sale_item_details'), 'inner');
			$select->join('users', 'users.id = invoice.user_id', array('first_name', 'last_name'), 'inner');
			$select->join('user_address', 'user_address.user_id = users.id', array(), 'inner');
			$select->join('address', 'address.id = user_address.address_id', array('city'), 'inner');
			$select->join('state', 'state.id = address.state_id', array('state_name'), 'inner');
			$select->join('country', 'country.id = address.country_id', array('country_name'), 'inner');
			$select->join('subscription_duration', 'subscription_duration.id = invoice_details.subscription_duration_id', array(), 'left');
			$select->join('subscription', 'subscription.id = subscription_duration.subscription_id', array('subscription_name'), 'left');
			
			$select->join('lookup_status', 'lookup_status.status_id = payment_history.status_id', array('status'), 'left');
			
			/* Filter code starts */
			if (count($filter) > 0) {
				(isset($filter['name']) && $filter['name'] != "")?$select->where("CONCAT(users.first_name,' ',users.last_name) LIKE '%".$filter['name']."%'"):"";
				(isset($filter['product']) && $filter['product'] != "")?$select->where("CONCAT(invoice_details.sale_item_details) LIKE '%".$filter['product']."%'"):"";
				(isset($filter['city']) && $filter['city'] != "")?$select->where("address.city LIKE '%".$filter['city']."%'"):"";
				(isset($filter['state_id']) && $filter['state_id'] != "")?$select->where(array("address.state_id" => $filter['state_id'])):"";
				(isset($filter['country_id']) && $filter['country_id'] != "")?$select->where(array("address.country_id" => $filter['country_id'])):"";
				(isset($filter['subscription_id']) && $filter['subscription_id'] != "")?$select->where(array("subscription.id" => $filter['subscription_id'])):"";
				(isset($filter['status_id']) && $filter['status_id'] != "")?$select->where(array("invoice.status_id" => $filter['status_id'])):"";
				
				if (isset($filter['from_date']) && $filter['from_date'] != "" && isset($filter['to_date']) && $filter['to_date'] != "") {
					$select->where("DATE_FORMAT(invoice.created_date , '%Y-%m-%d') BETWEEN '".$filter['from_date']."' AND '".$filter['to_date']."'");
				} else if (isset($filter['from_date']) && isset($filter['to_date']) && $filter['from_date'] != "" && $filter['to_date'] == "") {
					$select->where("DATE_FORMAT(invoice.created_date , '%Y-%m-%d') = '".$filter['from_date']."'");
				} else if (isset($filter['from_date']) && isset($filter['to_date']) && $filter['from_date'] == "" && $filter['to_date'] != "") {
					$select->where("DATE_FORMAT(invoice.created_date , '%Y-%m-%d') = '".$filter['to_date']."'");
				}
			}
			/* Filter code ends */
			
			/* Data sorting code starts here */
			if (count($orderBy)>0 && $orderBy['sort_field'] != '' && $orderBy['sort_order'] != '') {
				switch ($orderBy['sort_field']) {
					case 'name' :
						$select->order('users.first_name '.$orderBy['sort_order']);
						break;
						
					case 'date' :
						$select->order('invoice.created_date '.$orderBy['sort_order']);
						break;
					
					case 'product' :
						$select->order('invoice_details.sale_item_details '.$orderBy['sort_order']);
						break;
					
					case 'city' :
						$select->order('address.city '.$orderBy['sort_order']);
						break;
					
					case 'state' :
						$select->order('state.state_name '.$orderBy['sort_order']);
						break;
					
					case 'country' :
						$select->order('country.country_name '.$orderBy['sort_order']);
						break;
						
					case 'status' :
						$select->order('invoice.status_id '.$orderBy['sort_order']);
						break;
						
					default :
						$select->order('invoice.created_date DESC');
						break;
					
				}
			} else {
				$select->order('invoice.created_date DESC');
			}
			/* Data sorting code ends here */
			
			//echo str_replace('"', '', $select->getSqlString()); exit;
			
			$resultSetPrototype = new ResultSet();
			$resultSetPrototype->setArrayObjectPrototype(new Revenues());
			
			$paginatorAdapter = new DbSelect(
				$select,
				$this->tableGateway->getAdapter(),
				$resultSetPrototype
			);
			$paginator = new Paginator($paginatorAdapter);
			
			return $paginator;
		} else {
			$select = new Select('payment_history');
			$select->columns(array(new Expression("payment_history.id, payment_history.amount_paid, invoice.status_id AS invoice_status, 
				CASE invoice.status_id WHEN 0 THEN 'Unpaid' WHEN 1 THEN 'Paid' WHEN 2 THEN 'Partially Paid' END AS payment_status")));
			$select->join('invoice', 'invoice.id = payment_history.invoice_id', array('invoice_total', 'created_date', 'sale_type'), 'inner');
			$select->join('users', 'users.id = invoice.user_id', array('first_name', 'last_name'), 'inner');
			$select->join('invoice_details', 'invoice_details.invoice_id = invoice.id', array('sale_item_details'), 'inner');
			$select->join('user_address', 'user_address.user_id = users.id', array(), 'inner');
			$select->join('address', 'address.id = user_address.address_id', array('city'), 'inner');
			$select->join('state', 'state.id = address.state_id', array('state_name'), 'inner');
			$select->join('country', 'country.id = address.country_id', array('country_name'), 'inner');
			$select->join('subscription_duration', 'subscription_duration.id = invoice_details.subscription_duration_id', array(), 'left');
			$select->join('subscription', 'subscription.id = subscription_duration.subscription_id', array('subscription_name'), 'left');
			$select->join('lookup_status', 'lookup_status.status_id = payment_history.status_id', array('status'), 'left');
			
			/* Filter code starts */
			if (count($filter) > 0) {
				(isset($filter['name']) && $filter['name'] != "")?$select->where("CONCAT(users.first_name,' ',users.last_name) LIKE '%".$filter['name']."%'"):"";
				(isset($filter['product']) && $filter['product'] != "")?$select->where("CONCAT(invoice_details.sale_item_details) LIKE '%".$filter['product']."%'"):"";
				(isset($filter['city']) && $filter['city'] != "")?$select->where("address.city LIKE '%".$filter['city']."%'"):"";
				(isset($filter['state_id']) && $filter['state_id'] != "")?$select->where(array("address.state_id" => $filter['state_id'])):"";
				(isset($filter['country_id']) && $filter['country_id'] != "")?$select->where(array("address.country_id" => $filter['country_id'])):"";
				(isset($filter['subscription_id']) && $filter['subscription_id'] != "")?$select->where(array("subscription.id" => $filter['subscription_id'])):"";
				(isset($filter['status_id']) && $filter['status_id'] != "")?$select->where(array("invoice.status_id" => $filter['status_id'])):"";
				if (isset($filter['from_date']) && $filter['from_date'] != "" && isset($filter['to_date']) && $filter['to_date'] != "") {
					$select->where("DATE_FORMAT(invoice.created_date , '%Y-%m-%d') BETWEEN '".$filter['from_date']."' AND '".$filter['to_date']."'");
				} else if (isset($filter['from_date']) && isset($filter['to_date']) && $filter['from_date'] != "" && $filter['to_date'] == "") {
					$select->where("DATE_FORMAT(invoice.created_date , '%Y-%m-%d') = '".$filter['from_date']."'");
				} else if (isset($filter['from_date']) && isset($filter['to_date']) && $filter['from_date'] == "" && $filter['to_date'] != "") {
					$select->where("DATE_FORMAT(invoice.created_date , '%Y-%m-%d') = '".$filter['to_date']."'");
				}
			}
			/* Filter code ends */
			
			
			return $this->tableGateway->selectwith($select);
		}
    }
    
    public function ExportAll($filter = array(), $orderBy=array())
    {
		$select = $this->tableGateway->getSql()->select();
		$select->columns(array('*', new Expression("service_provider.first_name AS sp_first_name, service_provider.last_name AS sp_last_name, service_provider.age AS sp_age, 
		CASE service_provider.gender WHEN 'M' THEN 'Male' WHEN 'F' THEN 'Female' END AS sp_gender, CASE users.gender WHEN 'M' THEN 'Male' WHEN 'F' THEN 'Female' END AS gender,
		parent_category.category_name AS parent_category,
		IF(service_provider_details.auth_to_issue_insurence_rem_receipt = 1, 'Yes', 'No') AS auth_to_issue_insurence_rem_receipt,
		IF(service_provider_details.treatment_for_physically_disabled_person = 1, 'Yes', 'No') AS treatment_for_physically_disabled_person,
		booking.id AS booking_id")));
		$select->join('invoice', 'invoice.id = payment_history.invoice_id', array('user_id', 'invoice_total', 'created_date', 'sale_type'), 'inner');
		$select->join('invoice_details', 'invoice_details.invoice_id = invoice.id', array('sale_item_details'), 'inner');
		
		$select->join('users', 'users.id = invoice.user_id', array('first_name', 'last_name', 'age'), 'inner');
		
		$select->join('user_address', 'user_address.user_id = users.id', array(), 'inner');
		$select->join('address', 'address.id = user_address.address_id', array('city'), 'inner');
		$select->join('state', 'state.id = address.state_id', array('state_name'), 'inner');
		$select->join('country', 'country.id = address.country_id', array('country_name'), 'inner');
		$select->join('subscription_duration', 'subscription_duration.id = invoice_details.subscription_duration_id', array(), 'left');
		$select->join('subscription', 'subscription.id = subscription_duration.subscription_id', array('subscription_name'), 'left');
		
		$select->join('booking', 'invoice.id = booking.invoice_id', array('booked_date', 'service_provider_id'), 'left');
		$select->join(array('service_provider' => 'users'), 'service_provider.id = booking.service_provider_id', array(), 'left');
		$select->join('service_provider_details', 'service_provider_details.user_id = booking.service_provider_id', array('degrees', 'years_of_experience', 'prof_membership'), 'left');
		
		$select->join('service_provider_service', 'service_provider_service.id = booking.service_provider_service_id', array('duration'), 'left');
		$select->join('service_category', 'service_category.id = service_provider_service.service_id', array('category_name'), 'left');
		$select->join(array('parent_category' => 'service_category'), 'parent_category.id = service_category.parent_id', array(), 'left');
		
		$select->join('banner_booking', 'invoice.id = banner_booking.invoice_id', array('booking_date'), 'left');
		$select->join('user_subscriptions', 'invoice.id = user_subscriptions.invoice_id', array('subscription_start_date'), 'left');
		$select->join('lookup_status', 'lookup_status.status_id = payment_history.status_id', array('status'), 'left');
		
		/* Filter code starts */
		if (count($filter) > 0) {
			(isset($filter['name']) && $filter['name'] != "")?$select->where("CONCAT(users.first_name,' ',users.last_name) LIKE '%".$filter['name']."%'"):"";
			(isset($filter['product']) && $filter['product'] != "")?$select->where("CONCAT(invoice_details.sale_item_details) LIKE '%".$filter['product']."%'"):"";
			(isset($filter['city']) && $filter['city'] != "")?$select->where("address.city LIKE '%".$filter['city']."%'"):"";
			(isset($filter['state_id']) && $filter['state_id'] != "")?$select->where(array("address.state_id" => $filter['state_id'])):"";
			(isset($filter['country_id']) && $filter['country_id'] != "")?$select->where(array("address.country_id" => $filter['country_id'])):"";
			(isset($filter['subscription_id']) && $filter['subscription_id'] != "")?$select->where(array("subscription.id" => $filter['subscription_id'])):"";
			(isset($filter['status_id']) && $filter['status_id'] != "")?$select->where(array("invoice.status_id" => $filter['status_id'])):"";
			if (isset($filter['from_date']) && $filter['from_date'] != "" && isset($filter['to_date']) && $filter['to_date'] != "") {
				$select->where("DATE_FORMAT(invoice.created_date , '%Y-%m-%d') BETWEEN '".$filter['from_date']."' AND '".$filter['to_date']."'");
			} else if (isset($filter['from_date']) && isset($filter['to_date']) && $filter['from_date'] != "" && $filter['to_date'] == "") {
				$select->where("DATE_FORMAT(invoice.created_date , '%Y-%m-%d') = '".$filter['from_date']."'");
			} else if (isset($filter['from_date']) && isset($filter['to_date']) && $filter['from_date'] == "" && $filter['to_date'] != "") {
				$select->where("DATE_FORMAT(invoice.created_date , '%Y-%m-%d') = '".$filter['to_date']."'");
			}
		}
		/* Filter code ends */
		
		//echo str_replace('"', '', $select->getSqlString()); exit;
		
		return $this->tableGateway->selectwith($select);
    }
    
    /* Function to get revenue stats */
    public function getRevenueStats($per = 'day')
    {
		switch ($per) {
			case 'day' :
				$SubsSelect = $this->tableGateway->getSql()->select();
				//$SubsSelect->columns(array(new Expression('COUNT(invoice.id) AS total_subs, SUM(payment_history.amount_paid) AS total_revenue')));
				$SubsSelect->columns(array(new Expression('COUNT(invoice.id) AS total_subs, SUM(CASE payment_history.currency WHEN "CAD" THEN payment_history.amount_paid END) AS total_crevenue,SUM(CASE payment_history.currency WHEN "USD" THEN payment_history.amount_paid END) AS total_urevenue')));
				$SubsSelect->join('invoice', 'invoice.id = payment_history.invoice_id', array(), 'inner');
				$SubsSelect->join('user_subscriptions', 'invoice.id = user_subscriptions.invoice_id', array(), 'inner');
				$SubsSelect->join('subscription_duration', 'subscription_duration.id = user_subscriptions.subscription_duration_id', array(), 'inner');
				$SubsSelect->join('subscription', 'subscription.id = subscription_duration.subscription_id', array('subscription_name'), 'inner');
				$SubsSelect->where("invoice.sale_type = 1 AND payment_history.status_id = 7 AND DATE_FORMAT(invoice.created_date, '%Y-%m-%d') = '".date('Y-m-d')."'");
				$SubsSelect->group('subscription.id');
				
				$BookSelect = $this->tableGateway->getSql()->select();
				//$BookSelect->columns(array(new Expression('COUNT(invoice.id) AS total_bookings, AVG(payment_history.amount_paid) AS avg_commision, SUM(payment_history.amount_paid) AS total_revenue')));
				$BookSelect->columns(array(new Expression('COUNT(invoice.id) AS total_bookings, AVG(CASE payment_history.currency WHEN "CAD" THEN payment_history.amount_paid END) AS avg_ccommision,AVG(CASE payment_history.currency WHEN "USD" THEN payment_history.amount_paid END) AS avg_ucommision, SUM(CASE payment_history.currency WHEN "CAD" THEN payment_history.amount_paid END) AS total_crevenue,SUM(CASE payment_history.currency WHEN "USD" THEN payment_history.amount_paid END) AS total_urevenue')));
				$BookSelect->join('invoice', 'invoice.id = payment_history.invoice_id', array(), 'inner');
				$BookSelect->where("invoice.sale_type = 3 AND payment_history.status_id = 7 AND DATE_FORMAT(invoice.created_date, '%Y-%m-%d') = '".date('Y-m-d')."'");
				
				$GrowthBookSelect = $this->tableGateway->getSql()->select();
				$GrowthBookSelect->columns(array(new Expression('COUNT(invoice.id) AS total_bookings, AVG(payment_history.amount_paid) AS avg_commision, SUM(payment_history.amount_paid) AS total_revenue')));
				$GrowthBookSelect->join('invoice', 'invoice.id = payment_history.invoice_id', array(), 'inner');
				$GrowthBookSelect->where("invoice.sale_type = 3 AND payment_history.status_id = 7 AND DATE_FORMAT(invoice.created_date, '%Y-%m-%d') = '".date('Y-m-d', strtotime('-1 days'))."'");
				
				break;
			
			case 'week' :
				$SubsSelect = $this->tableGateway->getSql()->select();
				//$SubsSelect->columns(array(new Expression('COUNT(invoice.id) AS total_subs, SUM(payment_history.amount_paid) AS total_revenue')));
				$SubsSelect->columns(array(new Expression('COUNT(invoice.id) AS total_subs, SUM(CASE payment_history.currency WHEN "CAD" THEN payment_history.amount_paid END) AS total_crevenue,SUM(CASE payment_history.currency WHEN "USD" THEN payment_history.amount_paid END) AS total_urevenue')));
				$SubsSelect->join('invoice', 'invoice.id = payment_history.invoice_id', array(), 'inner');
				$SubsSelect->join('user_subscriptions', 'invoice.id = user_subscriptions.invoice_id', array(), 'inner');
				$SubsSelect->join('subscription_duration', 'subscription_duration.id = user_subscriptions.subscription_duration_id', array(), 'inner');
				$SubsSelect->join('subscription', 'subscription.id = subscription_duration.subscription_id', array('subscription_name'), 'inner');
				$SubsSelect->where("invoice.sale_type = 1 AND payment_history.status_id = 7 AND DATE_FORMAT(invoice.created_date, '%Y-%m-%d') BETWEEN '".date('Y-m-d', strtotime('-6 days'))."' AND '".date('Y-m-d')."'");
				$SubsSelect->group('subscription.id');
				
				$BookSelect = $this->tableGateway->getSql()->select();
				//$BookSelect->columns(array(new Expression('COUNT(invoice.id) AS total_bookings, AVG(payment_history.amount_paid) AS avg_commision, SUM(payment_history.amount_paid) AS total_revenue')));
				$BookSelect->columns(array(new Expression('COUNT(invoice.id) AS total_bookings, AVG(CASE payment_history.currency WHEN "CAD" THEN payment_history.amount_paid END) AS avg_ccommision,AVG(CASE payment_history.currency WHEN "USD" THEN payment_history.amount_paid END) AS avg_ucommision, SUM(CASE payment_history.currency WHEN "CAD" THEN payment_history.amount_paid END) AS total_crevenue,SUM(CASE payment_history.currency WHEN "USD" THEN payment_history.amount_paid END) AS total_urevenue')));
				$BookSelect->join('invoice', 'invoice.id = payment_history.invoice_id', array(), 'inner');
				$BookSelect->where("invoice.sale_type = 3 AND payment_history.status_id = 7 AND DATE_FORMAT(invoice.created_date, '%Y-%m-%d') BETWEEN '".date('Y-m-d', strtotime('-6 days'))."' AND '".date('Y-m-d')."'");
				
				$GrowthBookSelect = $this->tableGateway->getSql()->select();
				$GrowthBookSelect->columns(array(new Expression('COUNT(invoice.id) AS total_bookings, AVG(payment_history.amount_paid) AS avg_commision, SUM(payment_history.amount_paid) AS total_revenue')));
				$GrowthBookSelect->join('invoice', 'invoice.id = payment_history.invoice_id', array(), 'inner');
				$GrowthBookSelect->where("invoice.sale_type = 3 AND payment_history.status_id = 7 AND DATE_FORMAT(invoice.created_date, '%Y-%m-%d') BETWEEN '".date('Y-m-d', strtotime('-12 days'))."' AND '".date('Y-m-d', strtotime('-6 days'))."'");
				
				break;
				
			case 'month' :
				$SubsSelect = $this->tableGateway->getSql()->select();
				//$SubsSelect->columns(array(new Expression('COUNT(invoice.id) AS total_subs, SUM(payment_history.amount_paid) AS total_revenue')));
				$SubsSelect->columns(array(new Expression('COUNT(invoice.id) AS total_subs, SUM(CASE payment_history.currency WHEN "CAD" THEN payment_history.amount_paid END) AS total_crevenue,SUM(CASE payment_history.currency WHEN "USD" THEN payment_history.amount_paid END) AS total_urevenue')));
				$SubsSelect->join('invoice', 'invoice.id = payment_history.invoice_id', array(), 'inner');
				$SubsSelect->join('user_subscriptions', 'invoice.id = user_subscriptions.invoice_id', array(), 'inner');
				$SubsSelect->join('subscription_duration', 'subscription_duration.id = user_subscriptions.subscription_duration_id', array(), 'inner');
				$SubsSelect->join('subscription', 'subscription.id = subscription_duration.subscription_id', array('subscription_name'), 'inner');
				$SubsSelect->where("invoice.sale_type = 1 AND payment_history.status_id = 7 AND DATE_FORMAT(invoice.created_date, '%Y-%m-%d') BETWEEN '".date('Y-m-d', strtotime('-1 month'))."' AND '".date('Y-m-d')."'");
				$SubsSelect->group('subscription.id');
				
				$BookSelect = $this->tableGateway->getSql()->select();
				//$BookSelect->columns(array(new Expression('COUNT(invoice.id) AS total_bookings, AVG(payment_history.amount_paid) AS avg_commision, SUM(payment_history.amount_paid) AS total_revenue')));
				$BookSelect->columns(array(new Expression('COUNT(invoice.id) AS total_bookings, AVG(CASE payment_history.currency WHEN "CAD" THEN payment_history.amount_paid END) AS avg_ccommision,AVG(CASE payment_history.currency WHEN "USD" THEN payment_history.amount_paid END) AS avg_ucommision, SUM(CASE payment_history.currency WHEN "CAD" THEN payment_history.amount_paid END) AS total_crevenue,SUM(CASE payment_history.currency WHEN "USD" THEN payment_history.amount_paid END) AS total_urevenue')));
				$BookSelect->join('invoice', 'invoice.id = payment_history.invoice_id', array(), 'inner');
				$BookSelect->where("invoice.sale_type = 3 AND payment_history.status_id = 7 AND DATE_FORMAT(invoice.created_date, '%Y-%m-%d') BETWEEN '".date('Y-m-d', strtotime('-1 month'))."' AND '".date('Y-m-d')."'");
				
				$GrowthBookSelect = $this->tableGateway->getSql()->select();
				$GrowthBookSelect->columns(array(new Expression('COUNT(invoice.id) AS total_bookings, AVG(payment_history.amount_paid) AS avg_commision, SUM(payment_history.amount_paid) AS total_revenue')));
				$GrowthBookSelect->join('invoice', 'invoice.id = payment_history.invoice_id', array(), 'inner');
				$GrowthBookSelect->where("invoice.sale_type = 3 AND payment_history.status_id = 7 AND DATE_FORMAT(invoice.created_date, '%Y-%m-%d') BETWEEN '".date('Y-m-d', strtotime('-2 month'))."' AND '".date('Y-m-d', strtotime('-1 month'))."'");
				
				break;
			
			case 'year' :
				$SubsSelect = $this->tableGateway->getSql()->select();
				//$SubsSelect->columns(array(new Expression('COUNT(invoice.id) AS total_subs, SUM(payment_history.amount_paid) AS total_revenue')));
				$SubsSelect->columns(array(new Expression('COUNT(invoice.id) AS total_subs, SUM(CASE payment_history.currency WHEN "CAD" THEN payment_history.amount_paid END) AS total_crevenue,SUM(CASE payment_history.currency WHEN "USD" THEN payment_history.amount_paid END) AS total_urevenue')));
							
				$SubsSelect->join('invoice', 'invoice.id = payment_history.invoice_id', array(), 'inner');
				$SubsSelect->join('user_subscriptions', 'invoice.id = user_subscriptions.invoice_id', array(), 'inner');
				$SubsSelect->join('subscription_duration', 'subscription_duration.id = user_subscriptions.subscription_duration_id', array(), 'inner');
				$SubsSelect->join('subscription', 'subscription.id = subscription_duration.subscription_id', array('subscription_name'), 'inner');
				$SubsSelect->where("invoice.sale_type = 1 AND payment_history.status_id = 7 AND DATE_FORMAT(invoice.created_date, '%Y-%m-%d') BETWEEN '".date('Y-m-d', strtotime('-1 year'))."' AND '".date('Y-m-d')."'");
				$SubsSelect->group('subscription.id');
				
				$BookSelect = $this->tableGateway->getSql()->select();
				$BookSelect->columns(array(new Expression('COUNT(invoice.id) AS total_bookings, AVG(CASE payment_history.currency WHEN "CAD" THEN payment_history.amount_paid END) AS avg_ccommision,AVG(CASE payment_history.currency WHEN "USD" THEN payment_history.amount_paid END) AS avg_ucommision, SUM(CASE payment_history.currency WHEN "CAD" THEN payment_history.amount_paid END) AS total_crevenue,SUM(CASE payment_history.currency WHEN "USD" THEN payment_history.amount_paid END) AS total_urevenue')));
				$BookSelect->join('invoice', 'invoice.id = payment_history.invoice_id', array(), 'inner');
				$BookSelect->where("invoice.sale_type = 3 AND payment_history.status_id = 7 AND DATE_FORMAT(invoice.created_date, '%Y-%m-%d') BETWEEN '".date('Y-m-d', strtotime('-1 year'))."' AND '".date('Y-m-d')."'");
				
				$GrowthBookSelect = $this->tableGateway->getSql()->select();
				$GrowthBookSelect->columns(array(new Expression('COUNT(invoice.id) AS total_bookings, AVG(payment_history.amount_paid) AS avg_commision, SUM(payment_history.amount_paid) AS total_revenue')));
				$GrowthBookSelect->join('invoice', 'invoice.id = payment_history.invoice_id', array(), 'inner');
				$GrowthBookSelect->where("invoice.sale_type = 3 AND payment_history.status_id = 7 AND DATE_FORMAT(invoice.created_date, '%Y-%m-%d') BETWEEN '".date('Y-m-d', strtotime('-2 year'))."' AND '".date('Y-m-d', strtotime('-1 year'))."'");
				
				break;
		}
		
		//echo '<pre>'.str_replace('"', '', $BookSelect->getSqlString()); exit;
		$subscriptions = $this->tableGateway->selectwith($SubsSelect);
		
		$bookings = $this->tableGateway->selectwith($BookSelect); 
		$Growthbookings = $this->tableGateway->selectwith($GrowthBookSelect);
		$total_revenue  = $bookings->current()->total_urevenue+$bookings->current()->total_crevenue;
		$new_revenue = $bookings->current()->total_urevenue.' USD +'.$bookings->current()->total_crevenue.' CAD';		
			
		$avg_commision = number_format($bookings->current()->avg_ucommision,2).' USD +'.number_format($bookings->current()->avg_ccommision,2).' CAD';
		
		$totalGrowth = (isset($Growthbookings->current()->total_revenue) && $Growthbookings->current()->total_revenue > 0)?round((($total_revenue-$Growthbookings->current()->total_revenue)/$Growthbookings->current()->total_revenue)*100):round(($total_revenue-$Growthbookings->current()->total_revenue)*100);
		$data = array('subscriptions' => array(), 'bookings' => array('total' => $bookings->current()->total_bookings, 'avg_commision' => $avg_commision, 'total_revenue' => $new_revenue, 'growth' => $totalGrowth));
		$data['revenue'] = $this->TotalRevenue($per);
		
		foreach ($subscriptions as $subscription) {
			
			$growth = $this->tableGateway->getSql()->select();
			$growth->columns(array(new Expression('COUNT(invoice.id) AS total_subs, SUM(payment_history.amount_paid) AS total_revenue')));
			
			$growth->join('invoice', 'invoice.id = payment_history.invoice_id', array(), 'inner');
			$growth->join('user_subscriptions', 'invoice.id = user_subscriptions.invoice_id', array(), 'inner');
			$growth->join('subscription_duration', 'subscription_duration.id = user_subscriptions.subscription_duration_id', array(), 'inner');
			$growth->join('subscription', 'subscription.id = subscription_duration.subscription_id', array('subscription_name'), 'inner');
			
			switch ($per) {
				case 'day' :
					$growth->where("invoice.sale_type = 1 AND payment_history.status_id = 7 AND DATE_FORMAT(invoice.created_date, '%Y-%m-%d') = '".date('Y-m-d', strtotime('-1 days'))."'");
					break;
					
				case 'week' :
					$growth->where("invoice.sale_type = 1 AND payment_history.status_id = 7 AND DATE_FORMAT(invoice.created_date, '%Y-%m-%d') BETWEEN '".date('Y-m-d', strtotime('-12 days'))."' AND '".date('Y-m-d', strtotime('-6 days'))."'");
					break;
					
				case 'month' :
					$growth->where("invoice.sale_type = 1 AND payment_history.status_id = 7 AND DATE_FORMAT(invoice.created_date, '%Y-%m-%d') BETWEEN '".date('Y-m-d', strtotime('-2 month'))."' AND '".date('Y-m-d', strtotime('-1 month'))."'");
					break;
					
				case 'year' :
					$growth->where("invoice.sale_type = 1 AND payment_history.status_id = 7 AND DATE_FORMAT(invoice.created_date, '%Y-%m-%d') BETWEEN '".date('Y-m-d', strtotime('-2 year'))."' AND '".date('Y-m-d', strtotime('-1 year'))."'");
					break;
			}
			
			$growth_result = $this->tableGateway->selectwith($growth);
			$growth = $growth_result->current();
			$growth->total_revenue = (isset($growth->total_revenue) && $growth->total_revenue > 0)?$growth->total_revenue:1;
			$subscription_revenue = $subscription->total_urevenue.' USD +'.$subscription->total_crevenue.' CAD';
			$data['subscriptions'][] = array('name' => $subscription->subscription_name, 'total' => $subscription->total_subs, 'cancelled' => 0, 'total_revenue' => $subscription_revenue, 'growth' => round((($subscription->total_revenue-$growth->total_revenue)/$growth->total_revenue)*100));
		}
		
		return $data;
	}
    
    public function getRevenue($id)
    {
        $id  = (int) $id;
        $select = $this->tableGateway->getSql()->select();
        $select->columns(array(new Expression("payment_history.invoice_id, payment_history.id, payment_history.amount_paid, invoice.status_id AS invoice_status, 
			CASE invoice.status_id WHEN 0 THEN 'Unpaid' WHEN 1 THEN 'Paid' WHEN 2 THEN 'Partially Paid' END AS payment_status")));
		$select->join('invoice', 'invoice.id = payment_history.invoice_id', array('invoice_total', 'created_date', 'sale_type'), 'inner');
		$select->join('users', 'users.id = invoice.user_id', array('first_name', 'last_name'), 'inner');
		$select->join('invoice_details', 'invoice_details.invoice_id = invoice.id', array('sale_item_details'), 'inner');
		$select->join('lookup_status', 'lookup_status.status_id = payment_history.status_id', array('status'), 'left');
		$select->where(array('payment_history.id' => $id));
		
        $rowset = $this->tableGateway->selectwith($select);
        $row = $rowset->current();
        if (!$row) {
			return false;
            //throw new \Exception("Could not find row $id");
        }
        return $row;
    }
    
    /* Function to get users adderess */
    public function getUserAddress($user_id)
    {
		$select = $this->user_address->getSql()->select();
		$select->join('address', 'address.id = user_address.address_id', array('city', 'zip_code'), 'left');
		$select->join('state', 'state.id = address.state_id', array('state_name'), 'left');
		$select->join('country', 'country.id = address.country_id', array('country_name'), 'left');
		$select->where(array('user_id' => $user_id));
		$rowset = $this->user_address->selectwith($select);
		return $rowset->current();
	}
	
	/* Function to fetch Invoice */
    public function getInvoice($invoice_id)
    {
		$result = $this->invoice->select(array('id' => $invoice_id));
		$row = $result->current();
        if (!$row) {
            return false;
        }
        return $row;
	}
	
	/* Function to fetch Invoice Details */
    public function getInvoiceDetails($invoice_id)
    {
		$result = $this->invoice_details->select(array('invoice_id' => $invoice_id));
		$row = $result->current();
        if (!$row) {
            return false;
        }
        return $row;
	}
    
	public function getRevenues()
	{
		$result = DataCache::getData($this->CacheKey);
		
		// Update cache if data not found
		if ($result == false) {
			$result = $this->fetchAll();
			
			// Update cache records
			DataCache::updateData($this->CacheKey,$result);
			
			// Get latest records
			$result = DataCache::getData($this->CacheKey);
		}
		return $result;
	}
	
	public function getfilteredData(array $filter)
	{
		if (count($filter) > 0) {
			$key = serialize($filter);
			$result = DataCache::getData($key);
		
			// Update cache if data not found
			if ($result == false) {
				$result = $this->tableGateway->select($filter);
				
				// Update cache records
				DataCache::updateData($key, $result);
			}
			return $result;
		}
	}
  
    public function changeStatus($id, $status)
    {
		$this->tableGateway->update(array('status_id' => $status),array('id' => $id));
	}

    public function deleteRevenue($id)
    {
		$data = $this->getRevenue($id);
		
		$this->invoice->delete(array('id' => $data->invoice_id));
		$this->invoice_details->delete(array('invoice_id' => $data->invoice_id));
		
		switch ($data->sale_type) {
			case 1 :
				$this->user_subscriptions->delete(array('invoice_id' => $data->invoice_id));
				break;
			case 2 :
				$this->banner_booking->delete(array('invoice_id' => $data->invoice_id));
				break;
			case 3 :
				$this->booking->delete(array('invoice_id' => $data->invoice_id));
				break;
			case 4 : 
				// other deletion code goes here
				break;
			
		}
		
        $this->tableGateway->delete(array('id' => (int) $id));
        
        // Update cache records
        //DataCache::updateData($this->CacheKey, $this->fetchAll());
    }
    
    public function CountNewOrders()
    {
		$select = $this->tableGateway->getSql()->select();
		$select->where('payment_date > NOW() - INTERVAL 4 DAY AND status_id = 7');
		$rowset = $this->tableGateway->selectwith($select);
		
		if( $rowset->count() > 0 ){
			return $rowset->count();
		}else{
			return false;
		}
	}
	
	public function TotalRevenue($per = 'day')
	{
		$select = $this->tableGateway->getSql()->select();
		//$select->columns(array(new Expression('SUM(amount_paid)  as amount_paid')));
		$select->columns(array(new Expression('SUM(CASE currency WHEN "CAD" THEN amount_paid END) as amount_cpaid , SUM(CASE currency WHEN "USD" THEN amount_paid END) as amount_upaid')));
		switch ($per) {
			case 'day' :
				$select->where("DATE_FORMAT(payment_date, '%Y-%m-%d') = '".date('Y-m-d')."'");
				break;
				
			case 'week' :
				$select->where("DATE_FORMAT(payment_date, '%Y-%m-%d') BETWEEN '".date('Y-m-d', strtotime('-6 days'))."' AND '".date('Y-m-d')."'");
				break;
				
			case 'month' :
				$select->where("DATE_FORMAT(payment_date, '%Y-%m-%d') BETWEEN '".date('Y-m-d', strtotime('-1 month'))."' AND '".date('Y-m-d')."'");
				break;
			
			case 'year' :
				$select->where("DATE_FORMAT(payment_date, '%Y-%m-%d') BETWEEN '".date('Y-m-d', strtotime('-1 year'))."' AND '".date('Y-m-d')."'");
				break;
		}
		
		$select->where('status_id = 7');
		
		$rowset = $this->tableGateway->selectwith($select);
		$row = $rowset->current();
		$amount = ceil($row->amount_upaid).' USD + '.ceil($row->amount_cpaid).' CAD';
		//return ceil($row->amount_paid);
		return $amount;
	}
}
