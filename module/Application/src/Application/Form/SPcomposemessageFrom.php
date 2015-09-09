<?php
namespace Application\Form;

 use Zend\Form\Form;
 
class SPcomposemessageFrom extends Form
{
    public function __construct($list = array())
    {
        parent::__construct('composemessage');
        $this->setAttribute('method', 'post');  
        
        
        $this->add(array(
			'type' => 'Zend\Form\Element\Select',
			'name' => 'to',
			'options' => array(
				'label' => 'TO',
				'value_options' => $this->getUsersList($list),	 
			),
			'attributes' => array(
				'required' => 'required', 
				'id' => 'to_user',
				'multiple' => 'multiple',
			),
		));
         
        $this->add(array(
            'name' => 'subject',
            'attributes' => array(
                'type'  => 'text',
                'name'  => 'subject',
                'id'  => 'subject',
                'required' => 'required', 
            ),
            'options' => array(
                'label' => 'SUBJECT',
            ),
        ));
        
        $this->add(array(
             'name' => 'message',
             'type' => 'Zend\Form\Element\Textarea',
             'options' => array(
                 'label' => ' ',
             ),
             'attributes' => array(
				'id' => 'message',
				'required' => 'required',
			 ),
         ));
               
    }
    
    public function getUsersList($list)
    {

		$selectData = array();
		$selectData['1'] = "Admin";
		if(count($list)>0){
			foreach($list as $value ){
				 $selectData[$value['id']] = $value['name'];
			}
		} /*else{
			$selectData[''] = "No users";
		}*/
		
		return $selectData;
	}
    
}
