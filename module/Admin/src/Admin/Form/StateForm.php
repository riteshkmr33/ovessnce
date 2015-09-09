<?php
namespace Admin\Form;

 use Zend\Form\Form;
 use Admin\Model\Countries;
 use Admin\Model\CountriesTable;
 use Zend\Db\Adapter\AdapterInterface;

 class StateForm extends Form
 {
     public function __construct(CountriesTable $selectTable)
     {
		 $this->setSelectTable($selectTable);
		 
         
         parent::__construct('countries');

         $this->add(array(
             'name' => 'id',
             'type' => 'Hidden',
         ));
         $this->add(array(
             'name' => 'state_code',
             'type' => 'Text',
             'options' => array(
                 'label' => 'State Code',
             ),
             'attributes' => array(
				'class'=>'form-control input-large'
			 )
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
				'class'=>'form-control input-large'
			 )
		 ));
         $this->add(array(
             'name' => 'state_name',
             'type' => 'Text',
             'options' => array(
                 'label' => 'State Name',
             ),
             'attributes' => array(
				'class'=>'form-control input-large'
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
        
        $table = $this->getSelectTable();
        $data  = $table->fetchAll(false);

        $selectData = array();

        foreach ($data as $selectOption) {
            $selectData[$selectOption->id] = ucwords($selectOption->country_name);
        }

        return $selectData; 
       
		
	}

	public function setSelectTable($selectTable)
    {
        $this->selectTable = $selectTable;

        return $this;
    }
    
     public function getSelectTable()
    {
        return $this->selectTable;
    }
 }
