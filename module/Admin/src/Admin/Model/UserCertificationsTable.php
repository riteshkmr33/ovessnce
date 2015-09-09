<?php

namespace Admin\Model;

use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\DataCache;
use Zend\Db\Sql\Select;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

class UserCertificationsTable
{

    protected $tableGateway;
    private $CacheKey = 'user-certifications';

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll($paginate = true)
    {
        if ($paginate) {

            $select = new Select('user_certification');
            $select->join('users', 'user_certification.user_id = users.id', array('user_name', 'first_name', 'last_name'), 'left');
            $select->join('lookup_status', 'lookup_status.status_id = user_certification.status_id', array('status'), 'left');
            $resultSetPrototype = new ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new UserCertifications());

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
        $select->join('users', 'user_certification.user_id = users.id', array('user_name', 'first_name', 'last_name'), 'left');
        $select->join('lookup_status', 'lookup_status.status_id = user_certification.status_id', array('status'), 'left');

        /* Data filter code start here */
        if (count($filter) > 0) {

            // Filter code goes here
        }
        /* Data filter code end here */

        /* Data sorting code start here */
        if (count($orderBy) > 0) {

            // sorting code goes here
        }
        /* Data sorting code end here */

        return $this->tableGateway->selectwith($select);
    }

    public function getCertification($id)
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

    public function getCertifications()
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

    public function saveCertification(UserCertifications $certification)
    {
        $data = array(
            'id' => $certification->id,
            'user_id' => $certification->user_id,
            'title' => $certification->title,
            'logo' => $certification->logo,
            'professional_licence_number' => $certification->professional_licence_number,
            'organization_name' => $certification->organization_name,
            'certification_date' => $certification->certification_date,
            'validity' => $certification->validity,
            'status_id' => $certification->status_id,
        );

        foreach ($data as $key => $value) {
            if ($value == null) {
                unset($data[$key]);
            }
        }

        $id = (int) $certification->id;
        if ($id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getCertification($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('Certification id does not exist');
            }
        }

        // Update cache records
        //DataCache::updateData($this->CacheKey, $this->fetchAll());
    }

    public function deleteCertification($id)
    {

        $this->tableGateway->delete(array('id' => (int) $id));

        // Update cache records
        //DataCache::updateData($this->CacheKey, $this->fetchAll());
    }

}
