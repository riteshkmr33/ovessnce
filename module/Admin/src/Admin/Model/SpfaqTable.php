<?php 

namespace Admin\Model;

use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\DataCache;
use Zend\Db\Sql\Select;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;
use Zend\Session\Container;
use Zend\Db\Sql\Expression;

class SpfaqTable
{
    protected $tableGateway;
    private $CacheKey = 'sp_faq';
        
    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
        $adapter=$this->tableGateway->getAdapter(); 
    }
    
    public function fetchAll($paginate=true)
    {
		
		if ($paginate) {
			
			$select = new Select('sp_faq'); 
			$select->columns(array('*',new Expression("from.user_name  AS from_user_name,to.user_name AS to_user_name,answered_by.user_name AS answered_by_user")));
			$select->join(array('from' => 'users'),'sp_faq.from_user_id = from.id', array(),'left'); 
			$select->join(array('to' => 'users'),'sp_faq.to_user_id = to.id', array(),'left'); 
			$select->join(array('answered_by' => 'users'),'sp_faq.answered_by_id = answered_by.id', array(),'left'); 
			$select->join('lookup_status','sp_faq.status_id = lookup_status.status_id', array('status'),'left'); 
			$resultSetPrototype = new ResultSet();
			$resultSetPrototype->setArrayObjectPrototype(new Spfaq());
			
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
    
    public function getSpfaq($id)
    {
        $id  = (int) $id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
			return false;
        }
        return $row;
    }
    
	public function getSpfaqs()
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
  
    public function saveSpfaq(Spfaq $spfaq)
    {
		
		$user_details = new Container('user_details');
		$details = $user_details->details;
		
        $data = array(
            'from_user_id' => $spfaq->from_user_id,
            'to_user_id' => $spfaq->to_user_id,
            'question' => trim($spfaq->question),
            'answer' => trim($spfaq->answer),
            'answered_by_id' => $spfaq->answered_by_id,
            'asked_on' => $spfaq->asked_on,
            'answered_on' => $spfaq->answered_on,
            'status_id' => $spfaq->status_id,
        );
		
		!isset($details['user_id']) ? $data['answered_by_id'] = $details['user_id'] : $data['answered_by_id'] = "0" ; 
		
        $id = (int) $spfaq->id;
        if ($id == 0) {
			
			unset($data['answered_on']);
			echo empty($data['asked_on']) ? $data['asked_on'] = date('Y-m-d h:i:s') : $data['asked_on'] ;				
			
            $this->tableGateway->insert($data);
            
        } else {
            if ($this->getSpfaq($id)) {
				
				unset($data['asked_on']); 
				echo empty($data['answered_on']) ? $data['answered_on'] = date('Y-m-d h:i:s') : $data['answered_on'] ; 
							
                $this->tableGateway->update($data, array('id' => $id));
                
            } else {
                throw new \Exception('Page id does not exist');
            }
        }
	
    }
	
    public function deleteSpfaq($id)
    {
        $this->tableGateway->delete(array('id' => (int) $id));
    }
    
    public function changeStatus($id, $status)
    {
		$this->tableGateway->update(array('status_id'=>$status), array('id' => $id));
	}
}
