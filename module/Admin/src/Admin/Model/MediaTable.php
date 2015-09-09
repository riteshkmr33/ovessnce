<?php 

namespace Admin\Model;

use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\DataCache;
use Zend\Db\Sql\Select;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

class MediaTable
{
    protected $tableGateway;
    private $CacheKey = 'media';
    
    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }
    
    public function fetchAll($paginate=true, $filter = array(), $orderBy = array())
    {
		
		if ($paginate) {
			
			$select = new Select('media');
			$select->join('users', 'users.id = media.user_id', array('first_name', 'last_name'), 'inner');
			$select->join('lookup_status', 'lookup_status.status_id = media.status_id', array('status'), 'inner');
			
			/* Data filter code start here*/
			if (count($filter)>0) {
				
				(isset($filter['name']) && $filter['name'] != "")?$select->where("CONCAT(users.first_name,' ',users.last_name) LIKE '%".$filter['name']."%'"):"";
				(isset($filter['title']) && $filter['title'] != "")?$select->where("media.media_title LIKE '%".$filter['title']."%'"):"";
				(isset($filter['media_type']) && $filter['media_type'] != "")?$select->where("media.media_type = ".$filter['media_type']):"";
				
				if (isset($filter['from_date']) && $filter['from_date'] != "" && isset($filter['to_date']) && $filter['to_date'] != "") {
					$select->where("DATE_FORMAT(media.created_date , '%Y-%m-%d') BETWEEN '".$filter['from_date']."' AND '".$filter['to_date']."'");
				} else if (isset($filter['from_date']) && !isset($filter['to_date']) && $filter['from_date'] != "" ) {
					$select->where("DATE_FORMAT(media.created_date , '%Y-%m-%d') = '".$filter['from_date']."'");
				} else if (!isset($filter['from_date']) && isset($filter['to_date']) && $filter['to_date'] != "") {
					$select->where("DATE_FORMAT(media.created_date , '%Y-%m-%d') = '".$filter['to_date']."'");
				}
				
				(isset($filter['status_id']) && $filter['status_id'] != "")?$select->where("media.status_id = ".$filter['status_id']):"";
			}
			/* Data filter code end here*/
			
			/* Data sorting code starts here */
			if (count($orderBy)>0 && $orderBy['sort_field'] != '' && $orderBy['sort_order'] != '') {
				switch ($orderBy['sort_field']) {
					case 'name' :
						$select->order('users.first_name '.$orderBy['sort_order']);
						break;
						
					case 'title' :
						$select->order('media.media_title '.$orderBy['sort_order']);
						break;
						
					case 'media_type' :
						$select->order('media.media_type '.$orderBy['sort_order']);
						break;
						
					case 'date' :
						$select->order('media.created_date '.$orderBy['sort_order']);
						break;
						
					case 'status' :
						$select->order('lookup_status.status '.$orderBy['sort_order']);
						break;
				}
			}
			/* Data sorting code ends here */
			
			//echo str_replace('"','',$select->getSqlString()); exit;
			
			$resultSetPrototype = new ResultSet(); 
			$resultSetPrototype->setArrayObjectPrototype(new Media());
			
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
    
    public function getMedia($id)
    {
        $id  = (int) $id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
			return false;
        }
        return $row;
    }
    
    public function getUserMedia($user_id, $media_type = 1, $dataForm = 'data')
    {
		$results = $this->tableGateway->select(array('user_id' => $user_id, 'media_type' => $media_type));
		return ($dataForm == 'count')?$results->count():$results;
	}
    
	public function getMedias()
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
  
    public function saveMedia(Media $Media)
    {
        $data = array(
            'user_id' => $Media->user_id,
            'media_url' => $Media->media_url,
            'media_title' => $Media->media_title,
            'media_description' => $Media->media_description,
            'media_type' => $Media->media_type,
            'created_date' => date('Y-m-d H:i:s'),
            'created_by' => $Media->created_by,
            'updated_date' => $Media->updated_date,
            'updated_by' => $Media->updated_by,
            'status_id' => $Media->status_id,
        );
 
        $id = (int) $Media->id;
        if ($id == 0) {
			
			unset($data['updated_date']);
			unset($data['updated_by']); 				
            $this->tableGateway->insert($data);
            return $this->tableGateway->lastInsertValue;
            
        } else {
            if ($this->getMedia($id)) {
				
				unset($data['created_date']); 
				unset($data['created_by']); 
							
                $this->tableGateway->update($data, array('id' => $id));
                return $id;
            } else {
                throw new \Exception('id does not exist');
            }
        }
        
        // Update cache records
        //DataCache::updateData($this->CacheKey, $this->fetchAll());
    }
    
    public function updateMedia($field, $value, $id)
    {
		return $this->tableGateway->update(array($field => $value), array('id' => $id));
	}

    public function deleteMedia($id)
    {
        $this->tableGateway->delete(array('id' => (int) $id));
        
        // Update cache records
        //DataCache::updateData($this->CacheKey, $this->fetchAll());
    }
    
    public function changeStatus($id, $status)
    {
		$this->tableGateway->update(array('status_id'=>$status), array('id' => $id));
	}
	
	public function getUploadCount($date)
	{
		
		$data = array();
		$adapter=$this->tableGateway->getAdapter(); 
		
		$sql = "SELECT COUNT(*) AS upload_count FROM `media` WHERE media_type = 2 AND date_format(date(media.created_date ),'%Y-%m-%d') >= '". $date ."' "; 
		
		$statement = $adapter->query($sql); 
		$result = $statement->execute(); 
		
		foreach($result as $key => $value ){
			$data['upload_count'] = $value['upload_count'];
		}
		
		return $data;
		
	}
	
	public function getPendingVideo()
	{
		
		$data = array();
		$data['video_count'] = '0';
		$adapter=$this->tableGateway->getAdapter();
		
		$sql = "SELECT 	u.user_name,HOUR(TIMEDIFF(m.created_date, now() )) as timediff_hour,
					MINUTE(TIMEDIFF(m.created_date, now() )) as timediff_minute,
					SECOND(TIMEDIFF(m.created_date, now() )) as timediff_second 
					FROM media AS m 
					LEFT JOIN users AS u ON u.id = m.created_by 
					WHERE m.media_type= 2 AND m.status_id = 5";
		
		$statement = $adapter->query($sql); 
		$result = $statement->execute(); 
		
		foreach($result as $key => $value){
			$data['pending_video'][$key] = $value;
			$data['video_count']++;
		}
		
		return $data;
	
	}
}
