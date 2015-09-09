<?php

namespace Admin\Model; 

use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\DataCache;
use Zend\Db\Sql\Select;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;
use Zend\Db\Sql\Expression;

class ServiceProviderMediaTable
{
    protected $tableGateway;
    private $CacheKey = 'serviceprovidermedia';
    private $users;
    
    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
        $adapter = $this->tableGateway->getAdapter();
        $this->users = new TableGateway('users', $adapter);
        $this->user_subscriptions = new TableGateway('user_subscriptions', $adapter);
    }
    
    public function fetchAll($id, $paginate=true, $filter=array(), $orderBy=array(), $where = '')
    {
		if ($paginate) {
			
			$select = new Select('media');
			//$select->columns(array('*', new Expression("user_feature_setting.email as email_status")));
			$select->join('users','users.id = media.user_id', array('first_name', 'last_name'),'inner');
			$select->join('lookup_status','lookup_status.status_id = media.status_id', array('status'),'left');
			$select->where(array('user_id' => $id));
			
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
			
			//echo str_replace('"', '', $select->getSqlString()); exit;
			
			$resultSetPrototype = new ResultSet(); 
			$resultSetPrototype->setArrayObjectPrototype(new ServiceProviderMedia());
			
			$paginatorAdapter = new DbSelect(
				$select,   
				$this->tableGateway->getAdapter(),  
				$resultSetPrototype   
			); 
			 
			$paginator = new Paginator($paginatorAdapter);
			
			return $paginator;
		} else {
			$select = $this->tableGateway->getSql()->select();
			$select->where(array('user_id' => $id));
			if (count($filter) > 0) {
				$select->where($filter);
			}
			
			if ($where != "") {
				$select->where($where);
			}
			return $this->tableGateway->selectwith($select);
		}
    }
    
    public function getServiceProviderMedia($user_id)
    {
		$user_id  = (int) $user_id;
		$select = $this->tableGateway->getSql()->select();
		$select->join('users','users.id = media.user_id', array('first_name', 'last_name'),'inner');
		$select->join('lookup_status','lookup_status.status_id = users.status_id', array('status'),'left');
		$select->where('media.user_id = '.$user_id);
		$rowset = $this->tableGateway->selectwith($select);
		
        $row = $rowset->current();
       
        if (!$row) {
			return false;
            //throw new \Exception("Could not find row $id");
        }
        return $row;
    }
    
    public function getMedia($id)
    {
		$id  = (int) $id;
		$select = $this->tableGateway->getSql()->select();
		$select->join('users','users.id = media.user_id', array('first_name', 'last_name'),'inner');
		$select->join('lookup_status','lookup_status.status_id = users.status_id', array('status'),'left');
		$select->where('media.id = '.$id);
		$rowset = $this->tableGateway->selectwith($select);
		
        $row = $rowset->current();
       
        if (!$row) {
			return false;
            //throw new \Exception("Could not find row $id");
        }
        return $row;
    }
    
    public function getVideoUploadLimit($user_id)
    {
		$select = $this->user_subscriptions->getSql()->select();
		$select->join('subscription_duration', 'subscription_duration.id = user_subscriptions.subscription_duration_id', array(), 'inner');
		$select->join('feature_video_limit', 'feature_video_limit.subscription_plan_id = subscription_duration.subscription_id', array('limit'), 'inner');
		$select->where(array('user_subscriptions.user_id' => $user_id, 'user_subscriptions.status_id' => 1));
		
		$result = $this->user_subscriptions->selectwith($select);
		$row = $result->current();
		
		if (!$row) {
			return false;
		} else {
			return $row;
		}
	}
    
    public function getServiceProvidersMedia()
	{
		$result = DataCache::getData($this->CacheKey);
		
		// Update cache if data not found
		if ($result == false) {
			$result = $this->fetchAll(false);
			
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
				DataCache::updateData($key, $result);
			}
			return $result;
		}
	}
	
	public function getUserAvtar($id)
	{
		$result = $this->users->select(array('id' => $id));
		return $result->current()->avtar_url;
	}
	
	public function setUserAvtar($id, $url)
	{
		$this->users->update(array('avtar_url' => $url), array('id' => $id));
	}
  
    public function saveServiceProviderMedia(ServiceProviderMedia $Media)
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

    public function deleteServiceProviderMedia($id)
    {
		$this->tableGateway->delete(array('id' => (int) $id)); 
        
        // Update cache data
        //DataCache::updateData($this->CacheKey,$this->fetchAll(false));
    }
    
    public function changeStatus($id, $status)
    {
		$this->tableGateway->update(array('status_id'=>$status), array('id' => $id));
	}

}
