<?php

namespace Admin\Model; 

use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\DataCache;
use Zend\Db\Sql\Select;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

class ServiceProviderServicesTable
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
			
			$select = new Select('service_provider_service');
			$select->join('service_category','service_category.id = service_provider_service.service_id',array('category_name'),'left');
			$select->join('lookup_status','lookup_status.status_id = service_provider_service.status_id',array('status'),'left');
			$select->where(array('user_id' => $id));
			
			$resultSetPrototype = new ResultSet();
			$resultSetPrototype->setArrayObjectPrototype(new ServiceProviderServices());
			
			$paginatorAdapter = new DbSelect(
				$select,
				$this->tableGateway->getAdapter(),
				$resultSetPrototype
			);
			$paginator = new Paginator($paginatorAdapter);
			
			return $paginator;
		} else {
			$select = $this->tableGateway->getSql()->select();
			$select->join('service_category','service_category.id = service_provider_service.service_id',array('category_name'),'left');
			if ($id != "") { $select->where(array('user_id' => $id)); }
			$select->order('category_name ASC');	
			return $this->tableGateway->selectwith($select);
		}
    }
    
    public function getService($id)
    {
        $id  = (int) $id;
        $select = $this->tableGateway->getSql()->select();
        $select->join('service_category','service_category.id = service_provider_service.service_id',array('category_name'),'left');
        $select->where(array('service_provider_service.id' => $id));
        $rowset = $this->tableGateway->selectwith($select);
        $row = $rowset->current();
        if (!$row) {
			return false;
            //throw new \Exception("Could not find row $id");
        }
        return $row;
    }
    
    public function getPractitionerServices($id="")
    {
		$select = $this->service_provider_service->getSql()->select();
		$select->join('service_category','service_category.id = service_provider_service.service_id', array('category_name'), 'left');
		if ($id != "") { $select->where(array('user_id' => $id)); }
		//echo str_replace('"','',$select->getSqlString());
		$results = $this->service_provider_service->selectwith($select);
		$data = array();
		
		foreach ($results as $result) {
			$data[] = array('id' => $result->id, 'service' => $result->category_name." - ".$result->duration." mins");
		}
		
		return $data;
	}
     
	public function getServices()
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
  
    public function saveService(ServiceProviderServices $service)
    {
        $data = array(
            'service_id' => $service->service_id,
            'user_id' => $service->user_id,
            'duration'  => $service->duration,
            'price'  => $service->price,
            'status_id'  => $service->status_id,
        );

        $id = (int) $service->id;
        if ($id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getService($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('Service id does not exist');
            }
        }
        
        // Update cache records
        //DataCache::updateData($this->CacheKey, $this->fetchAll());
    }
    
    public function changeStatus($id, $status)
    {
		$this->tableGateway->update(array('status_id'=>$status), array('id' => $id));
	}

    public function deleteService($id)
    {
        $this->tableGateway->delete(array('id' => (int) $id));
        
        // Update cache records
        //DataCache::updateData($this->CacheKey, $this->fetchAll());
    }
}
