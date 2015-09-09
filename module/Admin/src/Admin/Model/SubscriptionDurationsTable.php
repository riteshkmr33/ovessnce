<?php

namespace Admin\Model;

use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\DataCache;
use Zend\Db\Sql\Select;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;
use Zend\Db\Sql\Expression;

class SubscriptionDurationsTable
{

    protected $tableGateway;
    private $CacheKey = 'subscriptiondurations';

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll($subscription_id = "", $paginate = true, $filter = array())
    {
        if ($paginate) {

            $select = new Select('subscription_duration');
            $select->columns(array(new Expression('subscription_duration.id, subscription_duration.duration, subscription_duration.price, subscription_duration.status_id, CASE subscription_duration.duration_in WHEN 1 THEN "Years" WHEN 2 THEN "Months" WHEN 3 THEN "Days" WHEN 4 THEN "Lifetime" END AS durationin, subscription_duration.duration_in')));
            $select->join('subscription', 'subscription.id = subscription_duration.subscription_id', array('subscription_name'), 'inner');
            $select->join('lookup_status', 'lookup_status.status_id = subscription_duration.status_id', array('status'), 'left');
            ($subscription_id != "") ? $select->where(array('subscription_id' => $subscription_id)) : "";
            //echo str_replace('"','',$select->getSqlString()); exit;

            $resultSetPrototype = new ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new SubscriptionDurations());

            $paginatorAdapter = new DbSelect(
                    $select, $this->tableGateway->getAdapter(), $resultSetPrototype
            );
            $paginator = new Paginator($paginatorAdapter);

            return $paginator;
        } else {
            $select = $this->tableGateway->getSql()->select();
            $select->columns(array(new Expression('subscription_duration.id, subscription_duration.duration, subscription_duration.price, subscription_duration.status_id, CASE subscription_duration.duration_in WHEN 1 THEN "Years" WHEN 2 THEN "Months" WHEN 3 THEN "Days" WHEN 4 THEN "Lifetime" END AS durationin, subscription_duration.duration_in')));
            $select->join('subscription', 'subscription.id = subscription_duration.subscription_id', array('subscription_name'), 'inner');
            $select->join('lookup_status', 'lookup_status.status_id = subscription_duration.status_id', array('status'), 'left');
            ($subscription_id != "") ? $select->where(array('subscription_id' => $subscription_id)) : "";

            return $this->tableGateway->selectwith($select);
        }
    }

    public function getSubscriptionDuration($id)
    {
        $id = (int) $id;
        $select = $this->tableGateway->getSql()->select();
        $select->columns(array(new Expression('subscription_duration.id, subscription_duration.subscription_id, subscription_duration.duration, subscription_duration.price, subscription_duration.status_id, CASE subscription_duration.duration_in WHEN 1 THEN "Years" WHEN 2 THEN "Months" WHEN 3 THEN "Days" WHEN 4 THEN "Lifetime" END AS durationin, subscription_duration.duration_in')));
        $select->join('subscription', 'subscription.id = subscription_duration.subscription_id', array('subscription_name'), 'inner');
        $select->join('lookup_status', 'lookup_status.status_id = subscription_duration.status_id', array('status'), 'left');
        $select->where('subscription_duration.id = ' . $id);
        $rowset = $this->tableGateway->selectwith($select);
        $row = $rowset->current();
        if (!$row) {
            return false;
            //throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function getSubscriptionDurations()
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

    public function saveSubscriptionDuration(SubscriptionDurations $sd)
    {
        $data = array(
            'subscription_id' => $sd->subscription_id,
            'duration' => $sd->duration,
            'duration_in' => $sd->duration_in,
            'price' => $sd->price,
            'status_id' => $sd->status_id,
        );

        /* Adding subscription duration */
        $id = (int) $sd->id;
        if ($id == 0) {
            $this->tableGateway->insert($data);
            $id = $this->tableGateway->lastInsertValue;
        } else {
            if ($this->getSubscriptionDuration($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('Subscription duration id does not exist');
            }
        }

        // Update cache records
        //DataCache::updateData($this->CacheKey, $this->fetchAll(false));
    }

    public function changeStatus($id, $status)
    {
        $this->tableGateway->update(array('status_id' => $status), array('id' => $id));
    }

    public function deleteSubscriptionDuration($id)
    {
        $this->tableGateway->delete(array('id' => (int) $id));

        // Update cache records
        //DataCache::updateData($this->CacheKey, $this->fetchAll(false));
    }

}
