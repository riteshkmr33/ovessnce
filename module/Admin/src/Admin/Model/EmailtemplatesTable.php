<?php

namespace Admin\Model;

use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\DataCache;
use Zend\Db\Sql\Select;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;
use Zend\Session\Container;

class EmailtemplatesTable
{

    protected $tableGateway;
    private $CacheKey = 'emailtemplates';

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll($paginate = true)
    {

        if ($paginate) {

            $select = new Select('emailtemplates');
            $resultSetPrototype = new ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new Emailtemplates());

            $paginatorAdapter = new DbSelect(
                    $select, $this->tableGateway->getAdapter(), $resultSetPrototype
            );
            $paginator = new Paginator($paginatorAdapter);

            return $paginator;
        }
        return $this->tableGateway->select();
    }

    public function getEmailtemplate($id)
    {
        $id = (int) $id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            return false;
            //throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function getEmailtemplates()
    {
        $result = DataCache::getData($this->CacheKey);

        // Update cache if data not found
        if ($result == false) {
            $result = $this->fetchAll();

            // Update cache records
            DataCache::updateData($this->CacheKey, $result);

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

    public function saveEmailtemplate(Emailtemplates $Emailtemplates)
    {

        $user_details = new Container('user_details');
        $details = $user_details->details;

        $data = array(
            'subject' => $Emailtemplates->subject,
            'content' => $Emailtemplates->content,
            'status' => $Emailtemplates->status,
            'fromEmail' => $Emailtemplates->fromEmail,
            'created_date' => $Emailtemplates->created_date,
            'modified_date' => $Emailtemplates->modified_date,
            'modified_by' => $details['user_id'],
        );

        $id = (int) $Emailtemplates->id;
        if ($id == 0) {

            unset($data['modified_date']);
            $this->tableGateway->insert($data);
        } else {
            if ($this->getEmailtemplate($id)) {

                unset($data['created_date']);
                $data['modified_date'] = date('Y-m-d h:i:s', time());

                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('id does not exist');
            }
        }

        // Update cache records
        //DataCache::updateData($this->CacheKey, $this->fetchAll());
    }

    public function deleteEmailtemplate($id)
    {
        $this->tableGateway->delete(array('id' => (int) $id));

        // Update cache records
        //DataCache::updateData($this->CacheKey, $this->fetchAll());
    }

    public function changeStatus($id, $status)
    {
        $this->tableGateway->update(array('status' => $status), array('id' => $id));
    }

}
