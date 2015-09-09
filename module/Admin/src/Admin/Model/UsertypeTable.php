<?php

namespace Admin\Model;

use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\DataCache;
use Zend\Db\Sql\Select;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

class UsertypeTable
{

    protected $tableGateway;
    private $CacheKey = 'userTypes';

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll($paginate = true, $ids = array())
    {
        if ($paginate) {

            $select = new Select('lookup_user_type');
            $resultSetPrototype = new ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new Usertype());

            $paginatorAdapter = new DbSelect(
                    $select, $this->tableGateway->getAdapter(), $resultSetPrototype
            );

            $paginator = new Paginator($paginatorAdapter);

            return $paginator;
        } else {
            $select = $this->tableGateway->getSql()->select();
            if (is_array($ids) && count($ids) > 0) {
                    $select->where('id IN (' . implode(",", $ids) . ')');
                }
            $select->order('user_type ASC');
            return $this->tableGateway->selectwith($select);
        }
    }

    public function getUsertype($id)
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

    public function getUserTypes()
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
                DataCache::updateData($key, $result);
            }
            return $result;
        }
    }

    public function saveUsertype(Usertype $Usertype)
    {
        $data = array(
            'id' => $Usertype->id,
            'user_type' => $Usertype->user_type,
        );

        $id = (int) $Usertype->id;
        if ($id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getUsertype($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('user type id does not exist');
            }
        }

        // Update Cache records
        //DataCache::updateData($this->CacheKey,$this->fetchAll());
    }

    public function deleteUsertype($id)
    {
        $this->tableGateway->delete(array('id' => (int) $id));

        // Update Cache records
        //DataCache::updateData($this->CacheKey,$this->fetchAll());
    }

}
