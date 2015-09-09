<?php 

namespace Admin\Model;

use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\DataCache;
use Zend\Db\Sql\Select;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;
use Zend\Db\Sql\Expression;

class VideoViewsTable
{
    protected $tableGateway;
    private $CacheKey = 'videoviews';
    
    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }
    
    public function fetchAll($paginate=true)
    {
		
		if ($paginate) {
			
			$select = new Select('video_views');   
			$resultSetPrototype = new ResultSet(); 
			$resultSetPrototype->setArrayObjectPrototype(new VideoViews());
			
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
    
    public function getVideoViews($id)
    {
        $id  = (int) $id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
			return false;
        }
        return $row;
    }
    
	public function getVideoViewss()
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
  
    public function saveVideoViews(VideoViews $VideoViews)
    {
        $data = array(
            'video_id' => $VideoViews->video_id,
            'user_id' => $VideoViews->user_id,
            'remote_ip' => $VideoViews->remote_ip,
            'media_description' => $VideoViews->media_description,
            'date_added' => $VideoViews->date_added,
        );
 
        $id = (int) $VideoViews->id;
        if ($id == 0) {
			
            $this->tableGateway->insert($data);
            
        } else {
            if ($this->getVideoViews($id)) {
							
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('id does not exist');
            }
        }
        
        // Update cache records
        //DataCache::updateData($this->CacheKey, $this->fetchAll());
    }

    public function deleteVideoViews($id)
    {
        $this->tableGateway->delete(array('id' => (int) $id));
        
        // Update cache records
        //DataCache::updateData($this->CacheKey, $this->fetchAll());
    }
    
    public function getViewsCount($date)
    {
		
		$data = array();
		$adapter=$this->tableGateway->getAdapter(); 
		
		$sql = "SELECT COUNT(*) AS views_count FROM `video_views` WHERE date_format(date(video_views.date_added ),'%Y-%m-%d') >= '". $date ."' "; 
		
		$statement = $adapter->query($sql); 
		$result = $statement->execute(); 
		
		foreach($result as $key => $value ){
			$data['views_count'] = $value['views_count'];
		}
		
		return $data;
	}
    
}
