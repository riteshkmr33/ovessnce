<?php

namespace Admin\Model;

use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\DataCache;
use Zend\Db\Sql\Select;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

class NewsletterSubscribersTable
{

    protected $tableGateway;
    private $CacheKey = 'newslettersubscribers';

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll($paginate = true, $usertype = 8)
    {
        if ($paginate) {
            
            switch ($usertype) {
                case 3 :
                    $select = new Select('users');
                    $select->join('lookup_status', 'lookup_status.status_id = users.status_id', array('status'), 'left');
                    $select->join('user_feature_setting', 'user_feature_setting.user_id = users.id', array(), 'left');
                    $select->where('users.user_type_id = 3 and user_feature_setting.newsletter in (1,4)');
                    break;
                case 4 :
                    $select = new Select('users');
                    $select->join('lookup_status', 'lookup_status.status_id = users.status_id', array('status'), 'left');
                    $select->join('user_feature_setting', 'user_feature_setting.user_id = users.id', array(), 'left');
                    $select->where('users.user_type_id = 4 and user_feature_setting.newsletter in (1,4)');
                    break;
                case 8 :
                    $select = new Select('newsletter_subscription');
                    $select->join('lookup_status', 'lookup_status.status_id = newsletter_subscription.status_id', array('status'), 'left');
                    break;
                default :
                    $select = new Select('newsletter_subscription');
                    $select->join('lookup_status', 'lookup_status.status_id = newsletter_subscription.status_id', array('status'), 'left');
                    break;
            }

            $resultSetPrototype = new ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new NewsletterSubscribers());

            $paginatorAdapter = new DbSelect(
                    $select, $this->tableGateway->getAdapter(), $resultSetPrototype
            );
            $paginator = new Paginator($paginatorAdapter);

            return $paginator;
        }
        return $this->tableGateway->select();
    }

    public function getSubscriber($id)
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

    public function getSubscribers()
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

    public function saveSubscriber(NewsletterSubscribers $newslettersubscriber)
    {
        $data = array(
            'email' => $newslettersubscriber->email,
            'status_id' => $newslettersubscriber->status_id,
        );

        $id = (int) $newslettersubscriber->id;
        if ($id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getSubscriber($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('Newsletter Subscriber id does not exist');
            }
        }

        // Update cache records
        //DataCache::updateData($this->CacheKey, $this->fetchAll());
    }

    public function changeStatus($id, $status)
    {
        $this->tableGateway->update(array('status_id' => $status), array('id' => $id));
    }

    public function deleteSubscriber($id)
    {
        $this->tableGateway->delete(array('id' => (int) $id));

        // Update cache records
        //DataCache::updateData($this->CacheKey, $this->fetchAll());
    }

}
