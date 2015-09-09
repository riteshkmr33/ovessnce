<?php
/**
 * ForgetPassword.php
 * @author Bade lal <badelalk@clavax.us><badelal143@gmail.com>
 * @package Model
 */
namespace Application\Model;
 
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
 
class ForgetPassword implements InputFilterAwareInterface
{
    public $email;
    
    protected $inputFilter;
     
    public function exchangeArray($data)
    {
        $this->email  = (isset($data['email']))  ? $data['email']     : null; 
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
                    'name'     => 'email',
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
                                    \Zend\Validator\NotEmpty::IS_EMPTY => 'Email is required',
                                ),
                                'break_chain_on_failure' => true
                            ),
                        ),
                        array(
                            'name' => 'EmailAddress',
                            'options' => array(
                                'messages' => array(
                                    \Zend\Validator\EmailAddress::INVALID_FORMAT => 'Invalid email address',
                                ),
                                'break_chain_on_failure' => true
                            ),
                        ),
                    ),
                ))
            );
            
            $this->inputFilter = $inputFilter;
        }
         
        return $this->inputFilter;
    }
    
    public function generateRandomPassword($length = 7) {
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $string = '';
        for ($i = 0; $i < $length; $i++) {
            $string .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $string;
    }
}