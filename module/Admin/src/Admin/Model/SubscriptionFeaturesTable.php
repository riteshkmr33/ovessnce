<?php

namespace Admin\Model;

use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\DataCache;
use Zend\Db\Sql\Select;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

class SubscriptionFeaturesTable
{

    protected $tableGateway;
    private $CacheKey = 'countries';

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll($paginate = true, $filter = array(), $orderBy = array())
    {
        if ($paginate) {

            $select = new Select('site_feature');   // create a new Select object for the table country
            $select->join('lookup_status', 'lookup_status.status_id = site_feature.status_id', array('status'), 'left');

            /* Data filter code start here */
            if (count($filter) > 0) {
                ($filter['feature_name'] != "") ? $select->where("site_feature.feature_name LIKE '%" . $filter['feature_name'] . "%'") : "";
                ($filter['status_id'] != "") ? $select->where("site_feature.status_id = " . $filter['status_id']) : "";
                //echo str_replace('"','',$select->getSqlString()); //exit;
            }
            /* Data filter code end here */

            /* Data sorting code starts here */
            if (count($orderBy) > 0 && $orderBy['sort_field'] != '' && $orderBy['sort_order'] != '') {
                switch ($orderBy['sort_field']) {

                    case 'feature_name' :
                        $select->order('site_feature.feature_name ' . $orderBy['sort_order']);
                        break;
                }
            } else {
                $select->order('site_feature.feature_name ASC');
            }
            /* Data sorting code ends here */

            $resultSetPrototype = new ResultSet(); // create a new result set based on the country entity
            $resultSetPrototype->setArrayObjectPrototype(new SubscriptionFeatures());

            // create a new pagination adapter object
            $paginatorAdapter = new DbSelect(
                    $select, // our configured select object
                    $this->tableGateway->getAdapter(), // the adapter to run it against
                    $resultSetPrototype   // the result set to hydrate
            );
            $paginator = new Paginator($paginatorAdapter);

            return $paginator;
        }
        return $this->tableGateway->select(function(Select $select) {
                    $select->order('site_feature.feature_name ASC');
                });
    }

    public function getFeature($id)
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

    public function getFeatures()
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

    public function saveFeature(SubscriptionFeatures $sf)
    {
        $data = array(
            'description' => $sf->description,
        );

        $id = (int) $sf->id;
        
        if ($this->getFeature($id)) {
            $this->tableGateway->update($data, array('id' => $id));
        } else {
            throw new \Exception('Subscription Feature id does not exist');
        }
        

        // Update cache records
        //DataCache::updateData($this->CacheKey, $this->fetchAll());
    }

    public function changeStatus($id, $status)
    {
        $this->tableGateway->update(array('status_id' => $status), array('id' => $id));
    }

}
