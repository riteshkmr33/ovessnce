<?php
namespace Admin\Form;

 use Zend\Form\Form;
 use Admin\Model\Users;
 use Admin\Model\UsersTable;
 use Admin\Model\Status;
 use Admin\Model\StatusTable;
 use Zend\Db\Adapter\AdapterInterface;
  
class MediaForm extends Form
{
	private $status;
	private $users;

    public function __construct(UsersTable $users, StatusTable $status)
    {
		$this->users = $users;
		$this->status = $status;
		 
        // we want to ignore the name passed
        parent::__construct('media');
         
        $this->setAttribute('method', 'post');
        $this->setAttribute('enctype','multipart/form-data');
         

        $this->add(array(
            'name' => 'id',
            'type' => 'Hidden',
        ));
        
        $this->add(array(
            'name' => 'user_id',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'User',
                'value_options' => $this->getUsers(),
                'empty_option'  => '--- Select Users ---'
            ),
            'attributes' => array(
				'class'=>'form-control input-large',
			) 
        ));
         
        $this->add(array(
            'name' => 'media_title',
            'type' => 'Text',
            'options' => array(
                'label' => 'Media Title',
            ),
            'attributes' => array(
				'class'=>'form-control input-large'
			)
        ));
         
          $this->add(array(
            'name' => 'media_url',
            'attributes' => array(
                'type'  => 'file',
            ),
            'options' => array(
                'label' => 'File',
            )
        ));
        
        $this->add(array(
            'name' => 'media_description',
            'type' => 'Zend\Form\Element\Textarea',
            'options' => array(
                'label' => 'Description',
            ),
            'attributes' => array(
				'class' => 'form-control',
			),
        ));
         
        $this->add(array(
            'name' => 'media_type',
            'type' => 'Radio',
            'options' => array(
                'label' => 'Media Type',
                'label_attributes' => array(
					'class'  => 'radio-inline'
				),
                'value_options' => array(
					array('value' => 1, 'label'  => 'Image', 'selected' => 'selected' ), 
					array('value' => 2, 'label'  => 'Video'), 
				),
            ),
            'attributes' => array(
				'class'=>'',
			) 
        ));
         
        $this->add(array(
            'name' => 'status_id',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'Status',
                'value_options' => $this->getStatus(),
                'empty_option'  => '--- Select Status ---'
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
    
    function getUsers()
    {
		$results = $this->users->fetchAll(false);
		
		$selectData = array();
		
		foreach ($results as $result) {
			$selectData[$result->id] = ucwords($result->first_name." ".$result->last_name);
		}
		
		return $selectData;
	}
	
    function getStatus()
    {
		$results = $this->status->fetchAll(false, array(9,5,10));
		
		$selectData = array();
		
		foreach ($results as $result) {
			$selectData[$result->status_id] = ucwords($result->status);
		}
		
		return $selectData;
	}
 }
