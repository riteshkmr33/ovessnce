<?php

namespace Admin\Model; 

use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\DataCache;
use Zend\Db\Sql\Select;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;
use Zend\Db\Sql\Expression;

class SubscriptionPlansTable
{
    protected $tableGateway;
    private $CacheKey = 'subscriptionplans';
    private $features;
    private $subs_features;
    private $subscription;
    
    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
        $adapter=$this->tableGateway->getAdapter();
        $this->features = new TableGateway('site_feature', $adapter);
        $this->subs_features = new TableGateway('subscription_feature', $adapter);
        $this->subscription = new TableGateway('subscription', $adapter);
        $this->feature_video_limit = new TableGateway('feature_video_limit', $adapter);
    }
    
    public function fetchAll($paginate=true, $filter=array())
    {
		if ($paginate) {
			
			$select = new Select('subscription');
			$select->join('lookup_status','lookup_status.status_id = subscription.status_id', array('status'),'left');
			//echo str_replace('"','',$select->getSqlString()); exit;
			
			$resultSetPrototype = new ResultSet();
			$resultSetPrototype->setArrayObjectPrototype(new SubscriptionPlans());
			
			$paginatorAdapter = new DbSelect(
				$select,
				$this->tableGateway->getAdapter(),
				$resultSetPrototype
			);
			$paginator = new Paginator($paginatorAdapter);
			
			return $paginator;
		} else {
			$select = $this->tableGateway->getSql()->select();
			
			return $this->tableGateway->selectwith($select);
		}
    }
    
    public function getSubscriptionPlan($id)
    {
        $id  = (int) $id;
        $select = $this->tableGateway->getSql()->select();
		$select->join('lookup_status','lookup_status.status_id = subscription.status_id', array('status'),'left');
        $select->where('subscription.id = '.$id);
        $rowset = $this->tableGateway->selectwith($select);
        $row = $rowset->current();
        if (!$row) {
			return false;
            //throw new \Exception("Could not find row $id");
        }
        return $row;
    }
    
    public function getSubscribedUsers($subscription_id)
    {
		$select = $this->subscription->getSql()->select();
		$select->join('subscription_duration', 'subscription_duration.subscription_id = subscription.id', array(), 'inner');
		$select->join('user_subscriptions', 'user_subscriptions.subscription_duration_id = subscription_duration.id', array(), 'inner');
		$select->where(array('subscription.id' => $subscription_id));
		$results = $this->subscription->selectwith($select);
		return $results;
	}
    
    public function getFeatures()
    {
		return $this->features->select();
	}
	
	public function getFeatureVideoLimit($plan_id, $feature_id)
	{
		$select = $this->feature_video_limit->getSql()->select();
		$select->where(array('subscription_plan_id' => $plan_id, 'site_feature_id' => $feature_id));
		$results = $this->feature_video_limit->selectwith($select);
		$row = $results->current();
		if (!$row) {
			return false;
		} else {
			return $row;
		}
	}
    
	public function getSubscriptionPlans()
	{
		$result = DataCache::getData($this->CacheKey);
		
		// Update cache if data not found
		if ($result == false) {
			$result = $this->fetchAll(false);
			
			// Update cache records
			DataCache::updateData($this->CacheKey,$result);
			
			// Get latest records
			$result = DataCache::getData($this->CacheKey);
		}
		return $result;
	}
	
	public function getSubscriptionFeatures($subscription_id)
	{
		$features = array();
		$select = $this->subs_features->getSql()->select();
		$select->join('site_feature', 'site_feature.id = subscription_feature.site_feature_id', array('feature_name'), 'inner');
		$select->where(array('subscription_id' => $subscription_id));
		$results = $this->subs_features->selectwith($select);
		
		foreach ($results as $result) {
			$features[$result->site_feature_id] = $result->feature_name;
		}
		
		return $features;
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
  
    public function saveSubscriptionPlan(SubscriptionPlans $sp, $features = array())
    {
        $data = array(
            'subscription_name' => $sp->subscription_name,
            'status_id'       => $sp->status_id,
        );
        
		/* Adding subscription duration */
        $id = (int) $sp->id;
        if ($id == 0) {
            $this->tableGateway->insert($data);
            $id = $this->tableGateway->lastInsertValue;
        } else {
            if ($this->getSubscriptionPlan($id)) { 
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('Subscription id does not exist');
            }
        }
        
        // Deleting old records
        $this->subs_features->delete(array('subscription_id' => $id));
        if (count($features) > 0) {
			foreach ($features as $key=>$value) {
				if ($value == 1) { 
					$this->subs_features->insert(array('subscription_id' => $id, 'site_feature_id' => $key));
					
					// in case of demo video feature add number of video to upload
					if ($key == 2) {
						if (!$this->getFeatureVideoLimit($id, 2)) {
							$this->feature_video_limit->insert(array('subscription_plan_id' => $id, 'site_feature_id' => $key, 'limit' => $sp->limit, 'created_date' => date('Y-m-d h:i:s')));
							$id = $this->feature_video_limit->lastInsertValue;
						} else {
							if ($this->getFeatureVideoLimit($id, 2)) { 
								$this->feature_video_limit->update(array('subscription_plan_id' => $id, 'site_feature_id' => $key, 'limit' => $sp->limit), array('subscription_plan_id' => $id, 'site_feature_id' => $key));
							} else {
								throw new \Exception('Featured video limit id does not exist');
							}
						}
						
					}
				}
			}
		}
        
        // Update cache records
        //DataCache::updateData($this->CacheKey, $this->fetchAll(false));
    }
    
    public function changeStatus($id, $status)
    {
		$this->tableGateway->update(array('status_id'=>$status), array('id' => $id));
	}

    public function deleteSubscriptionPlan($id)
    {
        $this->tableGateway->delete(array('id' => (int) $id));
        $this->subs_features->delete(array('subscription_id' => (int) $id));
        $this->feature_video_limit->delete(array('subscription_plan_id' => (int) $id));
        
        // Update cache records
        //DataCache::updateData($this->CacheKey, $this->fetchAll(false));
    }
}
