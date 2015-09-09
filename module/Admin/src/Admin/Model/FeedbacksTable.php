<?php

namespace Admin\Model;

use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\DataCache;
use Zend\Db\Sql\Select;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;
use Zend\Db\Sql\Expression;

class FeedbacksTable
{
    protected $tableGateway;
    private $CacheKey = 'feedbacks';
    
    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }
    
    public function fetchAll($paginate=true, $filter = array(), $orderBy = array())
    {
		if ($paginate) {
			$select = new Select('feedback');
			$select->columns(array('*', new Expression("service_provider_contact.first_name AS sp_first_name, service_provider_contact.last_name AS sp_last_name")));
			$select->join(array('service_provider_contact' => 'users'),'service_provider_contact.id = feedback.users_id',array(),'inner');
			$select->join('users','users.id = feedback.created_by',array('first_name', 'last_name'),'inner');
			$select->join('service_provider_service','service_provider_service.id = feedback.service_id',array('duration'),'inner');
			$select->join('service_category','service_category.id = service_provider_service.service_id',array('category_name'),'inner');
			$select->join('lookup_status','lookup_status.status_id = feedback.status_id',array('status'),'left');
			
			/* Data filter code start here*/
			if (count($filter)>0) {
				
				(isset($filter['name']) && $filter['name'] != "")?$select->where("CONCAT(service_provider_contact.first_name,' ',service_provider_contact.last_name) LIKE '%".$filter['name']."%'"):"";
				
				if (isset($filter['from_date']) && $filter['from_date'] != "" && isset($filter['to_date']) && $filter['to_date'] != "") {
					$select->where("DATE_FORMAT(feedback.created_date , '%Y-%m-%d') BETWEEN '".$filter['from_date']."' AND '".$filter['to_date']."'");
				} else if (isset($filter['from_date']) && !isset($filter['to_date']) && $filter['from_date'] != "" ) {
					$select->where("DATE_FORMAT(feedback.created_date , '%Y-%m-%d') = '".$filter['from_date']."'");
				} else if (!isset($filter['from_date']) && isset($filter['to_date']) && $filter['to_date'] != "") {
					$select->where("DATE_FORMAT(feedback.created_date , '%Y-%m-%d') = '".$filter['to_date']."'");
				}
				
				if (isset($filter['service_id']) && $filter['service_id'] != "") {
					$select->where("feedback.service_id = ".$filter['service_id']);
					
				}
				
				(isset($filter['status_id']) && $filter['status_id'] != "")?$select->where("users.status_id = ".$filter['status_id']):"";
			}
			/* Data filter code end here*/
			
			/* Data sorting code starts here */
			if (count($orderBy)>0 && $orderBy['sort_field'] != '' && $orderBy['sort_order'] != '') {
				switch ($orderBy['sort_field']) {
					case 'name' :
						$select->order('service_provider_contact.first_name '.$orderBy['sort_order']);
						break;
						
					case 'service' :
						$select->order('service_category.category_name '.$orderBy['sort_order']);
						break;
						
					case 'date' :
						$select->order('feedback.created_date '.$orderBy['sort_order']);
						break;
				}
			}
			/* Data sorting code ends here */
			
			//echo str_replace('"','',$select->getSqlString()); exit;
			
			$resultSetPrototype = new ResultSet();
			$resultSetPrototype->setArrayObjectPrototype(new Feedbacks());
			
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
    
    public function getFeedback($user, $service)
    {
        $id  = (int) $id;
        $rowset = $this->tableGateway->select(array('users_id' => (int) $user, 'service_id' => (int) $service));
        $row = $rowset->current();
        if (!$row) {
			return false;
            //throw new \Exception("Could not find row $id");
        }
        return $row;
    }
    
	public function getFeedbacks()
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
  
    public function deleteFeedback($users_id, $service_id)
    {
        $this->tableGateway->delete(array('users_id' => (int) $users_id, 'service_id' => (int) $service_id));
        
        // Update cache records
        //DataCache::updateData($this->CacheKey, $this->fetchAll());
    }
    
    public function changeStatus($id, $service_id, $status)
    {
		$this->tableGateway->update(array('status_id'=>$status), array('users_id' => $id, 'service_id' => $service_id));
	}
    
    public function CountNewFeedbacks()
    {
		$select = $this->tableGateway->getSql()->select();
		$select->where('feedback.created_date > NOW() - INTERVAL 4 DAY');
		$rowset = $this->tableGateway->selectwith($select);
		
		if( $rowset->count() > 0 ){
			return $rowset->count();
		}else{
			return false;
		}
	}
	
	public function getPendingFeedback()
	{
		$data = array();
		$adapter=$this->tableGateway->getAdapter();
		
		$sql = "SELECT u.user_name,
					HOUR(TIMEDIFF(f.created_date, now() )) as timediff_hour,
					MINUTE(TIMEDIFF(f.created_date, now() )) as timediff_minute,
					SECOND(TIMEDIFF(f.created_date, now() )) as timediff_second 
					FROM feedback as f 
					LEFT JOIN users AS u ON u.id = f.created_by 
					WHERE f.status_id = 5";
		$statement = $adapter->query($sql); 
		$result = $statement->execute(); 
		
		foreach($result as $key => $value){
			$data['pending_feedback'][$key] = $value;
			$data['pending_feedback_count']++;
		}
		
		return $data;
	}
}
