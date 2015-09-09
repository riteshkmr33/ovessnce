<?php 

namespace Admin\Model;

use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\DataCache;
use Zend\Db\Sql\Select;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;
use Zend\Db\Sql\Expression;

class SmsHistoryTable
{
    protected $tableGateway;
    private $CacheKey = 'smshistory';
    
    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }
    
    public function fetchAll($paginate=true)
    {
		
		if ($paginate) {
			
			$select = new Select('sms_history');   
			$resultSetPrototype = new ResultSet(); 
			$resultSetPrototype->setArrayObjectPrototype(new SmsHistory());
			
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
    
	public function GetSmsCountByDays($days)
	{
		$select = $this->tableGateway->getSql()->select();
		$select->where("sent_date > NOW() - INTERVAL $days DAY");
		$select->where('1=1');
		$rowset = $this->tableGateway->selectwith($select);
			
		if( $rowset->count() > 0 ){
			return $rowset->count();
		}else{
			return false;
		}
	}
	
	public function getSmsByDays($per = 'day')
	{
		$select = $this->tableGateway->getSql()->select();
		$select->columns(array(new Expression('COUNT(id) AS total')));
		switch ($per) {
			case 'day' :
				$select->where("DATE_FORMAT(sent_date, '%Y-%m-%d') = '".date('Y-m-d')."'");
				break;
				
			case 'week' :
				$select->where("DATE_FORMAT(sent_date, '%Y-%m-%d') BETWEEN '".date('Y-m-d', strtotime('-6 days'))."' AND '".date('Y-m-d')."'");
				break;
				
			case 'month' :
				$select->where("DATE_FORMAT(sent_date, '%Y-%m-%d') BETWEEN '".date('Y-m-d', strtotime('-1 month'))."' AND '".date('Y-m-d')."'");
				break;
				
			case 'year' :
				$select->where("DATE_FORMAT(sent_date, '%Y-%m-%d') BETWEEN '".date('Y-m-d', strtotime('-1 year'))."' AND '".date('Y-m-d')."'");
				break;
		}
		
		$result = $this->tableGateway->selectwith($select);
		
		return array('total' => $result->current()->total);
	}
    
    public function getSmsHistory($id)
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
    
	public function getSmsHistorys()
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
  
    public function saveSmsHistory(SmsHistory $SmsHistory)
    {
        $data = array(
            'to_user_id' => $SmsHistory->to_user_id,
            'from_user_id' => $SmsHistory->from_user_id,
            'subject'  => $SmsHistory->subject,
            'message' => $SmsHistory->message,
            'sent_date' => date('Y-m-d H:i:s'),
            'status' => $SmsHistory->status,
        );
 
        $id = (int) $SmsHistory->id;
        if ($id == 0) {
		
            $this->tableGateway->insert($data);
            
        } else {
            if ($this->getSmsHistory($id)) {
				
				unset($data['created_date']); 
							
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('id does not exist');
            }
        }
        
        // Update cache records
        //DataCache::updateData($this->CacheKey, $this->fetchAll());
    }

    public function deleteSmsHistory($id)
    {
        $this->tableGateway->delete(array('id' => (int) $id));
        
        // Update cache records
        //DataCache::updateData($this->CacheKey, $this->fetchAll());
    }
   
}
