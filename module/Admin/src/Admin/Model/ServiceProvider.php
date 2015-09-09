<?php

/*
 * For more validation refer to
 * http://framework.zend.com/manual/2.0/en/modules/zend.validator.set.html 
 * */

namespace Admin\Model;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\Db\Adapter\Adapter;

class ServiceProvider implements InputFilterAwareInterface
{

    public $id;
    public $user_id;
    public $first_name;
    public $last_name;
    public $avtar_url;
    public $phone_number;
    public $cellphone;

    /* Dashboard data fields */
    public $month;
    public $total;

    /* service_provider_details table fields */
    public $company_name;
    public $description;
    public $dob;
    public $degrees;
    public $years_of_experience;
    public $specialties;
    public $prof_membership;
    public $professional_license_number;
    public $prac_org;
    public $awards_and_publication;
    public $auth_to_issue_insurence_rem_receipt;
    public $auth_to_bill_insurence_copany;
    public $treatment_for_physically_disabled_person;
    public $detail_id;
    public $designation;

    /* user_address table fields */
    public $street1_address;
    public $street2_address;
    public $city;
    public $zip_code;
    public $state_id;
    public $country_id;

    /* users table fields */
    public $user_name;
    public $email;
    public $pass;
    public $gender;
    public $age;
    public $created_date;

    /* state table field */
    public $state_name;

    /* country table field */
    public $country_name;

    /* service_provider_service table field */
    public $service_id;

    /* site_settings table field */
    public $setting_value;

    /* lookup_status table field */
    public $status_id;
    public $status;
    public $adapter;  // DB adapter
    protected $inputFilter;

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->user_id = (!empty($data['user_id'])) ? $data['user_id'] : null;
        $this->first_name = (!empty($data['first_name'])) ? $data['first_name'] : null;
        $this->last_name = (!empty($data['last_name'])) ? $data['last_name'] : null;
        $this->avtar_url = (!empty($data['avtar_url'])) ? $data['avtar_url'] : null;
        $this->phone_number = (!empty($data['phone_number'])) ? $data['phone_number'] : null;
        $this->cellphone = (!empty($data['cellphone'])) ? $data['cellphone'] : null;

        $this->month = (!empty($data['month'])) ? $data['month'] : null;
        $this->total = (!empty($data['total'])) ? $data['total'] : null;

        $this->company_name = (!empty($data['company_name'])) ? $data['company_name'] : null;
        $this->description = (!empty($data['description'])) ? $data['description'] : null;
        $this->dob = (!empty($data['dob'])) ? $data['dob'] : null;
        $this->degrees = (!empty($data['degrees'])) ? $data['degrees'] : null;
        $this->years_of_experience = (!empty($data['years_of_experience'])) ? $data['years_of_experience'] : null;
        $this->specialties = (!empty($data['specialties'])) ? $data['specialties'] : null;
        $this->prof_membership = (!empty($data['prof_membership'])) ? $data['prof_membership'] : null;
        $this->professional_license_number = (!empty($data['professional_license_number'])) ? $data['professional_license_number'] : null;
        $this->prac_org = (!empty($data['prac_org'])) ? $data['prac_org'] : null;
        $this->awards_and_publication = (!empty($data['awards_and_publication'])) ? $data['awards_and_publication'] : null;
        $this->auth_to_issue_insurence_rem_receipt = (!empty($data['auth_to_issue_insurence_rem_receipt'])) ? $data['auth_to_issue_insurence_rem_receipt'] : 0;
        $this->auth_to_bill_insurence_copany = (!empty($data['auth_to_bill_insurence_copany'])) ? $data['auth_to_bill_insurence_copany'] : 0;
        $this->treatment_for_physically_disabled_person = (!empty($data['treatment_for_physically_disabled_person'])) ? $data['treatment_for_physically_disabled_person'] : 0;
        $this->detail_id = (!empty($data['detail_id'])) ? $data['detail_id'] : null;
        $this->designation = (!empty($data['designation'])) ? $data['designation'] : null;
        $this->offering_at_home = (!empty($data['offering_at_home'])) ? $data['offering_at_home'] : 0;
        $this->offering_at_work_office = (!empty($data['offering_at_work_office'])) ? $data['offering_at_work_office'] : 0;

        $this->street1_address = (!empty($data['street1_address'])) ? $data['street1_address'] : null;
        $this->street2_address = (!empty($data['street2_address'])) ? $data['street2_address'] : null;
        $this->city = (!empty($data['city'])) ? $data['city'] : null;
        $this->zip_code = (!empty($data['zip_code'])) ? $data['zip_code'] : null;
        $this->state_id = (!empty($data['state_id'])) ? $data['state_id'] : null;
        $this->country_id = (!empty($data['country_id'])) ? $data['country_id'] : null;

