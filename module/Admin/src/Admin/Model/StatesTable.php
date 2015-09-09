<?php

namespace Admin\Model; 

use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\DataCache;
use Zend\Db\Sql\Select;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

class StatesTable
{
    protected $tableGateway;
    private $CacheKey = 'countries';
    
    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }
    
    public function fetchAll($paginate=true, $filter=array(), $orderBy = array())
    {
		if ($paginate) {
			
			$select = new Select('state');
			$select->join('country','country.id = state.country_id',array('country_name'),'left');
			$select->join('lookup_status','lookup_status.status_id = state.status_id',array('status'),'left');
			
			/* Data filter code start here*/
			if (count($filter)>0) {
				
				($filter['country_id'] != "")?$select->where("state.country_id = ".$filter['country_id']):"";
				($filter['state_code'] != "")?$select->where("state.state_code LIKE '%".$filter['state_code']."%'"):"";
				($filter['state_name'] != "")?$select->where("state.state_name LIKE '%".$filter['state_name']."%'"):"";
				($filter['status_id'] != "")?$select->where("state.status_id = ".$filter['status_id']):"";
			}
			/* Data filter code end here*/
			
			/* Data sorting code starts here */
			if (count($orderBy)>0 && $orderBy['sort_field'] != '' && $orderBy['sort_order'] != '') {
				switch ($orderBy['sort_field']) {
					case 'state_name' :
						$select->order('state.state_name '.$orderBy['sort_order']);
						break;
						
					case 'country' :
						$select->order('country.country_name '.$orderBy['sort_order']);
						break;
						
					case 'status' :
						$select->order('lookup_status.status '.$orderBy['sort_order']);
						break;
				}
			} else {
				$select->order('state.state_name ASC');
			}
			/* Data sorting code ends here */
			
			//echo str_replace('"','',$select->getSqlString()); exit;
			
			$resultSetPrototype = new ResultSet();
			$resultSetPrototype->setArrayObjectPrototype(new States());
			
			$paginatorAdapter = new DbSelect(
				$select,
				$this->tableGateway->getAdapter(),
				$resultSetPrototype
			);
			$paginator = new Paginator($paginatorAdapter);
			
			return $paginator;
		}
		return $this->tableGateway->select(function(Select $select){
			$select->order('state_name ASC');
		});
    }
    
    public function getState($id)
    {
        $id  = (int) $id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
			return false;
            //throw new \Exception("Could not find row $id");
        }
        return $row;
    }
    
    public function getStatesByCountry($country="")
    {
		$select = $this->tableGateway->getSql()->select();
		($country != "")?$select->where(array('country_id' => $country)):''; 
		$select->order('state.state_name ASC');
		return $this->tableGateway->selectwith($select);
	}
    
	public function getStates()
	{
		$result = DataCache::getData($this->CacheKey);
		
		// Update cache if data not found
		if ($result == false) {
			$result = $this->fetchAll();
			
			// Update cache records
			DataCache::updateData($this->CacheKey,$result);
			
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
  
    public function saveState(States $state) 
    {
        $data = array(
            'state_code' => $state->state_code,
            'country_id' => $state->country_id,
            'state_name'  => $state->state_name,
        );

        $id = (int) $state->id;
        if ($id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getState($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('State id does not exist');
            }
        }
        
        // Update cache records
        //DataCache::updateData($this->CacheKey, $this->fetchAll());
    }
    
    public function changeStatus($id, $status)
    {
		$this->tableGateway->update(array('status_id' => $status),array('id' => $id));
	}

    public function deleteState($id)
    {
        $this->tableGateway->delete(array('id' => (int) $id));
        
        // Update cache records
        //DataCache::updateData($this->CacheKey, $this->fetchAll());
    }
}
