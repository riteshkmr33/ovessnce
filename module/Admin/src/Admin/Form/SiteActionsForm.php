<?php
namespace Admin\Form;

 use Zend\Form\Form;
 use Admin\Model\SiteMeta;
 use Admin\Model\SiteMetaTable;

 class SiteActionsForm extends Form
 {
	 private $sitemeta;
	 
     public function __construct(SiteMetaTable $sitemeta,$action_metaArr = array())
     {
		 $this->sitemeta = $sitemeta;
		 $this->action_metaArr = $action_metaArr;
		 
         // we want to ignore the name passed
         parent::__construct('siteactions');

         $this->add(array(
             'name' => 'id', 
             'type' => 'Hidden',
         ));
         
         $this->add(array(
             'name' => 'controller_name',
             'type' => 'Text',
             'options' => array(
                 'label' => 'Controller Name',
             ),
             'attributes' => array(
				'class' => 'form-control input-large',
			 ),
         ));
         
         $this->add(array(
             'name' => 'action_name',
             'type' => 'Text',
             'options' => array(
                 'label' => 'Action Name',
             ),
             'attributes' => array(
				'class' => 'form-control input-large',
			 ),
         ));
         
         $this->add(array(
             'type' => 'Zend\Form\Element\Select',
             'name' => 'action_meta',
             'options' => array(
                     'label' => 'Action Meta',
                     'value_options' => $this->getActionMeta(),
             ),
             'attributes' => array(
				'class'=>'form-control input-large select2',
				'multiple' => 'multiple',
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
	
	public function getActionMeta()
	{
		$data = $this->sitemeta->fetchAll(false);
		$selectData = array();
		
         foreach ($data as $selectOption) {
            $selectData[] = array_key_exists($selectOption->id,$this->action_metaArr)?array('value' => $selectOption->id, 'label' => ucwords($selectOption->meta_title), 'selected' => 'selected'):array('value' => $selectOption->id, 'label' => ucwords($selectOption->meta_title));
        }

        return $selectData; 
	}
 }
