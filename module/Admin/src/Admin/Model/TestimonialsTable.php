<?php 

namespace Admin\Model;

use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\DataCache;
use Zend\Db\Sql\Select;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

class TestimonialsTable
{
    protected $tableGateway;
    private $CacheKey = 'testimonials';
    
    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }
    
    public function fetchAll($paginate=true)
    {
		
		if ($paginate) {
			
			$select = new Select('testimonials');  
			$select->join('users','testimonials.created_by = users.id', array('user_name'),'left'); 
			$resultSetPrototype = new ResultSet(); 
			$resultSetPrototype->setArrayObjectPrototype(new Testimonials());
			
			 
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
    
    public function getTestimonial($id)
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
    
	public function getTestimonials()
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
  
    public function saveTestimonial(Testimonials $testimonials)
    {
        $data = array(
            'id' => $testimonials->id, 
            'text'  => $testimonials->text,
            'created_by'  => $testimonials->created_by,
            'created_on'  => $testimonials->created_on,
            'status_id'  => $testimonials->status_id,
        );

        $id = (int) $testimonials->id;
        if ($id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getTestimonial($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('Page id does not exist');
            }
        }
        
        // Update cache records
        //DataCache::updateData($this->CacheKey, $this->fetchAll());
    }

    public function deleteTestimonial($id)
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
