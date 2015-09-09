<?php
namespace Admin\Form;

use Zend\Form\Form;
use Admin\Model\Status;
use Admin\Model\StatusTable;
use Admin\Model\PageBannerLocations;
use Admin\Model\PageBannerLocationsTable;

class BannerPlanForm extends Form
{
	private $status;
	private $pbl;
 
	public function __construct(PageBannerLocationsTable $pbl, StatusTable $status)
	{
		$this->status = $status;
		$this->pbl = $pbl;
	 
		// we want to ignore the name passed
		parent::__construct('bannerplan');

		$this->add(array(
			'name' => 'id',
			'type' => 'Hidden',
		));
		
		$this->add(array(
			'name' => 'plan_name',
			'type' => 'Text',
			'options' => array(
				'label' => 'Plan Name',
			),
			'attributes' => array(
				'class'=>'form-control input-large'
			)
		));
		
		$this->add(array(
			'type' => 'Zend\Form\Element\Select',
			'name' => 'page_banner_location_id',
			'options' => array(
				'label' => 'Banner Location',
				'value_options' => $this->getPBLlist(),
				'empty_option'  => '--- Choose Page Banner Location ---'
			),
			'attributes' => array(
				'class'=>'form-control input-large',
			)
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
				'class'=>'form-control input-large',
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
 
	private function getPBLlist()
	{
		$data = $this->pbl->fetchAll(false);
		
		$selectData = array();

        foreach ($data as $selectOption) {
            $selectData[$selectOption->id] = ucwords($selectOption->page_name." - ".$selectOption->banner_name." - ".$selectOption->location_name);
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
