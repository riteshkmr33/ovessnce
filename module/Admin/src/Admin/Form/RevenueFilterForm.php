<?php
namespace Admin\Form;

use Zend\Form\Form;
use Admin\Model\States;
use Admin\Model\StatesTable;
use Admin\Model\Countries;
use Admin\Model\CountriesTable;
use Admin\Model\ServiceCategory;
use Admin\Model\ServiceCategoryTable;
use Admin\Model\SubscriptionPlans;
use Admin\Model\SubscriptionPlansTable;
 
class RevenueFilterForm extends Form
{
	private $state;
	private $country;
	private $category;
	private $subscriptionplans;
	
	public function __construct(StatesTable $state, CountriesTable $country, SubscriptionPlansTable $subscriptionplans, ServiceCategoryTable $category)
    {
		$this->state = $state;
		$this->country = $country;
		$this->category = $category;
		$this->subscriptionplans = $subscriptionplans;
		
		parent::__construct('serviceproviderfilter');

		$this->add(array(
			'name' => 'user_name',
			'type' => 'Text',
			'options' => array(
				'label' => 'User Name',
			),
			'attributes' => array(
				'class' => 'form-control form-filter input-sm',
			),
		));
		
		$this->add(array(
			'name' => 'product',
			'type' => 'Text',
			'options' => array(
				'label' => 'Product',
			),
			'attributes' => array(
				'class' => 'form-control form-filter input-sm',
			),
		));
		
		$this->add(array(
			'name' => 'from',
			'type' => 'Text',
			'options' => array(
				'label' => 'From',
			),
			'attributes' => array(
				'id' => 'from',
				'class' => 'form-control form-filter input-sm',
				'readonly' => true,
				'placeholder' => 'From'
			),
		));
		
		$this->add(array(
			'name' => 'to',
			'type' => 'Text',
			'options' => array(
				'label' => 'To',
			),
			'attributes' => array(
				'id' => 'to',
			),
			'attributes' => array(
				'id' => 'to',
				'class' => 'form-control form-filter input-sm',
				'readonly' => true,
				'placeholder' => 'To'
			),
		));
		
		$this->add(array(
			'name' => 'city',
			'type' => 'Text',
			'options' => array(
				'label' => 'City',
			),
			'attributes' => array(
				'class' => 'form-control form-filter input-sm',
			),
		));
		
		$this->add(array(
			'type' => 'Zend\Form\Element\Select',
			'name' => 'state_id',
			'options' => array(
				'label' => 'Select State',
				'value_options' => array(),
				'empty_option'  => 'Choose State'
			),
			'attributes' => array(
				'class' => 'form-control form-filter input-sm select2',
				'id' => 'states',
			),
		));
		
		$this->add(array(
			'type' => 'Zend\Form\Element\Select',
			'name' => 'country_id',
			'options' => array(
				'label' => 'Select Country',
				'value_options' => $this->getCountries(),
				'empty_option'  => 'Choose Country'
			),
			'attributes' => array(
				'class' => 'form-control form-filter input-sm select2 getStates',
				'data-id' => 'states',
			),
		));
		
		$this->add(array(
			'type' => 'Zend\Form\Element\Select',
			'name' => 'subscription_id',
			'options' => array(
				'label' => 'Select Subscription',
				'value_options' => $this->getSubscriptionPlans(),
				'empty_option'  => 'Subscriptions'
			),
			'attributes' => array(
				'class' => 'form-control form-filter input-sm select2',
			),
		));
		
		$this->add(array(
			'type' => 'Zend\Form\Element\Select',
			'name' => 'category_id',
			'options' => array(
				'label' => 'Select Category',
				'value_options' => $this->getCategories(),
				'empty_option'  => 'Choose Category'
			),
			'attributes' => array(
				'class' => 'form-control form-filter input-sm',
				'id' => 'e9'
			),
		));
		
		$this->add(array(
			'type' => 'Zend\Form\Element\Select',
			'name' => 'status_id',
			'options' => array(
				'label' => 'Select Status',
				'value_options' => array('0' => 'Unpaid', '1' => 'Paid', '2' => 'Partially Paid'),
				'empty_option'  => 'Choose Status'
			),
			'attributes' => array(
				'class' => 'form-control form-filter input-sm select2',
			),
		));
		
		$this->add(array(
			'name' => 'submit',
			'type' => 'Submit',
			'attributes' => array(
				'value' => 'Filter',
				'id' => 'submitbutton',
			),
		));
    }
     
    public function getCountries()
    {
        $data  = $this->country->fetchAll(false);

        $selectData = array();

        foreach ($data as $selectOption) {
            $selectData[$selectOption->id] = ucwords($selectOption->country_name);
        }

        return $selectData; 
	}
	
	public function getStates()
    {
        $data  = $data  = $this->state->fetchAll(false);

        $selectData = array();

        foreach ($data as $selectOption) {
            $selectData[$selectOption->id] = ucwords($selectOption->state_name);
        }

        return $selectData; 
	}
	
	public function getCategories()
    {
        $data  = $data  = $this->category->fetchAll(false);

        $selectData = array();

        foreach ($data as $selectOption) {
            $selectData[$selectOption->id] = ucwords($selectOption->category_name);
        }

        return $selectData; 
	}
	
	public function getSubscriptionPlans()
    {
        $data  = $data  = $this->subscriptionplans->fetchAll(false);

        $selectData = array();

        foreach ($data as $selectOption) {
            $selectData[$selectOption->id] = ucwords($selectOption->subscription_name);
        }

        return $selectData; 
	}
	
	public function getServices()
    {
        $data  = $data  = $this->service->fetchAll(false);

        $selectData = array();

        foreach ($data as $selectOption) {
            $selectData[$selectOption->id] = $selectOption->category_name." - ".$selectOption->service_period." mins";
        }

        return $selectData; 
	}
 }
