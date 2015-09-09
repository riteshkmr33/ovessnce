<?php 
namespace Admin\Model;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\DataCache;
use Zend\Db\Sql\Select;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

class TestTable
{
	protected $tableGateway;
	private $CacheKey = 'Test';
	
	public function __construct(TableGateway $tableGateway){
		$this->tableGateway = $tableGateway;
	}
	
	public function fetchAll($paginate=true)
	{
		if($paginate){
			$select = new Select('testOnly');
			$resultSetPrototype = new ResultSet();
			$resultSetPrototype->setArrayObjectPrototype(new Test());
			
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
	 public function getTest($id)
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
	public function saveTest(Test $test){
		
		$data = array(
				'name'=>$test->name,
				'contactNumber'=>$test->contactNumber,
				'language'=>implode(',',$test->language),
				'country'=>$test->country,
				'status'=>$test->status,
				'document'=>$test->document,
				);
		$id = (int) $test->id;
		
		if($id == 0){
			$this->tableGateway->insert($data);
		}else{
				 if ($this->getTest($id)) {
                $this->tableGateway->update($data, array('id' => $id));
				} else {
					throw new \Exception('Test id does not exist');
				}
		}
	}
	 public function deleteTest($id)
    {
        $this->tableGateway->delete(array('id' => (int) $id));
        
        // Update cache records
        //DataCache::updateData($this->CacheKey, $this->fetchAll());
    }
	}
	
?>
