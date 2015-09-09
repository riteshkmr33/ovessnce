<?php
namespace Application\Model;
 
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
 
class Login implements InputFilterAwareInterface
{
    public $user_type_id;
    public $user_name;
    public $Pass;
    public $rememberme;
    
    protected $inputFilter;
     
    public function exchangeArray($data)
    {
        $this->user_type_id  = (isset($data['user_type_id']))  ? $data['user_type_id']     : null; 
        $this->user_name  = (isset($data['user_name']))  ? $data['user_name']     : null; 
        $this->Pass  = (isset($data['Pass']))  ? $data['Pass']     : null; 
        $this->rememberme  = (isset($data['rememberme']))  ? $data['rememberme']     : null; 
    } 
     
    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }
     
    public function getInputFilter()
    {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();
            $factory     = new InputFactory();
              
            $inputFilter->add(
                $factory->createInput(array(
                    'name'     => 'user_type_id',
                    'required' => true,
                    'filters'  => array(
                        array('name' => 'StripTags'),
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'NotEmpty',
                            'options' => array(
                                'messages' => array(
                                    \Zend\Validator\NotEmpty::IS_EMPTY => 'User Type is required',
                                ),
                            ),
                        ),
                    ),
                ))
            );
            
            $inputFilter->add(
                $factory->createInput(array(
                    'name'     => 'user_name',
                    'required' => true,
                    'filters'  => array(
                        array('name' => 'StripTags'),
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
//                        array(
//                            'name'    => 'StringLength',
//                            'options' => array(
//                                'encoding' => 'UTF-8',
//                                'min'      => 4,
//                                'max'      => 20,
//                            ),
//                        ),
                        array(
                            'name' => 'NotEmpty',
                            'options' => array(
                                'messages' => array(
                                    \Zend\Validator\NotEmpty::IS_EMPTY => 'Username is required',
                                ),
                            ),
                        ),
                    ),
                ))
            );
            
            $inputFilter->add(
                $factory->createInput(array(
                    'name'     => 'Pass',
                    'required' => true,
                    'filters'  => array(
                        array('name' => 'StripTags'),
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
//                        array(
//                            'name'    => 'StringLength',
//                            'options' => array(
//                                'encoding' => 'UTF-8',
//                                'min'      => 6
//                            ),
//                        ),
                        array(
                            'name' => 'NotEmpty',
                            'options' => array(
                                'messages' => array(
                                    \Zend\Validator\NotEmpty::IS_EMPTY => 'Password is required',
                                ),
                            ),
                        ),
                    ),
                ))
            );
            
            $inputFilter->add(
                $factory->createInput(array(
                    'name'     => 'rememberme',
                    'required' => false,
                    'filters'  => array(
                        array('name' => 'StripTags'),
                        array('name' => 'StringTrim'),
                    )
                ))
            );
            
            /* For file upload
            $inputFilter->add(
                $factory->createInput(array(
                    'name'     => 'fileupload',
                    'required' => true,
                ))
            );
            */
            $this->inputFilter = $inputFilter;
        }
         
        return $this->inputFilter;
    }
    
}