<?php
namespace Admin\Form;

 use Zend\Form\Form;
 use Admin\Model\Banners;
 use Admin\Model\BannersTable;
 use Admin\Model\PageLocations;
 use Admin\Model\PageLocationsTable;
 use Zend\Db\Adapter\AdapterInterface;

 class PageBannerLocationForm extends Form
 {
	 private $banners;
	 private $pageLocations;
	 
     public function __construct(BannersTable $banners, PageLocationsTable $pl)
     {
		 $this->banners = $banners;
		 $this->pageLocations = $pl;
		 
		 parent::__construct('banners');

         $this->add(array(
             'name' => 'id',
             'type' => 'Hidden',
         ));
         $this->add(array(
             'type' => 'Zend\Form\Element\Select',
             'name' => 'banner_id',
             'options' => array(
                     'label' => 'Banner',
                     'value_options' => $this->getBanners(),
                     'empty_option'  => '--- Choose Banners ---'
             ),
             'attributes' => array(
				'class'=>'form-control input-large'
			 )
		 ));
         $this->add(array(
             'type' => 'Zend\Form\Element\Select',
             'name' => 'location_id',
             'options' => array(
                     'label' => 'Location',
                     'value_options' => $this->getLocations(),
                     'empty_option'  => '--- Choose Location ---'
             ),
             'attributes' => array(
				'class'=>'form-control input-large'
			 )
		 ));
         $this->add(array(
             'name' => 'page_name',
             'type' => 'Text',
             'options' => array(
                 'label' => 'Page Name',
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
     
    public function getLocations()
    {
        $data  = $this->pageLocations->fetchAll(false);

        $selectData = array();

        foreach ($data as $selectOption) {
            $selectData[$selectOption->id] = ucwords($selectOption->location_name);
        }

        return $selectData; 
	}
	
	public function getBanners()
    {
        $data  = $this->banners->fetchAll(false);

        $selectData = array();

        foreach ($data as $selectOption) {
            $selectData[$selectOption->id] = ucwords($selectOption->banner_name);
        }

        return $selectData; 
	}
 }
