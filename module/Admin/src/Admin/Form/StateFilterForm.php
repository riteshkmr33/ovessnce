<?php
namespace Admin\Form;

 use Zend\Form\Form;
 
 use Admin\Model\Countries;
 use Admin\Model\CountriesTable;
 use Admin\Model\Status;
 use Admin\Model\StatusTable;

 class StateFilterForm extends Form
 {
	 private $country;
	 private $status;
	 
     public function __construct(CountriesTable $country, StatusTable $status)
     {
		 $this->country = $country;
		 $this->status = $status;
         
         parent::__construct('statefilter');

         
         $this->add(array(
             'name' => 'state_code',
             'type' => 'Text',
             'options' => array(
                 'label' => 'State Code',
             ),
             'attributes' => array(
				'class' => 'form-control form-filter input-sm',
             ),
         ));
         $this->add(array(
             'name' => 'state_name',
             'type' => 'Text',
             'options' => array(
                 'label' => 'State Name',
             ),
             'attributes' => array(
				'class' => 'form-control form-filter input-sm',
             ),
         ));
		 $this->add(array(
             'type' => 'Zend\Form\Element\Select',
             'name' => 'country_id',
             'options' => array(
                     'label' => 'Select Country',
                     'value_options' => $this->getCountries(),
                     'empty_option'  => '--- Choose Country ---'
             ),
             'attributes' => array(
				'class' => 'form-control form-filter input-sm',
             ),
		 ));
		 
         $this->add(array(
             'type' => 'Zend\Form\Element\Select',
             'name' => 'status_id',
             'options' => array(
                     'label' => 'Select Status',
                     'value_options' => $this->getStatus(),
                     'empty_option'  => '--- Choose Status ---'
             ),
             'attributes' => array(
				'class' => 'form-control form-filter input-sm',
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
	
	public function getStatus()
    {
        $data  = $this->status->fetchAll(false, array(1,2));

        $selectData = array();

        foreach ($data as $selectOption) {
            $selectData[$selectOption->status_id] = ucwords($selectOption->status);
        }

        return $selectData; 
	}
 }
