<?php 

namespace Admin\Model;

use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\DataCache;
use Zend\Db\Sql\Select;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

class NotificationSettingsTable
{
    protected $tableGateway;
    private $CacheKey = 'notificationsettings';
        
    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }
    
    public function fetchAll($paginate=true)
    {
		
		if ($paginate) {
			
			$select = new Select('notification_settings');
			$select->join('users','users.id = notification_settings.user_id', array('user_name'),'left');
			$select->join('site_modules','site_modules.id = notification_settings.module_id', array('module_name'),'left');
			$resultSetPrototype = new ResultSet(); 
			$resultSetPrototype->setArrayObjectPrototype(new NotificationSettings());
			
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
    
    public function getNotificationSetting($id)
    {
        $id  = (int) $id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
			return false;
        }
        return $row;
    }
    
	public function getNotificationSettings()
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
  
    public function saveNotificationSettings(NotificationSettings $NotificationSettings)
    {
        $data = array(
            'user_id' => $NotificationSettings->user_id,
            'module_id' => $NotificationSettings->module_id,
            'sms_flag' => $NotificationSettings->sms_flag,
            'email_flag' => $NotificationSettings->email_flag,
            'page_alert_flag' => $NotificationSettings->page_alert_flag,
        );
		
		empty($data['sms_flag']) ? $data['sms_flag'] = 0 : $data['sms_flag'] ; 
		empty($data['email_flag']) ? $data['email_flag'] = 0 : $data['email_flag'] ; 
		empty($data['page_alert_flag']) ? $data['page_alert_flag'] = 0 : $data['page_alert_flag'] ; 
		
        $id = (int) $NotificationSettings->id;
        if ($id == 0) {
			
            $this->tableGateway->insert($data);
            
        } else {
            if ($this->getNotificationSetting($id)) {
				
			    $this->tableGateway->update($data, array('id' => $id));
			
                
            } else {
                throw new \Exception('id does not exist');
            }
        }
        
        // Update cache records
        //DataCache::updateData($this->CacheKey, $this->fetchAll());
    }
	
	public function deleteNotificationSettings($id)
    {
        $this->tableGateway->delete(array('id' => (int) $id));
    }
}
