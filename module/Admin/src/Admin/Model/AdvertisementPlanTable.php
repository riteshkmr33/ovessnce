<?php

namespace Admin\Model;

use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\DataCache;
use Zend\Db\Sql\Select;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;
use Zend\Db\Sql\Expression;

class AdvertisementPlanTable
{

    protected $tableGateway;
    private $CacheKey = 'advertisementplan';

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll($paginate = true, $filter = array())
    {
        if ($paginate) {

            $select = new Select('advertisement_plan');
            $select->columns(array(new Expression("advertisement_plan.id, advertisement_plan.plan_name, duration, price, CASE duration_in WHEN 1 THEN 'Years' WHEN 2 THEN 'Months' WHEN 3 THEN 'Days' END AS duration_in")));
            $select->join('advertisement', 'advertisement.id = advertisement_plan.advertisement_id', array('banner_name'), 'inner');
            $select->join('advertisement_page', 'advertisement_page.id = advertisement_plan.advertisement_page_id', array('page_name'), 'inner');
            $select->where($filter);
            
            $resultSetPrototype = new ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new AdvertisementPlan());

            $paginatorAdapter = new DbSelect($select, $this->tableGateway->getAdapter(), $resultSetPrototype);
            $paginator = new Paginator($paginatorAdapter);

            return $paginator;
        } else {
            $select = $this->tableGateway->getSql()->select();
            $select->columns(array(new Expression("advertisement_plan.id, advertisement_plan.plan_name, duration, price, CASE duration_in WHEN 1 THEN 'Years' WHEN 2 THEN 'Months' WHEN 3 THEN 'Days' END AS duration_in")));
            $select->join('advertisement', 'advertisement.id = advertisement_plan.advertisement_id', array('banner_name'), 'inner');
            $select->join('advertisement_page', 'advertisement_page.id = advertisement_plan.advertisement_page_id', array('page_name'), 'inner');
            $select->where($filter);
            //echo str_replace('"', '', $select->getSqlString()); exit;
            return $this->tableGateway->selectwith($select);
        }
    }

    public function getAdvertisementPlan($id)
    {
        $id = (int) $id;
        $select = $this->tableGateway->getSql()->select();
        $select->columns(array(new Expression("advertisement_plan.id, advertisement_plan.advertisement_page_id, advertisement_plan.plan_name, duration, price, CASE duration_in WHEN 1 THEN 'Years' WHEN 2 THEN 'Months' WHEN 3 THEN 'Days' END AS duration_unit, duration_in")));        
        $select->join('advertisement', 'advertisement.id = advertisement_plan.advertisement_id', array('banner_height', 'banner_width'), 'inner');
        $select->join('advertisement_page', 'advertisement_page.id = advertisement_plan.advertisement_page_id', array(), 'inner');
        $select->where(array('advertisement_plan.id' => $id));
        //echo str_replace('"', '', $select->getSqlString()); exit;

        $rowset = $this->tableGateway->selectwith($select);
        $row = $rowset->current();
        if (!$row) {
            return false;
            //throw new \Exception("Could not find row $id");
        }
        return $row;
    }
    
    public function getBannerDetails($booking_id)
    {
        $id = (int) $id;
        $select = $this->tableGateway->getSql()->select();
        $select->join('banner_booking', 'banner_booking.advertisement_plan_id = advertisement_plan.id', array(), 'inner');
        $select->join('advertisement', 'advertisement.id = advertisement_plan.advertisement_id', array('banner_height', 'banner_width'), 'inner');
        $select->where(array('banner_booking.id' => $booking_id));
        //echo str_replace('"', '', $select->getSqlString()); exit;

        $rowset = $this->tableGateway->selectwith($select);
        $row = $rowset->current();
        if (!$row) {
            return false;
            //throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function saveAdvertisementPlan(AdvertisementPlan $ap)
    {
        $data = array(
            'advertisement_id' => $ap->advertisement_id,
            'advertisement_page_id' => $ap->advertisement_page_id,
            'plan_name' => $ap->plan_name,
            'duration' => $ap->duration,
            'duration_in' => $ap->duration_in,
            'price' => $ap->price,
        );

        $id = (int) $ap->id;
        if ($id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getAdvertisementPlan($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('Advertisement Plan id does not exist');
            }
        }
    }

    public function deleteAdvertisementPlan($id)
    {
        $this->tableGateway->delete(array('id' => (int) $id));
    }

}
