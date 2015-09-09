<?php

namespace Admin\Model;

use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\DataCache;
use Zend\Db\Sql\Select;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;
use Zend\Db\Sql\Expression;

class ServiceProviderTable
{

    protected $tableGateway;
    private $CacheKey = 'serviceprovider';
    private $user;
    private $media;
    private $address;
    private $user_address;
    private $site_settings;
    private $service_provider_contact;
    private $practitioner_organization;
    private $service_provider_address;
    private $service_provider_details;
    private $service_provider_educations;
    private $service_provider_site_commision;
    private $service_provider_service_language;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
        $adapter = $this->tableGateway->getAdapter();
        $this->user = new TableGateway('users', $adapter);
        $this->media = new TableGateway('media', $adapter);
        $this->address = new TableGateway('address', $adapter);
        $this->user_address = new TableGateway('user_address', $adapter);
        $this->site_settings = new TableGateway('site_settings', $adapter);
        $this->services = new TableGateway('service_provider_service', $adapter);
        $this->service_provider_contact = new TableGateway('service_provider_contact', $adapter);
        $this->practitioner_organization = new TableGateway('practitioner_organization', $adapter);
        $this->service_provider_address = new TableGateway('service_provider_address', $adapter);
        $this->service_provider_details = new TableGateway('service_provider_details', $adapter);
        $this->service_provider_educations = new TableGateway('service_provider_educations', $adapter);
        $this->service_provider_site_commision = new TableGateway('service_provider_site_commision', $adapter);
        $this->service_provider_service_language = new TableGateway('service_provider_service_language', $adapter);
    }

    public function fetchAll($paginate = true, $filter = array(), $orderBy = array())
    {
        if ($paginate) {
            
            $subquery = new Select('service_provider_address');
            $subquery->columns(array('*', new Expression('min(service_provider_address.address_id) AS adrs_id')));
            $subquery->group('user_id');
            
            $select = new Select('users');
            $select->join(array('spa' => $subquery), 'spa.user_id = users.id', array(), 'left');
            $select->join('address', 'address.id = spa.adrs_id', array('city', 'state_id', 'country_id'), 'left');
            $select->join('state', 'address.state_id = state.id', array('state_name'), 'left');
            $select->join('country', 'address.country_id = country.id', array('country_name'), 'left');
            $select->join('lookup_status', 'lookup_status.status_id = users.status_id', array('status'), 'left');
            $select->where("users.user_type_id = 3");

            /* Data filter code start here */
            if (count($filter) > 0) {

                (isset($filter['name']) && $filter['name'] != "") ? $select->where("CONCAT(users.first_name,' ',users.last_name) LIKE '%" . $filter['name'] . "%'") : "";
                if (isset($filter['from_date']) && $filter['from_date'] != "" && isset($filter['to_date']) && $filter['to_date'] != "") {
                    $select->where("DATE_FORMAT(users.created_date , '%Y-%m-%d') BETWEEN '" . $filter['from_date'] . "' AND '" . $filter['to_date'] . "'");
                } else if (isset($filter['from_date']) && !isset($filter['to_date']) && $filter['from_date'] != "") {
                    $select->where("DATE_FORMAT(users.created_date , '%Y-%m-%d') = '" . $filter['from_date'] . "'");
                } else if (!isset($filter['from_date']) && isset($filter['to_date']) && $filter['to_date'] != "") {
                    $select->where("DATE_FORMAT(users.created_date , '%Y-%m-%d') = '" . $filter['to_date'] . "'");
                }
                if (isset($filter['service_id']) && $filter['service_id'] != "") {
                    $select->join('service_provider_service', 'users.id = service_provider_service.user_id', array(), 'left');
                    $select->where("service_provider_service.id = " . $filter['service_id']);
                }
                (isset($filter['country_id']) && $filter['country_id'] != "") ? $select->where("address.country_id = " . $filter['country_id']) : "";
                (isset($filter['state_id']) && $filter['state_id'] != "") ? $select->where("address.state_id = " . $filter['state_id']) : "";
                (isset($filter['city']) && $filter['city'] != "") ? $select->where("address.city LIKE '%" . $filter['city'] . "%'") : "";
                (isset($filter['status_id']) && $filter['status_id'] != "") ? $select->where("users.status_id = " . $filter['status_id']) : "";
            }
            /* Data filter code end here */

            /* Data sorting code starts here */
            if (count($orderBy) > 0 && $orderBy['sort_field'] != '' && $orderBy['sort_order'] != '') {
                switch ($orderBy['sort_field']) {
                    case 'name' :
                        $select->order('users.first_name ' . $orderBy['sort_order']);
                        break;

                    case 'date' :
                        $select->order('users.created_date ' . $orderBy['sort_order']);
                        break;

                    case 'country' :
                        $select->order('country.country_name ' . $orderBy['sort_order']);
                        break;

                    case 'state' :
                        $select->order('state.state_name ' . $orderBy['sort_order']);
                        break;

                    case 'city' :
                        $select->order('address.city ' . $orderBy['sort_order']);
                        break;

                    case 'status' :
                        $select->order('lookup_status.status ' . $orderBy['sort_order']);
                        break;
                }
            }
            /* Data sorting code ends here */

            //echo str_replace('"','',$select->getSqlString()); exit;

            $resultSetPrototype = new ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new ServiceProvider());

            $paginatorAdapter = new DbSelect(
                    $select, $this->tableGateway->getAdapter(), $resultSetPrototype
            );
            $paginator = new Paginator($paginatorAdapter);

            return $paginator;
        } else {
            $select = $this->tableGateway->getSql()->select();
            $select->where("users.user_type_id = 3");
            //echo str_replace('"','',$select->getSqlString()); exit;
            return $this->tableGateway->selectwith($select);
        }
    }

    public function ExportAll($filter = array(), $orderBy = array())
    {
        $select = $this->tableGateway->getSql()->select();
        $select->join('user_address', 'user_address.user_id = users.id', array('address_id'), 'left');
        $select->join('address', 'address.id = user_address.address_id', array('city', 'state_id', 'country_id'), 'left');
        $select->join('state', 'address.state_id = state.id', array('state_name'), 'left');
        $select->join('country', 'address.country_id = country.id', array('country_name'), 'left');
        $select->join('lookup_status', 'lookup_status.status_id = users.status_id', array('status'), 'left');
        $select->where("users.user_type_id = 3");
        /* Data filter code start here */
        if (count($filter) > 0) {

            (isset($filter['name']) && $filter['name'] != "") ? $select->where("CONCAT(users.first_name,' ',users.last_name) LIKE '%" . $filter['name'] . "%'") : "";
            if (isset($filter['from_date']) && $filter['from_date'] != "" && isset($filter['to_date']) && $filter['to_date'] != "") {
                $select->where("DATE_FORMAT(users.created_date , '%Y-%m-%d') BETWEEN '" . $filter['from_date'] . "' AND '" . $filter['to_date'] . "'");
            } else if (isset($filter['from_date']) && !isset($filter['to_date']) && $filter['from_date'] != "") {
                $select->where("DATE_FORMAT(users.created_date , '%Y-%m-%d') = '" . $filter['from_date'] . "'");
            } else if (!isset($filter['from_date']) && isset($filter['to_date']) && $filter['to_date'] != "") {
                $select->where("DATE_FORMAT(users.created_date , '%Y-%m-%d') = '" . $filter['to_date'] . "'");
            }
            if (isset($filter['service_id']) && $filter['service_id'] != "") {
                $select->join('service_provider_service', 'users.id = service_provider_service.user_id', array(), 'left');
                $select->where("service_provider_service.id = " . $filter['service_id']);
            }
            (isset($filter['country_id']) && $filter['country_id'] != "") ? $select->where("address.country_id = " . $filter['country_id']) : "";
            (isset($filter['state_id']) && $filter['state_id'] != "") ? $select->where("address.state_id = " . $filter['state_id']) : "";
            (isset($filter['city']) && $filter['city'] != "") ? $select->where("address.city LIKE '%" . $filter['city'] . "%'") : "";
            (isset($filter['status_id']) && $filter['status_id'] != "") ? $select->where("users.status_id = " . $filter['status_id']) : "";
        }
        /* Data filter code end here */

        /* Data sorting code starts here */
        if (count($orderBy) > 0 && $orderBy['sort_field'] != '' && $orderBy['sort_order'] != '') {
            switch ($orderBy['sort_field']) {
                case 'name' :
                    $select->order('users.first_name ' . $orderBy['sort_order']);
                    break;

                case 'date' :
                    $select->order('users.created_date ' . $orderBy['sort_order']);
                    break;

                case 'country' :
                    $select->order('country.country_name ' . $orderBy['sort_order']);
                    break;

                case 'state' :
                    $select->order('state.state_name ' . $orderBy['sort_order']);
                    break;

                case 'city' :
                    $select->order('address.city ' . $orderBy['sort_order']);
                    break;

                case 'status' :
                    $select->order('lookup_status.status ' . $orderBy['sort_order']);
                    break;
            }
        }
        /* Data sorting code ends here */

        return $this->tableGateway->selectwith($select);
    }

    /* Function to fetch service provider with specific id */

    public function getServiceProvider($id, $checkon = 'id')
    {
        $id = (int) $id;
        $select = $this->tableGateway->getSql()->select();
        $select->columns(array('*', new Expression("service_provider_details.id as detail_id")));
        $select->join('service_provider_contact', 'service_provider_contact.user_id = users.id', array('cellphone', 'phone_number'), 'left');
        $select->join('user_address', 'user_address.user_id = users.id', array('address_id'), 'left');
        $select->join('service_provider_details', 'service_provider_details.user_id = users.id', array('company_name', 'description', 'dob', 'degrees', 'years_of_experience', 'prof_membership', 'professional_license_number', 'awards_and_publication', 'auth_to_issue_insurence_rem_receipt', 'auth_to_bill_insurence_copany', 'treatment_for_physically_disabled_person', 'specialties', 'designation', 'offering_at_home', 'offering_at_work_office'), 'left');
        $select->join('address', 'address.id = user_address.address_id', array('street1_address', 'street2_address', 'city', 'zip_code', 'state_id', 'country_id'), 'left');
        $select->where("users.$checkon = " . $id);
        $select->where("users.user_type_id = 3");
        $rowset = $this->tableGateway->selectwith($select);
        $row = $rowset->current();
        if (!$row) {
            return false;
            //throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    /* Function to get users's basic details */

    public function getUserDetails($user_id)
    {
        $result = $this->tableGateway->select(array('id' => $user_id));
        return $result->current();
    }

    /* Function to get current month data */

    public function getDataByMonth($start, $end)
    {
        $select = $this->user->getSql()->select();
        $select->columns(array(new Expression("count(*) as total, DATE_FORMAT(created_date, '%d-%m-%Y') as month")));
        $select->where("user_type_id = 3 AND DATE_FORMAT(created_date, '%Y-%m-%d') BETWEEN '" . date('Y-m-d', strtotime($start)) . "' AND '" . date('Y-m-d', strtotime($end)) . "'");
        $select->group("month");
        //echo str_replace('"','',$select->getSqlString()); exit;

        $results = $this->user->selectwith($select);
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

        krsort($data);

        return $data;
    }

    /* Function to get service providers contact */

    public function getServiceProviderContact($id)
    {
        $select = $this->service_provider_contact->getSql()->select();
        $select->where(array('user_id' => $id));
        //echo str_replace('"','',$select->getSqlString()); exit;
        $results = $this->service_provider_contact->selectwith($select);
        return $results;
    }

    /* Function to get service provider organization */

    public function getServiceProviderOrganization($id, $inarray = false)
    {
        $select = $this->practitioner_organization->getSql()->select();
        $select->join('practitioner_organization_list', 'practitioner_organization.organization_id = practitioner_organization_list.organization_id');
        $select->where(array('practitioner_id' => $id));
        $results = $this->practitioner_organization->selectwith($select);

        if ($inarray == true) {
            $data = array();
            foreach ($results as $result) {
                $data[$result->organization_id] = $result->organization_name;
            }
            return $data;
        } else {
            return $results;
        }
    }

    /* Function to get service providers service rendering addresses */

    public function getServiceProviderServiceAddress($id = '')
    {
        $select = $this->service_provider_address->getSql()->select();
        $select->join('address', 'address.id = service_provider_address.address_id', array('id', 'street1_address', 'city', 'zip_code', 'state_id', 'country_id'), 'inner');
        $select->join('country', 'country.id = address.country_id', array('country_name'), 'left');
        $select->join('state', 'state.id = address.state_id', array('state_name'), 'left');
        ($id != '') ? $select->where(array('user_id' => $id)) : '';
        //echo str_replace('"','',$select->getSqlString()); exit;
        $results = $this->service_provider_address->selectwith($select);
        return $results;
    }

    /* Function to fetch service providers languages */

    public function getServiceProviderServiceLanguage($id, $inarray = false)
    {
        $select = $this->service_provider_service_language->getSql()->select();
        $select->join('service_language', 'service_language.id = service_provider_service_language.service_language_id');
        $select->where(array('user_id' => $id));
        //echo str_replace('"','',$select->getSqlString()); exit;
        $results = $this->service_provider_service_language->selectwith($select);

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

    /* Function to fetch service providers educations */

    public function getServiceProviderServiceEducation($id, $inarray = false)
    {
        $select = $this->service_provider_educations->getSql()->select();
        $select->join('educations', 'educations.id = service_provider_educations.education_id');
        $select->where(array('user_id' => $id));
        //echo str_replace('"','',$select->getSqlString()); exit;
        $results = $this->service_provider_educations->selectwith($select);

        if ($inarray == true) {
            $data = array();
            foreach ($results as $result) {
                $data[$result->id] = $result->education_label;
            }
            return $data;
        } else {
            return $results;
        }
    }

    /* Function to fetch service provider main address */

    public function getServiceProviderAddress($id)
    {
        $id = (int) $id;
        $select = $this->user_address->getSql()->select();
        $select->join('address', 'address.id = user_address.address_id', array('id', 'street1_address', 'city', 'zip_code', 'state_id', 'country_id'), 'inner');
        $select->join('country', 'country.id = address.country_id', array('country_name'), 'left');
        $select->join('state', 'state.id = address.state_id', array('state_name'), 'left');
        $select->where(array('user_id' => $id));
        $rowset = $this->user_address->selectwith($select);
        $row = $rowset->current();
        if (!$row) {
            return false;
            //throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    /* Function to fetch service provider details */

    public function getServiceProviderDetails($id)
    {
        $id = (int) $id;
        $rowset = $this->service_provider_details->select(array("id" => $id));
        $row = $rowset->current();
        if (!$row) {
            return false;
            //throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    /* Function to fetch address details */

    public function getAddress($id)
    {
        $id = (int) $id;
        $rowset = $this->address->select(array("id" => $id));
        $row = $rowset->current();
        if (!$row) {
            return false;
            //throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    /* Function to fetch user details */

    public function getUser($id)
    {
        $id = (int) $id;
        $rowset = $this->user->select(array("id" => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    /* Function to get default commision value */

    public function getDefaultCommission()
    {
        $result = $this->site_settings->select(array('id' => 1));
        return $result->current()->setting_value;
    }

    /* Function to fetch service by name */

    public function getServicesByName($id, $string = false, $service_provider_service_id = "")
    {
        $select = $this->services->getSql()->select();
        $select->join('service_category', 'service_category.id = service_provider_service.service_id', array('category_name'), 'inner');
        $select->where(array('user_id' => $id));
        ($service_provider_service_id != "") ? $select->where(array('service_provider_service.id' => $service_provider_service_id)) : "";
        //echo str_replace('"','',$select->getSqlString()); exit;
        $results = $this->services->selectwith($select);
        if ($string == true) {
            $data = "";
            foreach ($results as $result) {
                $data .= $result->category_name . " " . $result->duration . " mins, ";
            }
            return rtrim($data, ", ");
        } else {
            $data = array();
            foreach ($results as $result) {
                $data[] = $result->category_name . " " . $result->duration . " mins";
            }
            return $data;
        }
    }

    public function getServiceProviders()
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

    public function saveServiceProvider(ServiceProvider $sp, $serviceRenderAddresses = array(), $languages = array(), $educations = array())
    {
        $data = array(
            'first_name' => $sp->first_name,
            'last_name' => $sp->last_name,
            'phone_number' => $sp->phone_number,
            'cellphone' => $sp->cellphone,
        );

        $user_data = array(
            'first_name' => $sp->first_name,
            'last_name' => $sp->last_name,
            'user_type_id' => 3,
            'user_name' => $sp->user_name,
            'email' => $sp->email,
            'age' => $sp->age,
            'gender' => $sp->gender,
        );

        if ($sp->pass != null && $sp->pass != "") {
            $user_data['pass'] = MD5($sp->pass);
        }


        $sp_details = array(
            'company_name' => $sp->company_name,
            'description' => $sp->description,
            'dob' => date("Y-m-d", strtotime($sp->dob)),
            'degrees' => $sp->degrees,
            'years_of_experience' => $sp->years_of_experience,
            'specialties' => $sp->specialties,
            'prof_membership' => $sp->prof_membership,
            'professional_license_number' => $sp->professional_license_number,
            'awards_and_publication' => $sp->awards_and_publication,
            'auth_to_issue_insurence_rem_receipt' => $sp->auth_to_issue_insurence_rem_receipt,
            'auth_to_bill_insurence_copany' => $sp->auth_to_bill_insurence_copany,
            'treatment_for_physically_disabled_person' => $sp->treatment_for_physically_disabled_person,
            'designation' => $sp->designation,
            'offering_at_work_office' => $sp->offering_at_work_office,
            'offering_at_home' => $sp->offering_at_home,
        );

        $sp_pracorg = array(
            'organization_id' => $sp->prac_org,
        );

        /* Adding user code starts here */
        $user = (int) $sp->id;
        if ($user == 0) {

            $user_data['status_id'] = 9;
            $user_data['created_date'] = date('Y-m-d h:i:s');
            $this->tableGateway->insert($user_data);
            $sp_pracorg['practitioner_id'] = $data['user_id'] = $user_id = $this->tableGateway->lastInsertValue;

            $this->service_provider_site_commision->insert(array('user_id' => $user_id, 'commision' => $this->getDefaultCommission(), 'status_id' => 1, 'created_date' => date('Y-m-d h:i:s')));
        } else {
            if ($this->getUser($user)) {

                $data['user_id'] = $user_id = $user;
                $this->tableGateway->update($user_data, array('id' => $user));
            } else {

                throw new \Exception('User id does not exist');
            }
        }
        /* Adding user code ends here */

        /* Adding service provider code starts here */
        $id = (int) $sp->id;
        if ($id == 0) {
            if ($sp_pracorg != '') {
                $this->service_provider_contact->insert($data);
            }
            $id = $this->tableGateway->lastInsertValue;
        } else {
            if ($this->getServiceProviderContact($id)) {
                $this->service_provider_contact->update($data, array('user_id' => $id));
            } else {
                $data['user_id'] = $id;
                $this->service_provider_contact->insert($data);
            }
        }
        /* Adding service provider code ends here */

        /* Adding service provider organization starts here */
        $id = (int) $sp->id;
        if ($id == 0) {
            if ($sp_pracorg['practitioner_id'] != '' && $sp_pracorg['organization_id'] != '') {
                $this->practitioner_organization->insert($sp_pracorg);
            }
        } else {
            if ($this->getServiceProviderOrganization($id)) {
                if ($sp_pracorg['organization_id'] != '') {
                    $this->practitioner_organization->update($sp_pracorg, array('practitioner_id' => $id));
                }
            } else {
                if ($sp_pracorg['organization_id'] != '') {
                    $this->practitioner_organization->insert($sp_pracorg);
                }
                //throw new \Exception('Service Provider id does not exist ');
            }
        }
        /* Adding service provider oraganization ends here */

        /* Adding service provider details code starts here */
        $detail_id = (int) $sp->detail_id;
        if ($detail_id == 0) {
            $sp_details['user_id'] = $user_id;
            $this->service_provider_details->insert($sp_details);
        } else {
            if ($this->getServiceProviderDetails($detail_id)) {
                $this->service_provider_details->update($sp_details, array('id' => $detail_id));
            } else {
                throw new \Exception('Service Provider details id does not exist');
            }
        }

        // deleting old entries
        $this->service_provider_service_language->delete(array('user_id' => $user_id));
        $this->service_provider_educations->delete(array('user_id' => $user_id));

        // adding new entries
        if (isset($languages) && is_array($languages) && count($languages) > 0) {
            foreach ($languages as $language) {
                $this->service_provider_service_language->insert(array('user_id' => $user_id, 'service_language_id' => $language));
            }
        }

        if (isset($educations) && is_array($educations) && count($educations) > 0) {
            foreach ($educations as $education) {
                $this->service_provider_educations->insert(array('user_id' => $user_id, 'education_id' => $education));
            }
        }

        /* Adding service provider details code ends here */

        /* Adding service provider's service rendering addresses code starts here */
        /* echo '<pre>';
          print_r($serviceRenderAddresses);
          exit; */
        if (count($serviceRenderAddresses['street1_address']) > 0) {
            $tempIdArray = array();
            foreach ($serviceRenderAddresses['street1_address'] as $key => $value) {
                $service_render_data_id = (int) $serviceRenderAddresses['id'][$key];
                $service_render_data['street1_address'] = $serviceRenderAddresses['street1_address'][$key];
                $service_render_data['street2_address'] = $serviceRenderAddresses['street2_address'][$key];
                $service_render_data['city'] = $serviceRenderAddresses['city'][$key];
                $service_render_data['zip_code'] = $serviceRenderAddresses['zip_code'][$key];
                $service_render_data['state_id'] = $serviceRenderAddresses['state_id'][$key];
                $service_render_data['country_id'] = $serviceRenderAddresses['country_id'][$key];

                if ($service_render_data['street1_address'] != '' || $service_render_data['city'] != '' || $service_render_data['state_id'] != '' || $service_render_data['country_id'] != '' || $service_render_data['zip_code'] != '') {
                    $tempIdArray[] = $service_render_data_id;
                    if ($service_render_data_id == 0) {
                        $this->address->insert($service_render_data);
                        $address_insert_id = $this->address->lastInsertValue; // get last insert id for address
                        $this->service_provider_address->insert(array('user_id' => $user_id, 'address_id' => $address_insert_id));
                    } else {
                        if ($this->getAddress($service_render_data_id)) {
                            $this->address->update($service_render_data, array('id' => $service_render_data_id));
                        } else {
                            $this->address->insert($service_render_data);
                            $address_insert_id = $this->address->lastInsertValue; // get last insert id for address
                            $this->service_provider_address->insert(array('user_id' => $user_id, 'address_id' => $address_insert_id));
                        }
                    }
                }

                if (isset($serviceRenderAddresses['id']) && is_array($serviceRenderAddresses['id']) && is_array($tempIdArray)) {
                    $delIds = array_diff($serviceRenderAddresses['id'], $tempIdArray);

                    if (is_array($delIds) && count($delIds) > 0) {
                        foreach ($delIds as $Id) {
                            $this->service_provider_address->delete(array('address_id' => $Id));
                            $this->address->delete(array('id' => $Id));
                        }
                    }
                }
            }
        }
        /* Adding service provider's service rendering addresses code ends here */

        // Update cache records 
        //DataCache::updateData($this->CacheKey, $this->fetchAll(false));
    }

    public function changeStatus($id, $status)
    {
        $this->user->update(array('status_id' => $status), array('id' => $id));
    }

    public function deleteServiceProvider($id)
    {
        $sp = $this->getServiceProvider($id);

        //$this->service_provider_details->delete(array('user_id' => (int) $sp->user_id)); // delete service providers details
        $this->service_provider_details->delete(array('user_id' => (int) $id)); // delete service providers details
        $this->service_provider_contact->delete(array('user_id' => (int) $id)); // delete service providers contact
        $this->practitioner_organization->delete(array('practitioner_id' => (int) $id)); // delete service providers details

        /* Deleting service rendering addresses */
        //$addresses = $this->getServiceProviderServiceAddress($sp->user_id);
        $addresses = $this->getServiceProviderServiceAddress($id);
        foreach ($addresses as $address) {
            $this->address->delete(array('id' => (int) $address->id));
        }

        // Deleting links
        //$this->service_provider_address->delete(array('user_id'=>$sp->user_id));
        $this->service_provider_address->delete(array('user_id' => $id));
        //$this->service_provider_service_language->delete(array('user_id'=>$sp->user_id));
        $this->service_provider_service_language->delete(array('user_id' => $id));
        $this->service_provider_educations->delete(array('user_id' => $id));
        $this->service_provider_site_commision->delete(array('user_id' => $id));
        $this->media->delete(array('user_id' => $id));

        //if($spAdd = $this->getServiceProviderAddress($sp->user_id)){
        if ($spAdd = $this->getServiceProviderAddress($id)) {
            $this->address->delete(array('id' => (int) $spAdd->address_id)); // delete organization address 
            //$this->user_address->delete(array('user_id' => $sp->user_id, 'address_id' => $spAdd->address_id));
            $this->user_address->delete(array('user_id' => $id, 'address_id' => $spAdd->address_id));
        }

        //$this->user->delete(array('id' => (int) $sp->user_id));
        $this->user->delete(array('id' => (int) $id));
        $this->tableGateway->delete(array('id' => (int) $id));

        // Update cache records
        //DataCache::updateData($this->CacheKey, $this->fetchAll(false));
    }

}
