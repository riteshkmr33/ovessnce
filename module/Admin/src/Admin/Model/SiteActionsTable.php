<?php 

namespace Admin\Model;

use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\DataCache;
use Zend\Db\Sql\Select;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

class SiteActionsTable
{
    protected $tableGateway;
    private $CacheKey = 'siteactions';
    private $action_meta; 
    private $site_meta;
        
    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
        $adapter=$this->tableGateway->getAdapter(); 
        $this->action_meta = new TableGateway('action_meta', $adapter);
    }
    
    public function fetchAll($paginate=true)
    {
		
		if ($paginate) {
			
			$select = new Select('site_actions');   
			$resultSetPrototype = new ResultSet(); 
			$resultSetPrototype->setArrayObjectPrototype(new SiteActions());
			
			$paginatorAdapter = new DbSelect(
				$select,  
				$this->tableGateway->getAdapter(),  
				$resultSetPrototype   
			);  
			$paginator = new Paginator($paginatorAdapter);
			
			return $paginator;
		}
		return $this->tableGateway->select();
    }
    
    public function getSiteAction($id)
    {
        $id  = (int) $id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        
        if (!$row) {
			return false;
        }
        return $row;
    }
    
	public function getSiteActions()
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
  
    public function saveSiteAction(SiteActions $SiteActions,$actionmeta = array())
    {
        $data = array(
            'controller_name' => $SiteActions->controller_name,
            'action_name' => $SiteActions->action_name,
        );
 
        $id = (int) $SiteActions->id;
        if ($id == 0) {
			
            $this->tableGateway->insert($data);
            $last_insert_id = $this->tableGateway->lastInsertValue; 
                
            $action_id = $last_insert_id;
            
        } else {
            if ($this->getSiteAction($id)) {
							
                $this->tableGateway->update($data, array('id' => $id));
				$action_id = $id;	
                
            } else {
                throw new \Exception('Action id does not exist');
            }
        }
        
       if($action_id != '' && count($actionmeta) > 0){       
			
			// deleting old entries
			$this->action_meta->delete(array('action_id' => $action_id)); 
			
			// adding new entries
			foreach ($actionmeta as $meta) { $this->action_meta->insert(array('action_id' => $action_id,'meta_id' => $meta)); }
		
		}
		
        // Update cache records
        //DataCache::updateData($this->CacheKey, $this->fetchAll());
    }
	
	/* Function to fetch Action meta */
	public function getActionMeta($id, $inarray = false)
    {
		$select = $this->action_meta->getSql()->select();
		$select->join('site_meta', 'site_meta.id = action_meta.meta_id');
		$select->where(array('action_id'=>$id));
		$results = $this->action_meta->selectwith($select);
		
		if ($inarray == true) {
			$data = array();
			foreach ($results  as $result) {
				
				$data[$result->id] = $result->meta_id;
			}
			return $data;
		} else {
			return $results;
		}
	}
	
    public function deleteSiteAction($id)
    {
        $this->tableGateway->delete(array('id' => (int) $id));
        
        $this->action_meta->delete(array('action_id'=>$id));
        
        // Update cache records
        //DataCache::updateData($this->CacheKey, $this->fetchAll());
    }
}
