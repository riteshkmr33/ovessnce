<?php

namespace Admin\Form;

use Zend\Form\Form;
use Admin\Model\States;
use Admin\Model\StatesTable;
use Admin\Model\Countries;
use Admin\Model\CountriesTable;
use Admin\Model\Status;
use Admin\Model\StatusTable;
use Admin\Model\Services;
use Admin\Model\ServicesTable;
use Admin\Model\Educations;
use Admin\Model\EducationsTable;
use Admin\Model\ServiceLanguages;
use Admin\Model\ServiceLanguagesTable;
use Admin\Model\PractitionerOrganizations;
use Admin\Model\PractitionerOrganizationsTable;

class ServiceProviderForm extends Form
{

    private $country;
    private $state;
    private $status;
    private $service;
    private $services;
    private $education;
    private $organization;
    private $servicelanguage;
    private $pracOrg;

    public function __construct(StatesTable $state, CountriesTable $country, StatusTable $status, ServicesTable $service, EducationsTable $education, ServiceLanguagesTable $servicelanguage, PractitionerOrganizationsTable $PractitionerOrganizationsTable, $languages = array(), $educations = array(), $organization = array())
    {
        $this->state = $state;
        $this->country = $country;
        $this->status = $status;
        $this->service = $service;
        $this->education = $education;
        $this->servicelanguage = $servicelanguage;
        $this->pracOrg = $PractitionerOrganizationsTable;

        $this->educations = $educations;
        $this->languages = $languages;
        $this->organization = $organization;


        parent::__construct('activity');

        $this->add(array(
            'name' => 'id',
            'type' => 'Hidden',
        ));

        $this->add(array(
            'name' => 'detail_id',
            'type' => 'Hidden',
        ));

        /* Personal info */
        $this->add(array(
            'name' => 'first_name',
            'type' => 'Text',
            'options' => array(
                'label' => 'First Name',
            ),
            'attributes' => array(
                'class' => 'form-control'
            )
        ));
        $this->add(array(
            'name' => 'last_name',
            'type' => 'Text',
            'options' => array(
                'label' => 'Last Name',
            ),
            'attributes' => array(
                'class' => 'form-control'
            )
        ));
        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'gender',
            'options' => array(
                'label' => 'Select Gender',
                'value_options' => array('M' => 'Male', 'F' => 'Female'),
            ),
            'attributes' => array(
                'class' => 'form-control select2'
            )
        ));
        $this->add(array(
            'name' => 'age',
            'type' => 'Text',
            'options' => array(
                'label' => 'Age',
            ),
            'attributes' => array(
                'class' => 'form-control',
            )
        ));
        $this->add(array(
            'name' => 'dob',
            'type' => 'Text',
            'options' => array(
                'label' => 'Date of Birth',
            ),
            'attributes' => array(
                'class' => 'form-control',
                'readonly' => 'true',
            )
        ));
        $this->add(array(
            'name' => 'user_name',
            'type' => 'Text',
            'options' => array(
                'label' => 'User Name',
            ),
            'attributes' => array(
                'class' => 'form-control'
            )
        ));
        $this->add(array(
            'name' => 'email',
            'type' => 'Text',
            'options' => array(
                'label' => 'Email',
            ),
            'attributes' => array(
                'class' => 'form-control',
                'autocomplete' => "off",
            )
        ));
        $this->add(array(
            'name' => 'designation',
            'type' => 'Text',
            'options' => array(
                'label' => 'Designation',
            ),
            'attributes' => array(
                'class' => 'form-control',
                'autocomplete' => "off",
            )
        ));
        $this->add(array(
            'name' => 'pass',
            'type' => 'Password',
            'options' => array(
                'label' => 'Password',
            ),
            'attributes' => array(
                'class' => 'form-control',
                'autocomplete' => "off",
            )
        ));
        $this->add(array(
            'name' => 'phone_number',
            'type' => 'Text',
            'options' => array(
                'label' => 'Phone Number',
            ),
            'attributes' => array(
                'class' => 'form-control'
            )
        ));
        $this->add(array(
            'name' => 'cellphone',
            'type' => 'Text',
            'options' => array(
                'label' => 'Cell Phone',
            ),
            'attributes' => array(
                'class' => 'form-control'
            )
        ));
        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'service_language_id',
            'options' => array(
                'label' => 'Language Spoken',
                'value_options' => $this->getLanguages(),
            ),
            'attributes' => array(
                'class' => 'form-control select2',
                'multiple' => 'multiple',
            )
        ));

        /* Professional info */
        $this->add(array(
            'name' => 'company_name',
            'type' => 'Text',
            'options' => array(
                'label' => 'Company Name',
            ),
            'attributes' => array(
                'class' => 'form-control'
            )
        ));
        $this->add(array(
            'name' => 'description',
            'type' => 'Textarea',
            'options' => array(
                'label' => 'Profile Description',
            ),
            'attributes' => array(
                'class' => 'form-control'
            )
        ));
        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'education_id',
            'options' => array(
                'label' => 'School',
                'value_options' => $this->getEducations(),
            ),
            'attributes' => array(
                'class' => 'form-control select2',
                'multiple' => 'multiple',
            )
        ));
        $this->add(array(
            'name' => 'degrees',
            'type' => 'Text',
            'options' => array(
                'label' => 'Degrees',
            ),
            'attributes' => array(
                'class' => 'form-control'
            )
        ));
        $this->add(array(
            'name' => 'professional_license_number',
            'type' => 'Text',
            'options' => array(
                'label' => 'Professional License Number',
            ),
            'attributes' => array(
                'class' => 'form-control'
            )
        ));
        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'prac_org',
            'options' => array(
                'label' => 'Select Organization',
                'value_options' => $this->getOrganizations(),
                'empty_option' => '--- Select Organization ---'
            ),
            'attributes' => array(
                'class' => 'form-control',
            )
        ));
        $this->add(array(
            'name' => 'years_of_experience',
            'type' => 'Text',
            'options' => array(
                'label' => 'Experience (in years)',
            ),
            'attributes' => array(
                'class' => 'form-control'
            )
        ));

        $this->add(array(
            'name' => 'specialties',
            'type' => 'Text',
            'options' => array(
                'label' => 'Specialties',
            ),
            'attributes' => array(
                'class' => 'form-control'
            )
        ));
        $this->add(array(
            'name' => 'prof_membership',
            'type' => 'Text',
            'options' => array(
                'label' => 'Professional Membership',
            ),
            'attributes' => array(
                'class' => 'form-control'
            )
        ));
        $this->add(array(
            'name' => 'awards_and_publication',
            'type' => 'Text',
            'options' => array(
                'label' => 'Awards and Publication',
            ),
            'attributes' => array(
                'class' => 'form-control'
            )
        ));
        $this->add(array(
            'name' => 'auth_to_issue_insurence_rem_receipt',
            'type' => 'Radio',
            'options' => array(
                'label' => 'Authorized to offer insurance receipt for reimbursement',
                'label_attributes' => array(
                    'class' => 'radio-inline',
                    'style' => 'vertical-align: bottom;',
                ),
                'value_options' => array(1 => 'Yes', 0 => 'No'),
            ),
            'attributes' => array(
                'class' => ''
            )
        ));
        $this->add(array(
            'name' => 'auth_to_bill_insurence_copany',
            'type' => 'Radio',
            'options' => array(
                'label' => 'Authorized to bill insurance companies for reimbursement of services performed',
                'label_attributes' => array(
                    'class' => 'radio-inline',
                    'style' => 'vertical-align: bottom;',
                ),
                'value_options' => array(1 => 'Yes', 0 => 'No'),
            ),
            'attributes' => array(
                'class' => ''
            )
        ));
        $this->add(array(
            'name' => 'treatment_for_physically_disabled_person',
            'type' => 'Radio',
            'options' => array(
                'label' => 'Treatment For Physically Disabled Person',
                'label_attributes' => array(
                    'class' => 'radio-inline',
                    'style' => 'vertical-align: bottom;',
                ),
                'value_options' => array(1 => 'Yes', 0 => 'No'),
            ),
            'attributes' => array(
                'class' => ''
            )
        ));
        $this->add(array(
            'name' => 'offering_at_home',
            'type' => 'Radio',
            'options' => array(
                'label' => 'Offering at Home',
                'label_attributes' => array(
                    'class' => 'radio-inline',
                    'style' => 'vertical-align: bottom;',
                ),
                'value_options' => array(1 => 'Yes', 0 => 'No'),
            ),
            'attributes' => array(
                'class' => ''
            )
        ));
        $this->add(array(
            'name' => 'offering_at_work_office',
            'type' => 'Radio',
            'options' => array(
                'label' => 'Offering at Work Office',
                'label_attributes' => array(
                    'class' => 'radio-inline',
                    'style' => 'vertical-align: bottom;',
                ),
                'value_options' => array(1 => 'Yes', 0 => 'No'),
            ),
            'attributes' => array(
                'class' => ''
            )
        ));

        /* Billing address */
        /*$this->add(array(
            'name' => 'street1_address',
            'type' => 'Text',
            'options' => array(
                'label' => 'Street Address',
            ),
            'attributes' => array(
                'class' => 'form-control'
            )
        ));
        $this->add(array(
            'name' => 'street2_address',
            'type' => 'Text',
            'options' => array(
                'label' => 'Street Address 2',
            ),
            'attributes' => array(
                'class' => 'form-control'
            )
        ));
        $this->add(array(
            'name' => 'city',
            'type' => 'Text',
            'options' => array(
                'label' => 'City',
            ),
            'attributes' => array(
                'class' => 'form-control'
            )
        ));
        $this->add(array(
            'name' => 'zip_code',
            'type' => 'Text',
            'options' => array(
                'label' => 'Zip Code',
            ),
            'attributes' => array(
                'class' => 'form-control'
            )
        ));
        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'state_id',
            'options' => array(
                'label' => 'Select State',
                'value_options' => $this->getStates(),
                'empty_option' => '--- Choose State ---'
            ),
            'attributes' => array(
                'class' => 'form-control select2',
                'id' => 'states'
            )
        ));
        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'country_id',
            'options' => array(
                'label' => 'Select Country',
                'value_options' => $this->getCountries(),
                'empty_option' => '--- Choose Country ---'
            ),
            'attributes' => array(
                'class' => 'form-control select2 getStates',
                'data-id' => 'states'
            )
        ));*/

        /* Address where the services are rendered (4 Maximum) */
        $this->add(array(
            'name' => 's_id[]',
            'type' => 'Hidden',
        ));
        $this->add(array(
            'name' => 's_street1_address[]',
            'type' => 'Text',
            'options' => array(
                'label' => 'Street Address',
            ),
            'attributes' => array(
                'class' => 'form-control'
            )
        ));
        $this->add(array(
            'name' => 's_street2_address[]',
            'type' => 'Text',
            'options' => array(
                'label' => 'Street Address 2',
            ),
            'attributes' => array(
                'class' => 'form-control'
            )
        ));
        $this->add(array(
            'name' => 's_city[]',
            'type' => 'Text',
            'options' => array(
                'label' => 'City',
            ),
            'attributes' => array(
                'class' => 'form-control'
            )
        ));
        $this->add(array(
            'name' => 's_zip_code[]',
            'type' => 'Text',
            'options' => array(
                'label' => 'Zip Code',
            ),
            'attributes' => array(
                'class' => 'form-control'
            )
        ));
        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 's_state_id[]',
            'options' => array(
                'label' => 'Select State',
                'value_options' => $this->getStates(),
                'empty_option' => '--- Choose State ---'
            ),
            'attributes' => array(
                'class' => 'form-control',
                'id' => 's_state',
            )
        ));
        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 's_country_id[]',
            'options' => array(
                'label' => 'Select Country',
                'value_options' => $this->getCountries(),
                'empty_option' => '--- Choose Country ---'
            ),
            'attributes' => array(
                'class' => 'form-control getStates',
                'data-id' => 's_state'
            )
        ));

        /* Misc fields */
        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'status_id',
            'options' => array(
                'label' => 'Select Status',
                'value_options' => $this->getStatus(),
                'empty_option' => '--- Choose Status ---'
            ),
            'attributes' => array(
                'class' => 'form-control',
            )
        ));

        $this->add(array(
            'name' => 'submit',
            'type' => 'Submit',
            'attributes' => array(
                'value' => 'Go',
                'id' => 'submitbutton',
            ),
        ));
    }

    public function getCountries()
    {
        $data = $this->country->fetchAll(false);

        $selectData = array();

        foreach ($data as $selectOption) {
            $selectData[$selectOption->id] = ucwords($selectOption->country_name);
        }

        return $selectData;
    }

    public function getStatus()
    {
        $data = $this->status->fetchAll(false, array(9, 5, 10, 3));

        $selectData = array();

        foreach ($data as $selectOption) {
            $selectData[$selectOption->status_id] = ucwords($selectOption->status);
        }

        return $selectData;
    }

    public function getStates()
    {
        $data = $this->state->fetchAll(false);

        $selectData = array();

        foreach ($data as $selectOption) {
            $selectData[$selectOption->id] = ucwords($selectOption->state_name);
        }

        return $selectData;
    }

    public function getOrganizations()
    {
        $data = $this->pracOrg->fetchAll(false);
        $selectData = array();

        foreach ($data as $selectOption) {
            //$selectData[$selectOption->organization_id] = ucwords($selectOption->organization_name);
            //echo "<pre>";print_r($this->organization);die;
            $selectData[] = array_key_exists($selectOption->organization_id, $this->organization) ? array('value' => $selectOption->organization_id, 'label' => ucwords($selectOption->organization_name), 'selected' => 'selected') : array('value' => $selectOption->organization_id, 'label' => ucwords($selectOption->organization_name));
        }

        return $selectData;
    }

    public function getLanguages()
    {
        $data = $this->servicelanguage->fetchAll(false);
        $selectData = array();

        foreach ($data as $selectOption) {
            $selectData[] = array_key_exists($selectOption->id, $this->languages) ? array('value' => $selectOption->id, 'label' => ucwords($selectOption->language_name), 'selected' => 'selected') : array('value' => $selectOption->id, 'label' => ucwords($selectOption->language_name));
        }

        return $selectData;
    }

    public function getEducations()
    {
        $data = $this->education->fetchAll(false);
        $selectData = array();

        foreach ($data as $selectOption) {
            $selectData[] = array_key_exists($selectOption->id, $this->educations) ? array('value' => $selectOption->id, 'label' => ucwords($selectOption->education_label), 'selected' => 'selected') : array('value' => $selectOption->id, 'label' => ucwords($selectOption->education_label));
        }

        return $selectData;
    }

}
