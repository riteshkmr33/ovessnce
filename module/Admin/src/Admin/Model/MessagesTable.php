<?php 

namespace Admin\Model;

use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\DataCache;
use Zend\Db\Sql\Select;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;
use Zend\Db\Sql\Expression;

class MessagesTable
{
    protected $tableGateway;
    private $CacheKey = 'messages';
    
    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }
    
    public function fetchAll($paginate=true, $Flag = false ,$user_id='')
    {
		
		if ($paginate) {
			
			$select = new Select('messages');
			$select->columns(array('*',new Expression("from_user.user_name as from_user, to_user.user_name as to_user")));
			$select->join(array('from_user' => 'users'),'from_user.id = messages.from_user_id',array(),'left');
			$select->join(array('to_user' => 'users'),'to_user.id = messages.to_user_id',array(),'left');
			
			if ($Flag == "trash") {
				
				$select->where(array('deleteFlag' => '1'));
				$select->order('created_date ASC');
				
			}else if($Flag == "outbox"){
				
				$select->where(array('from_user_id' => $user_id, 'deleteFlag' => '0'));
				$select->order('created_date ASC');
				
			}else if($Flag == "inbox"){
				
				$select->where('to_user_id = '.$user_id.' AND deleteFlag = 0');
				$select->order('created_date ASC');
					
			}
			
			
			   
			$resultSetPrototype = new ResultSet(); 
			$resultSetPrototype->setArrayObjectPrototype(new Messages());
			
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
    
    public function fetchAllReplies($id) {
		 
		$select = new Select('messages');
		$select->columns(array('*',new Expression("from_user.user_name as from_user, to_user.user_name as to_user")));
		$select->join(array('from_user' => 'users'),'from_user.id = messages.from_user_id',array(),'left');
		$select->join(array('to_user' => 'users'),'to_user.id = messages.to_user_id',array(),'left');
		$select->where(array('topLevel_id' => $id));
		$select->order('created_date desc');
		$rowset = $this->tableGateway->selectwith($select);
		
		if(!$rowset){
			return false;
		}
		
		return $rowset;
		
    }
    
    public function getMessage($id)
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
    
    public function getUnreadMessages($id)
    {
		return $this->tableGateway->select(array('to_user_id' => $id, 'readFlag' => 0));
	}
    
	public function getMessages()
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
  
    public function saveMessage(Messages $Messages)
    {
        $data = array(
            'from_user_id' => $Messages->from_user_id,
            'from_name' => $Messages->from_name,
            'to_user_id'  => $Messages->to_user_id,
            'subject' => $Messages->subject,
            'message' => $Messages->message,
            'replyId' => $Messages->replyId,
            'topLevel_id' => $Messages->topLevel_id,
            'readFlag' => $Messages->readFlag,
            'deleteFlag' => $Messages->deleteFlag,
            'created_date' => date('Y-m-d H:i:s'),
        );
		
        $id = (int) $Messages->id;
        if ($id == 0) {
			
			/* all these fields will be set to zero while inserting */
			$data['replyId'] = '0';
			$data['topLevel_id'] = '0';
			$data['readFlag'] = '0';
			$data['deleteFlag'] = '0';
			
			if(is_array($data['to_user_id']) && count($data['to_user_id']) > 0 ) {
				foreach($data['to_user_id'] as $to){
					$data['to_user_id'] = $to;
					$this->tableGateway->insert($data);
					/* update the message record 'reply id' and 'topL' after insert */
					$last_insert_id = $this->tableGateway->lastInsertValue; // get last insert id for user
					$LastInsertedMessage = $this->getMessage($last_insert_id);
					
					$data_new['replyId'] = $LastInsertedMessage->id;
					$data_new['topLevel_id'] = $LastInsertedMessage->id;
					
					$this->tableGateway->update($data_new, array('id' => $LastInsertedMessage->id));
					/****************************************************************/
				}
			}else{
				$this->tableGateway->insert($data);
				
				$last_insert_id = $this->tableGateway->lastInsertValue; // get last insert id for user
				$LastInsertedMessage = $this->getMessage($last_insert_id);
				
				$data_new['replyId'] = $LastInsertedMessage->id;
				$data_new['topLevel_id'] = $LastInsertedMessage->id;
				
				$this->tableGateway->update($data_new, array('id' => $LastInsertedMessage->id));
			}
            
        } else {
			
            if ($this->getMessage($id)) {
							
                $this->tableGateway->update($data, array('id' => $id));
                
            } else {
                throw new \Exception('Message id does not exist');
            }
        }
         
        // Update cache records
        //DataCache::updateData($this->CacheKey, $this->fetchAll());
    }
    
    public function saveReply($data){

		$this->tableGateway->insert($data);
		
		$last_insert_id = $this->tableGateway->lastInsertValue;
		
		if(isset($last_insert_id) && !empty($last_insert_id)){
			return true;
		}else{
			return false;
		}
		
	}

    public function deleteMessage($id)
    {
		//$this->tableGateway->delete(array('id' => (int) $id));
        //or in this case we just have to set the delete flag to 1 
       
		$this->tableGateway->update(array('deleteFlag' => 1), array('id' => $id));
        
        // Update cache records
        //DataCache::updateData($this->CacheKey, $this->fetchAll());
    }
    
    public function undodeleteMessage($id)
    {
		$this->tableGateway->update(array('deleteFlag' => 0), array('id' => $id));
        
        // Update cache records
        //DataCache::updateData($this->CacheKey, $this->fetchAll());
    }
   
}
