<?php

namespace Admin\Model; 

use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\DataCache;
use Zend\Db\Sql\Select;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;
use Zend\Db\Sql\Expression;

class ServiceCategoryTable
{
    protected $tableGateway;
    private $CacheKey = 'service_category';
    
    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
        $adapter = $this->tableGateway->getAdapter();
        $this->service_provider_service = new TableGateway('service_provider_service', $adapter);
    }
    
    public function fetchAll($paginate=true,$condtn = array())
    {
		if ($paginate) {
			
			$select = new Select('service_category');
			$select->columns(array('*',new Expression("IF(parent_id != 0, (SELECT category_name FROM service_category as sc WHERE sc.id = service_category.parent_id LIMIT 1), 'Parent') as parents")));
			$resultSetPrototype = new ResultSet();
			$resultSetPrototype->setArrayObjectPrototype(new ServiceCategory());
			//echo str_replace('"','',$select->getSqlString()); exit;
			$paginatorAdapter = new DbSelect(
				$select,
				$this->tableGateway->getAdapter(),
				$resultSetPrototype
			);  
			$paginator = new Paginator($paginatorAdapter);
			
			return $paginator;
		}
		return $this->tableGateway->select($condtn);
    }
    
    public function checkUsedStatus($id, &$msg)
    {
		$categories = $this->tableGateway->select(array('parent_id' => $id));
		
		if ($categories->count() == 0) {
			$services = $this->service_provider_service->select(array('service_id' => $id));
			if ($services->count() == 0) {
				return true;
			} else {
				$msg .= ' is in use as service provider services. Please remove it from service provider services first..!!';
				return false;
			}
		} else {
			$msg .= ' contains few sub categories. Please delete them first..!!';
			return false;
		}
	}
    
    public function getServiceCategory($id)
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
    
	public function getServiceCategories()
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
  
    public function saveServiceCategory(ServiceCategory $country)
    {
        $data = array(
            'category_name' => $country->category_name,
            'parent_id'  => $country->parent_id,
        );

        $id = (int) $country->id;
        if ($id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getServiceCategory($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('Service category id does not exist');
            }
        }
        
        // Update cache records
        //DataCache::updateData($this->CacheKey, $this->fetchAll());
    }

    public function deleteServiceCategory($id)
    {
        $this->tableGateway->delete(array('id' => (int) $id));
        
        // Update cache records
        //DataCache::updateData($this->CacheKey, $this->fetchAll());
    }
}
