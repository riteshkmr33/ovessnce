<?php
/**
 * ResetPassword.php
 * @author Bade lal <badelalk@clavax.us><badelal143@gmail.com>
 * @package Model
 */
namespace Application\Model;
 
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
 
class ResetPassword implements InputFilterAwareInterface
{
    
    public $resettoken;
    public $password;
    public $repassword;
    
    protected $inputFilter;
     
    public function exchangeArray($data)
    {
        $this->resettoken  = (isset($data['resettoken']))  ? $data['resettoken']     : null; 
        $this->password  = (isset($data['password']))  ? $data['password']     : null; 
        $this->repassword  = (isset($data['repassword']))  ? $data['repassword']     : null; 
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
                    'name'     => 'resettoken',
                    'required' => false,
                    'filters'  => array(
                        array('name' => 'StripTags'),
                        array('name' => 'StringTrim'),
                    ),
                ))
            );
            
            $inputFilter->add(
                $factory->createInput(array(
                    'name'     => 'password',
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
                                    \Zend\Validator\NotEmpty::IS_EMPTY => 'Password is required',
                                ),
                                'break_chain_on_failure' => true,
                            ),
                        ),
                         array (
							'name' => 'Regex',
							'options' => array(
								'pattern'=>'/^.*(?=.{6,})(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])(?=.*[^a-zA-Z0-9])(?!.*\s).*$/',
								'messages' => array(
									\Zend\Validator\Regex::NOT_MATCH    => 'Password must be at least 6 characters and must contain at least one lower case letter, one upper case letter, one digit and one special character.',
								),
							),
							'break_chain_on_failure' => true
						),
                    ),
                ))
            );
            
            $inputFilter->add(
                $factory->createInput(array(
                    'name'     => 'repassword',
                    //'required' => true,
                    'filters'  => array(
                        array('name' => 'StripTags'),
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'NotEmpty',
                            'options' => array(
                                'messages' => array(
                                    \Zend\Validator\NotEmpty::IS_EMPTY => 'Confirm password is required',
                                ),
                                'break_chain_on_failure' => true,
                            ),
                        ),
                        array(
                            'name'    => 'Identical',
                            'options' => array(
                                'token' => 'password',
                                'messages' => array(
                                    \Zend\Validator\Identical::NOT_SAME => 'Confirm password does not match',
                                ),
                                'break_chain_on_failure' => true,
                            ),
                        ),
                    ),
                ))
            );
            
            $this->inputFilter = $inputFilter;
        }
         
        return $this->inputFilter;
    }
}