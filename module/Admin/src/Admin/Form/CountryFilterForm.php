<?php
namespace Admin\Form;

 use Zend\Form\Form;
 use Admin\Model\Status;
 use Admin\Model\StatusTable;

 class CountryFilterForm extends Form
 {
	 private $status;
	 
     public function __construct(StatusTable $status)
     {
		 $this->status = $status;
		 
		 parent::__construct('countryfilter');

         
         $this->add(array(
             'name' => 'country_code',
             'type' => 'Text',
             'options' => array(
                 'label' => 'Country Code',
             ),
             'attributes' => array(
				'class' => 'form-control form-filter input-sm',
             ),
         ));
         $this->add(array(
             'name' => 'country_name',
             'type' => 'Text',
             'options' => array(
                 'label' => 'Country Name',
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
