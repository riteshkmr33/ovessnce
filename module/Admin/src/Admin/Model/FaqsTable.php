<?php

namespace Admin\Model;

use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\DataCache;
use Zend\Db\Sql\Select;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

class FaqsTable
{

    protected $tableGateway;
    private $CacheKey = 'faqs';

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
        $adapter = $this->tableGateway->getAdapter();
    }

    public function fetchAll($paginate = true)
    {
        if ($paginate) {
            $select = new Select('faqs');
            $select->join('lookup_user_type', 'lookup_user_type.id = faqs.user_type_id', array('user_type'), 'left');
            $select->join('faq_index', 'faq_index.id = faqs.index_id', array('index_name'), 'left');
            $select->join('lookup_status', 'lookup_status.status_id = faqs.status_id', array('status'), 'left');
            //echo str_replace('"','',$select->getSqlString()); exit;
            $resultSetPrototype = new ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new Faqs());

            $paginatorAdapter = new DbSelect(
                    $select, $this->tableGateway->getAdapter(), $resultSetPrototype
            );
            $paginator = new Paginator($paginatorAdapter);

            return $paginator;
        }
        return $this->tableGateway->select();
    }

    public function getFaq($id)
    {
        $id = (int) $id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            return false;
        }
        return $row;
    }

    public function saveFaq(Faqs $faq)
    {
        $data = array(
            'index_id' => $faq->index_id,
            'user_type_id' => $faq->user_type_id,
            'question' => $faq->question,
            'answer' => $faq->answer,
            'order_by' => $faq->order_by,
            'status_id' => $faq->status_id,
        );

        $id = (int) $faq->id;
        if ($id == 0) {
            $data['created_on'] = date('Y-m-d h:i:s');
            $this->tableGateway->insert($data);
        } else {
            if ($this->getFaq($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('Faq id does not exist');
            }
        }
    }

    public function deleteFaq($id)
    {
        $this->tableGateway->delete(array('id' => (int) $id));
    }

    public function changeStatus($id, $status)
    {
        $this->tableGateway->update(array('status_id' => $status), array('id' => $id));
    }

}