        $this->user_name = (!empty($data['user_name'])) ? $data['user_name'] : null;
        $this->email = (!empty($data['email'])) ? $data['email'] : null;
        $this->pass = (!empty($data['pass'])) ? $data['pass'] : null;
        $this->gender = (!empty($data['gender'])) ? $data['gender'] : 'M';
        $this->age = (!empty($data['age'])) ? $data['age'] : 0;
        $this->created_date = (!empty($data['created_date'])) ? $data['created_date'] : null;

        $this->state_name = (!empty($data['state_name'])) ? $data['state_name'] : null;

        $this->country_name = (!empty($data['country_name'])) ? $data['country_name'] : null;

        $this->service_id = (!empty($data['service_id'])) ? $data['service_id'] : array();

        $this->setting_value = (!empty($data['setting_value'])) ? $data['setting_value'] : array();

        $this->status_id = (!empty($data['status_id'])) ? $data['status_id'] : null;
        $this->status = (!empty($data['status'])) ? $data['status'] : null;
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }

    public function setAdapter(Adapter $adapter)
    {
        $this->adapter = $adapter;
    }

    // Add content to this method:
    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }

    public function getInputFilter()
    {

        if (!$this->inputFilter) {

            $inputFilter = new InputFilter();

            $inputFilter->add(array(
                'name' => 'first_name',
                'required' => true,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min' => 1,
                            'max' => 50,
                        ),
                    ),
                ),
            ));

            $inputFilter->add(array(
                'name' => 'last_name',
                'required' => true,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min' => 1,
                            'max' => 50,
                        ),
                    ),
                ),
            ));

            $inputFilter->add(array(
                'name' => 'user_name',
                'required' => true,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min' => 1,
                            'max' => 50,
                        ),
                    ),
                ),
            ));

            $inputFilter->add(array(
                'name' => 'email',
                'required' => true,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min' => 1,
                            'max' => 70,
                        ),
                    ),
                    array(
                        'name' => 'EmailAddress'
                    ),
                ),
            ));

            $inputFilter->add(array(
                'name' => 'pass',
                'required' => true,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min' => 1,
                            'max' => 100,
                        ),
                    ),
                    array(
                        'name' => 'Regex',
                        'options' => array(
                            'pattern' => '/^.*(?=.{6,})(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])(?=.*[^a-zA-Z0-9])(?!.*\s).*$/',
                            'messages' => array(
                                \Zend\Validator\Regex::NOT_MATCH => 'Password must be at least 6 characters and must contain at least one lower case letter, one upper case letter, one digit and one special character.',
                            ),
                        ),
                        'break_chain_on_failure' => true
                    ),
                ),
            ));

            $inputFilter->add(array(
                'name' => 'phone_number',
                'required' => true,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min' => 1,
                            'max' => 16,
                        ),
                    ),
                    array(
                        'name' => 'Digits',
                    ),
                ),
            ));

            $inputFilter->add(array(
                'name' => 'age',
                'required' => true,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'Digits',
                    ),
                ),
            ));

            $inputFilter->add(array(
                'name' => 'prac_org',
                'required' => false,
            ));

            $inputFilter->add(array(
                'name' => 'cellphone',
                'required' => true,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min' => 1,
                            'max' => 15,
                        ),
                    ),
                    array(
                        'name' => 'Digits',
                    ),
                ),
            ));
            
            $inputFilter->add(array(
                'name' => 'education_id',
                'required' => false,
                ));

            /*$inputFilter->add(array(
                'name' => 'street1_address',
                'required' => true,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min' => 1,
                            'max' => 100,
                        ),
                    ),
                ),
            ));

            $inputFilter->add(array(
                'name' => 'street2_address',
                'required' => false,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min' => 1,
                            'max' => 100,
                        ),
                    ),
                ),
            ));

            $inputFilter->add(array(
                'name' => 'city',
                'required' => true,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min' => 1,
                            'max' => 100,
                        ),
                    ),
                ),
            ));

            $inputFilter->add(array(
                'name' => 'zip_code',
                'required' => true,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min' => 1,
                            'max' => 10,
                        ),
                    ),
                ),
            ));

            $inputFilter->add(array(
                'name' => 'state_id',
                'required' => true,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'Digits',
                    ),
                ),
            ));

            $inputFilter->add(array(
                'name' => 'country_id',
                'required' => true,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'Digits',
                    ),
                ),
            ));*/

            $inputFilter->add(array(
                'name' => 's_street1_address',
                'required' => false,
            ));

            $inputFilter->add(array(
                'name' => 's_street2_address',
                'required' => false,
            ));

            $inputFilter->add(array(
                'name' => 's_city',
                'required' => false,
            ));

            $inputFilter->add(array(
                'name' => 's_zip_code',
                'required' => false,
            ));

            $inputFilter->add(array(
                'name' => 's_state_id[]',
                'required' => false,
            ));

            $inputFilter->add(array(
                'name' => 's_country_id[]',
                'required' => false,
            ));

            $inputFilter->add(array(
                'name' => 'status_id',
                'required' => false,
            ));


            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }

}
