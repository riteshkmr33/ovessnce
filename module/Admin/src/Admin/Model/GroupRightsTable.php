<?php 

namespace Admin\Model;

use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\DataCache;
use Zend\Db\Sql\Select;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

class GroupRightsTable
{
    protected $tableGateway;
    private $CacheKey = 'grouprights';
    
    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }
    
    public function fetchAll($paginate=true)
    {
		
		if ($paginate) {
			
			$select = new Select('grouprights');   
			$resultSetPrototype = new ResultSet(); 
			$resultSetPrototype->setArrayObjectPrototype(new GroupRights());
			
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
    
    public function getGroupRight($id)
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
    
    public function getGroupRightByGrpId($id)
    {
		$select = $this->tableGateway->getSql()->select();
		$select->where("group_id = ".$id);
		$rowset = $this->tableGateway->selectwith($select);
		//$row = $rowset->current();
		
		if(0 === $rowset->count()) { 
            return false;
        }
        
        //return $rowset;
        
        $module = array();
        
		foreach($rowset as $row){
			
			!empty($row->can_add) ? $module["add_".$row->module_id] = $row->can_add : $module["add_".$row->module_id] = 0 ;
			!empty($row->can_edit) ? $module["edit_".$row->module_id] = $row->can_edit : $module["edit_".$row->module_id] = 0 ;
			!empty($row->can_view) ? $module["view_".$row->module_id] = $row->can_view : $module["view_".$row->module_id] = 0 ;
			!empty($row->can_del) ? $module["del_".$row->module_id] = $row->can_del : $module["del_".$row->module_id] = 0 ;
		}
        
		return $module;
		
    }
    
    public function getGroupRightsArr($id)
    {
		$select = $this->tableGateway->getSql()->select();
		$select->join('site_modules',' grouprights.module_id = site_modules.id', array('module_name'),'left');
		$select->where("group_id = ".$id);
		$rowset = $this->tableGateway->selectwith($select);
				
		if(0 === $rowset->count()) { 
            return false;
        }
        
        $PermissionArr  = array();
        foreach($rowset as $row){
			
			//echo "<pre>";print_r($row);die;
			$PermissionArr['module'][$row->module_name]['can_add'] = $row->can_add;
			$PermissionArr['module'][$row->module_name]['can_edit'] = $row->can_edit;
			$PermissionArr['module'][$row->module_name]['can_view'] = $row->can_view;
			$PermissionArr['module'][$row->module_name]['can_del'] = $row->can_del;
			
		}
  
		//return $rowset;
		return $PermissionArr;
		
    }
    
	public function getGroupRights()
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
	
	  
    public function saveGroupRight(GroupRights $GroupRights,$module_id,$can_add,$can_edit,$can_view,$can_del)
    {
        if (isset($module_id) && count($module_id) > 0) {
			
			$data = array(
				'group_id' => $GroupRights->group_id,
			);
			
			// delete old entries
			//$this->deleteGroupRight($data['group_id']);
			
			foreach($module_id as $key => $value){
				
				$data['module_id'] = $value;
				$data['can_add'] = $can_add[$value];
				$data['can_edit'] = $can_edit[$value];
				$data['can_view'] = $can_view[$value];
				$data['can_del'] = $can_del[$value];
				 
				// check for entry
				
				$select = $this->tableGateway->getSql()->select();
				$select->where('grouprights.group_id = '.$data['group_id'].' AND grouprights.module_id = '.$data['module_id']);
				$rowset = $this->tableGateway->selectwith($select);
				$row = $rowset->current();

				if (!$row) {
					
					$this->tableGateway->insert($data);	
				}else{
					
					$this->tableGateway->update($data, array('group_id' => $data['group_id'], 'module_id' => $data['module_id']));		
				}
				
			}
			return true;
			// Update cache records
			//DataCache::updateData($this->CacheKey, $this->fetchAll());
		
		}else{
			
			return false;
			
		}
		
    }
   
    public function deleteGroupRight($id)
    {
        $delete = $this->tableGateway->getSql()->delete();
		$delete->where(array('group_id' => $id));
        $this->tableGateway->deletewith($delete);
        
        // Update cache records
        //DataCache::updateData($this->CacheKey, $this->fetchAll());
    }
   
}
