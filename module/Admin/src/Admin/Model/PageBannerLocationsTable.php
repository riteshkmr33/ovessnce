<?php

namespace Admin\Model; 

use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\DataCache;
use Zend\Db\Sql\Select;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

class PageBannerLocationsTable
{
    protected $tableGateway;
    private $CacheKey = 'pagebannerlocation';
    
    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }
    
    public function fetchAll($paginate=true, $filter=array())
    {
		if ($paginate) {
			
			$select = new Select('page_banner_location');
			$select->join('banner','banner.id = page_banner_location.banner_id',array('banner_name'),'left');
			$select->join('page_location','page_location.id = page_banner_location.location_id',array('location_name'),'left');
			
			/* Data filter code start here*/
			if (count($filter)>0) {
				
				// Filter code goes here
				
				//echo str_replace('"','',$select->getSqlString()); //exit;
			}
			/* Data filter code end here*/
			
			$resultSetPrototype = new ResultSet();
			$resultSetPrototype->setArrayObjectPrototype(new PageBannerLocations());
			
			$paginatorAdapter = new DbSelect(
				$select,
				$this->tableGateway->getAdapter(),
				$resultSetPrototype
			);
			$paginator = new Paginator($paginatorAdapter);
			
			return $paginator;
		} else {
			$select = $this->tableGateway->getSql()->select();
			$select->join('banner','banner.id = page_banner_location.banner_id',array('banner_name'),'left');
			$select->join('page_location','page_location.id = page_banner_location.location_id',array('location_name'),'left');
			
			return $this->tableGateway->selectwith($select);
		}
    }
    
    public function getPageBannerLocation($id)
    {
        $id  = (int) $id;
        $select = $this->tableGateway->getSql()->select();
        $select->join('banner','banner.id = page_banner_location.banner_id',array('banner_name'),'left');
		$select->join('page_location','page_location.id = page_banner_location.location_id',array('location_name'),'left');
		$select->where(array('page_banner_location.id' => $id));
		
        $rowset = $this->tableGateway->selectwith($select);
        $row = $rowset->current();
        if (!$row) {
			return false;
            //throw new \Exception("Could not find row $id");
        }
        return $row;
    }
    
	public function getPageBannerLocations()
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
  
    public function savePageBannerLocation(PageBannerLocations $pbl)
    {
        $data = array(
            'banner_id' => $pbl->banner_id,
            'location_id' => $pbl->location_id,
            'page_name'  => $pbl->page_name,
        );

        $id = (int) $pbl->id;
        if ($id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getPageBannerLocation($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('Page Banner Location id does not exist');
            }
        }
        
        // Update cache records
        //DataCache::updateData($this->CacheKey, $this->fetchAll());
    }

    public function deletePageBannerLocation($id)
    {
        $this->tableGateway->delete(array('id' => (int) $id));
        
        // Update cache records
        //DataCache::updateData($this->CacheKey, $this->fetchAll());
    }
}
