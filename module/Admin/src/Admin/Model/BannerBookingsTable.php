<?php

namespace Admin\Model;

use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\DataCache;
use Zend\Db\Sql\Select;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;
use Zend\Db\Sql\Expression;

class BannerBookingsTable
{

    protected $tableGateway;
    private $CacheKey = 'bannerbooking';
    private $invoice;
    private $invoice_details;
    private $payment_history;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
        $adapter = $this->tableGateway->getAdapter();
        $this->invoice = new TableGateway('invoice', $adapter);
        $this->invoice_details = new TableGateway('invoice_details', $adapter);
        $this->payment_history = new TableGateway('payment_history', $adapter);
    }

    public function fetchAll($paginate = true, $orderBy = array())
    {
        if ($paginate) {

            $select = new Select('banner_booking');
            $select->columns(array(new Expression("banner_booking.id, banner_booking.status_id, invoice.status_id AS invoice_status,CASE invoice.status_id WHEN 0 THEN 'Unpaid' WHEN 1 THEN 'Paid' WHEN 2 THEN 'Partially Paid' END AS payment_status, booking_date, start_date, end_date")));
            $select->join('users', 'users.id = banner_booking.user_id', array('first_name', 'last_name'), 'inner');
            $select->join('invoice', 'invoice.id = banner_booking.invoice_id', array('invoice_total', 'created_date'), 'inner');
            $select->join('invoice_details', 'invoice_details.invoice_id = invoice.id', array('sale_item_details'), 'inner');
            $select->join('payment_history', 'payment_history.invoice_id = invoice.id', array('currency'), 'inner');
            $select->join('lookup_status', 'lookup_status.status_id = banner_booking.status_id', array('status'), 'left');

            /* Data sorting code starts here */
            if (count($orderBy) > 0 && $orderBy['sort_field'] != '' && $orderBy['sort_order'] != '') {
                switch ($orderBy['sort_field']) {
                    case 'name' :
                        $select->order('users.first_name ' . $orderBy['sort_order']);
                        break;

                    case 'date' :
                        $select->order('booking_date ' . $orderBy['sort_order']);
                        break;
                    
                    case 'start_date' :
                        $select->order('start_date ' . $orderBy['sort_order']);
                        break;
                    
                    case 'end_date' :
                        $select->order('end_date ' . $orderBy['sort_order']);
                        break;

                    case 'plan' :
                        $select->order('invoice_details.sale_item_details ' . $orderBy['sort_order']);
                        break;
                }
            }
            /* Data sorting code ends here */

            //echo str_replace('"', '', $select->getSqlString()); exit;

            $resultSetPrototype = new ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new BannerBookings());

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
        $select->columns(array(new Expression("banner_booking.id, banner_booking.status_id, invoice.status_id AS invoice_status,CASE invoice.status_id WHEN 0 THEN 'Unpaid' WHEN 1 THEN 'Paid' WHEN 2 THEN 'Partially Paid' END AS payment_status")));
        $select->join('users', 'users.id = banner_booking.user_id', array('first_name', 'last_name'), 'inner');
        $select->join('invoice', 'invoice.id = banner_booking.invoice_id', array('invoice_total', 'created_date'), 'inner');
        $select->join('invoice_details', 'invoice_details.invoice_id = invoice.id', array('sale_item_details'), 'inner');
        $select->join('payment_history', 'payment_history.invoice_id = invoice.id', array(), 'inner');
        $select->join('lookup_status', 'lookup_status.status_id = banner_booking.status_id', array('status'), 'left');

        /* Data sorting code starts here */
        if (count($orderBy) > 0 && $orderBy['sort_field'] != '' && $orderBy['sort_order'] != '') {
            switch ($orderBy['sort_field']) {
                case 'name' :
                    $select->order('users.first_name ' . $orderBy['sort_order']);
                    break;

                case 'date' :
                    $select->order('invoice.created_date ' . $orderBy['sort_order']);
                    break;

                case 'plan' :
                    $select->order('invoice_details.sale_item_details ' . $orderBy['sort_order']);
                    break;
            }
        }
        /* Data sorting code ends here */

        return $this->tableGateway->selectwith($select);
    }

    public function getBannerBooking($id)
    {
        $id = (int) $id;
        $select = $this->tableGateway->getSql()->select();
        $select->columns(array('*', new Expression("invoice.status_id AS invoice_status,CASE invoice.status_id WHEN 0 THEN 'Unpaid' WHEN 1 THEN 'Paid' WHEN 2 THEN 'Partially Paid' END AS payment_status")));
        $select->join('users', 'users.id = banner_booking.user_id', array('first_name', 'last_name'), 'inner');
        $select->join('invoice', 'invoice.id = banner_booking.invoice_id', array('invoice_total', 'created_date'), 'inner');
        $select->join('invoice_details', 'invoice_details.invoice_id = invoice.id', array('sale_item_details'), 'inner');
        $select->join('payment_history', 'payment_history.invoice_id = invoice.id', array('payment_method_id', 'payment_instrument_no', 'amount_paid', 'currency', 'status_id'), 'inner');
        $select->join('lookup_status', 'lookup_status.status_id = banner_booking.status_id', array('status'), 'left');
        $select->where(array('banner_booking.id' => $id));

        $rowset = $this->tableGateway->selectwith($select);
        $row = $rowset->current();
        if (!$row) {
            return false;
            //throw new \Exception("Could not find row $id");
        }
        return $row;
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

    public function getBannerBookings()
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
    
    public function getBookingEndDate($duration, $duration_in)
    {
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
        
        return $end_date;
    }

    public function saveBannerBooking(BannerBookings $bb, $created_by, $ap)
    {

        $ap_data = $ap->getAdvertisementPlan($bb->advertisement_plan_id);

        if ($bb->status_id != 7 || $bb->amount_paid == 0) {
            $invoice_status = 0;
        } else if ($bb->invoice_total == $bb->amount_paid && $bb->status_id == 7) {
            $invoice_status = 1;
        } else if ($bb->invoice_total > $bb->amount_paid && $bb->status_id == 7) {
            $invoice_status = 2;
        } else {
            $invoice_status = 1;
        }

        $data = array(
            'user_id' => $bb->user_id,
            'advertisement_plan_id' => $bb->advertisement_plan_id,
        );

        $invoice_data = array(
            'user_id' => $bb->user_id,
            'sale_type' => 2, // 2 for banners
            'invoice_total' => $bb->invoice_total,
            'created_by' => $created_by,
            'status_id' => $invoice_status,
        );

        $invoice_details_data = array(
            'sale_item_details' => "Advertisement Plan - " . $ap_data->plan_name . " " . $ap_data->duration . " " . $ap_data->duration_unit,
            'amount' => $bb->invoice_total,
            'advertisement_plan_id' => $bb->advertisement_plan_id,
        );

        $ph_data = array(
            'payment_method_id' => $bb->payment_method_id,
            'payment_instrument_no' => $bb->payment_instrument_no,
            'amount_paid' => $bb->amount_paid,
            'currency' => $bb->currency,
            'status_id' => $bb->status_id,
        );

        /* Adding invoice code starts */
        $invoice_id = (int) $bb->invoice_id;
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

        /* Adding banner booking code starts */
        $id = (int) $bb->id;
        if ($id == 0) {
            $data['status_id'] = 1;
            $data['invoice_id'] = $invoice_id;
            $data['booking_date'] = date('Y-m-d');
            $data['start_date'] = date('Y-m-d');
            $data['end_date'] = $this->getBookingEndDate($ap_data->duration, $ap_data->duration_unit);
            $this->tableGateway->insert($data);
        } else {
            if ($this->getBannerBooking($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('Banner Booking id does not exist');
            }
        }
        /* Adding banner booking code ends */

        // Update cache records
        //DataCache::updateData($this->CacheKey, $this->fetchAll());
    }

    public function changeStatus($id, $status)
    {
        $this->tableGateway->update(array('status_id' => $status), array('id' => $id));
    }

    public function deleteBannerBooking($id)
    {
        $data = $this->getBannerBooking($id);

        $this->invoice->delete(array('id' => $data->invoice_id));
        $this->invoice_details->delete(array('invoice_id' => $data->invoice_id));
        $this->payment_history->delete(array('invoice_id' => $data->invoice_id));

        $this->tableGateway->delete(array('id' => (int) $id));

        // Update cache records
        //DataCache::updateData($this->CacheKey, $this->fetchAll());
    }

}
