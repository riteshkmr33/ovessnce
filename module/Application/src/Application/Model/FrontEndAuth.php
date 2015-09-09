<?php
/**
 * FrontEndAuth.php
 * @author Bade lal <badelalk@clavax.us><badelal143@gmail.com>
 * @package model
 */
namespace Application\Model;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\Container;

class FrontEndAuth extends AbstractActionController 
{

    public function hasIdentity($userType = '')
    {

        $session = new Container('frontend');

        if ($session->userid) {
			if ($userType != '') {
				if ($session->user_type_id == $userType) {
					return true;
				} else {
					return false;
				}
			}
			
            return true;
        }

        return false;
    }

    public function logout()
    {
        $session = new Container('frontend');
        $session->getManager()->getStorage()->clear();
        
        $fb_login = new Container('facebook');
		$fb_login->getManager()->getStorage()->clear();
		
		$google_login = new Container('google');
        $google_login->getManager()->getStorage()->clear();
        
        $linkedin_login = new Container('linkedin');
        $linkedin_login->getManager()->getStorage()->clear();
    }
    
    public function wordpress_user_detail($user_name)
    {
        //$user_login = 'admin';
        $user = get_userdatabylogin($user_name);
        return $user;
    }
    
    public function wordpress_login($user_login)
    {
        //$user_login = 'admin';
        $user = get_userdatabylogin($user_login);
        //print_r($user);
        //exit;
        $user_id = $user->ID;
        wp_set_current_user($user_id, $user_login);
        wp_set_auth_cookie($user_id);
        do_action('wp_login', $user_login);
        //return $this->redirect()->toUrl('http://blog.ovessence.loc/wp-admin/');
    }
    
    public function wordpress_logout()
    {
        wp_logout();
    }
    
    public function wordpress_create_user($user_name, $user_email, $random_password)
    {
        //$user_name="badelal";
        $user_id = username_exists( $user_name );
        //$user_email = "badelalk@clavax.us";
        
        if ( !$user_id and email_exists($user_email) == false ) {
            //$random_password = wp_generate_password( $length=12, $include_standard_special_chars=false );
            //$random_password = "tech";
            $user_id = wp_create_user( $user_name, $random_password, $user_email );
        } else {
            //$random_password = __('User already exists.  Password inherited.');
        }
    }
    
    public function wordpress_set_password($password, $user_id)
    {
        //wp_update_user(array('ID' => $userid, 'user_pass' => 'myNeWpaSSword'));
        wp_set_password( $password, $user_id );
    }

}
