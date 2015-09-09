<?php

namespace Admin\Model;

use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\DataCache;
use Zend\Db\Sql\Select;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;
use Zend\Db\Sql\Expression;

class BookingsTable
{

    protected $tableGateway;
    private $CacheKey = 'bookings';

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
        $adapter = $this->tableGateway->getAdapter();
        $this->invoice = new TableGateway('invoice', $adapter);
        $this->booking_sug_history = new TableGateway('booking_suggestion_history', $adapter);
        $this->invoice_details = new TableGateway('invoice_details', $adapter);
        $this->payment_history = new TableGateway('payment_history', $adapter);
    }

    public function fetchAll($paginate = true, $filter = array(), $orderBy = array())
    {
        if ($paginate) {
            $select = new Select('booking');
            $select->columns(array('*', new Expression("service_provider.first_name as sp_first_name, service_provider.last_name as sp_last_name,
			invoice.status_id AS invoice_status,payment_history.currency as currency,CASE invoice.status_id WHEN 0 THEN 'Unpaid' WHEN 1 THEN 'Paid' WHEN 2 THEN 'Partially Paid' END AS PaymentStatus")));
            $select->join('booking_suggestion_history', 'booking_suggestion_history.booking_id = booking.id', array('booking_time', 'booking_status'), 'inner');
            $select->join('users', 'users.id = booking.user_id', array('first_name', 'last_name'), 'left');
            $select->join(array('service_provider' => 'users'), 'service_provider.id = booking.service_provider_id', array(), 'left');
            $select->join('service_provider_service', 'service_provider_service.id = booking.service_provider_service_id', array('duration', 'price'), 'left');
            $select->join('service_category', 'service_category.id = service_provider_service.service_id', array('category_name'), 'left');
            $select->join('invoice', 'invoice.id = booking.invoice_id', array('invoice_total', 'site_commision'), 'inner');
            $select->join('invoice_details', 'invoice_details.invoice_id = invoice.id', array('sale_item_details'), 'inner');
            $select->join('payment_history', 'payment_history.invoice_id = invoice.id', array(), 'inner');
            $select->join('lookup_status', 'lookup_status.status_id = booking_suggestion_history.booking_status', array('status'), 'left');
            $select->where('booking_suggestion_history.id = (SELECT id FROM booking_suggestion_history WHERE booking_id = booking.id ORDER BY id DESC LIMIT 1)');
            (count($filter) > 0) ? $select->where($filter) : "";

            /* Data sorting code starts here */
            if (count($orderBy) > 0 && $orderBy['sort_field'] != '' && $orderBy['sort_order'] != '') {
                switch ($orderBy['sort_field']) {
                    case 'user' :
                        $select->order('users.first_name ' . $orderBy['sort_order']);
                        break;

                    case 'service_provider' :
                        $select->order('service_provider.first_name ' . $orderBy['sort_order']);
                        break;

                    case 'service' :
                        $select->order('service_category.category_name ' . $orderBy['sort_order']);
                        break;

                    case 'booked_date' :
                        $select->order('booking.booked_date ' . $orderBy['sort_order']);
                        break;
                }
            } else {
                $select->order('booking.id desc');
            }
            /* Data sorting code ends here */

            //echo str_replace('"', '', $select->getSqlString()); exit;

            $resultSetPrototype = new ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new Bookings());

            $paginatorAdapter = new DbSelect(
                    $select, $this->tableGateway->getAdapter(), $resultSetPrototype
            );
            $paginator = new Paginator($paginatorAdapter);

            return $paginator;
        } else {
            $select = $this->tableGateway->getSql()->select();
            $select->columns(array('*', new Expression("service_provider.first_name as sp_first_name, service_provider.last_name as sp_last_name,
			invoice.status_id AS invoice_status,CASE invoice.status_id WHEN 0 THEN 'Unpaid' WHEN 1 THEN 'Paid' WHEN 2 THEN 'Partially Paid' END AS PaymentStatus")));
            $select->join('booking_suggestion_history', 'booking_suggestion_history.booking_id = booking.id', array('booking_time', 'booking_status'), 'inner');
            $select->join('users', 'users.id = booking.user_id', array('first_name', 'last_name'), 'left');
            $select->join(array('service_provider' => 'users'), 'service_provider.id = booking.service_provider_id', array(), 'left');
            $select->join('service_provider_service', 'service_provider_service.id = booking.service_provider_service_id', array('duration', 'price'), 'left');
            $select->join('service_category', 'service_category.id = service_provider_service.service_id', array('category_name'), 'left');
            $select->join('invoice', 'invoice.id = booking.invoice_id', array('invoice_total', 'site_commision', 'created_date'), 'inner');
            $select->join('invoice_details', 'invoice_details.invoice_id = invoice.id', array('sale_item_details'), 'inner');
            $select->join('payment_history', 'payment_history.invoice_id = invoice.id', array(), 'inner');
            $select->join('lookup_status', 'lookup_status.status_id = booking_suggestion_history.booking_status', array('status'), 'left');
            $select->where('booking_suggestion_history.id = (SELECT id FROM booking_suggestion_history WHERE booking_id = booking.id ORDER BY id DESC LIMIT 1)');
            (count($filter) > 0) ? $select->where($filter) : "";

            return $this->tableGateway->selectwith($select);
        }
    }

    public function ExportAll($filter = array(), $orderBy = array())
    {
        $select = $this->tableGateway->getSql()->select();
        $select->columns(array('*', new Expression("service_provider.first_name as sp_first_name, service_provider.last_name as sp_last_name,
		invoice.status_id AS invoice_status,CASE invoice.status_id WHEN 0 THEN 'Unpaid' WHEN 1 THEN 'Paid' WHEN 2 THEN 'Partially Paid' END AS PaymentStatus")));
        $select->join('booking_suggestion_history', 'booking_suggestion_history.booking_id = booking.id', array('booking_time', 'booking_status'), 'inner');
        $select->join('users', 'users.id = booking.user_id', array('first_name', 'last_name'), 'left');
        $select->join(array('service_provider' => 'users'), 'service_provider.id = booking.service_provider_id', array(), 'left');
        $select->join('service_provider_service', 'service_provider_service.id = booking.service_provider_service_id', array('duration', 'price'), 'left');
        $select->join('service_category', 'service_category.id = service_provider_service.service_id', array('category_name'), 'left');
        $select->join('invoice', 'invoice.id = booking.invoice_id', array('invoice_total', 'site_commision'), 'inner');
        $select->join('invoice_details', 'invoice_details.invoice_id = invoice.id', array('sale_item_details'), 'inner');
        $select->join('payment_history', 'payment_history.invoice_id = invoice.id', array(), 'inner');
        $select->join('lookup_status', 'lookup_status.status_id = booking_suggestion_history.booking_status', array('status'), 'left');
        $select->where('booking_suggestion_history.id = (SELECT id FROM booking_suggestion_history WHERE booking_id = booking.id ORDER BY id DESC LIMIT 1)');
        (count($filter) > 0) ? $select->where($filter) : "";

        /* Data sorting code starts here */
        if (count($orderBy) > 0 && $orderBy['sort_field'] != '' && $orderBy['sort_order'] != '') {
            switch ($orderBy['sort_field']) {
                case 'user' :
                    $select->order('users.first_name ' . $orderBy['sort_order']);
                    break;

                case 'service_provider' :
                    $select->order('service_provider.first_name ' . $orderBy['sort_order']);
                    break;

                case 'service' :
                    $select->order('service_category.category_name ' . $orderBy['sort_order']);
                    break;

                case 'booked_date' :
                    $select->order('booking.booked_date ' . $orderBy['sort_order']);
                    break;
            }
        }
        /* Data sorting code ends here */

        return $this->tableGateway->selectwith($select);
    }

    public function getBooking($id)
    {
        $id = (int) $id;
        $select = $this->tableGateway->getSql()->select();
        $select->columns(array('*', new Expression("booking_suggestion_history.id as suggestion_id, booking.service_provider_service_id AS service_id, service_provider.first_name as sp_first_name, service_provider.last_name as sp_last_name, service_provider.email as sp_email,
		invoice.status_id AS invoice_status,CASE invoice.status_id WHEN 0 THEN 'Unpaid' WHEN 1 THEN 'Paid' WHEN 2 THEN 'Partially Paid' END AS PaymentStatus,
		payment_history.status_id AS payment_status_id, DATE_FORMAT(booking_suggestion_history.booking_time, '%d/%m/%Y %H:%i') as booking_time, booking.service_address_id")));
        $select->join('booking_suggestion_history', 'booking_suggestion_history.booking_id = booking.id', array( 'booking_status'), 'inner');
        $select->join('users', 'users.id = booking.user_id', array('first_name', 'last_name', 'email'), 'left');
        $select->join(array('service_provider' => 'users'), 'service_provider.id = booking.service_provider_id', array(), 'left');
        $select->join('service_provider_service', 'service_provider_service.id = booking.service_provider_service_id', array('duration', 'price'), 'left');
        $select->join('service_category', 'service_category.id = service_provider_service.service_id', array('category_name'), 'left');
        $select->join('invoice', 'invoice.id = booking.invoice_id', array('invoice_total', 'site_commision'), 'inner');
        $select->join('invoice_details', 'invoice_details.invoice_id = invoice.id', array('sale_item_details'), 'inner');
        $select->join('payment_history', 'payment_history.invoice_id = invoice.id', array('payment_method_id', 'payment_instrument_no', 'amount_paid'), 'inner');
        $select->join('lookup_status', 'lookup_status.status_id = booking_suggestion_history.booking_status', array('status'), 'left');
        $select->where(array('booking.id' => (int) $id));
        $select->where('booking_suggestion_history.id = (SELECT id FROM booking_suggestion_history WHERE booking_id = booking.id ORDER BY id DESC LIMIT 1)');
        //echo str_replace('"', '', $select->getSqlString()); exit;
        $rowset = $this->tableGateway->selectwith($select);
        $row = $rowset->current();
        if (!$row) {
            return false;
            //throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function booked($filter = array())
    {
        $select = $this->tableGateway->getSql()->select();
        $select->join('users', 'users.id = booking.user_id', array('first_name', 'last_name'), 'left');
        $select->join(array('service_provider' => 'users'), 'service_provider.id = booking.service_provider_id', array(), 'left');
        $select->join('invoice_details', 'invoice_details.invoice_id = booking.invoice_id', array('sale_item_details'), 'inner');
        $select->where(array('booking.status_id' => 4));
        if (count($filter) > 0) {
            if (isset($filter['startDate']) && $filter['startDate'] != "" && isset($filter['endDate']) && $filter['endDate'] != "") {
                $select->where("DATE_FORMAT(booking.booked_date , '%Y-%m-%d') BETWEEN '" . $filter['startDate'] . "' AND '" . $filter['endDate'] . "'");
            } else if (isset($filter['startDate']) && isset($filter['endDate']) && $filter['startDate'] != "" && $filter['endDate'] == "") {
                $select->where("DATE_FORMAT(booking.booked_date , '%Y-%m-%d') = '" . $filter['startDate'] . "'");
            } else if (isset($filter['startDate']) && isset($filter['endDate']) && $filter['startDate'] == "" && $filter['endDate'] != "") {
                $select->where("DATE_FORMAT(booking.booked_date , '%Y-%m-%d') = '" . $filter['endDate'] . "'");
            }
        }

        return $this->tableGateway->selectwith($select);

        //DATE_FORMAT(booking.booked_date , '%Y-%m-%d')
    }

    /* Function to get bookings by states */

    public function getBookingsByStates($per = 'day')
    {
        switch ($per) {
            case 'day' :
                $select = $this->tableGateway->getSql()->select();
                $select->columns(array(new Expression('COUNT(address.state_id) AS total, state_name')));
                $select->join('invoice', 'invoice.id = booking.invoice_id', array(), 'inner');
                $select->join('user_address', 'user_address.user_id = booking.user_id', array(), 'inner');
                $select->join('address', 'address.id = user_address.address_id', array(), 'inner');
                $select->join('state', 'state.id = address.state_id', array(), 'inner');
                $select->where("DATE_FORMAT(invoice.created_date, '%Y-%m-%d') = '" . date('Y-m-d') . "'");
                $select->group('address.state_id');
                $select->limit(10);
                break;

            case 'week' :
                $select = $this->tableGateway->getSql()->select();
                $select->columns(array(new Expression('COUNT(address.state_id) AS total, state_name')));
                $select->join('invoice', 'invoice.id = booking.invoice_id', array(), 'inner');
                $select->join('user_address', 'user_address.user_id = booking.user_id', array(), 'inner');
                $select->join('address', 'address.id = user_address.address_id', array(), 'inner');
                $select->join('state', 'state.id = address.state_id', array(), 'inner');
                $select->where("DATE_FORMAT(invoice.created_date, '%Y-%m-%d') BETWEEN '" . date('Y-m-d', strtotime('-6 days')) . "' AND '" . date('Y-m-d') . "'");
                $select->group('address.state_id');
                $select->limit(10);
                break;

            case 'month' :
                $select = $this->tableGateway->getSql()->select();
                $select->columns(array(new Expression('COUNT(address.state_id) AS total, state_name')));
                $select->join('invoice', 'invoice.id = booking.invoice_id', array(), 'inner');
                $select->join('user_address', 'user_address.user_id = booking.user_id', array(), 'inner');
                $select->join('address', 'address.id = user_address.address_id', array(), 'inner');
                $select->join('state', 'state.id = address.state_id', array(), 'inner');
                $select->where("DATE_FORMAT(invoice.created_date, '%Y-%m-%d') BETWEEN '" . date('Y-m-d', strtotime('-1 month')) . "' AND '" . date('Y-m-d') . "'");
                $select->group('address.state_id');
                $select->limit(10);
                break;

            case 'year' :
                $select = $this->tableGateway->getSql()->select();
                $select->columns(array(new Expression('COUNT(address.state_id) AS total, state_name')));
                $select->join('invoice', 'invoice.id = booking.invoice_id', array(), 'inner');
                $select->join('user_address', 'user_address.user_id = booking.user_id', array(), 'inner');
                $select->join('address', 'address.id = user_address.address_id', array(), 'inner');
                $select->join('state', 'state.id = address.state_id', array(), 'inner');
                $select->where("DATE_FORMAT(invoice.created_date, '%Y-%m-%d') BETWEEN '" . date('Y-m-d', strtotime('-1 year')) . "' AND '" . date('Y-m-d') . "'");
                $select->group('address.state_id');
                $select->limit(10);
                break;
        }

        //echo '<pre>'.str_replace('"', '', $select->getSqlString()); exit;
        $results = $this->tableGateway->selectwith($select);
        $bookings = array();
        $total = 0;

        foreach ($results as $result) {
            if ($result->state_name != "") {

                $growth = $this->tableGateway->getSql()->select();
                $growth->columns(array(new Expression('COUNT(address.state_id) AS total, state_name, address.state_id')));
                $growth->join('invoice', 'invoice.id = booking.invoice_id', array(), 'inner');
                $growth->join('user_address', 'user_address.user_id = booking.user_id', array(), 'inner');
                $growth->join('address', 'address.id = user_address.address_id', array(), 'inner');
                $growth->join('state', 'state.id = address.state_id', array(), 'inner');

                switch ($per) {
                    case 'day' :
                        $growth->where("address.state_id = " . $result->state_id . " AND DATE_FORMAT(invoice.created_date, '%Y-%m-%d') = '" . date('Y-m-d', strtotime('-1 days')) . "'");
                        break;

                    case 'week' :
                        $growth->where("address.state_id = " . $result->state_id . " AND DATE_FORMAT(invoice.created_date, '%Y-%m-%d') BETWEEN '" . date('Y-m-d', strtotime('-14 days')) . "' AND '" . date('Y-m-d', strtotime('-7 days')) . "'");
                        break;

                    case 'month' :
                        $growth->where("address.state_id = " . $result->state_id . " AND DATE_FORMAT(invoice.created_date, '%Y-%m-%d') BETWEEN '" . date('Y-m-d', strtotime('-2 month')) . "' AND '" . date('Y-m-d', strtotime('-1 month')) . "'");
                        break;

                    case 'year' :
                        $growth->where("address.state_id = " . $result->state_id . " AND DATE_FORMAT(invoice.created_date, '%Y-%m-%d') BETWEEN '" . date('Y-m-d', strtotime('-2 year')) . "' AND '" . date('Y-m-d', strtotime('-1 year')) . "'");
                        break;
                }

                $growth_result = $this->tableGateway->selectwith($growth);
                $growth = $growth_result->current();
                $totalGrowth = (isset($growth->total) && $growth->total > 0) ? round((($result->total - $growth->total) / $growth->total) * 100) : round(($result->total - $growth->total) * 100);

                $bookings[] = array('total' => $result->total, 'state_name' => $result->state_name, 'growth' => $totalGrowth);
                $total = $total + $result->total;
            }
        }

        // Calculating total %
        foreach ($bookings as $key => $value) {
            $bookings[$key]['total_percentage'] = round(($bookings[$key]['total'] / $total) * 100);
        }

        return $bookings;
    }

    /* Function to get current month data */

    public function getDataByMonth($start, $end)
    {
        $select = $this->tableGateway->getSql()->select();
        $select->columns(array(new Expression("count(*) as total, DATE_FORMAT(invoice.created_date, '%d-%m-%Y') as month")));
        $select->join('invoice', 'invoice.id = booking.invoice_id', array(), 'inner');
        $select->where("DATE_FORMAT(invoice.created_date, '%Y-%m-%d') BETWEEN '" . date('Y-m-d', strtotime($start)) . "' AND '" . date('Y-m-d', strtotime($end)) . "'");
        $select->group("month");
        //echo str_replace('"','',$select->getSqlString()); exit;

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

        krsort($data);

        return $data;
    }

    public function getBookings()
    {
        $result = DataCache::getData($this->CacheKey);

        // Update cache if data not found
        if ($result == false) {
            $result = $this->fetchAll();

            // Update cache records
            DataCache::updateData($this->CacheKey, $result);

            // Get latest records
            $result = DataCache::getData($this->CacheKey);
        }
        return $result;
    }

    public function getAllSuggestions($booking_id, $in_array = false)
    {
        $select = $this->booking_sug_history->getSql()->select();
        $select->join('users', 'users.id = booking_suggestion_history.user_id', array('first_name', 'last_name'), 'inner');
        $select->where(array('booking_id' => $booking_id));
        $select->order('id asc');
        //echo str_replace('"','',$select->getSqlString()); exit;
        $results = $this->booking_sug_history->selectwith($select);
        
        if ($in_array == true) {
            $data = array();
            foreach ($results as $result) {
                $data[] = array('booking_time' => $result->booking_time, 'name' => $result->first_name.' '.$result->last_name);
            }
            return $data;
        } else {
            return $results;
        }
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
    
    /* Function to get total booking per category */
    public function getTotalPerCategory($catId)
    {
        $select = $this->tableGateway->getSql()->select();
        $select->join('service_provider_service', 'booking.service_provider_service_id = service_provider_service.id', array(), 'inner');
        $select->where(array('service_provider_service.service_id' => $catId));
        $result = $this->tableGateway->selectwith($select);
        return $result->count();
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
    
    /* Function to get number of confirmations of booking */
    public function getConfirmations($id)
    {
        $row = $this->booking_sug_history->select(array('booking_id' => $id, 'booking_status' => 4));
        return $row->count(); 
        
    }

    public function saveBooking(Bookings $booking, $created_by, $sp)
    {
        $dateTime = str_replace('/', '-', $booking->booking_time);
        //echo $dateTime; exit;

        $service = $sp->getServicesByName($booking->service_provider_id, true, $booking->service_provider_service_id);

        if ($booking->payment_status_id != 7 || $booking->amount_paid == 0) {
            $invoice_status = 0;
        } else if ($booking->site_commision == $booking->amount_paid && $booking->payment_status_id == 7) {
            $invoice_status = 1;
        } else if ($booking->site_commision > $booking->amount_paid && $booking->payment_status_id == 7) {
            $invoice_status = 2;
        } else {
            $invoice_status = 1;
        }

        $data = array(
            'user_id' => $booking->user_id,
            'service_provider_id' => $booking->service_provider_id,
            'service_address_id' => $booking->service_address_id,
            'service_provider_service_id' => $booking->service_provider_service_id,
            'booked_date' => date('Y-m-d h:i:s', strtotime($dateTime)),
            'payment_status' => $booking->payment_status,
            'status_id' => $booking->status_id,
        );
        //echo '<pre>'; print_r($data); exit;

        $invoice_data = array(
            'user_id' => $booking->user_id,
            'sale_type' => 3, // 3 for services
            'invoice_total' => $booking->invoice_total,
            'site_commision' => $booking->site_commision,
            'created_by' => $created_by,
            'status_id' => $invoice_status,
        );

        $invoice_details_data = array(
            'sale_item_details' => "Service - " . $service,
            'amount' => $booking->invoice_total,
            'service_provider_service_id' => $booking->service_provider_service_id,
        );

        $ph_data = array(
            'payment_method_id' => $booking->payment_method_id,
            'payment_instrument_no' => substr($booking->payment_instrument_no, (strlen($booking->payment_instrument_no) - 4), 4),
            'amount_paid' => $booking->amount_paid,
            'status_id' => $booking->payment_status_id,
        );

        $booking_history = array(
            'booking_time' => date('Y-m-d H:i:s', strtotime($dateTime)),
            'booking_status' => $booking->booking_status,
        );

        /* Adding invoice code starts */
        $invoice_id = (int) $booking->invoice_id;
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

        $id = (int) $booking->id;
        if ($id == 0 || $booking->parent_booking_id != null) {
            $data['invoice_id'] = $invoice_id;
            $data['created_date'] = date("Y-m-d h:i:s");
            if ($booking->parent_booking_id != null) {
                $data['parent_booking_id'] = $booking->parent_booking_id;
            }
            $this->tableGateway->insert($data);

            /* Adding booking suggestion history code starts */
            $booking_history['booking_id'] = $this->tableGateway->lastInsertValue;
            $booking_history['user_id'] = $booking->user_id;
            $this->booking_sug_history->insert($booking_history);
            /* Adding booking suggestion history code ends */
        } else {
            if ($this->getBooking($id)) {
                $data['invoice_id'] = $invoice_id;
                $data['modified_date'] = date("Y-m-d h:i:s");
                $data['modified_by'] = $user->id;
                $this->tableGateway->update($data, array('id' => $id));
                /* Adding booking suggestion history code starts */

                $this->booking_sug_history->update($booking_history, array('id' => $booking->suggestion_id));
                /* Adding booking suggestion history code ends */
            } else {
                throw new \Exception('Booking id does not exist');
            }
        }

        // Update cache records
        //DataCache::updateData($this->CacheKey, $this->fetchAll()); 
    }
    
    /* Function to reschedule the booking */
    public function reschedule(Bookings $booking)
    {
        $dateTime = str_replace('/', '-', $booking->booking_time);
        
        $booking_history = array(
            'user_id' => 1,
            'booking_id' => $booking->id,
            'booking_time' => date('Y-m-d H:i:s', strtotime($dateTime)),
            'booking_status' => 5,
        );
        
        $this->booking_sug_history->insert($booking_history);
    }

    public function changeStatus($ids, $status)
    {
        //$this->tableGateway->update(array('status_id' => $status), array('id' => $id));
        foreach ($ids as $id) {
            $select = $this->booking_sug_history->getSql()->select();
            $select->where(array('booking_id' => $id));
            $select->order('id desc');
            $select->limit(1);
            $data = $this->booking_sug_history->selectwith($select)->current();
            
            $this->booking_sug_history->update(array('booking_status' => $status), array('booking_id' => $id, 'id' => $data->id));
        }
    }

    public function deleteBooking($id)
    {
        $this->tableGateway->delete(array('id' => (int) $id,));
        $this->booking_sug_history->delete(array('booking_id' => (int) $id,));
        // Update cache records
        //DataCache::updateData($this->CacheKey, $this->fetchAll());
    }

    public function CountNewBookings()
    {
        $select = $this->tableGateway->getSql()->select();
        $select->where('booking.created_date > NOW() - INTERVAL 4 DAY AND booking.payment_status=1');
        $rowset = $this->tableGateway->selectwith($select);

        if ($rowset->count() > 0) {
            return $rowset->count();
        } else {
            return false;
        }
    }

}
