<?php

namespace Admin\Model;

use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\DataCache;
use Zend\Db\Sql\Select;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

class StatusTable
{

    protected $tableGateway;
    private $CacheKey = 'status';

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll($paginate = true, $ids = array())
    {
        if ($paginate) {

            $select = new Select('lookup_status');

            $resultSetPrototype = new ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new Activity());

            $paginatorAdapter = new DbSelect(
                    $select, $this->tableGateway->getAdapter(), $resultSetPrototype
            );
            $paginator = new Paginator($paginatorAdapter);

            return $paginator;
        } else {
            $select = $this->tableGateway->getSql()->select();
            if (is_array($ids) && count($ids) > 0) {
                $select->where('status_id IN (' . implode(",", $ids) . ')');
            }
            $select->order('status ASC');

            return $this->tableGateway->selectwith($select);
        }
    }

    public function getStatus($id)
    {
        $id = (int) $id;
        $rowset = $this->tableGateway->select(array('status_id' => $id));
        $row = $rowset->current();
        if (!$row) {
            return false;
            //throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function getAllStatus()
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

    public function saveStatus(Status $status)
    {
        $data = array(
            'status' => $status->status,
        );

        $id = (int) $status->status_id;
        if ($id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getStatus($id)) {
                $this->tableGateway->update($data, array('status_id' => $id));
            } else {
                throw new \Exception('Status id does not exist');
            }
        }

        // Update cache records
        //DataCache::updateData($this->CacheKey, $this->fetchAll());
    }

    public function deleteStatus($id)
    {
        $this->tableGateway->delete(array('status_id' => (int) $id));

        // Update cache records
        //DataCache::updateData($this->CacheKey, $this->fetchAll());
    }

}
