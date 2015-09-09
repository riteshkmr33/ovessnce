<?php

namespace Admin\Model;

use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\DataCache;
use Zend\Db\Sql\Select;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

class FaqIndexTable
{

    protected $tableGateway;
    private $CacheKey = 'faq_index';

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
        $adapter = $this->tableGateway->getAdapter();
    }

    public function fetchAll($paginate = true)
    {
        if ($paginate) {
            $select = new Select('faq_index');
            $select->join('lookup_status', 'lookup_status.status_id = faq_index.status_id', array('status'), 'left');
            $resultSetPrototype = new ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new FaqIndex());

            $paginatorAdapter = new DbSelect(
                    $select, $this->tableGateway->getAdapter(), $resultSetPrototype
            );
            $paginator = new Paginator($paginatorAdapter);

            return $paginator;
        }
        return $this->tableGateway->select();
    }

    public function getFaqIndex($id)
    {
        $id = (int) $id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            return false;
        }
        return $row;
    }

    public function saveFaqIndex(FaqIndex $fi)
    {
        $data = array(
            'index_name' => $fi->index_name,
            'order_by' => $fi->order_by,
            'status_id' => $fi->status_id,
        );

        $id = (int) $fi->id;
        if ($id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getFaqIndex($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('Faq Index id does not exist');
            }
        }
    }

    public function deleteFaqIndex($id)
    {
        $this->tableGateway->delete(array('id' => (int) $id));
    }

    public function changeStatus($id, $status)
    {
        $this->tableGateway->update(array('status_id' => $status), array('id' => $id));
    }

}
