<?php

namespace Admin\Model;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Users implements InputFilterAwareInterface
{
    /* Users fields */

    public $id;
    public $user_type_id;
    public $register_from;
    public $first_name;
    public $last_name;
    public $avtar_url;
    public $user_name;
    public $email;
    public $pass;
    public $age;
    public $gender;
    public $social_media_id;
    public $created_date;
    public $last_login;
    public $expiration_date;
    public $status_id;

    /* Dashboard data fields */
    public $total;
    public $month;
    public $users_count;

    /* user_address table fields */
    public $street1_address;
    public $street2_address;
    public $city;
    public $zip_code;
    public $state_id;
    public $country_id;

    /* user_feature_setting table fields */
    public $chat;
    public $sms;
    public $email_status;

    /* user_contact table fields */
    public $home_phone;
    public $work_phone;
    public $cell_phone;
    public $fax;

    /* state table field */
    public $state_name;

    /* country table field */
    public $country_name;

    /* Users Type fields */
    public $user_type;

    /* lookup status fields */
    public $status;
    protected $inputFilter;

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->user_type_id = (!empty($data['user_type_id'])) ? $data['user_type_id'] : null;
        //$this->register_from = (!empty($data['register_from'])) ? $data['register_from'] : null;
        $this->register_from = 0;
        $this->first_name = (!empty($data['first_name'])) ? $data['first_name'] : null;
        $this->last_name = (!empty($data['last_name'])) ? $data['last_name'] : null;
        $this->avtar_url = (!empty($data['avtar_url'])) ? $data['avtar_url'] : null;
        $this->user_name = (!empty($data['user_name'])) ? $data['user_name'] : null;
        $this->email = (!empty($data['email'])) ? $data['email'] : null;
        $this->pass = (!empty($data['pass'])) ? $data['pass'] : null;
        $this->age = (!empty($data['age'])) ? $data['age'] : null;
        $this->gender = (!empty($data['gender'])) ? $data['gender'] : null;
        $this->social_media_id = (!empty($data['social_media_id'])) ? $data['social_media_id'] : null;
        $this->created_date = (!empty($data['created_date'])) ? $data['created_date'] : date("Y-m-d H:i:s");
        $this->last_login = (!empty($data['last_login'])) ? $data['last_login'] : null;
        $this->expiration_date = (!empty($data['expiration_date'])) ? $data['expiration_date'] : null;
        $this->status_id = (!empty($data['status_id'])) ? $data['status_id'] : 1;

        $this->total = (!empty($data['total'])) ? $data['total'] : 0;
        $this->month = (!empty($data['month'])) ? $data['month'] : 0;
        $this->users_count = (!empty($data['users_count'])) ? $data['users_count'] : 0;

        $this->user_type = (!empty($data['user_type'])) ? $data['user_type'] : null;

        $this->street1_address = (!empty($data['street1_address'])) ? $data['street1_address'] : null;
        $this->street2_address = (!empty($data['street2_address'])) ? $data['street2_address'] : null;
        $this->city = (!empty($data['city'])) ? $data['city'] : null;
        $this->zip_code = (!empty($data['zip_code'])) ? $data['zip_code'] : null;
        $this->state_id = (!empty($data['state_id'])) ? $data['state_id'] : null;
        $this->country_id = (!empty($data['country_id'])) ? $data['country_id'] : null;

        $this->home_phone = (!empty($data['home_phone'])) ? $data['home_phone'] : null;
        $this->work_phone = (!empty($data['work_phone'])) ? $data['work_phone'] : null;
        $this->cell_phone = (!empty($data['cell_phone'])) ? $data['cell_phone'] : null;
        $this->fax = (!empty($data['fax'])) ? $data['fax'] : null;

        $this->chat = (!empty($data['chat'])) ? $data['chat'] : 0;
        $this->sms = (!empty($data['sms'])) ? $data['sms'] : 0;
        $this->email_status = (!empty($data['email_status'])) ? $data['email_status'] : 0;

        $this->state_name = (!empty($data['state_name'])) ? $data['state_name'] : null;
        $this->country_name = (!empty($data['country_name'])) ? $data['country_name'] : null;

        $this->status = (!empty($data['status'])) ? $data['status'] : null;
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
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
                'validators' => array(
                    array(
                        'name' => 'EmailAddress',
                    ),
                ),
            ));

            $inputFilter->add(array(
                'name' => 'register_from',
                'required' => false,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
            ));

            $inputFilter->add(array(
                'name' => 'user_type_id',
                'required' => true,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
            ));

            $inputFilter->add(array(
                'name' => 'status_id',
                'required' => true,
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
                        'name' => 'NotEmpty',
                        'options' => array(
                            'messages' => array(
                                \Zend\Validator\NotEmpty::IS_EMPTY => 'Password is required',
                            ),
                            'break_chain_on_failure' => true,
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
                'name' => 'gender',
                'required' => 'true',
            ));

            $inputFilter->add(array(
                'name' => 'c_pass',
                'required' => true,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'Identical',
                        'options' => array(
                            'token' => 'pass', // name of first password field
                        ),
                    ),
                ),
            ));

            $inputFilter->add(array(
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
            ));

            $inputFilter->add(array(
                'name' => 'home_phone',
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
                'name' => 'work_phone',
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
                'name' => 'cell_phone',
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
                'name' => 'fax',
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



            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }

}
