<?php 

namespace Admin\Model;

use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\DataCache;
use Zend\Db\Sql\Select;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

class PageTable
{
    protected $tableGateway;
    private $CacheKey = 'pages';
    private $page_meta; 
    private $site_meta;
        
    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
        $adapter=$this->tableGateway->getAdapter(); 
        $this->page_meta = new TableGateway('page_meta', $adapter);
    }
    
    public function fetchAll($paginate=true)
    {
		
		if ($paginate) {
			
			$select = new Select('page');   // create a new Select object for the table album
			$resultSetPrototype = new ResultSet(); // create a new result set based on the Album entity
			$resultSetPrototype->setArrayObjectPrototype(new Page());
			
			 // create a new pagination adapter object
			$paginatorAdapter = new DbSelect(
				$select,   // our configured select object
				$this->tableGateway->getAdapter(),   // the adapter to run it against
				$resultSetPrototype   // the result set to hydrate
			);  
			$paginator = new Paginator($paginatorAdapter);
			
			return $paginator;
		}
		return $this->tableGateway->select();
    }
    
    public function getPage($id)
    {
        $id  = (int) $id;
        $rowset = $this->tableGateway->select(array('page_id' => $id));
        $row = $rowset->current();
        if (!$row) {
			return false;
            //throw new \Exception("Could not find row $id");
        }
        return $row;
    }
    
	public function getPages()
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
  
    public function savePage(Page $page,$pagemeta = array())
    {
        $data = array(
            'title' => $page->title,
            'slug' => preg_replace('/\s+/','-',strtolower(trim($page->title))),
            'content'  => $page->content,
            'page_status' => $page->page_status,
            'created_date' => date('Y-m-d H:i:s'),
            'created_by' => 1,
            'updated_date' => $page->updated_date,
            'updated_by' => 1,
        );
 
        $id = (int) $page->page_id;
        if ($id == 0) {
			
			unset($data['updated_date']);				
            $this->tableGateway->insert($data);
            $last_insert_id = $this->tableGateway->lastInsertValue; // get last insert id 
                
            $page_id = $last_insert_id;
            
        } else {
            if ($this->getPage($id)) {
				
				unset($data['slug']); // no need to update slug ( generated only when creating a page )
				unset($data['created_date']); 
							
                $this->tableGateway->update($data, array('page_id' => $id));
				$page_id = $id;	
                
            } else {
                throw new \Exception('Page id does not exist');
            }
        }
        
       if($page_id != '' && count($pagemeta) > 0){       
			
			// deleting old entries
			$this->page_meta->delete(array('page_id' => $page_id)); 
			
			// adding new entries
			foreach ($pagemeta as $meta) { $this->page_meta->insert(array('page_id' => $page_id,'meta_id' => $meta)); }
		
		}
		
        // Update cache records
        //DataCache::updateData($this->CacheKey, $this->fetchAll());
    }
	
	/* Function to fetch page meta */
	public function getPageMeta($id, $inarray = false)
    {
		$select = $this->page_meta->getSql()->select();
		$select->join('site_meta', 'site_meta.id = page_meta.meta_id');
		$select->where(array('page_id'=>$id));
		$results = $this->page_meta->selectwith($select);
		
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
	
    public function deletePage($id)
    {
        $this->tableGateway->delete(array('page_id' => (int) $id));
        
        $this->page_meta->delete(array('page_id'=>$id));
        
        // Update cache records
        //DataCache::updateData($this->CacheKey, $this->fetchAll());
    }
    
    public function changeStatus($id, $status)
    {
		$this->tableGateway->update(array('page_status'=>$status), array('page_id' => $id));
	}
}
