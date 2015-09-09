<?php

namespace Admin\Model; 

use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\DataCache;
use Zend\Db\Sql\Select; 
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;
use Zend\Db\Sql\Expression;

class ServicesTable
{
    protected $tableGateway;
    private $CacheKey = 'services';
    private $service_provider_commision;
    private $site_settings;
    
    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
        $adapter=$this->tableGateway->getAdapter();
        $this->service_provider_commision = new TableGateway('service_provider_site_commision', $adapter);
        $this->site_settings = new TableGateway('site_settings', $adapter);
    }
    
    public function fetchAll($paginate=true)
    {
		if ($paginate) {
			$select = new Select('service_provider_service');
			$select->join('service_category','service_category.id = service_provider_service.service_id',array('category_name'),'left');
			$select->join('lookup_status','lookup_status.status_id = service.status_id',array('status'),'left');
			
			$resultSetPrototype = new ResultSet();
			$resultSetPrototype->setArrayObjectPrototype(new Services());
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
			$select->order('service_category.category_name ASC');
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
    
    public function getPractitionerServices($id="", $service="", $duration="")
    {
		
		$select = $this->tableGateway->getSql()->select();
		$select->join('service_category','service_category.id = service_provider_service.service_id', array('category_name'), 'left');
		if ($id != "") { $select->where(array('user_id' => $id)); }
		if ($service != "" && $service != "All") { $select->where(array('service_provider_service.id' => $service)); }
		if ($duration != "") { $select->where(array('duration' => (int)$duration)); }
		//echo str_replace('"','',$select->getSqlString()); exit;
		$results = $this->tableGateway->selectwith($select);
		$data = array();
		if ($service != "" && $duration != "") {
			$data['price'] = $results->current()->price;
			$data['id'] = $results->current()->id;
			$commision = $this->service_provider_commision->select(array('user_id' => $id, 'status_id' => 1));
			 
			if ($commision->current()->commision) {
				// $data['commision'] = number_format((($commision->current()->commision/100)*$results->current()->price), 2);
				$data['commision'] = number_format($commision->current()->commision, 2); 
			} else {
				
				$site_settings = $this->site_settings->getSql()->select();
				$site_settings->columns(array(new Expression('site_settings.setting_value as default_comission')));
				
				$site_settings->where('site_settings.id=1');
				$results = $this->site_settings->selectwith($site_settings);
				
				$data['commision'] =$results->current()->default_comission;
			}
		} else if ($service != "") {
			foreach ($results as $result) {
				$data[] = array('id' => $result->duration, 'duration' => $result->duration." mins");
			}
		} else {
			foreach ($results as $result) {
				$data[] = array('id' => $result->id, 'service' => $result->category_name);
			}
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
