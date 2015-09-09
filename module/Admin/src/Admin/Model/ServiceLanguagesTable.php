<?php 

namespace Admin\Model;

use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\DataCache;
use Zend\Db\Sql\Select;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

class ServiceLanguagesTable
{
    protected $tableGateway;
    private $CacheKey = 'servicelanguage';
    
    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }
    
    public function fetchAll($paginate=true, $filter = array(), $orderBy = array())
    {
		if ($paginate) {
			
			$select = new Select('service_language');
			$select->join('lookup_status', 'lookup_status.status_id = service_language.status_id', array('status'), 'left');
			
			/* Data filter code start here*/
			if (count($filter)>0) {	
				($filter['language_name'] != "")?$select->where("service_language.language_name LIKE '%".$filter['language_name']."%'"):"";
				($filter['status_id'] != "")?$select->where("service_language.status_id = ". $filter['status_id']):"";
			}
			/* Data filter code end here*/
			
			/* Data sorting code starts here */
			if (count($orderBy)>0 && $orderBy['sort_field'] != '' && $orderBy['sort_order'] != '') {
				switch ($orderBy['sort_field']) {
					case 'language' :
						$select->order('service_language.language_name '.$orderBy['sort_order']);
						break;
						
					case 'status' :
						$select->order('lookup_status.status '.$orderBy['sort_order']);
						break;
				}
			}
			/* Data sorting code ends here */
			
			$resultSetPrototype = new ResultSet();
			$resultSetPrototype->setArrayObjectPrototype(new ServiceLanguages());
			
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
    
    public function getServiceLanguage($id)
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
    
	public function getServiceLanguages()
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
  
    public function saveServiceLanguage(ServiceLanguages $sl)
    {
        $data = array(
            'language_name' => $sl->language_name,
            'status_id' => $sl->status_id,
        );

        $id = (int) $sl->id;
        if ($id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getServiceLanguage($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('Service language id does not exist');
            }
        }
        
        // Update cache records
        //DataCache::updateData($this->CacheKey, $this->fetchAll());
    }
    
    public function changeStatus($id, $status)
    {
		$this->tableGateway->update(array('status_id' => $status),array('id' => $id));
	}

    public function deleteServiceLanguage($id)
    {
        $this->tableGateway->delete(array('id' => (int) $id));
        
        // Update cache records
        //DataCache::updateData($this->CacheKey, $this->fetchAll());
    }
}
