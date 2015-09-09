<?php 

namespace Admin\Model;

use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\DataCache;
use Zend\Db\Sql\Select;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

class PartnersTable
{
    protected $tableGateway;
    private $CacheKey = 'partners';
    
    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }
    
    public function fetchAll($paginate=true)
    {
        if ($paginate) {
			
			$select = new Select('partners');   
			$resultSetPrototype = new ResultSet(); 
			$resultSetPrototype->setArrayObjectPrototype(new Partners());
			
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
    
    public function ExportAll($filter = array(), $orderBy=array())
    {
		$select = $this->tableGateway->getSql()->select();
		
		/* Data filter code start here*/
		if (count($filter)>0) {
			
			// Filter code goes here
		}
		/* Data filter code end here*/
		
		return $this->tableGateway->selectwith($select);
	}
    
    public function getPartner($id)
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
    
	public function getPartners()
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
  
    public function savePartner(Partners $partners)
    {
		$data = array(
            'id' => $partners->id,
            'title'  => $partners->title,
            'desc'  => $partners->desc,
            'url'  => $partners->url,
            'logo'  => $partners->logo,
            'status_id'  => $partners->status_id, 
        ); 
        
		foreach($data as $key => $value){
				if($value == null){
					unset($data[$key]);
				}
		}

        $id = (int) $partners->id;
        if ($id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getPartner($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('Partner id does not exist');
            }
        }
        
        // Update cache records
        //DataCache::updateData($this->CacheKey, $this->fetchAll());
    }

    public function deletePartner($id)
    {
				
        $this->tableGateway->delete(array('id' => (int) $id));
        
        // Update cache records
        //DataCache::updateData($this->CacheKey, $this->fetchAll());
    }
    
    public function changeStatus($id, $status)
    {
		$this->tableGateway->update(array('status_id'=>$status), array('id' => $id));
	}
}
