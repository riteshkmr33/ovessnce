<?php
namespace Admin\Form;

 use Zend\Form\Form;
 use Admin\Model\Status;
 use Admin\Model\StatusTable;
 use Admin\Model\SiteMeta;
 use Admin\Model\SiteMetaTable;

 class PageForm extends Form
 {
	 private $status;
	 private $sitemeta;
	 
     public function __construct(StatusTable $status,SiteMetaTable $sitemeta,$page_metaArr = array())
     {
		 $this->status = $status;
		 $this->sitemeta = $sitemeta;
		 $this->page_metaArr = $page_metaArr;
		 
         // we want to ignore the name passed
         parent::__construct('page');

         $this->add(array(
             'name' => 'page_id', 
             'type' => 'Hidden',
         ));
         $this->add(array(
             'name' => 'title',
             'type' => 'Text',
             'options' => array(
                 'label' => 'Title',
             ),
             'attributes' => array(
				'class' => 'form-control input-large',
			 ),
         ));
         
         $this->add(array(
             'type' => 'Zend\Form\Element\Select',
             'name' => 'page_meta',
             'options' => array(
                     'label' => 'Page Meta',
                     'value_options' => $this->getPageMeta(),
             ),
             'attributes' => array(
				'class'=>'form-control input-large select2',
				'multiple' => 'multiple',
			 )
         ));
         
         $this->add(array(
             'name' => 'content',
             'type' => 'Zend\Form\Element\Textarea',
             'options' => array(
                 'label' => 'Content',
             ),
             'attributes' => array(
				'class' => 'ckeditor form-control',
			 ),
         ));
         
         $this->add(array(
             'type' => 'Zend\Form\Element\Radio',
             'name' => 'page_status',
             'options' => array(
					 'label_attributes' => array(
						'class'  => 'radio-inline'
     				 ),
                     'label' => 'Status',
                     'value_options' => $this->getStatus(),
                     'attributes' => array(
						'value' => '1' //set checked to '1'
					 )
             ),
             
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
    
    public function getStatus()
    {
        $data  = $this->status->fetchAll(false, array(1,2));

        $selectData = array();

        foreach ($data as $selectOption) {
            $selectData[$selectOption->status_id] = ucwords($selectOption->status);
        }

        return $selectData; 
	}
	
	public function getPageMeta()
	{
		$data = $this->sitemeta->fetchAll(false);
		$selectData = array();
		
         foreach ($data as $selectOption) {
            $selectData[] = array_key_exists($selectOption->id,$this->page_metaArr)?array('value' => $selectOption->id, 'label' => ucwords($selectOption->meta_title), 'selected' => 'selected'):array('value' => $selectOption->id, 'label' => ucwords($selectOption->meta_title));
        }

        return $selectData; 
	}
 }
