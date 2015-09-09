<?php

namespace Admin\Model;

use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\DataCache;
use Zend\Db\Sql\Select;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

class SiteBannersTable
{

    protected $tableGateway;
    private $CacheKey = 'sitebanners';

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll($paginate = true, $filter = array())
    {
        if ($paginate) {

            $select = new Select('banners');
            $select->join('advertisement_page', 'advertisement_page.id = banners.page_location_id', array('page_name'), 'left');
            $select->join('lookup_status', 'lookup_status.status_id = banners.status_id', array('status'), 'left');

            /* Data filter code start here */
            if (count($filter) > 0) {

                // Filter code goes here
                //echo str_replace('"','',$select->getSqlString()); //exit;
            }
            /* Data filter code end here */

            $resultSetPrototype = new ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new SiteBanners());

            $paginatorAdapter = new DbSelect(
                    $select, $this->tableGateway->getAdapter(), $resultSetPrototype
            );
            $paginator = new Paginator($paginatorAdapter);

            return $paginator;
        }
        return $this->tableGateway->select();
    }

    public function getBanner($id)
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

    public function getBanners()
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

    public function saveBanner(SiteBanners $banner)
    {
        $data = array(
            'page_location_id' => $banner->page_location_id,
            'banner_url' => $banner->banner_url,
            'title' => $banner->title,
            'status_id' => $banner->status_id,
        );

        $id = (int) $banner->id;
        if ($id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getBanner($id)) {
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

    public function deleteBanner($id)
    {
        $this->tableGateway->delete(array('id' => (int) $id));

        // Update cache records
        //DataCache::updateData($this->CacheKey, $this->fetchAll());
    }

}
