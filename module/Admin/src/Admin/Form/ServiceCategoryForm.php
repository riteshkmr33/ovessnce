<?php
namespace Admin\Form; 

use Zend\Form\Form;
use Admin\Model\ServiceCategory;
use Admin\Model\ServiceCategoryTable;
use Zend\Db\Adapter\AdapterInterface;

class ServiceCategoryForm extends Form
{
	public function __construct(ServiceCategoryTable $selectTable)
    {
		$this->setSelectTable($selectTable);
		
		// we want to ignore the name passed
	    parent::__construct('servicecategories');

		$this->add(array(
			'name' => 'id',
			'type' => 'Hidden',
		));
		$this->add(array(
             'type' => 'Zend\Form\Element\Select',
             'name' => 'parent_id',
             'options' => array(
                     'label' => 'Select Parent Category',
                     'value_options' => $this->getCategories(),
                     'empty_option'  => '--- Parent Category ---',
                     //'disable_inarray_validator' => true, // <-- disable
             ),
             'attributes' => array(
				'class'=>'form-control parent'
			 )
		 ));
		$this->add(array(
			'name' => 'category_name',
			'type' => 'Text',
			'options' => array(
				'label' => 'Category Name',
			),
			'attributes' => array(
				'class'=>'form-control'
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
    
    public function getCategories()
    {
        
        $table = $this->getSelectTable();
        $data  = $table->fetchAll(false, array("parent_id"=>0));

        $selectData = array(0 => 'Set As Parent');
		foreach($data as $selectOption){
			$selectData[$selectOption->id] = $sep.ucwords($selectOption->category_name);
		}
			
        return $selectData; 
       
		
	}
	
	/*public function getChild($sep,$level,$data){
		
		$res = array();
		
		if ($level > 0 && count($data) > 0) {
			$sep = "|";
			
			for ($i=1; $i<=$level; $i++) {
				$sep .= "_";
			}
			
		}
		
		foreach($data as $selectOption){
			$res[$selectOption->id] = $sep.$selectOption->category_name;
			$newData = $this->getSelectTable()->fetchAll(false, array("parent_id"=>$selectOption->id));
			
			if (count($newData) > 0) {
				$res = $res+$this->getChild($sep,$level+1,$newData);
			}
		}
		return $res; 
		
	} */

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
