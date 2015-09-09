<?php

namespace Admin\Model;

use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\DataCache;
use Zend\Db\Sql\Select;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;
use Zend\Db\Sql\Expression;

class ServiceProviderAvailabilityTable
{

    protected $tableGateway;
    private $CacheKey = 'serviceprovideravailability';
    private $service_provider_appointment_delay;
    private $availability_days;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
        $adapter = $this->tableGateway->getAdapter();
        $this->availability_days = new TableGateway('availability_days', $adapter);
        $this->service_provider_appointment_delay = new TableGateway('service_provider_appointment_delay', $adapter);
    }

    public function fetchAll($id, $paginate = true, $filter = array(), $orderBy = array())
    {
        $select = $this->tableGateway->getSql()->select();
        return $this->tableGateway->selectwith($select);
    }

    public function getServiceProviderAvailability($user_id)
    {
        $user_id = (int) $user_id;
        $select = $this->tableGateway->getSql()->select();
        $select->join('users', 'users.id = service_provider_availability.user_id', array('first_name', 'last_name'), 'inner');
        $select->join('service_provider_appointment_delay', 'service_provider_appointment_delay.user_id = service_provider_availability.user_id', array('delay_time'), 'left');
        $select->where('service_provider_availability.user_id = ' . $user_id);
        $rowset = $this->tableGateway->selectwith($select);

        if (!$rowset->current()) {
            return false;
            //throw new \Exception("Could not find row $id");
        }
        return $rowset;
    }

    public function getServiceProviderDelayTime($id)
    {
        $user_id = (int) $id;
        $select = $this->service_provider_appointment_delay->getSql()->select();
        $select->join('users', 'users.id = service_provider_appointment_delay.user_id', array('first_name', 'last_name'), 'inner');
        $select->where('service_provider_appointment_delay.user_id = ' . $user_id);
        $rowset = $this->service_provider_appointment_delay->selectwith($select);
        $row = $rowset->current();
        if (!$row) {
            return false;
            //throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function getServiceProviderAvailabilityByDay($user_id, $day)
    {
        $user_id = (int) $user_id;
        $select = $this->tableGateway->getSql()->select();
        $select->join('users', 'users.id = service_provider_availability.user_id', array('first_name', 'last_name'), 'inner');
        $select->join('service_provider_appointment_delay', 'service_provider_appointment_delay.user_id = service_provider_availability.user_id', array('delay_time'), 'left');
        $select->where('service_provider_availability.user_id = ' . $user_id . ' AND service_provider_availability.days_id = ' . $day);
        //echo str_replace('"','',$select->getSqlString()); exit;
        $rowset = $this->tableGateway->selectwith($select);
        $row = $rowset->current();
        if (!$row) {
            return false;
            //throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function getUserWorkdays($user_id, $returnArray = 'false')
    {
        $select = $this->tableGateway->getSql()->select();
        $select->join('availability_days', 'availability_days.id = service_provider_availability.days_id', array('day'), 'inner');
        $select->where('service_provider_availability.user_id = ' . $user_id . ' AND start_time != "00:00:00" OR end_time != "00:00:00"');
        $select->order('availability_days.id ASC');
        //echo str_replace('"','',$select->getSqlString()); exit;
        $rowset = $this->tableGateway->selectwith($select);
        if ($returnArray == true) {
            $weekdays = array();
            foreach ($rowset as $weekday) {
                $weekdays[] = $weekday->day;
            }
            return $weekdays;
        } else {
            return $rowset;
        }
    }

    public function getAvailabilityDays()
    {
        return $this->availability_days->select();
    }

    public function saveServiceProviderAvailability($user_id, $start_time, $end_time, $lunch_start_time, $lunch_end_time, $delay_time = "", $address_id = "")
    {
        $id = (int) $user_id;
        if ($id != 0) {
            if ($this->getServiceProviderAvailability($id)) {
                foreach ($start_time as $key => $value) {
                    $data = array('start_time' => date('H:i:s', strtotime($value)), 'end_time' => date('H:i:s', strtotime($end_time[$key])), 'lunch_start_time' => date('H:i:s', strtotime($lunch_start_time[$key])), 'lunch_end_time' => date('H:i:s', strtotime($lunch_end_time[$key])), 'address_id' => $address_id[$key]);
                    $this->tableGateway->update($data, array('user_id' => $user_id, 'days_id' => $key));
                }
            } else {
                foreach ($start_time as $key => $value) {
                    $data = array('user_id' => $user_id, 'days_id' => $key, 'start_time' => date('H:i:s', strtotime($value)), 'end_time' => date('H:i:s', strtotime($end_time[$key])), 'lunch_start_time' => date('H:i:s', strtotime($lunch_start_time[$key])), 'lunch_end_time' => date('H:i:s', strtotime($lunch_end_time[$key])), 'address_id' => $address_id[$key]);
                    $this->tableGateway->insert($data);
                }
            }

            /* Adding appointment delay time for service provider starts */
            if ($delay_time != "") {

                if ($this->getServiceProviderDelayTime($id)) {
                    $this->service_provider_appointment_delay->update(array('delay_time' => $delay_time), array('user_id' => $user_id));
                } else {
                    $this->service_provider_appointment_delay->insert(array('user_id' => $user_id, 'delay_time' => $delay_time));
                }
            }
            /* Adding appointment delay time for service provider ends */
        }

        // Update cache records
        //DataCache::updateData($this->CacheKey, $this->fetchAll());
    }

    public function deleteServiceProviderMedia($id)
    {
        $this->tableGateway->delete(array('id' => (int) $id));

        // Update cache data
        //DataCache::updateData($this->CacheKey,$this->fetchAll(false));
    }

    public function changeStatus($id, $status)
    {
        $this->tableGateway->update(array('status_id' => $status), array('id' => $id));
    }

}
