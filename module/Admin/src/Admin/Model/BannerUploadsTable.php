<?php

namespace Admin\Model;

use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\DataCache;
use Zend\Db\Sql\Select;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

class BannerUploadsTable
{

    protected $tableGateway;
    private $CacheKey = 'banners';
    private $banner_booking;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
        $adapter = $this->tableGateway->getAdapter();
        $this->banner_booking = new TableGateway('banner_booking', $adapter);
    }

    public function fetchAll($booking_id, $paginate = true, $filter = array())
    {
        if ($paginate) {
            $select = new Select('publisher_banner');
            $select->join('banner_booking', 'banner_booking.id = publisher_banner.booking_id', array(), 'inner');
            $select->join('lookup_status', 'lookup_status.status_id = publisher_banner.status_id', array('status'), 'left');
            $select->where(array('publisher_banner.booking_id' => $booking_id));

            /* Data filter code start here */
            if (count($filter) > 0) {

                // Filter code goes here
            }
            /* Data filter code end here */

            //echo str_replace('"','',$select->getSqlString()); //exit;

            $resultSetPrototype = new ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new BannerUploads());

            $paginatorAdapter = new DbSelect(
                    $select, $this->tableGateway->getAdapter(), $resultSetPrototype
            );
            $paginator = new Paginator($paginatorAdapter);

            return $paginator;
        }
        return $this->tableGateway->select();
    }

    public function getBannerUpload($id)
    {
        $id = (int) $id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            return false;
            //throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function getBookingDetails($id)
    {
        $id = (int) $id;
        $select = $this->banner_booking->getSql()->select();
        $select->join('advertisement_plan', 'advertisement_plan.id = banner_booking.advertisement_plan_id', array(), 'inner');
        $select->where(array('banner_booking.id' => $id));

        $rowset = $this->banner_booking->selectwith($select);
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function getBannerUploads()
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

    public function saveBannerUpload(BannerUploads $bu)
    {
        $data = array(
            'user_id' => $bu->user_id,
            'booking_id' => $bu->booking_id,
            'banner_type' => $bu->banner_type,
            'banner_title' => $bu->banner_title,
            'banner_content' => $bu->banner_content,
            'target_url' => $bu->target_url,
            'status_id' => $bu->status_id,
        );

        $id = (int) $bu->id;
        if ($id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getBannerUpload($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('Banner id does not exist');
            }
        }

        // Update cache records
        //DataCache::updateData($this->CacheKey, $this->fetchAll());
    }

    public function changeStatus($id, $status)
    {
        $this->tableGateway->update(array('status_id' => $status), array('id' => $id));
    }

    public function deleteBannerUpload($id)
    {
        $this->tableGateway->delete(array('id' => (int) $id));

        // Update cache records
        //DataCache::updateData($this->CacheKey, $this->fetchAll());
    }

    public function getUploadCount($date)
    {

        $data = array();
        $adapter = $this->tableGateway->getAdapter();

        $sql = "SELECT COUNT(*) AS upload_count FROM `publisher_banner` WHERE banner_type = 2 AND date_format(date(publisher_banner.created_date ),'%Y-%m-%d') >= '" . $date . "' ";

        $statement = $adapter->query($sql);
        $result = $statement->execute();

        foreach ($result as $key => $value) {
            $data['upload_count'] = $value['upload_count'];
        }

        return $data;
    }

}
