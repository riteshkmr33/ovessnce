<?php

namespace Admin\Model; 

use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\DataCache;
use Zend\Db\Sql\Select;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

class CountriesTable
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
			
			$select = new Select('country');   // create a new Select object for the table country
			$select->join('lookup_status','lookup_status.status_id = country.status_id',array('status'),'left');
			
			/* Data filter code start here*/
			if (count($filter)>0) {
				
				($filter['country_code'] != "")?$select->where("country.country_code LIKE '%".$filter['country_code']."%'"):"";
				($filter['country_name'] != "")?$select->where("country.country_name LIKE '%".$filter['country_name']."%'"):"";
				($filter['status_id'] != "")?$select->where("country.status_id = ".$filter['status_id']):"";
				//echo str_replace('"','',$select->getSqlString()); //exit;
			}
			/* Data filter code end here*/
			
			/* Data sorting code starts here */
			if (count($orderBy)>0 && $orderBy['sort_field'] != '' && $orderBy['sort_order'] != '') {
				switch ($orderBy['sort_field']) {
					case 'country_code' :
						$select->order('country.country_code '.$orderBy['sort_order']);
						break;
						
					case 'country_name' :
						$select->order('country.country_name '.$orderBy['sort_order']);
						break;
				}
			} else {
				$select->order('country.country_name ASC');
			}
			/* Data sorting code ends here */
			
			$resultSetPrototype = new ResultSet(); // create a new result set based on the country entity
			$resultSetPrototype->setArrayObjectPrototype(new Countries());
			
			 // create a new pagination adapter object
			$paginatorAdapter = new DbSelect(
				$select,   // our configured select object
				$this->tableGateway->getAdapter(),   // the adapter to run it against
				$resultSetPrototype   // the result set to hydrate
			);  
			$paginator = new Paginator($paginatorAdapter);
			
			return $paginator;
		}
		return $this->tableGateway->select(function(Select $select){
			$select->order('country_name ASC');
		});
    }
    
    public function getCountry($id)
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
    
	public function getCountries()
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
  
    public function saveCountry(Countries $country)
    {
        $data = array(
            'country_code' => $country->country_code,
            'country_name'  => $country->country_name,
        );

        $id = (int) $country->id;
        if ($id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getCountry($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('Country id does not exist');
            }
        }
        
        // Update cache records
        //DataCache::updateData($this->CacheKey, $this->fetchAll());
    }
    
    public function changeStatus($id, $status)
    {
		$this->tableGateway->update(array('status_id' => $status),array('id' => $id));
	}

    public function deletePage($id)
    {
        $this->tableGateway->delete(array('id' => (int) $id));
        
        // Update cache records
        //DataCache::updateData($this->CacheKey, $this->fetchAll());
    }
}
