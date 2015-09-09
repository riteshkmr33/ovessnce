<?php

namespace Admin\Model;

use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\DataCache;
use Zend\Db\Sql\Select;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

class AdvertisementTable
{

    protected $tableGateway;
    private $CacheKey = 'advertisement';

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll($paginate = true, $filter = array())
    {
        if ($paginate) {

            $select = new Select('advertisement');
            $select->join('lookup_status', 'lookup_status.status_id = advertisement.status_id', array('status'), 'left');

            /* Data filter code start here */
            if (count($filter) > 0) {

                // Filter code goes here
                //echo str_replace('"','',$select->getSqlString()); //exit;
            }
            /* Data filter code end here */

            $resultSetPrototype = new ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new Advertisement());

            $paginatorAdapter = new DbSelect($select, $this->tableGateway->getAdapter(), $resultSetPrototype);
            $paginator = new Paginator($paginatorAdapter);

            return $paginator;
        }
        return $this->tableGateway->select();
    }

    public function getAdvertisement($id)
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

    public function saveAdvertisement(Advertisement $ad)
    {
        $data = array(
            'banner_name' => $ad->banner_name,
            'banner_height' => $ad->banner_height,
            'banner_width' => $ad->banner_width,
            'status_id' => $ad->status_id,
        );

        $id = (int) $ad->id;
        if ($id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getAdvertisement($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('Advertisement id does not exist');
            }
        }
    }

    public function changeStatus($id, $status)
    {
        $this->tableGateway->update(array('status_id' => $status), array('id' => $id));
    }

    public function deleteAdvertisement($id)
    {
        $this->tableGateway->delete(array('id' => (int) $id));
    }

}
