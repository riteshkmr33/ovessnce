<?php 

namespace Admin\Model;

use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\DataCache;
use Zend\Db\Sql\Select;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

class PractitionerOrganizationsTable
{
    protected $tableGateway;
    private $CacheKey = 'practiotioner-organizations';
    private $address;
    private $prac_org;
    private $organization_address;
    
    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
        $adapter=$this->tableGateway->getAdapter(); 
        $this->address = new TableGateway('address', $adapter);
        $this->prac_org = new TableGateway('practitioner_organization', $adapter);
        $this->organization_address = new TableGateway('organization_address', $adapter);
    }
    
    public function fetchAll($paginate=true,$filter=array())
    {
        if ($paginate) {
			
			$select = new Select(array('poa' => 'practitioner_organization_list')); 
			$select->join(array('oa' => 'organization_address'),'oa.organization_id = poa.organization_id', array('address_id'),'left'); 
			$select->join('address','address.id = oa.address_id', array('*'),'left'); 
			$select->join('state','address.state_id = state.id', array('state_name'),'left');
			$select->join('country','address.country_id = country.id', array('country_name'),'left'); 
			
			/* Data filter code start here*/
			if (count($filter)>0) {
				
				($filter['organization_name'] != "")?$select->where("poa.organization_name LIKE '%".$filter['organization_name']."%'"):"";
				($filter['state_id'] != "")?$select->where("address.state_id = ".$filter['state_id']):"";
				($filter['country_id'] != "")?$select->where("address.country_id = ".$filter['country_id']):"";
				($filter['status_id'] != "")?$select->where("poa.status_id = ".$filter['status_id']):"";
			}
			/* Data filter code end here*/
			
			$resultSetPrototype = new ResultSet(); 
			$resultSetPrototype->setArrayObjectPrototype(new PractitionerOrganizations());
			
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
		$select->join(array('oa' => 'organization_address'),'oa.organization_id = practitioner_organization_list.organization_id', array('address_id'),'left'); 
		$select->join('address','address.id = oa.address_id', array('*'),'left'); 
		$select->join('state','address.state_id = state.id', array('state_name'),'left');
		$select->join('country','address.country_id = country.id', array('country_name'),'left'); 
		
		/* Data filter code start here*/
		if (count($filter)>0) {
			
			($filter['organization_name'] != "")?$select->where("poa.organization_name LIKE '%".$filter['organization_name']."%'"):"";
			($filter['state_id'] != "")?$select->where("address.state_id = ".$filter['state_id']):"";
			($filter['country_id'] != "")?$select->where("address.country_id = ".$filter['country_id']):"";
			($filter['status_id'] != "")?$select->where("poa.status_id = ".$filter['status_id']):"";
		}
		/* Data filter code end here*/
		
		return $this->tableGateway->selectwith($select);
	}
    
    public function getOrganizationAddress($id)
    {
		$id  = (int) $id;
        $rowset = $this->organization_address->select(array("organization_id"=>$id));
        $row = $rowset->current();
        if (!$row) {
			return false;
            //throw new \Exception("Could not find row $id");
        }
        return $row;
    }
    
    public function getAddress($id)
    {
		$id  = (int) $id;
        $rowset = $this->address->select(array("id"=>$id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }
    
    public function getPractitionerOrganization($id)
    {
        $id  = (int) $id;
        
        $select = $this->tableGateway->getSql()->select();
        $select->join('organization_address','organization_address.organization_id = practitioner_organization_list.organization_id', array('address_id'),'left');
        $select->join('address','address.id = organization_address.address_id', array('street1_address','street2_address','city','zip_code','state_id','country_id'),'left');
		$select->where('practitioner_organization_list.organization_id = '.$id);
		$rowset = $this->tableGateway->selectwith($select);
			
        $row = $rowset->current();
        if (!$row) {
			return false;
            //throw new \Exception("Could not find row $id");
        }
        return $row;
    }
    
    public function getPracOrg($id)
    {	
		$id = (int) $id;
		$select = $this->prac_org->getSql()->select();
		$select->where('practitioner_organization.organization_id = '.$id);
		$rowset = $this->prac_org->selectwith($select);
		
		$row = $rowset->current();
        if (!$row) {
			return false;
        }
        return $row;
		
	}
    
	public function getPractitionerOrganizations()
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
  
    public function savePractitionerOrganization(PractitionerOrganizations $PractitionerOrganization)
    {
		
		$data = array(
            'organization_id' => $PractitionerOrganization->organization_id,
            'organization_name'  => $PractitionerOrganization->organization_name,
            'logo'  => $PractitionerOrganization->logo,
            'phone_no'  => $PractitionerOrganization->phone_no,
            'email'  => $PractitionerOrganization->email,
            'status_id'  => $PractitionerOrganization->status_id,
        );
        
         $address_data = array(
            'street1_address' => $PractitionerOrganization->street1_address,
            'street1_address' => $PractitionerOrganization->street1_address,
            'street2_address' => $PractitionerOrganization->street2_address,
            'city'            => $PractitionerOrganization->city,
            'zip_code'        => $PractitionerOrganization->zip_code,
            'state_id'        => $PractitionerOrganization->state_id,
            'country_id'      => $PractitionerOrganization->country_id,
        );
        
		foreach($data as $key => $value){
				if($value == null){
					unset($data[$key]);
				}
		}

        $id = (int) $PractitionerOrganization->organization_id;
        
        if ($id == 0) {
				
            $this->tableGateway->insert($data); // insert organization info 
            $organization_id = $this->tableGateway->lastInsertValue; // get last insert id for organization
            
            $this->address->insert($address_data); // insert address
			$address_id = $this->address->lastInsertValue; // get last insert id for address 
			
			/* Insert in oraganization_address */
            $organization_address = array(
				'organization_id' => $organization_id,
				'address_id' => $address_id,
			);
            $this->organization_address->insert($organization_address);
            
        } else {
            if ($this->getPractitionerOrganization($id)) {
				
				/* updating organization address - starts here */
				if($OrgAdd = $this->getOrganizationAddress($id)){
					if($this->getAddress($OrgAdd->address_id))
						$this->address->update($address_data, array('id' => $OrgAdd->address_id));		
				} else {
					
					/* insert if address is not there */
					$this->address->insert($address_data); // insert address	
					$address_id = $this->address->lastInsertValue; // get last insert id for address 	
					
					/* Insert in oraganization_address */ 
					$organization_address = array(
						'organization_id' => $id,
						'address_id' => $address_id,
					);
					$this->organization_address->insert($organization_address);
					
				}
				/* updating organization address - ends here */
				
                $this->tableGateway->update($data, array('organization_id' => $id));
            } else {
                throw new \Exception('ordanization id does not exist');
            }
        }
        
        // Update cache records
        //DataCache::updateData($this->CacheKey, $this->fetchAll());
    }

    public function deletePractitionerOrganization($id)
    {
				
        $this->tableGateway->delete(array('organization_id' => (int) $id));
        
        if($orgAdd = $this->getOrganizationAddress($id)){
			
			$this->address->delete(array('id' => (int) $orgAdd->address_id)); // delete organization address 
			
			$this->organization_address->delete(array('organization_id' => $id, 'address_id' => $orgAdd->address_id));	
			
		}
        
        // Update cache records
        //DataCache::updateData($this->CacheKey, $this->fetchAll());
    }
    
    public function changeStatus($id, $status)
    {
		$this->tableGateway->update(array('status_id'=>$status), array('organization_id' => $id));
	}
}
