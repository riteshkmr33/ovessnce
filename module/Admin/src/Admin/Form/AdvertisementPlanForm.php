<?php

namespace Admin\Form;

use Zend\Form\Form;
use Admin\Model\Advertisement;
use Admin\Model\AdvertisementTable;
use Admin\Model\AdvertisementPage;
use Admin\Model\AdvertisementPageTable;
use Zend\Db\Adapter\AdapterInterface;

class AdvertisementPlanForm extends Form
{

    private $advertisement;
    private $advertisement_page;

    public function __construct(AdvertisementTable $advertisement, AdvertisementPageTable $advertisement_page)
    {
        $this->advertisement = $advertisement;
	$this->advertisement_page = $advertisement_page;
        
        parent::__construct('advertisement');

        $this->add(array(
            'name' => 'id',
            'type' => 'Hidden',
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'advertisement_id',
            'options' => array(
                'label' => 'Advertisement',
                'value_options' => $this->getAdvertisements(),
            ),
            'attributes' => array(
                'class' => 'form-control input-large'
            )
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'advertisement_page_id',
            'options' => array(
                'label' => 'Page',
                'value_options' => $this->getAdvertisementPages(),
            ),
            'attributes' => array(
                'class' => 'form-control input-large'
            )
        ));

        $this->add(array(
            'name' => 'plan_name',
            'type' => 'Text',
            'options' => array(
                'label' => 'Plan Name',
            ),
            'attributes' => array(
                'class' => 'form-control input-large'
            )
        ));

        $this->add(array(
            'name' => 'duration',
            'type' => 'Text',
            'options' => array(
                'label' => 'Duration',
            ),
            'attributes' => array(
                'class' => 'form-control input-large'
            )
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'duration_in',
            'options' => array(
                'label' => 'Duration In',
                'value_options' => array('1' => 'Years', '2' => 'Months', '3' => 'Days'),
            ),
            'attributes' => array(
                'class' => 'form-control input-large'
            )
        ));

        $this->add(array(
            'name' => 'price',
            'type' => 'Text',
            'options' => array(
                'label' => 'Price',
            ),
            'attributes' => array(
                'class' => 'form-control input-large'
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

    public function getAdvertisements()
    {
        $data = $this->advertisement->fetchAll(false);

        $selectData = array();

        foreach ($data as $selectOption) {
            $selectData[$selectOption->id] = ucwords($selectOption->banner_name);
        }

        return $selectData;
    }

    public function getAdvertisementPages()
    {
        $data = $this->advertisement_page->fetchAll(array('status_id' => 1));

        $selectData = array();

        foreach ($data as $selectOption) {
            $selectData[$selectOption->id] = ucwords($selectOption->page_name);
        }

        return $selectData;
    }

}
