<?php

namespace Admin\Form;

use Zend\Form\Form;
use Admin\Model\Status;
use Admin\Model\StatusTable;
use Admin\Model\AdvertisementPage;
use Admin\Model\AdvertisementPageTable;
use Zend\Db\Adapter\AdapterInterface;

class SiteBannerForm extends Form
{

    private $status;
    private $pagelocations;

    public function __construct(AdvertisementPageTable $pagelocations, StatusTable $status)
    {
        $this->status = $status;
        $this->pagelocations = $pagelocations;

        // we want to ignore the name passed
        parent::__construct('sitebanner');

        $this->setAttribute('method', 'post');
        $this->setAttribute('enctype', 'multipart/form-data');


        $this->add(array(
            'name' => 'id',
            'type' => 'Hidden',
        ));

        $this->add(array(
            'name' => 'title',
            'type' => 'Text',
            'options' => array(
                'label' => 'Banner Title',
            ),
            'attributes' => array(
                'class' => 'form-control input-large'
            )
        ));
        
        $this->add(array(
            'name' => 'banner_url',
            'attributes' => array(
                'type'  => 'file',
            ),
            'options' => array(
                'label' => 'Banner',
            )
        ));

        $this->add(array(
            'name' => 'status_id',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'Status',
                'value_options' => $this->getStatus(),
                'empty_option' => '--- Select Status ---'
            ),
            'attributes' => array(
                'class' => 'form-control input-large',
            )
        ));
        
        $this->add(array(
            'name' => 'page_location_id',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'Page',
                'value_options' => $this->getPageLocations(),
                'empty_option' => '--- Select Page ---'
            ),
            'attributes' => array(
                'class' => 'form-control input-large',
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

    function getStatus()
    {
        $results = $this->status->fetchAll(false, array(1, 2));

        $selectData = array();

        foreach ($results as $result) {
            $selectData[$result->status_id] = ucwords($result->status);
        }

        return $selectData;
    }
    
    function getPageLocations()
    {
        $results = $this->pagelocations->fetchAll();

        $selectData = array();

        foreach ($results as $result) {
            $selectData[$result->id] = ucwords($result->page_name);
        }

        return $selectData;
    }

}
