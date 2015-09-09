<?php ini_set('display_errors',1); 
/*
 * @auther Badelal<badelalk@clavax.us><badelal143@gmail.com>
 */
return array(
    'router' => array(
        'routes' => array(
            'home' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Index',
                        'action' => 'index',
                    ),
                ),
            ),
            'application' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'Index',
                        'action' => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'default' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '[:controller[/:action]]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                            ),
                        ),
                    ),
                    
                    
                    
                ),
            ),
            
            'resetpassword' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/resetpassword[/:resettoken]',
                    'constraints' => array(
                        'resettoken' => '[a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Application\Controller\Resetpassword',
                        'action' => 'index',
                    ),
                ),
            ),
            
            'page' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/page[/:slug]',
                    'constraints' => array(
                        'slug' => '[a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Application\Controller\Page',
                        'action' => 'index',
                    ),
                ),
            ),
            
            'partners' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/partners[/:action][/]',
                    'constraints' => array(
                        'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Application\Controller\Partners',
                        'action' => 'index',
                    ),
                ),
            ),
            
            'practitioner' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/practitioner[/:action][/:id][/]',
                    'constraints' => array(
                        'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Application\Controller\Practitioner',
                        'action' => 'index',
                    ),
                ),
            ),
            
            'consumer' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/consumer[/:action][/:id][/]',
                    'constraints' => array(
                        'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Application\Controller\Consumer',
                        'action' => 'index',
                    ),
                ),
            ),
            
            'forgetpassword' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/Forgetpassword[/:action][/:id][/]',
                    'constraints' => array(
                        'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Application\Controller\Forgetpassword',
                        'action' => 'index',
                    ),
                ),
            ),
            
            'forgetusername' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/forgetusername[/:action][/:id][/]',
                    'constraints' => array(
                        'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Application\Controller\ForgetUsername',
                        'action' => 'index',
                    ),
                ),
            ),
            
            'verification' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/verification[/:userid]',
                    'constraints' => array(
                        'userid' => '[0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Application\Controller\Verification',
                        'action' => 'index',
                    ),
                ),
            ),
            
            'facebooklogin' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/Facebooklogin[/:action][/:id][/]',
                    'constraints' => array(
                        'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Application\Controller\Facebooklogin',
                        'action' => 'index',
                    ),
                ),
            ),
            'googlelogin' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/Googlelogin[/:action][/:id][/]',
                    'constraints' => array(
                        'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Application\Controller\Googlelogin',
                        'action' => 'index',
                    ),
                ),
            ),
             
             'linkedinlogin' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/linkedinlogin[/:flag]',
                    'constraints' => array(
                        'flag' => '[a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Application\Controller\Linkedinlogin',
                        'action' => 'index',
                    ),
                ),
            ),
            
			
            'membership' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/membership[/:action][/:id][/]',
                    'constraints' => array(
                        'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Application\Controller\Membership',
                        'action' => 'index',
                    ),
                ),
            ),
            
            'testimonials' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/testimonials[/:action][/:id][/]',
                    'constraints' => array(
                        'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Application\Controller\Testimonials',
                        'action' => 'index',
                    ),
                ),
            ),
            
            'contact' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/contact[/:action][/:id][/]',
                    'constraints' => array(
                        'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Application\Controller\Contactus',
                        'action' => 'index',
                    ),
                ),
            ),
            
            /* Booking route */
            'booking' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/booking[/:action][/:id][/]',
                    'constraints' => array(
                        'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Application\Controller\Booking',
                        'action' => 'index',
                    ),
                ),
            ),
            
            /* Login route */
            'login' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/login[/:action][/:id][/]',
                    'constraints' => array(
                        'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Application\Controller\Login',
                        'action' => 'index',
                    ),
                ),
            ),
            
            /* register route */
            'register' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/register[/:action][/:id][/]',
                    'constraints' => array(
                        'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Application\Controller\Register',
                        'action' => 'index',
                    ),
                ),
            ),
            
            /* cron route */
            'cron' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/cron[/:action][/:id][/]',
                    'constraints' => array(
                        'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Application\Controller\Cron',
                        'action' => 'index',
                    ),
                ),
            ),
            
            /* help center route */
            'helpcenter' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/helpcenter[/:action][/:id][/]',
                    'constraints' => array(
                        'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Application\Controller\HelpCenter',
                        'action' => 'index',
                    ),
                ),
            ),
        ),
    ),
    'service_manager' => array(
        'abstract_factories' => array(
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
            'Zend\Log\LoggerAbstractServiceFactory',
        ),
        'aliases' => array(
            'translator' => 'MvcTranslator',
        ),
    ),
    'translator' => array(
        'locale' => 'en_US',
        'translation_file_patterns' => array(
            array(
                'type' => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern' => '%s.mo',
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'Application\Controller\Index' => 'Application\Controller\IndexController',
            'Application\Controller\Test' => 'Application\Controller\TestController',
            'Application\Controller\Page' => 'Application\Controller\PageController',
            'Application\Controller\Login' => 'Application\Controller\LoginController',
            'Application\Controller\Register' => 'Application\Controller\RegisterController',
            'Application\Controller\Logout' => 'Application\Controller\LogoutController',
            'Application\Controller\Resetpassword' => 'Application\Controller\ResetpasswordController',
            'Application\Controller\Forgetpassword' => 'Application\Controller\ForgetpasswordController',
            'Application\Controller\Partners' => 'Application\Controller\PartnersController',
            'Application\Controller\Practitioner' => 'Application\Controller\PractitionerController',
            'Application\Controller\Membership' => 'Application\Controller\MembershipController',
            'Application\Controller\Testimonials' => 'Application\Controller\testimonialsController',
            'Application\Controller\Contactus' => 'Application\Controller\ContactusController',
            'Application\Controller\Booking' => 'Application\Controller\BookingController',
            'Application\Controller\Facebooklogin' => 'Application\Controller\FacebookloginController',
            'Application\Controller\Googlelogin' => 'Application\Controller\GoogleloginController',
            'Application\Controller\Linkedinlogin' => 'Application\Controller\LinkedinloginController',
            'Application\Controller\Consumer' => 'Application\Controller\ConsumerController',
            'Application\Controller\Wishlist' => 'Application\Controller\WishlistController',
            'Application\Controller\Verification' => 'Application\Controller\VerificationController',
            'Application\Controller\Cron' => 'Application\Controller\CronController',
            'Application\Controller\ForgetUsername' => 'Application\Controller\ForgetUsernameController',
            'Application\Controller\HelpCenter' => 'Application\Controller\HelpCenterController',
        ),
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions' => true,
        'doctype' => 'HTML5',
        'not_found_template' => 'error/404',
        'exception_template' => 'error/index',
        'template_map' => array(
            'layout/layout' => __DIR__ . '/../view/layout/layout.phtml',
            'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
            'error/404' => __DIR__ . '/../view/error/404.phtml',
            'error/index' => __DIR__ . '/../view/error/index.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
    // Placeholder for console routes
    'console' => array(
        'router' => array(
            'routes' => array(
            ),
        ),
    )
    ,
    'GMaps' => array(
        'api_key' => 'AIzaSyC4541bKcpmGkAB_s-aTDvX1mQdfPtfnJA',
    //'api_key' => 'AIzaSyASPo5uQ-QiQzhyg5pxMAasPMI5XZQ1w3c',
    ),
);
