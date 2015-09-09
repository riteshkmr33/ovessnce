<?php
namespace Admin\Form;

 use Zend\Form\Form;
 use Admin\Model\Status;
 use Admin\Model\StatusTable;

class UsersMediaFilterForm extends Form
{
	private $status;
	 
    public function __construct(StatusTable $status)
    {
		$this->status = $status;
		 
         
        parent::__construct('media');

         
        $this->add(array(
            'name' => 'username',
            'type' => 'Text',
            'options' => array(
                'label' => 'User Name',
            ),
            'attributes' => array(
				'class' => 'form-control form-filter input-sm',
            ),
        ));
        $this->add(array(
            'name' => 'title',
            'type' => 'Text',
            'options' => array(
                'label' => 'Media Title',
            ),
            'attributes' => array(
				'class' => 'form-control form-filter input-sm',
            ),
        ));
        $this->add(array(
            'name' => 'media_type',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'Media Type',
                'value_options' => array(
					array('value' => 1, 'label'  => 'Image', 'selected' => 'selected' ), 
					//array('value' => 2, 'label'  => 'Video'), 
				),
            ),
            'attributes' => array(
				'class'=>'form-control form-filter input-sm select2',
			) 
        ));
        $this->add(array(
            'name' => 'from',
            'type' => 'Text',
            'options' => array(
                'label' => 'From',
            ),
            'attributes' => array(
				'id' => 'from',
				'class' => 'form-control form-filter input-sm',
				'readonly' => true,
				'placeholder' => 'From'
            ),
        ));
        $this->add(array(
            'name' => 'to',
            'type' => 'Text',
            'options' => array(
                'label' => 'To',
            ),
            'attributes' => array(
				'id' => 'to',
				'class' => 'form-control form-filter input-sm',
				'readonly' => true,
				'placeholder' => 'To'
            ),
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
				'class' => 'form-control form-filter input-sm select2',
            ),
		));
        $this->add(array(
            'name' => 'submit',
            'type' => 'Submit',
            'attributes' => array(
                'value' => 'Filter',
                'id' => 'submitbutton',
            ),
        ));
    }
	
	public function getStatus()
    {
        $data  =  $this->status->fetchAll(false, array(9,5,10));

        $selectData = array();

        foreach ($data as $selectOption) {
            $selectData[$selectOption->status_id] = ucwords($selectOption->status);
        }

        return $selectData; 
	}
 }
