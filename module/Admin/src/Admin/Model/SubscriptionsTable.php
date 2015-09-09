<?php

namespace Admin\Model;

use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\DataCache;
use Zend\Db\Sql\Select;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;
use Zend\Db\Sql\Expression;

class SubscriptionsTable
{

    protected $tableGateway;
    private $CacheKey = 'subscriptions';

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
        $adapter = $this->tableGateway->getAdapter();
        $this->invoice = new TableGateway('invoice', $adapter);
        $this->invoice_details = new TableGateway('invoice_details', $adapter);
        $this->payment_history = new TableGateway('payment_history', $adapter);
        $this->subscription_feature = new TableGateway('subscription_feature', $adapter);
        $this->user_feature_setting = new TableGateway('user_feature_setting', $adapter);
    }

    public function fetchAll($paginate = true, $orderBy = array())
    {
        if ($paginate) {

            $select = new Select('user_subscriptions');
            $select->columns(array(new Expression('user_subscriptions.id, user_subscriptions.subscription_start_date, user_subscriptions.subscription_end_date, user_subscriptions.status_id,
			CASE subscription_duration.duration_in WHEN 1 THEN "Years" WHEN 2 THEN "Months" WHEN 3 THEN "Days" END AS duration_in, 
			invoice.status_id AS invoice_status, CASE invoice.status_id WHEN 0 THEN "Unpaid" WHEN 1 THEN "Paid" WHEN 2 THEN "Partially Paid" END AS payment_status')));
            $select->join('users', 'users.id = user_subscriptions.user_id', array('first_name', 'last_name'), 'inner');
            $select->join('subscription_duration', 'subscription_duration.id = user_subscriptions.subscription_duration_id', array('duration'), 'inner');
            $select->join('subscription', 'subscription.id = subscription_duration.subscription_id', array('subscription_name'), 'inner');
            $select->join('invoice', 'invoice.id = user_subscriptions.invoice_id', array('invoice_total'), 'inner');
            $select->join('lookup_status', 'lookup_status.status_id = user_subscriptions.status_id', array('status'), 'left');
            $select->join('payment_history', 'payment_history.invoice_id = user_subscriptions.invoice_id', array('currency'), 'inner');
            /* Data sorting code starts here */
            if (count($orderBy) > 0 && $orderBy['sort_field'] != '' && $orderBy['sort_order'] != '') {
                switch ($orderBy['sort_field']) {
                    case 'name' :
                        $select->order('users.first_name ' . $orderBy['sort_order']);
                        break;

                    case 'subscription' :
                        $select->order('subscription.subscription_name ' . $orderBy['sort_order']);
                        break;

                    case 'start' :
                        $select->order('user_subscriptions.subscription_start_date ' . $orderBy['sort_order']);
                        break;

                    case 'end' :
                        $select->order('user_subscriptions.subscription_end_date ' . $orderBy['sort_order']);
                        break;
                }
            }
            /* Data sorting code ends here */

            //echo str_replace('"','',$select->getSqlString()); exit;

            $resultSetPrototype = new ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new Subscriptions());

            $paginatorAdapter = new DbSelect(
                    $select, $this->tableGateway->getAdapter(), $resultSetPrototype
            );
            $paginator = new Paginator($paginatorAdapter);

            return $paginator;
        }
        return $this->tableGateway->select();
    }

    public function ExportAll($filter = array(), $orderBy = array())
    {
        $select = $this->tableGateway->getSql()->select();
        $select->columns(array(new Expression('user_subscriptions.id, user_subscriptions.subscription_start_date, user_subscriptions.subscription_end_date, user_subscriptions.status_id,
		CASE subscription_duration.duration_in WHEN 1 THEN "Years" WHEN 2 THEN "Months" WHEN 3 THEN "Days" END AS duration_in, 
		invoice.status_id AS invoice_status, CASE invoice.status_id WHEN 0 THEN "Unpaid" WHEN 1 THEN "Paid" WHEN 2 THEN "Partially Paid" END AS payment_status')));
        $select->join('users', 'users.id = user_subscriptions.user_id', array('first_name', 'last_name'), 'inner');
        $select->join('subscription_duration', 'subscription_duration.id = user_subscriptions.subscription_duration_id', array('duration'), 'inner');
        $select->join('subscription', 'subscription.id = subscription_duration.subscription_id', array('subscription_name'), 'inner');
        $select->join('invoice', 'invoice.id = user_subscriptions.invoice_id', array('invoice_total'), 'inner');
        $select->join('lookup_status', 'lookup_status.status_id = user_subscriptions.status_id', array('status'), 'left');

        /* Data sorting code starts here */
        if (count($orderBy) > 0 && $orderBy['sort_field'] != '' && $orderBy['sort_order'] != '') {
            switch ($orderBy['sort_field']) {
                case 'name' :
                    $select->order('users.first_name ' . $orderBy['sort_order']);
                    break;

                case 'subscription' :
                    $select->order('subscription.subscription_name ' . $orderBy['sort_order']);
                    break;

                case 'start' :
                    $select->order('user_subscriptions.subscription_start_date ' . $orderBy['sort_order']);
                    break;

                case 'end' :
                    $select->order('user_subscriptions.subscription_end_date ' . $orderBy['sort_order']);
                    break;
            }
        }
        /* Data sorting code ends here */

        return $this->tableGateway->selectwith($select);
    }

    public function getSubscription($id)
    {
        $id = (int) $id;
        $select = $this->tableGateway->getSql()->select();
        $select->columns(array('*', new Expression("payment_history.status_id AS payment_status_id, CASE subscription_duration.duration_in WHEN 1 THEN 'Years' WHEN 2 THEN 'Months' WHEN 3 THEN 'Days' END AS duration_in, 
		invoice.status_id AS invoice_status, CASE invoice.status_id WHEN 0 THEN 'Unpaid' WHEN 1 THEN 'Paid' WHEN 2 THEN 'Partially Paid' END AS payment_status")));
        $select->join('users', 'users.id = user_subscriptions.user_id', array('first_name', 'last_name'), 'inner');
        $select->join('subscription_duration', 'subscription_duration.id = user_subscriptions.subscription_duration_id', array('duration'), 'inner');
        $select->join('subscription', 'subscription.id = subscription_duration.subscription_id', array('subscription_name'), 'inner');
        $select->join('invoice', 'invoice.id = user_subscriptions.invoice_id', array('invoice_total'), 'inner');
        $select->join('payment_history', 'payment_history.invoice_id = invoice.id', array('payment_method_id', 'payment_instrument_no', 'amount_paid'), 'inner');
        $select->join('lookup_status', 'lookup_status.status_id = user_subscriptions.status_id', array('status'), 'left');
        $select->where(array('user_subscriptions.id' => $id));
        //echo str_replace('"','',$select->getSqlString()); exit;

        $rowset = $this->tableGateway->selectwith($select);
        $row = $rowset->current();
        if (!$row) {
            return false;
            //throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    /* Function to get subscriptions by states */

    public function getSubscriptionByStates($per = 'day')
    {
        $subquery = new Select('service_provider_address');
        $subquery->columns(array('*', new Expression('min(service_provider_address.address_id) AS adrs_id')));
        $subquery->group('user_id');

        switch ($per) {
            case 'day' :
                $select = $this->tableGateway->getSql()->select();
                $select->columns(array(new Expression('COUNT(address.state_id) AS total, state_name, address.state_id')));
                $select->join('invoice', 'invoice.id = user_subscriptions.invoice_id', array(), 'inner');
                $select->join(array('address_link' => $subquery), 'address_link.user_id = user_subscriptions.user_id', array(), 'inner');
                $select->join('address', 'address.id = address_link.address_id', array(), 'inner');
                $select->join('state', 'state.id = address.state_id', array(), 'inner');
                $select->where("DATE_FORMAT(invoice.created_date, '%Y-%m-%d') = '" . date('Y-m-d') . "'");
                $select->group('address.state_id');
                $select->limit(10);
                break;

            case 'week' :
                $select = $this->tableGateway->getSql()->select();
                $select->columns(array(new Expression('COUNT(address.state_id) AS total, state_name, address.state_id')));
                $select->join('invoice', 'invoice.id = user_subscriptions.invoice_id', array(), 'inner');
                $select->join(array('address_link' => $subquery), 'address_link.user_id = user_subscriptions.user_id', array(), 'inner');
                $select->join('address', 'address.id = address_link.address_id', array(), 'inner');
                $select->join('state', 'state.id = address.state_id', array(), 'inner');
                $select->where("DATE_FORMAT(invoice.created_date, '%Y-%m-%d') BETWEEN '" . date('Y-m-d', strtotime('-6 days')) . "' AND '" . date('Y-m-d') . "'");
                $select->group('address.state_id');
                $select->limit(10);
                break;

            case 'month' :
                $select = $this->tableGateway->getSql()->select();
                $select->columns(array(new Expression('COUNT(address.state_id) AS total, state_name, address.state_id')));
                $select->join('invoice', 'invoice.id = user_subscriptions.invoice_id', array(), 'inner');
                $select->join(array('address_link' => $subquery), 'address_link.user_id = user_subscriptions.user_id', array(), 'inner');
                $select->join('address', 'address.id = address_link.address_id', array(), 'inner');
                $select->join('state', 'state.id = address.state_id', array(), 'inner');
                $select->where("DATE_FORMAT(invoice.created_date, '%Y-%m-%d') BETWEEN '" . date('Y-m-d', strtotime('-1 month')) . "' AND '" . date('Y-m-d') . "'");
                $select->group('address.state_id');
                $select->limit(10);
                break;

            case 'year' :
                $select = $this->tableGateway->getSql()->select();
                $select->columns(array(new Expression('COUNT(address.state_id) AS total, state_name, address.state_id')));
                $select->join('invoice', 'invoice.id = user_subscriptions.invoice_id', array(), 'inner');
                $select->join(array('address_link' => $subquery), 'address_link.user_id = user_subscriptions.user_id', array(), 'inner');
                $select->join('address', 'address.id = address_link.address_id', array(), 'inner');
                $select->join('state', 'state.id = address.state_id', array(), 'inner');
                $select->where("DATE_FORMAT(invoice.created_date, '%Y-%m-%d') BETWEEN '" . date('Y-m-d', strtotime('-1 year')) . "' AND '" . date('Y-m-d') . "'");
                $select->group('address.state_id');
                $select->limit(10);
                break;
        }

        //echo str_replace('"', '', $select->getSqlString()); exit;
        $results = $this->tableGateway->selectwith($select);
        $subscriptions = array();
        $total = 0;

        foreach ($results as $result) {
            if ($result->state_name != "") {

                $subquery = new Select('service_provider_address');
                $subquery->columns(array('*', new Expression('min(service_provider_address.address_id) AS adrs_id')));
                $subquery->group('user_id');

                $growth = $this->tableGateway->getSql()->select();
                $growth->columns(array(new Expression('COUNT(address.state_id) AS total, state_name, address.state_id')));
                $growth->join('invoice', 'invoice.id = user_subscriptions.invoice_id', array(), 'inner');
                $growth->join(array('address_link' => $subquery), 'address_link.user_id = user_subscriptions.user_id', array(), 'inner');
                $growth->join('address', 'address.id = address_link.address_id', array(), 'inner');
                $growth->join('state', 'state.id = address.state_id', array(), 'inner');

                switch ($per) {
                    case 'day' :
                        $growth->where("address.state_id = " . $result->state_id . " AND DATE_FORMAT(invoice.created_date, '%Y-%m-%d') = '" . date('Y-m-d', strtotime('-1 days')) . "'");
                        break;

                    case 'week' :
                        $growth->where("address.state_id = " . $result->state_id . " AND DATE_FORMAT(invoice.created_date, '%Y-%m-%d') BETWEEN '" . date('Y-m-d', strtotime('-14 days')) . "' AND '" . date('Y-m-d', strtotime('-7 days')) . "'");
                        break;

                    case 'month' :
                        $growth->where("address.state_id = " . $result->state_id . " AND DATE_FORMAT(invoice.created_date, '%Y-%m-%d') BETWEEN '" . date('Y-m-d', strtotime('-2 month -1 days')) . "' AND '" . date('Y-m-d', strtotime('-1 month -1 days')) . "'");
                        break;

                    case 'year' :
                        $growth->where("address.state_id = " . $result->state_id . " AND DATE_FORMAT(invoice.created_date, '%Y-%m-%d') BETWEEN '" . date('Y-m-d', strtotime('-2 year')) . "' AND '" . date('Y-m-d', strtotime('-1 year')) . "'");
                        break;
                }

                $growth_result = $this->tableGateway->selectwith($growth);
                $growth = $growth_result->current();
                $totalGrowth = (isset($growth->total) && $growth->total > 0) ? round((($result->total - $growth->total) / $growth->total) * 100) : round(($result->total - $growth->total) * 100);

                $subscriptions[] = array('total' => $result->total, 'state_name' => $result->state_name, 'growth' => $totalGrowth);
                $total = $total + $result->total;
            }
        }

        // Calculating total %
        foreach ($subscriptions as $key => $value) {
            $subscriptions[$key]['total_percentage'] = round(($subscriptions[$key]['total'] / $total) * 100);
        }

        return $subscriptions;
    }

    /* Function to get current month data */

    public function getDataByMonth($start, $end)
    {
        $select = $this->tableGateway->getSql()->select();
        $select->columns(array(new Expression("count(*) as total, DATE_FORMAT(invoice.created_date, '%d-%m-%Y') as month")));
        $select->join('invoice', 'invoice.id = user_subscriptions.invoice_id', array(), 'inner');
        $select->where("DATE_FORMAT(invoice.created_date, '%Y-%m-%d') BETWEEN '" . date('Y-m-d', strtotime($start)) . "' AND '" . date('Y-m-d', strtotime($end)) . "'");
        $select->group("month");
        //echo str_replace('"', '', $select->getSqlString()); exit;
        $results = $this->tableGateway->selectwith($select);
        $data = array();

        foreach ($results as $result) {
            $data[$result->month] = $result->total;
        }

        $currentDate = $start;
        for ($i = 1; $i <= 31; $i++) {
            if (strtotime($currentDate) <= strtotime($end)) {
                !isset($data[$currentDate]) ? $data[$currentDate] = 0 : '';
                $currentDate = date('d-m-Y', strtotime($currentDate . ' +1 days'));
            } else {
                break;
            }
        }

        //echo '<pre>'; print_r($data); exit;

        krsort($data);

        return $data;
    }

    /* Function to get users subscription */

    public function getUserSubscription($user_id)
    {
        $select = $this->tableGateway->getSql()->select();
        $select->join('subscription_duration', 'subscription_duration.id = user_subscriptions.subscription_duration_id', array('duration'), 'inner');
        $select->join('subscription', 'subscription.id = subscription_duration.subscription_id', array('subscription_name'), 'inner');
        $select->where(array('user_id' => $user_id, 'user_subscriptions.status_id' => 1));
        $rowset = $this->tableGateway->selectwith($select);
        $row = $rowset->current();
        if (!$row) {
            return false;
        } else {
            return $row;
        }
    }

    /* Function to users feature settings */

    public function getFeatureSetting($user_id)
    {
        $results = $this->user_feature_setting->select(array('user_id' => $user_id));
        $row = $results->current();
        if (!$row) {
            return false;
        } else {
            return $row;
        }
    }

    /* Function to check users existing subscription */

    public function getUserCurrentSubscription($user_id, $subscription_id)
    {
        $select = $this->tableGateway->getSql()->select();
        $select->where(array('user_id' => $user_id, 'subscription_duration_id' => $subscription_id, 'status_id' => 1));
        $rowset = $this->tableGateway->selectwith($select);
        $row = $rowset->current();

        if ($row) {
            $current_end_date = strtotime($row->subscription_end_date);
            $today = strtotime(date('Y-m-d'));

            if ($current_end_date > $today) {
                return $row->id;
            } else {
                return 0;
            }
        } else {
            return 0;
        }
    }

    /* Function to get Users subscription end date */

    public function getSubscriptionEndDate($user_id, $subscription_id, $duration, $duration_in)
    {
        $select = $this->tableGateway->getSql()->select();
        $select->where(array('user_id' => $user_id, 'subscription_duration_id' => $subscription_id, 'status_id' => 1));
        $rowset = $this->tableGateway->selectwith($select);
        $row = $rowset->current();

        switch ($duration_in) {
            case "Years" :
                $end_date = date('Y-m-d', strtotime("+" . $duration . " year"));
                break;
            case "Months" :
                $end_date = date('Y-m-d', strtotime("+" . $duration . " month"));
                break;
            case "Days" :
                $end_date = date('Y-m-d', strtotime("+" . $duration . " day"));
                break;
        }
        if ($row) {
            if ($row->subscription_end_date > date('Y-m-d')) {
                $current_end_date = strtotime($row->subscription_end_date);
                $today = strtotime(date('Y-m-d'));
                $new_end_date = strtotime($end_date);

                return date('Y-m-d', ($current_end_date - $today) + $new_end_date);
            } else {
                return $end_date;
            }
        } else {
            return $end_date;
        }
    }

    /* Function to fetch Payment history */

    public function getPaymentHistory($invoice_id)
    {
        $result = $this->payment_history->select(array('invoice_id' => $invoice_id));
        $row = $result->current();
        if (!$row) {
            return false;
        }
        return $row;
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

    public function getSubscriptions()
    {
        $result = DataCache::getData($this->CacheKey);

        // Update cache if data not found
        if ($result == false) {
            $result = $this->fetchAll(false);

            // Update cache records
            DataCache::updateData($this->CacheKey, $result);

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

    public function saveSubscription(Subscriptions $subscription, $created_by, $sd)
    {
        $subs_details = $sd->getSubscriptionDuration($subscription->subscription_duration_id);

        if ($subscription->payment_status_id != 7 || $subscription->amount_paid == 0) {
            $invoice_status = 0;
        } else if ($subscription->invoice_total == $subscription->amount_paid && $subscription->payment_status_id == 7) {
            $invoice_status = 1;
        } else if ($subscription->invoice_total > $subscription->amount_paid && $subscription->payment_status_id == 7) {
            $invoice_status = 2;
        } else {
            $invoice_status = 1;
        }

        $data = array(
            'user_id' => $subscription->user_id,
            'subscription_duration_id' => $subscription->subscription_duration_id,
            'subscription_start_date' => date('Y-m-d'),
            'subscription_end_date' => $this->getSubscriptionEndDate($subscription->user_id, $subscription->subscription_duration_id, $subs_details->duration, $subs_details->duration_in),
            'status_id' => $subscription->status_id,
        );


        $invoice_data = array(
            'user_id' => $subscription->user_id,
            'sale_type' => 1, // 1 for subscription
            'invoice_total' => $subscription->invoice_total,
            'created_by' => $created_by,
            'status_id' => $invoice_status,
        );

        $invoice_details_data = array(
            'sale_item_details' => "Subscription Plan - " . $subs_details->subscription_name . " - " . $subs_details->duration . " " . $subs_details->duration_in,
            'amount' => $subscription->invoice_total,
            'subscription_duration_id' => $subscription->subscription_duration_id,
        );

        $ph_data = array(
            'payment_method_id' => $subscription->payment_method_id,
            'payment_instrument_no' => $subscription->payment_instrument_no,
            'amount_paid' => $subscription->amount_paid,
            'status_id' => $subscription->payment_status_id,
        );

        /* Adding invoice code starts */
        $invoice_id = (int) $subscription->invoice_id;
        if ($invoice_id == 0) {
            $invoice_data['created_date'] = date('Y-m-d h:i:s');
            $this->invoice->insert($invoice_data);
            $invoice_details_data['invoice_id'] = $invoice_id = $this->invoice->lastInsertValue;
            $this->invoice_details->insert($invoice_details_data);
        } else {
            if ($this->getInvoice($invoice_id)) {
                $this->invoice->update($invoice_data, array('id' => $invoice_id));
                if ($this->getInvoiceDetails($invoice_id)) {
                    $this->invoice_details->update($invoice_details_data, array('invoice_id' => $invoice_id));
                } else {
                    $invoice_details_data['invoice_id'] = $invoice_id;
                    $this->invoice_details->insert($invoice_details_data);
                }
            } else {
                $invoice_data['created_date'] = date('Y-m-d h:i:s');
                $this->invoice->insert($invoice_data);
                $invoice_details_data['invoice_id'] = $invoice_id = $this->invoice->lastInsertValue;
                $this->invoice_details->insert($invoice_details_data);
            }
        }
        /* Adding invoice code ends */

        /* Adding payment history code starts */
        if ($this->getPaymentHistory($invoice_id)) {
            $this->payment_history->update($ph_data, array('invoice_id' => $invoice_id));
        } else {
            $ph_data['invoice_id'] = $invoice_id;
            $ph_data['payment_date'] = date('Y-m-d h:i:s');
            $this->payment_history->insert($ph_data);
        }
        /* Adding payment history code ends */

        /* Adding subscription duration starts */
        $id = (int) $subscription->id;
        $id = ($id == 0) ? $this->getUserCurrentSubscription($subscription->user_id, $subscription->subscription_duration_id) : $id;
        if ($id == 0) {

            // Deactivating previous subscriptions 
            $this->tableGateway->update(array('status_id' => 2), array('user_id' => $subscription->user_id));

            $data['invoice_id'] = $invoice_id;
            $this->tableGateway->insert($data);
            $id = $this->tableGateway->lastInsertValue;
        } else {
            if ($this->getSubscription($id)) {
                // Deactivating previous subscriptions
                if ($data['status_id'] == 1) {
                    $this->tableGateway->update(array('status_id' => 2), array('user_id' => $subscription->user_id));
                }

                $data['invoice_id'] = $invoice_id;
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('Subscription id does not exist');
            }
        }
        /* Adding subscription duration ends */

        /* Updating feature permission of user according to subscription starts */

        if (isset($subs_details->subscription_id)) {

            $features = $this->subscription_feature->select(array('subscription_id' => $subs_details->subscription_id));
            $permData = array('chat' => 0, 'email' => 0, 'sms' => 0);
            foreach ($features as $feature) {
                switch ($feature->site_feature_id) {
                    case '6' :
                        $permData['chat'] = 1;
                        break;

                    case '11' :
                        $permData['email'] = 1;
                        break;

                    case '12' :
                        $permData['sms'] = 1;
                        break;
                }
            }

            if ($this->getFeatureSetting($subscription->user_id)) {
                $this->user_feature_setting->update($permData, array('user_id' => $subscription->user_id));
            } else {
                $permData['user_id'] = $subscription->user_id;
                $this->user_feature_setting->insert($permData);
            }
        }
        /* Updating feature permission of user according to subscription ends */


        // Update cache records
        //DataCache::updateData($this->CacheKey, $this->fetchAll(false));
    }

    public function changeStatus($id, $status)
    {
        $this->tableGateway->update(array('status_id' => $status), array('id' => $id));
    }

    public function deleteSubscription($id)
    {
        $this->tableGateway->delete(array('id' => (int) $id));

        // Update cache records
        //DataCache::updateData($this->CacheKey, $this->fetchAll(false));
    }

}
