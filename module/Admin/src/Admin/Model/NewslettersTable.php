<?php

namespace Admin\Model;

use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\DataCache;
use Zend\Db\Sql\Select;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

class NewslettersTable
{

    protected $tableGateway;
    private $CacheKey = 'newsletters';

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll($paginate = true)
    {
        if ($paginate) {

            $select = new Select('newsletter');
            $select->join('lookup_status', 'lookup_status.status_id = newsletter.status_id', array('status'), 'left');
            $select->join('lookup_user_type', 'lookup_user_type.id = newsletter.user_type_id', array('user_type'), 'left');
            $select->where('newsletter.created_by = 1');

            $resultSetPrototype = new ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new Newsletters());

            $paginatorAdapter = new DbSelect(
                    $select, $this->tableGateway->getAdapter(), $resultSetPrototype
            );
            $paginator = new Paginator($paginatorAdapter);

            return $paginator;
        }
        return $this->tableGateway->select();
    }

    public function getNewsletter($id)
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

    public function getNewsletters()
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

    public function saveNewsletter(Newsletters $newsletter)
    {
        $data = array(
            'subject' => $newsletter->subject,
            'user_type_id' => $newsletter->user_type_id,
            'message' => $newsletter->message,
            'attachment' => $newsletter->attachment,
            'send_date' => date('Y-m-d h:i:s', strtotime($newsletter->send_date)),
            'status_id' => $newsletter->status_id,
            'created_by' => 1,
        );

        $id = (int) $newsletter->id;
        if ($id == 0) {
            $data['date_created'] = date('Y-m-d h:i:s');
            $this->tableGateway->insert($data);
        } else {
            if ($this->getNewsletter($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('Newsletter id does not exist');
            }
        }

        // Update cache records
        //DataCache::updateData($this->CacheKey, $this->fetchAll());
    }

    public function changeStatus($id, $status)
    {
        $this->tableGateway->update(array('status_id' => $status), array('id' => $id));
    }

    public function deleteNewsletter($id)
    {
        $this->tableGateway->delete(array('id' => (int) $id));

        // Update cache records
        //DataCache::updateData($this->CacheKey, $this->fetchAll());
    }

}
