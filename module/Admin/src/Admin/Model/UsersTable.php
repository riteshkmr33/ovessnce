<?php

namespace Admin\Model;

use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\DataCache;
use Zend\Db\Sql\Select;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;
use Zend\Db\Sql\Expression;

class UsersTable
{

    protected $tableGateway;
    private $CacheKey = 'users';
    private $address;
    private $user_address;
    private $group_rights;
    private $user_rights;
    private $contact;
    private $user_service_language;
    private $user_feature_setting;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;

        $adapter = $this->tableGateway->getAdapter();
        $this->address = new TableGateway('address', $adapter);
        $this->user_address = new TableGateway('user_address', $adapter);
        $this->group_rights = new TableGateway('grouprights', $adapter);
        $this->user_rights = new TableGateway('user_rights', $adapter);
        $this->contact = new TableGateway('user_contact', $adapter);
        $this->user_service_language = new TableGateway('consumer_service_language', $adapter);
        $this->user_feature_setting = new TableGateway('user_feature_setting', $adapter);
    }

    public function fetchAll($paginate = true, $filter = array())
    {
        if ($paginate) {

            $select = new Select('users');
            $select->columns(array('*', new Expression("user_feature_setting.email as email_status")));
            $select->join('lookup_user_type', 'users.user_type_id = lookup_user_type.id', array('user_type'), 'left');
            $select->join('user_address', 'user_address.user_id = users.id', array('address_id'), 'left');
            $select->join('user_feature_setting', 'user_feature_setting.user_id = users.id', array('chat', 'sms'), 'left');
            $select->join('address', 'address.id = user_address.address_id', array('city', 'state_id', 'country_id'), 'left');
            $select->join('state', 'address.state_id = state.id', array('state_name'), 'left');
            $select->join('country', 'address.country_id = country.id', array('country_name'), 'left');
            $select->join('lookup_status', 'lookup_status.status_id = users.status_id', array('status'), 'left');


            /* Data filter code start here */
            if (count($filter) > 0) {

                ($filter['name'] != "") ? $select->where("CONCAT(users.first_name,' ',users.last_name) LIKE '%" . $filter['name'] . "%'") : "";
                ($filter['user_name'] != "") ? $select->where("users.user_name LIKE '%" . $filter['user_name'] . "%'") : "";
                ($filter['age'] != "") ? $select->where("users.age = " . $filter['age']) : "";
                ($filter['gender'] != "") ? $select->where("users.gender LIKE '%" . $filter['gender'] . "%'") : "";
                ($filter['email'] != "") ? $select->where("users.email LIKE '%" . $filter['email'] . "%'") : "";
                ($filter['created_on'] != "") ? $select->where("DATE_FORMAT(users.created_date , '%Y-%m-%d') = '" . $filter['created_on'] . "'") : "";
                if (isset($filter['from_date']) && $filter['from_date'] != "" && isset($filter['to_date']) && $filter['to_date'] != "") {
                    $select->where("DATE_FORMAT(users.created_date , '%Y-%m-%d') BETWEEN '" . $filter['from_date'] . "' AND '" . $filter['to_date'] . "'");
                } else if (isset($filter['from_date']) && isset($filter['to_date']) && $filter['from_date'] != "" && $filter['to_date'] == "") {
                    $select->where("DATE_FORMAT(users.created_date , '%Y-%m-%d') = '" . $filter['from_date'] . "'");
                } else if (isset($filter['from_date']) && isset($filter['to_date']) && $filter['from_date'] == "" && $filter['to_date'] != "") {
                    $select->where("DATE_FORMAT(users.created_date , '%Y-%m-%d') = '" . $filter['to_date'] . "'");
                }
                if (isset($filter['from_login_date']) && $filter['from_login_date'] != "" && isset($filter['to_login_date']) && $filter['to_login_date'] != "") {
                    $select->where("DATE_FORMAT(users.last_login , '%Y-%m-%d') BETWEEN '" . $filter['from_login_date'] . "' AND '" . $filter['to_login_date'] . "'");
                } else if (isset($filter['from_login_date']) && isset($filter['to_login_date']) && $filter['from_login_date'] != "" && $filter['to_login_date'] == "") {
                    $select->where("DATE_FORMAT(users.last_login , '%Y-%m-%d') = '" . $filter['from_login_date'] . "'");
                } else if (isset($filter['from_login_date']) && isset($filter['to_login_date']) && $filter['from_login_date'] == "" && $filter['to_login_date'] != "") {
                    $select->where("DATE_FORMAT(users.last_login , '%Y-%m-%d') = '" . $filter['to_login_date'] . "'");
                }
                ($filter['city'] != "") ? $select->where("address.city LIKE '%" . $filter['city'] . "%'") : "";
                ($filter['state_id'] != "") ? $select->where("address.state_id = " . $filter['state_id']) : "";
                ($filter['country_id'] != "") ? $select->where("address.country_id = " . $filter['country_id']) : "";
                ($filter['status_id'] != "") ? $select->where("users.status_id = " . $filter['status_id']) : "";
                ($filter['chat'] != "") ? $select->where("user_feature_setting.chat = " . $filter['chat']) : "";
                ($filter['sms'] != "") ? $select->where("user_feature_setting.sms = " . $filter['sms']) : "";
                ($filter['email_status'] != "") ? $select->where("user_feature_setting.email = " . $filter['email_status']) : "";
                ($filter['user_type_id'] != "") ? $select->where("users.user_type_id = " . $filter['user_type_id']) : "";
            }
            /* Data filter code end here */

            $resultSetPrototype = new ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new Users());



            $paginatorAdapter = new DbSelect(
                    $select, $this->tableGateway->getAdapter(), $resultSetPrototype
            );

            $paginator = new Paginator($paginatorAdapter);

            return $paginator;
        } else {
            $select = $this->tableGateway->getSql()->select();
            /* Data filter code start here */
            if (count($filter) > 0) {
                ($filter['user_type'] != "") ? $select->where(array("users.user_type_id" => $filter['user_type'])) : "";
            }
            $select->order('user_name ASC');
            /* Data filter code end here */
            return $this->tableGateway->selectwith($select);
        }
    }

    public function ExportAllConsumers($filter = array(), $orderBy = array(), $usersType = '')
    {
        $select = $this->tableGateway->getSql()->select();
        $select->join('lookup_user_type', 'users.user_type_id = lookup_user_type.id', array('user_type'), 'left');
        $select->join('user_address', 'user_address.user_id = users.id', array('address_id'), 'left');
        $select->join('user_feature_setting', 'user_feature_setting.user_id = users.id', array('chat', 'sms'), 'left');
        $select->join('address', 'address.id = user_address.address_id', array('city', 'state_id', 'country_id'), 'left');
        $select->join('state', 'address.state_id = state.id', array('state_name'), 'left');
        $select->join('country', 'address.country_id = country.id', array('country_name'), 'left');


        /* Data filter code start here */
        if (count($filter) > 0) {

            ($filter['name'] != "") ? $select->where("CONCAT(users.first_name,' ',users.last_name) LIKE '%" . $filter['name'] . "%'") : "";
            ($filter['user_name'] != "") ? $select->where("users.user_name LIKE '%" . $filter['user_name'] . "%'") : "";
            ($filter['age'] != "") ? $select->where("users.age = " . $filter['age']) : "";
            ($filter['gender'] != "") ? $select->where("users.gender LIKE '%" . $filter['gender'] . "%'") : "";
            ($filter['email'] != "") ? $select->where("users.email LIKE '%" . $filter['email'] . "%'") : "";
            ($filter['created_on'] != "") ? $select->where("DATE_FORMAT(users.created_date , '%Y-%m-%d') = '" . $filter['created_on'] . "'") : "";
            if (isset($filter['from_date']) && $filter['from_date'] != "" && isset($filter['to_date']) && $filter['to_date'] != "") {
                $select->where("DATE_FORMAT(users.created_date , '%Y-%m-%d') BETWEEN '" . $filter['from_date'] . "' AND '" . $filter['to_date'] . "'");
            } else if (isset($filter['from_date']) && isset($filter['to_date']) && $filter['from_date'] != "" && $filter['to_date'] == "") {
                $select->where("DATE_FORMAT(users.created_date , '%Y-%m-%d') = '" . $filter['from_date'] . "'");
            } else if (isset($filter['from_date']) && isset($filter['to_date']) && $filter['from_date'] == "" && $filter['to_date'] != "") {
                $select->where("DATE_FORMAT(users.created_date , '%Y-%m-%d') = '" . $filter['to_date'] . "'");
            }
            if (isset($filter['from_login_date']) && $filter['from_login_date'] != "" && isset($filter['to_login_date']) && $filter['to_login_date'] != "") {
                $select->where("DATE_FORMAT(users.last_login , '%Y-%m-%d') BETWEEN '" . $filter['from_login_date'] . "' AND '" . $filter['to_login_date'] . "'");
            } else if (isset($filter['from_login_date']) && isset($filter['to_login_date']) && $filter['from_login_date'] != "" && $filter['to_login_date'] == "") {
                $select->where("DATE_FORMAT(users.last_login , '%Y-%m-%d') = '" . $filter['from_login_date'] . "'");
            } else if (isset($filter['from_login_date']) && isset($filter['to_login_date']) && $filter['from_login_date'] == "" && $filter['to_login_date'] != "") {
                $select->where("DATE_FORMAT(users.last_login , '%Y-%m-%d') = '" . $filter['to_login_date'] . "'");
            }
            ($filter['city'] != "") ? $select->where("address.city LIKE '%" . $filter['city'] . "%'") : "";
            ($filter['state_id'] != "") ? $select->where("address.state_id = " . $filter['state_id']) : "";
            ($filter['country_id'] != "") ? $select->where("address.country_id = " . $filter['country_id']) : "";
            ($filter['status_id'] != "") ? $select->where("users.status_id = " . $filter['status_id']) : "";
            ($filter['chat'] != "") ? $select->where("user_feature_setting.chat = " . $filter['chat']) : "";
            ($filter['sms'] != "") ? $select->where("user_feature_setting.sms = " . $filter['sms']) : "";
            ($filter['email_status'] != "") ? $select->where("user_feature_setting.email = " . $filter['email_status']) : "";
            ($filter['user_type_id'] != "") ? $select->where("users.user_type_id = " . $filter['user_type_id']) : "";
        } else {
            if ($usersType != 'All') {
                $select->where("users.user_type_id = 4");
            }
        }
        /* Data filter code end here */

        return $this->tableGateway->selectwith($select);
    }

    public function getUser($id, $attr = 'id')
    {

        if ($attr == 'id') {

            $id = (int) $id;
            //$rowset = $this->tableGateway->select(array('id' => $id));
            $select = $this->tableGateway->getSql()->select();
            $select->columns(array('*', new Expression("user_feature_setting.email as email_status")));
            $select->join('user_address', 'user_address.user_id = users.id', array('address_id'), 'left');
            $select->join('user_feature_setting', 'user_feature_setting.user_id = users.id', array('chat', 'sms'), 'left');
            $select->join('address', 'address.id = user_address.address_id', array('street1_address', 'street2_address', 'city', 'zip_code', 'state_id', 'country_id'), 'left');
            $select->join('user_contact', 'user_contact.user_id = users.id', array('home_phone', 'work_phone', 'cell_phone', 'fax'), 'left');
            $select->where('users.id = ' . $id);
            $rowset = $this->tableGateway->selectwith($select);
        } else {
            //$rowset = $this->tableGateway->select(array($attr => $id));
            $select = $this->tableGateway->getSql()->select();
            $select->columns(array('*', new Expression("user_feature_setting.email as email_status")));
            $select->join('user_address', 'user_address.user_id = users.id', array('address_id'), 'left');
            $select->join('user_feature_setting', 'user_feature_setting.user_id = users.id', array('chat', 'sms'), 'left');
            $select->join('address', 'address.id = user_address.address_id', array('street1_address', 'street2_address', 'city', 'zip_code', 'state_id', 'country_id'), 'left');
            $select->join('user_contact', 'user_contact.user_id = users.id', array('home_phone', 'work_phone', 'cell_phone', 'fax'), 'left');
            $select->where('users.' . $attr . ' = "' . $id . '"');
            $rowset = $this->tableGateway->selectwith($select);
        }

        $row = $rowset->current();

        if (!$row) {
            return false;
            //throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function getUserData($where = array(), $field = 'id')
    {
        $select = $this->tableGateway->getSql()->select();
        $select->columns(array($field));
        $select->where($where);
        $result = $this->tableGateway->selectwith($select);
        return $result->current();
    }

    public function getFeatureSetting($user_id)
    {
        $results = $this->user_feature_setting->select(array('user_id' => $user_id));
        $row = $results->current();
        if (!$row) {
            return false;
        } else {
            return $row;
        }
    }

    public function updateFeatureSetting($ids, $data)
    {
        foreach ($ids as $id) {
            if (!$this->getFeatureSetting($id)) {
                $this->user_feature_setting->insert(array_merge($data, array('user_id' => $id)));
            } else {
                $this->user_feature_setting->update($data, array('user_id' => $id));
            }
        }
    }

    public function getUserAddress($id)
    {
        $id = (int) $id;
        $rowset = $this->user_address->select(array("user_id" => $id));
        $row = $rowset->current();
        if (!$row) {
            //throw new \Exception("Could not find row $id");
            return false;
        }
        return $row;
    }

    /* Function to fetch consumer languages */

    public function getConsumerServiceLanguage($id, $inarray = false)
    {
        $select = $this->user_service_language->getSql()->select();
        $select->join('service_language', 'service_language.id = consumer_service_language.service_language_id');
        $select->where(array('user_id' => $id));
        $results = $this->user_service_language->selectwith($select);

        if ($inarray == true) {
            $data = array();
            foreach ($results as $result) {
                $data[$result->id] = $result->language_name;
            }
            return $data;
        } else {
            return $results;
        }
    }

    /* Function to get current month data */

    public function getDataByMonth($start, $end)
    {
        $select = $this->tableGateway->getSql()->select();
        $select->columns(array(new Expression("count(*) as total, DATE_FORMAT(created_date, '%d-%m-%Y') as month")));
        $select->where("user_type_id = 4 AND DATE_FORMAT(created_date, '%Y-%m-%d') BETWEEN '" . date('Y-m-d', strtotime($start)) . "' AND '" . date('Y-m-d', strtotime($end)) . "'");
        $select->group("month");

        $results = $this->tableGateway->selectwith($select);
        $data = array();

        foreach ($results as $result) {
            $data[$result->month] = $result->total;
        }

        $currentDate = $start;
        for ($i = 1; $i <= 31; $i++) {
            if (strtotime($currentDate) <= strtotime($end)) {
                !isset($data[$currentDate]) ? $data[$currentDate] = 0 : '';
                $currentDate = date('d-m-Y', strtotime($currentDate . ' +1 days'));
                
            } else {
                break;
            }
        }
        
        //echo '<pre>'; print_r($data); exit;

        krsort($data);

        return $data;
    }

    public function getAddress($id)
    {
        $id = (int) $id;
        $rowset = $this->address->select(array("id" => $id));
        $row = $rowset->current();
        if (!$row) {
            //throw new \Exception("Could not find row $id");
            return false;
        }
        return $row;
    }

    public function getUserContact($id)
    {
        $id = (int) $id;
        $rowset = $this->contact->select(array("user_id" => $id));
        $row = $rowset->current();
        if (!$row) {
            //throw new \Exception("Could not find row $id");
            return false;
        }
        return $row;
    }

    public function getUsers()
    {
        $result = DataCache::getData($this->CacheKey);

        // Update cache if data not found
        if ($result == false) {
            $result = $this->fetchAll(false);

            // Update cache records
            DataCache::updateData($this->CacheKey, $result);

            // Get latest records
            $result = DataCache::getData($this->CacheKey);
        }
        return $result;
    }

    public function getData()
    {
        $result = DataCache::getData($this->CacheKey);

        // Update cache if data not found
        if ($result == false) {
            $result = $this->fetchAll();
            DataCache::updateData($this->CacheKey, $result);
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
                DataCache::updateData($key, $result);
            }
            return $result;
        }
    }

    public function saveUser(Users $users, $action = 'no_action', $languages = array())
    {

        $data = array(
            'user_type_id' => $users->user_type_id,
            'register_from' => $users->register_from,
            'first_name' => ucfirst($users->first_name),
            'last_name' => ucfirst($users->last_name),
            'user_name' => $users->user_name,
            'email' => $users->email,
            'pass' => md5($users->pass),
            'age' => $users->age,
            'gender' => $users->gender,
            'social_media_id' => $users->social_media_id,
            'created_date' => $users->created_date,
            'last_login' => $users->last_login,
            'expiration_date' => $users->expiration_date,
            'status_id' => $users->status_id,
        );

        $address_data = array(
            'street1_address' => $users->street1_address,
            'street2_address' => $users->street2_address,
            'city' => $users->city,
            'zip_code' => $users->zip_code,
            'state_id' => $users->state_id,
            'country_id' => $users->country_id,
        );

        $contact_data = array(
            'home_phone' => $users->home_phone,
            'work_phone' => $users->work_phone,
            'cell_phone' => $users->cell_phone,
            'fax' => $users->fax,
        );

        $UFS_data = array(
            'chat' => $users->chat,
            'sms' => $users->sms,
            'email' => $users->email_status,
        );

        foreach ($data as $key => $value) {
            if ($value == null) {
                unset($data[$key]);
            }
        }

        if ($action == 'update_last_login') {
            unset($data['pass']);
        }

        $consumer_id = '';
        $id = (int) $users->id;

        if ($id == 0) {

            $user_data['status_id'] = 1;
            $data['created_date'] = date('Y-m-d h:i:s');

            $this->tableGateway->insert($data); // insert user info
            $last_insert_id = $this->tableGateway->lastInsertValue; // get last insert id for user

            /* Inserting rights for user starts here */
            $rowset = $this->group_rights->select(array("group_id" => $data['user_type_id']));  // collecting group rights by user_type_id           
            foreach ($rowset as $row) {
                $UserRights_data = array(
                    'user_id' => $last_insert_id,
                    'module_id' => $row->module_id,
                    'can_add' => $row->can_add,
                    'can_edit' => $row->can_edit,
                    'can_view' => $row->can_view,
                    'can_del' => $row->can_del,
                );

                $this->user_rights->insert($UserRights_data); // Inserting row in user_rights 
            }
            /* Inserting rights for user ends here */

            $consumer_id = $contact_data['user_id'] = $last_insert_id;

            $this->address->insert($address_data); // insert user address
            $address_insert_id = $this->address->lastInsertValue; // get last insert id for address 

            /* make entry in user address */
            $user_address_data = array(
                'user_id' => $last_insert_id,
                'address_id' => $address_insert_id
            );
            $this->user_address->insert($user_address_data);

            $this->contact->insert($contact_data); // insert user address
        } else {
            if ($userObj = $this->getUser($id)) {

                unset($data['created_date']);

                $this->tableGateway->update($data, array('id' => $id));

                /* If user has been changed then change the rights also as per new group delete all old group permissions */
                if ($userObj->user_type_id != $data['user_type_id']) {

                    $this->user_rights->delete(array('user_id' => $id)); // delete all user rights associated to previous group

                    /* Updating rights for user starts here */
                    $rowset = $this->group_rights->select(array("group_id" => $data['user_type_id']));  // collecting group rights by user_type_id           
                    foreach ($rowset as $row) {

                        $UserRights_data = array(
                            'user_id' => $id,
                            'module_id' => $row->module_id,
                            'can_add' => $row->can_add,
                            'can_edit' => $row->can_edit,
                            'can_view' => $row->can_view,
                            'can_del' => $row->can_del,
                        );

                        $this->user_rights->insert($UserRights_data); // Inserting row in user_rights 
                    }
                    /* Updating rights for user ends here */
                }
                /* permissions code ends here */

                /* updating user address - starts here */
                if ($usrAdd = $this->getUserAddress($id)) {

                    if ($this->getAddress($usrAdd->address_id)) {
                        $this->address->update($address_data, array('id' => $usrAdd->address_id));
                    }
                } else {

                    //throw new \Exception('User Address id does not exist');

                    /* insert if address is not there */
                    $this->address->insert($address_data); // insert address	
                    $address_insert_id = $this->address->lastInsertValue; // get last insert id for address 	

                    /* Insert in oraganization_address */
                    $user_address = array(
                        'user_id' => $id,
                        'address_id' => $address_insert_id,
                    );
                    $this->user_address->insert($user_address);
                }
                /* updating user address - ends here */

                /* updating user contact - starts here */
                if ($this->getUserContact($id)) {
                    $this->contact->update($contact_data, array('user_id' => $id));
                } else {
                    //throw new \Exception('User Contact id does not exist');
                    $contact_data['user_id'] = $userObj->id;
                    $this->contact->insert($contact_data);
                }
                /* updating user contact - ends here */

                $consumer_id = $id;
            } else {
                throw new \Exception('User id does not exist');
            }
        }

        /* Inserting feature setting for this user start */
        $user_id = ($id == 0) ? $last_insert_id : $id;
        if (!$this->getFeatureSetting($user_id)) {
            $UFS_data['user_id'] = $user_id;
            $this->user_feature_setting->insert($UFS_data);
        } else {
            $this->user_feature_setting->update($UFS_data, array('user_id' => $user_id));
        }
        /* Inserting feature setting for this user end */

        if ($consumer_id != '' && count($languages) > 0) {

            // deleting old entries
            $this->user_service_language->delete(array('user_id' => $consumer_id));

            // adding new entries
            foreach ($languages as $language) {
                $this->user_service_language->insert(array('user_id' => $consumer_id, 'service_language_id' => $language));
            }
        }

        // Update cache data
        //DataCache::updateData($this->CacheKey,$this->fetchAll());
    }

    public function deleteUser($id)
    {
        $user = $this->getUser($id);

        $this->tableGateway->delete(array('id' => (int) $id));

        $this->user_rights->delete(array('user_id' => (int) $id));

        if ($usrAdd = $this->getUserAddress($id)) {

            $this->address->delete(array('id' => (int) $usrAdd->address_id)); // delete user address 

            $this->user_address->delete(array('user_id' => $id, 'address_id' => $usrAdd->address_id));
        }

        $this->contact->delete(array('user_id' => (int) $user->id)); // delete user contact  

        $this->user_service_language->delete(array('user_id' => $user->id));

        // Update cache data
        //DataCache::updateData($this->CacheKey,$this->fetchAll(false));
    }

    public function changeStatus($id, $status)
    {
        $this->tableGateway->update(array('status_id' => $status), array('id' => $id));
    }

    public function GetLatestUsers()
    {
        $select = $this->tableGateway->getSql()->select();
        $select->join('lookup_user_type', 'users.user_type_id = lookup_user_type.id', array('user_type'), 'left');
        $select->where('users.created_date > NOW() - INTERVAL 4 DAY');
        $select->where('1=1');
        $rowset = $this->tableGateway->selectwith($select);

        if ($rowset->count() > 0) {
            return $rowset;
        } else {
            return false;
        }
    }
    
    public function getPracStatsByCities($user_type_id, $date, $status)
    {
        $total = 0;
        $data = array();
        
        $selectTotal = $this->tableGateway->getSql()->select();
        $selectTotal->columns(array(new Expression('count(id) as total')));
        $selectTotal->where('user_type_id = "' . $user_type_id . '" AND date_format(created_date,"%Y-%m-%d") >= "' . $date . '"');
        ($status == 3 || $status == 10) ? $selectTotal->where('status_id = ' . $status . '') : $selectTotal->where('status_id IN (' . $status . ', 5)');
        //echo str_replace('"','',$selectTotal->getSqlString()); exit;
        $resultTotal = $this->tableGateway->selectwith($selectTotal)->current();
        $total = $resultTotal->total;
        
        $subquery = new Select('service_provider_address');
        $subquery->columns(array('*', new Expression('min(service_provider_address.address_id) AS adrs_id')));
        $subquery->group('user_id');

        $select = $this->tableGateway->getSql()->select();
        $select->columns(array(new Expression('users.user_name, date_format(date(users.created_date),"%Y-%m-%d") as created_date, COUNT( users.id ) AS users_count, state.state_name, address.state_id')));
        ($user_type_id == 3) ? $select->join(array('address_link' => $subquery), 'address_link.user_id = users.id', array(), 'left') : $select->join(array('address_link' => 'user_address'), 'users.id = address_link.user_id', array(), 'left');
        $select->join('address', 'address_link.address_id = address.id', array(), 'left');
        $select->join('state', 'address.state_id = state.id', array(), 'inner');
        $select->where('user_type_id = "' . $user_type_id . '" AND date_format(users.created_date,"%Y-%m-%d") >= "' . $date . '"');
        ($status == 3 || $status == 10) ? $select->where('users.status_id = ' . $status . '') : $select->where('users.status_id IN (' . $status . ', 5)');
        $select->group('address.state_id');
        $select->order('users_count DESC');
        $select->limit(10);
        
        //echo str_replace('"','',$select->getSqlString()); exit;

        $results = $this->tableGateway->selectwith($select);

        foreach ($results as $result) {
            //print_r($result);
            $row = array('state_name' => $result->state_name,'users_count' => $result->users_count, 'total' => 0, 'growth' => 0);
            $row['total'] = ($total > 0)?round(($result->users_count/$total)*100):0;
            
            $growthSubquery = new Select('service_provider_address');
            $growthSubquery->columns(array('*', new Expression('min(service_provider_address.address_id) AS adrs_id')));
            $growthSubquery->group('user_id');

            $growthSelect = $this->tableGateway->getSql()->select();
            $growthSelect->columns(array(new Expression('users.user_name, date_format(date(users.created_date),"%Y-%m-%d") as created_date, COUNT( users.id ) AS users_count, state.state_name, address.state_id')));
            ($user_type_id == 3) ? $growthSelect->join(array('address_link' => $growthSubquery), 'address_link.user_id = users.id', array(), 'left') : $growthSelect->join(array('address_link' => 'user_address'), 'users.id = address_link.user_id', array(), 'left');
            $growthSelect->join('address', 'address_link.address_id = address.id', array(), 'left');
            $growthSelect->join('state', 'address.state_id = state.id', array(), 'inner');
            ($status == 3 || $status == 10) ? $growthSelect->where('users.status_id = ' . $status . '') : $growthSelect->where('users.status_id IN (' . $status . ', 5)');
            $growthSelect->where('user_type_id = "' . $user_type_id . '" AND state.id = '. $result->state_id);

            switch ($date) {
                case date("Y-m-d", strtotime("now")) :
                    $growthSelect->where("date_format(users.created_date,'%Y-%m-%d') = '". date("Y-m-d",strtotime("-1 days")) ."'");
                    break;
                case date("Y-m-d", strtotime("-1 week")) :
                    $growthSelect->where("date_format(users.created_date,'%Y-%m-%d') BETWEEN '". date("Y-m-d",strtotime("-14 days")) ."' AND '". date("Y-m-d",strtotime("-7 days")) ."'");
                    break;
                case date("Y-m-d", strtotime("-1 month")) :
                    $growthSelect->where("date_format(users.created_date,'%Y-%m-%d') BETWEEN '". date("Y-m-d",strtotime("-2 month -1 days")) ."' AND '". date("Y-m-d",strtotime("-1 month -1 days")) ."'");
                    break;
                case date("Y-m-d", strtotime("-1 year")) :
                    $growthSelect->where("date_format(users.created_date,'%Y-%m-%d') BETWEEN '". date("Y-m-d",strtotime("-2 year")) ."' AND '". date("Y-m-d",strtotime("-1 year")) ."'");
                    break;
            }
            
            //echo str_replace('"','',$select->getSqlString()); exit;
            $growth = $this->tableGateway->selectwith($growthSelect)->current();
            //echo '<pre>'; print_r($growth);
            
            $row['growth'] = (isset($growth->users_count) && $growth->users_count > 0)?(round($result->users_count-$growth->users_count)/$growth->users_count)*100:($result->users_count-$growth->users_count)*100;
            $data[] = $row;
        }
        
        return $data;
    }

    public function getCancStats($date)
    {

        $data = array();
        $adapter = $this->tableGateway->getAdapter();

        $Consumersql = "SELECT u.user_name, u.user_type_id, date_format( date( dal.created_date ) , '%Y-%m-%d' ) AS created_date, COUNT( u.id ) AS users_count, st.state_name, st.id as state_id FROM users AS u
                        INNER JOIN deactivated_accounts_list AS dal ON dal.user_id = u.id
			LEFT JOIN user_address AS ua ON u.id = ua.user_id
			LEFT JOIN address AS a ON ua.address_id = a.id
			LEFT JOIN state AS st ON a.state_id = st.id
			WHERE `user_type_id` = 4 AND dal.created_date >= '" . $date . "' AND u.status_id IN ('3', '10') GROUP BY a.state_id ORDER BY users_count DESC LIMIT 0 , 10";

        $Practitionersql = "SELECT u.user_name, u.user_type_id, date_format( date( dal.created_date ) , '%Y-%m-%d' ) AS created_date, COUNT( u.id ) AS users_count, st.state_name, st.id AS state_id FROM users AS u
                        INNER JOIN deactivated_accounts_list AS dal ON dal.user_id = u.id
			LEFT JOIN (SELECT service_provider_address.*, min(service_provider_address.address_id) AS adrs_id FROM service_provider_address GROUP BY user_id) AS ua ON u.id = ua.user_id
			LEFT JOIN address AS a ON ua.adrs_id = a.id
			LEFT JOIN state AS st ON a.state_id = st.id
			WHERE `user_type_id` = 3 AND dal.created_date >= '" . $date . "' AND u.status_id IN ('3', '10') GROUP BY a.state_id ORDER BY users_count DESC LIMIT 0 , 10";

        $statement = $adapter->query($Consumersql);
        $consumers = $statement->execute();

        $statement = $adapter->query($Practitionersql);
        $practitioners = $statement->execute();
        $total = 0;

        foreach ($consumers as $consumer) {
            $data[$consumer['state_name']] = array('consumer_count' => $consumer['users_count']);
        }

        foreach ($practitioners as $practitioner) {

            $data[$practitioner['state_name']]['practitioner_count'] = $practitioner['users_count'];
            $data[$practitioner['state_name']]['consumer_count'] = isset($data[$practitioner['state_name']]['consumer_count']) ? $data[$practitioner['state_name']]['consumer_count'] : 0;
            $total = $total + $data[$practitioner['state_name']]['practitioner_count'] + $data[$practitioner['state_name']]['consumer_count'];

            // Calculating growth
            switch ($date) {
                case date("Y-m-d", strtotime("now")) :
                    $GrowthConsumer = "SELECT u.user_name, u.user_type_id, date_format( date( dal.created_date ) , '%Y-%m-%d' ) AS created_date, COUNT( u.id ) AS users_count, st.state_name, st.id FROM users AS u
                                                INNER JOIN deactivated_accounts_list AS dal ON dal.user_id = u.id
						LEFT JOIN user_address AS ua ON u.id = ua.user_id
						LEFT JOIN address AS a ON ua.address_id = a.id
						LEFT JOIN state AS st ON a.state_id = st.id
						WHERE `user_type_id` = 4 AND st.id = " . $practitioner['state_id'] . " AND dal.created_date = '" . date("Y-m-d", strtotime("-1 days")) . "' AND u.status_id IN ('3', '10') GROUP BY a.state_id ORDER BY users_count DESC LIMIT 0 , 10";

                    $GrowthPractitioner = "SELECT u.user_name, u.user_type_id, date_format( date( dal.created_date ) , '%Y-%m-%d' ) AS created_date, COUNT( u.id ) AS users_count, st.state_name, st.id FROM users AS u
                                                INNER JOIN deactivated_accounts_list AS dal ON dal.user_id = u.id
						LEFT JOIN (SELECT service_provider_address.*, min(service_provider_address.address_id) AS adrs_id FROM service_provider_address GROUP BY user_id) AS ua ON u.id = ua.user_id
						LEFT JOIN address AS a ON ua.adrs_id = a.id
						LEFT JOIN state AS st ON a.state_id = st.id
						WHERE `user_type_id` = 3 AND st.id = " . $practitioner['state_id'] . " AND dal.created_date = '" . date("Y-m-d", strtotime("-1 days")) . "' AND u.status_id IN ('3', '10') GROUP BY a.state_id ORDER BY users_count DESC LIMIT 0 , 10";
                    break;

                case date("Y-m-d", strtotime("-1 week")) :
                    $GrowthConsumer = "SELECT u.user_name, u.user_type_id, date_format( date( dal.created_date ) , '%Y-%m-%d' ) AS created_date, COUNT( u.id ) AS users_count, st.state_name, st.id FROM users AS u
                                                INNER JOIN deactivated_accounts_list AS dal ON dal.user_id = u.id
						LEFT JOIN user_address AS ua ON u.id = ua.user_id
						LEFT JOIN address AS a ON ua.address_id = a.id
						LEFT JOIN state AS st ON a.state_id = st.id
						WHERE `user_type_id` = 4 AND st.id = " . $practitioner['state_id'] . " AND date_format(dal.created_date,'%Y-%m-%d') BETWEEN '" . date("Y-m-d", strtotime("-14 days")) . "' AND '" . date("Y-m-d", strtotime("-7 days")) . "' AND u.status_id IN ('3', '10') GROUP BY a.state_id ORDER BY users_count DESC LIMIT 0 , 10";

                    $GrowthPractitioner = "SELECT u.user_name, u.user_type_id, date_format( date( dal.created_date ) , '%Y-%m-%d' ) AS created_date, COUNT( u.id ) AS users_count, st.state_name, st.id FROM users AS u
                                                INNER JOIN deactivated_accounts_list AS dal ON dal.user_id = u.id
						LEFT JOIN (SELECT service_provider_address.*, min(service_provider_address.address_id) AS adrs_id FROM service_provider_address GROUP BY user_id) AS ua ON u.id = ua.user_id
						LEFT JOIN address AS a ON ua.adrs_id = a.id
						LEFT JOIN state AS st ON a.state_id = st.id
						WHERE `user_type_id` = 3 AND st.id = " . $practitioner['state_id'] . " AND date_format(dal.created_date,'%Y-%m-%d') BETWEEN '" . date("Y-m-d", strtotime("-14 days")) . "' AND '" . date("Y-m-d", strtotime("-7 days")) . "' AND u.status_id IN ('3', '10') GROUP BY a.state_id ORDER BY users_count DESC LIMIT 0 , 10";
                    break;

                case date("Y-m-d", strtotime("-1 month")) :
                    $GrowthConsumer = "SELECT u.user_name, u.user_type_id, date_format( date( dal.created_date ) , '%Y-%m-%d' ) AS created_date, COUNT( u.id ) AS users_count, st.state_name, st.id FROM users AS u
                                                INNER JOIN deactivated_accounts_list AS dal ON dal.user_id = u.id
						LEFT JOIN user_address AS ua ON u.id = ua.user_id
						LEFT JOIN address AS a ON ua.address_id = a.id
						LEFT JOIN state AS st ON a.state_id = st.id
						WHERE `user_type_id` = 4 AND st.id = " . $practitioner['state_id'] . " AND date_format(dal.created_date,'%Y-%m-%d') BETWEEN '" . date("Y-m-d", strtotime("-2 month -1 days")) . "' AND '" . date("Y-m-d", strtotime("-1 month -1 days")) . "' AND u.status_id IN ('3', '10') GROUP BY a.state_id ORDER BY users_count DESC LIMIT 0 , 10";

                    $GrowthPractitioner = "SELECT u.user_name, u.user_type_id, date_format( date( dal.created_date ) , '%Y-%m-%d' ) AS created_date, COUNT( u.id ) AS users_count, st.state_name, st.id FROM users AS u
                                                INNER JOIN deactivated_accounts_list AS dal ON dal.user_id = u.id
						LEFT JOIN (SELECT service_provider_address.*, min(service_provider_address.address_id) AS adrs_id FROM service_provider_address GROUP BY user_id) AS ua ON u.id = ua.user_id
						LEFT JOIN address AS a ON ua.adrs_id = a.id
						LEFT JOIN state AS st ON a.state_id = st.id
						WHERE `user_type_id` = 3 AND st.id = " . $practitioner['state_id'] . " AND date_format(dal.created_date,'%Y-%m-%d') BETWEEN '" . date("Y-m-d", strtotime("-2 month -1 days")) . "' AND '" . date("Y-m-d", strtotime("-1 month -1 days")) . "' AND u.status_id IN ('3', '10') GROUP BY a.state_id ORDER BY users_count DESC LIMIT 0 , 10";
                    break;

                case date("Y-m-d", strtotime("-1 year")) :
                    $GrowthConsumer = "SELECT u.user_name, u.user_type_id, date_format( date( dal.created_date ) , '%Y-%m-%d' ) AS created_date, COUNT( u.id ) AS users_count, st.state_name, st.id FROM users AS u
                                                INNER JOIN deactivated_accounts_list AS dal ON dal.user_id = u.id
						LEFT JOIN user_address AS ua ON u.id = ua.user_id
						LEFT JOIN address AS a ON ua.address_id = a.id
						LEFT JOIN state AS st ON a.state_id = st.id
						WHERE `user_type_id` = 4 AND st.id = " . $practitioner['state_id'] . " AND date_format(dal.created_date,'%Y-%m-%d') BETWEEN '" . date("Y-m-d", strtotime("-2 year")) . "' AND '" . date("Y-m-d", strtotime("-1 year")) . "' AND u.status_id IN ('3', '10') GROUP BY a.state_id ORDER BY users_count DESC LIMIT 0 , 10";

                    $GrowthPractitioner = "SELECT u.user_name, u.user_type_id, date_format( date(dal.created_date ) , '%Y-%m-%d' ) AS created_date, COUNT( u.id ) AS users_count, st.state_name, st.id FROM users AS u
                                                INNER JOIN deactivated_accounts_list AS dal ON dal.user_id = u.id
						LEFT JOIN (SELECT service_provider_address.*, min(service_provider_address.address_id) AS adrs_id FROM service_provider_address GROUP BY user_id) AS ua ON u.id = ua.user_id
						LEFT JOIN address AS a ON ua.adrs_id = a.id
						LEFT JOIN state AS st ON a.state_id = st.id
						WHERE `user_type_id` = 3 AND st.id = " . $practitioner['state_id'] . " AND date_format(dal.created_date,'%Y-%m-%d') BETWEEN '" . date("Y-m-d", strtotime("-2 year")) . "' AND '" . date("Y-m-d", strtotime("-1 year")) . "' AND u.status_id IN ('3', '10') GROUP BY a.state_id ORDER BY users_count DESC LIMIT 0 , 10";
                    break;
            }

            $statement = $adapter->query($GrowthConsumer);
            $GrowthConsumers = $statement->execute();

            $statement = $adapter->query($GrowthPractitioner);
            $GrowthPractitioners = $statement->execute();

            $data[$practitioner['state_name']]['growth'] = (($GrowthConsumers->current()->users_count + $GrowthPractitioners->current()->users_count)>0)?round((($data[$practitioner['state_name']]['practitioner_count'] + $data[$practitioner['state_name']]['consumer_count']) - ($GrowthConsumers->current()->users_count + $GrowthPractitioners->current()->users_count) / ($GrowthConsumers->current()->users_count + $GrowthPractitioners->current()->users_count)) * 100):round((($data[$practitioner['state_name']]['practitioner_count'] + $data[$practitioner['state_name']]['consumer_count']) - ($GrowthConsumers->current()->users_count + $GrowthPractitioners->current()->users_count)) * 100);
        }

        // Calculating total %
        foreach ($data as $key => $value) {
            $data[$key]['total'] = round((($data[$key]['practitioner_count'] + $data[$key]['consumer_count']) / $total) * 100);
        }

        return $data;
    }

    public function getRegistrationStats($date)
    {
        $data = array();
        $adapter = $this->tableGateway->getAdapter();

        $sql1 = "SELECT COUNT(u.id) AS user_count FROM users as u WHERE u.status_id IN ('9','5') AND u.user_type_id = 3 AND date_format( date( u.created_date ) , '%Y-%m-%d' ) >= '" . $date . "' ";
        $sql2 = "SELECT COUNT(u.id) AS user_count FROM users as u WHERE u.status_id IN ('3','10') AND u.user_type_id = 3 AND date_format( date( u.created_date ) , '%Y-%m-%d' ) >= '" . $date . "' ";
        $sql3 = "SELECT COUNT(u.id) AS user_count FROM users as u WHERE u.status_id IN ('9','5') AND u.user_type_id = 4 AND date_format( date( u.created_date ) , '%Y-%m-%d' ) >= '" . $date . "' ";
        $sql4 = "SELECT COUNT(u.id) AS user_count FROM users as u WHERE u.status_id IN ('3','10') AND u.user_type_id = 4 AND date_format( date( u.created_date ) , '%Y-%m-%d' ) >= '" . $date . "' ";

        switch ($date) {
            case date("Y-m-d", strtotime("now")) :
                $Growthsql1 = "SELECT COUNT(u.id) AS user_count FROM users as u WHERE u.status_id IN ('9','5') AND u.user_type_id = 3 AND date_format( date( u.created_date ) , '%Y-%m-%d' ) = '" . date("Y-m-d", strtotime("-1 days")) . "' ";
                $Growthsql2 = "SELECT COUNT(u.id) AS user_count FROM users as u WHERE u.status_id IN ('3','10') AND u.user_type_id = 3 AND date_format( date( u.created_date ) , '%Y-%m-%d' ) = '" . date("Y-m-d", strtotime("-1 days")) . "' ";
                $Growthsql3 = "SELECT COUNT(u.id) AS user_count FROM users as u WHERE u.status_id IN ('9','5') AND u.user_type_id = 4 AND date_format( date( u.created_date ) , '%Y-%m-%d' ) = '" . date("Y-m-d", strtotime("-1 days")) . "' ";
                $Growthsql4 = "SELECT COUNT(u.id) AS user_count FROM users as u WHERE u.status_id IN ('3','10') AND u.user_type_id = 4 AND date_format( date( u.created_date ) , '%Y-%m-%d' ) = '" . date("Y-m-d", strtotime("-1 days")) . "' ";
                break;

            case date("Y-m-d", strtotime("-1 week")) :
                $Growthsql1 = "SELECT COUNT(u.id) AS user_count FROM users as u WHERE u.status_id IN ('9','5') AND u.user_type_id = 3 AND date_format( date( u.created_date ) , '%Y-%m-%d' ) BETWEEN '" . date("Y-m-d", strtotime("-14 days")) . "' AND '" . date("Y-m-d", strtotime("-7 days")) . "' ";
                $Growthsql2 = "SELECT COUNT(u.id) AS user_count FROM users as u WHERE u.status_id IN ('3','10') AND u.user_type_id = 3 AND date_format( date( u.created_date ) , '%Y-%m-%d' ) BETWEEN '" . date("Y-m-d", strtotime("-14 days")) . "' AND '" . date("Y-m-d", strtotime("-7 days")) . "' ";
                $Growthsql3 = "SELECT COUNT(u.id) AS user_count FROM users as u WHERE u.status_id IN ('9','5') AND u.user_type_id = 4 AND date_format( date( u.created_date ) , '%Y-%m-%d' ) BETWEEN '" . date("Y-m-d", strtotime("-14 days")) . "' AND '" . date("Y-m-d", strtotime("-7 days")) . "' ";
                $Growthsql4 = "SELECT COUNT(u.id) AS user_count FROM users as u WHERE u.status_id IN ('3','10') AND u.user_type_id = 4 AND date_format( date( u.created_date ) , '%Y-%m-%d' ) BETWEEN '" . date("Y-m-d", strtotime("-14 days")) . "' AND '" . date("Y-m-d", strtotime("-7 days")) . "' ";
                break;

            case date("Y-m-d", strtotime("-1 month")) :
                $Growthsql1 = "SELECT COUNT(u.id) AS user_count FROM users as u WHERE u.status_id IN ('9','5') AND u.user_type_id = 3 AND date_format( date( u.created_date ) , '%Y-%m-%d' ) BETWEEN '" . date("Y-m-d", strtotime("-2 month -1 days")) . "' AND '" . date("Y-m-d", strtotime("-1 month -1 days")) . "' ";
                $Growthsql2 = "SELECT COUNT(u.id) AS user_count FROM users as u WHERE u.status_id IN ('3','10') AND u.user_type_id = 3 AND date_format( date( u.created_date ) , '%Y-%m-%d' ) BETWEEN '" . date("Y-m-d", strtotime("-2 month -1 days")) . "' AND '" . date("Y-m-d", strtotime("-1 month -1 days")) . "' ";
                $Growthsql3 = "SELECT COUNT(u.id) AS user_count FROM users as u WHERE u.status_id IN ('9','5') AND u.user_type_id = 4 AND date_format( date( u.created_date ) , '%Y-%m-%d' ) BETWEEN '" . date("Y-m-d", strtotime("-2 month -1 days")) . "' AND '" . date("Y-m-d", strtotime("-1 month -1 days")) . "' ";
                $Growthsql4 = "SELECT COUNT(u.id) AS user_count FROM users as u WHERE u.status_id IN ('3','10') AND u.user_type_id = 4 AND date_format( date( u.created_date ) , '%Y-%m-%d' ) BETWEEN '" . date("Y-m-d", strtotime("-2 month -1 days")) . "' AND '" . date("Y-m-d", strtotime("-1 month -1 days")) . "' ";
                break;

            case date("Y-m-d", strtotime("-1 year")) :
                $Growthsql1 = "SELECT COUNT(u.id) AS user_count FROM users as u WHERE u.status_id IN ('9','5') AND u.user_type_id = 3 AND date_format( date( u.created_date ) , '%Y-%m-%d' ) BETWEEN '" . date("Y-m-d", strtotime("-2 year")) . "' AND '" . date("Y-m-d", strtotime("-1 year")) . "' ";
                $Growthsql2 = "SELECT COUNT(u.id) AS user_count FROM users as u WHERE u.status_id IN ('3','10') AND u.user_type_id = 3 AND date_format( date( u.created_date ) , '%Y-%m-%d' ) BETWEEN '" . date("Y-m-d", strtotime("-2 year")) . "' AND '" . date("Y-m-d", strtotime("-1 year")) . "' ";
                $Growthsql3 = "SELECT COUNT(u.id) AS user_count FROM users as u WHERE u.status_id IN ('9','5') AND u.user_type_id = 4 AND date_format( date( u.created_date ) , '%Y-%m-%d' ) BETWEEN '" . date("Y-m-d", strtotime("-2 year")) . "' AND '" . date("Y-m-d", strtotime("-1 year")) . "' ";
                $Growthsql4 = "SELECT COUNT(u.id) AS user_count FROM users as u WHERE u.status_id IN ('3','10') AND u.user_type_id = 4 AND date_format( date( u.created_date ) , '%Y-%m-%d' ) BETWEEN '" . date("Y-m-d", strtotime("-2 year")) . "' AND '" . date("Y-m-d", strtotime("-1 year")) . "' ";
                break;
        }

        $statement1 = $adapter->query($sql1);
        $result1 = $statement1->execute();
        foreach ($result1 as $key => $value) {
            $data['practitioner_registered'] = $value['user_count'];
        }


        $statement2 = $adapter->query($sql2);
        $result2 = $statement2->execute();
        foreach ($result2 as $key => $value) {
            $data['practitioner_cancelled'] = $value['user_count'];
        }

        $statement3 = $adapter->query($sql3);
        $result3 = $statement3->execute();
        foreach ($result3 as $key => $value) {
            $data['consumer_registered'] = $value['user_count'];
        }

        $statement4 = $adapter->query($sql4);
        $result4 = $statement4->execute();
        foreach ($result4 as $key => $value) {
            $data['consumer_cancelled'] = $value['user_count'];
        }

        // Growth calculation
        $growthStatement1 = $adapter->query($Growthsql1);
        $result1 = $growthStatement1->execute();
        foreach ($result1 as $key => $value) {
            $practitioner_registered = $value['user_count'];
        }


        $growthStatement2 = $adapter->query($Growthsql2);
        $result2 = $growthStatement2->execute();
        foreach ($result2 as $key => $value) {
            $practitioner_cancelled = $value['user_count'];
        }

        $growthStatement3 = $adapter->query($Growthsql3);
        $result3 = $growthStatement3->execute();
        foreach ($result3 as $key => $value) {
            $consumer_registered = $value['user_count'];
        }

        $growthStatement4 = $adapter->query($Growthsql4);
        $result4 = $growthStatement4->execute();
        foreach ($result4 as $key => $value) {
            $consumer_cancelled = $value['user_count'];
        }

        $data['practitioner_growth'] = (($practitioner_registered - $practitioner_cancelled) > 0) ? round((($data['practitioner_registered'] - $data['practitioner_cancelled']) - ($practitioner_registered - $practitioner_cancelled)) / ($practitioner_registered - $practitioner_cancelled) * 100) : round((($data['practitioner_registered'] - $data['practitioner_cancelled']) - ($practitioner_registered - $practitioner_cancelled)) * 100);
        $data['consumer_growth'] = (($consumer_registered - $consumer_cancelled) > 0) ? round((($data['consumer_registered'] - $data['consumer_cancelled']) - ($consumer_registered - $consumer_cancelled)) / ($consumer_registered - $consumer_cancelled) * 100) : round((($data['consumer_registered'] - $data['consumer_cancelled']) - ($consumer_registered - $consumer_cancelled)) * 100);

        return $data;
    }

    public function getPendingProfiles()
    {

        $data = array();
        $data['profile_count'] = '0';
        $adapter = $this->tableGateway->getAdapter();

        $sql = "SELECT  u.user_name,HOUR(TIMEDIFF(u.created_date, now() )) as timediff_hour,MINUTE(TIMEDIFF(u.created_date, now() )) as timediff_minute,SECOND(TIMEDIFF(u.created_date, now() )) as timediff_second FROM users AS u WHERE u.status_id = 2";
        $statement = $adapter->query($sql);
        $result = $statement->execute();

        foreach ($result as $key => $value) {
            $data['pending_profiles'][$key] = $value;
            $data['profile_count'] ++;
        }

        return $data;
    }

}
