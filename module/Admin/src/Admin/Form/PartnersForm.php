<?php
namespace Admin\Form;

 use Zend\Form\Form;
 use Admin\Model\Status;
 use Admin\Model\StatusTable;
 use Zend\Db\Adapter\AdapterInterface;
  
 class PartnersForm extends Form
 {
	 private $status;
	 
     public function __construct(StatusTable $status)
     {
		 $this->status = $status;
		 
         // we want to ignore the name passed
         parent::__construct('partners');
         
         $this->setAttribute('method', 'post');
         $this->setAttribute('enctype','multipart/form-data');
         

         $this->add(array(
             'name' => 'id',
             'type' => 'Hidden',
         ));
         
         $this->add(array(
             'name' => 'title',
             'type' => 'Text',
             'options' => array(
                 'label' => 'Title',
             ),
             'attributes' => array(
				'class'=>'form-control input-large'
			 )
         ));
         
         $this->add(array(
             'name' => 'desc',
             'type' => 'Zend\Form\Element\Textarea',
             'options' => array(
                 'label' => 'Description',
             ),
             'attributes' => array(
				'class' => 'ckeditor form-control',
			 ),
         ));
         
         $this->add(array(
             'name' => 'url',
             'type' => 'Zend\Form\Element\Url',
             'options' => array(
                 'label' => 'Url',
             ),
             'attributes' => array(
				'class'=>'form-control input-large'
			 )
         ));
                  
         $this->add(array(
            'name' => 'logo',
            'attributes' => array(
                'type'  => 'file',
            ),
            'options' => array(
                'label' => 'Logo',
            ),
         )); 
                  
         $this->add(array(
             'type' => 'Zend\Form\Element\Radio',
             'name' => 'status_id',
             'options' => array(
                     'label' => 'Status',
                     'label_attributes' => array(
						'class'  => 'radio-inline'
					 ),
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
        $data  = $this->status->fetchAll(false, array(1,2,3));

        $selectData = array();

        foreach ($data as $selectOption) {
            $selectData[$selectOption->status_id] = ucwords($selectOption->status);
        }

        return $selectData; 
	}
	 
 }
