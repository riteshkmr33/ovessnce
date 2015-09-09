<?php

namespace Admin\Model; 

use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\DataCache;
use Zend\Db\Sql\Select;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

class ServiceProviderCommisionsTable
{
    protected $tableGateway;
    private $CacheKey = 'serviceproviderservice';
    
    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
        $adapter=$this->tableGateway->getAdapter();
    }
    
    public function fetchAll($id,$paginate=true)
    {
		if ($paginate) {
			
			$select = new Select('service_provider_site_commision');
			$select->join('users','users.id = service_provider_site_commision.user_id',array('first_name', 'last_name'),'inner');
			$select->join('lookup_status','lookup_status.status_id = service_provider_site_commision.status_id',array('status'),'left');
			$select->where(array('user_id' => $id));
			$select->order('service_provider_site_commision.created_date DESC');
			
			$resultSetPrototype = new ResultSet();
			$resultSetPrototype->setArrayObjectPrototype(new ServiceProviderCommisions());
			
			$paginatorAdapter = new DbSelect(
				$select,
				$this->tableGateway->getAdapter(),
				$resultSetPrototype
			);
			$paginator = new Paginator($paginatorAdapter);
			
			return $paginator;
		} else {
			$select = $this->tableGateway->getSql()->select();
			$select->join('users','users.id = service_provider_site_commision.user_id',array('first_name', 'last_name'),'inner');
			$select->join('lookup_status','lookup_status.status_id = service_provider_site_commision.status_id',array('status'),'left');
			if ($id != "") { $select->where(array('user_id' => $id)); }
			return $this->tableGateway->selectwith($select);
		}
    }
    
    public function getServiceProviderCommision($id)
    {
        $id  = (int) $id;
        $select = $this->tableGateway->getSql()->select();
        $select->join('users','users.id = service_provider_site_commision.user_id',array('first_name', 'last_name'),'inner');
		$select->join('lookup_status','lookup_status.status_id = service_provider_site_commision.status_id',array('status'),'left');
        $select->where(array('service_provider_site_commision.id' => $id));
        $rowset = $this->tableGateway->selectwith($select);
        $row = $rowset->current();
        if (!$row) {
			return false;
            //throw new \Exception("Could not find row $id");
        }
        return $row;
    }
    
    public function getCommision($user_id)
    {
		$rowset = $this->tableGateway->select(array('user_id' => $user_id, 'status_id' => 1));
		$row = $rowset->current();
		if ($row) {
			return $row->commision;
		} else {
			return 0;
		}
	}
     
	public function getServiceProviderCommisions()
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
  
    public function saveServiceProviderCommision(ServiceProviderCommisions $spc)
    {
        $data = array(
            'user_id' => $spc->user_id,
            'commision'  => $spc->commision,
            'status_id'  => 1,
            'created_date' => date('Y-m-d h:i:s'),
        );

        $id = (int) $spc->id;
        if ($id == 0) {
			
			// Deactivating other commisions for this user
			$this->tableGateway->update(array('status_id' => 2), array('user_id' => $spc->user_id));
			
            $this->tableGateway->insert($data);
        } else {
            if ($this->getService($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('Service Provider Commision id does not exist');
            }
        }
        
        // Update cache records
        //DataCache::updateData($this->CacheKey, $this->fetchAll());
    }
    
    public function changeStatus($id, $status)
    {
		$this->tableGateway->update(array('status_id'=>$status), array('id' => $id));
	}

    public function deleteServiceProviderCommision($id)
    {
        $this->tableGateway->delete(array('id' => (int) $id));
        
        // Update cache records
        //DataCache::updateData($this->CacheKey, $this->fetchAll());
    }
}
