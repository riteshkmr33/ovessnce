<?php

namespace Admin\Model;

use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\DataCache;
use Zend\Db\Sql\Select;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;
use Zend\Db\Sql\Expression;

class RatingsTable
{

    protected $tableGateway;
    private $CacheKey = 'ratings';

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll($paginate = true)
    {
        if ($paginate) {
            $select = new Select('rating');
            //$select->columns(array('*', new Expression("service_provider_contact.first_name AS sp_first_name, service_provider_contact.last_name AS sp_last_name")));
            $select->columns(array('*', new Expression("service_provider_contact.first_name AS sp_first_name, service_provider_contact.last_name AS sp_last_name")));
            $select->join(array('service_provider_contact'=>'users'), 'service_provider_contact.id = rating.users_id', array(), 'left');
            $select->join('users', 'users.id = rating.created_by', array('first_name', 'last_name'), 'left');
           // $select->join('service_provider_service', 'service_provider_service.id = rating.service_id', array('duration'), 'left');
            //$select->join('service_category', 'service_category.id = service_provider_service.service_id', array('category_name'), 'left');
            $select->join('lookup_rating', 'lookup_rating.id = rating.rating_type_id', array('rating_type'), 'left');
            //echo str_replace('"', '', $select->getSqlString()); exit;
			
            $resultSetPrototype = new ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new Ratings());

            $paginatorAdapter = new DbSelect(
                    $select, $this->tableGateway->getAdapter(), $resultSetPrototype
            );
            $paginator = new Paginator($paginatorAdapter); 
            
            return $paginator;
        }
        return $this->tableGateway->select();
    }

    //public function getRating($user, $service, $created_by, $rating_type_id)
    public function getRating($user, $created_by, $rating_type_id)
    {
        $id = (int) $id;
        $select = $this->tableGateway->getSql()->select();
        $select->join('lookup_rating', 'lookup_rating.id = rating.rating_type_id', array('rating_type'), 'left');
        //$select->where(array('users_id' => (int) $user, 'service_id' => (int) $service, 'rating.created_by' => $created_by, 'rating_type_id' => $rating_type_id));
        $select->where(array('users_id' => (int) $user, 'rating.created_by' => $created_by, 'rating_type_id' => $rating_type_id));
        $rowset = $this->tableGateway->selectwith($select);
        $row = $rowset->current();
        if (!$row) {
            return false;
            //throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function getRatings()
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

    public function saveRating(Ratings $rating, $ratings, $user)
    {
        if (isset($ratings) && count($ratings) > 0) {
            $data = array(
                'users_id' => $rating->users_id,
              //  'service_id' => $rating->service_id,
                'created_by' => $user->id,
                'created_date' => date("Y-m-d h:i:s")
            );

            // delete old entries
            //$this->deleteRating($rating->users_id, '', $user->id);
            $this->deleteRating($rating->users_id, $user->id);

            foreach ($ratings as $key => $value) {
                $data['rating_type_id'] = $key;

                $data['rate'] = $value;

                $this->tableGateway->insert($data);
            }
            return true;
            // Update cache records
            //DataCache::updateData($this->CacheKey, $this->fetchAll());
        } else {
            return false;
        }
    }

    //public function deleteRating($users_id, $service_id, $created_by, $rating_type_id = array())
    public function deleteRating($users_id, $created_by, $rating_type_id = array())
    {
        $delete = $this->tableGateway->getSql()->delete();
        $where = array();
        ($users_id!='')?$where['users_id'] = $users_id:'';
        //($service_id!='')?$where['service_id'] = $service_id:'';
        ($created_by!='')?$where['created_by'] = $created_by:'';
        $delete->where($where);
        (count($rating_type_id) > 0) ? $delete->where(array('rating_type_id' => $rating_type_id)) : "";
        $this->tableGateway->deletewith($delete);
		
        // Update cache records
        //DataCache::updateData($this->CacheKey, $this->fetchAll());
    }

}
