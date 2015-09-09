<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\Authentication\Storage;
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Adapter\DbTable as DbTableAuthAdapter;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\Session\SessionManager;
use Zend\Session\Container;
use Application\Model\Api;
use Application\Model\FrontEndAuth;
use Application\Model\Practitioners;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Http\Response;

class Module
{

    public function onBootstrap(MvcEvent $e)
    {
        $eventManager = $e->getApplication()->getEventManager();
        $serviceManager = $e->getApplication()->getServiceManager();

        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);

        $this->bootstrapSession($e);
        // Added by sazid to use common data in every page
        $application = $e->getParam('application');
        $viewModel = $application->getMvcEvent()->getViewModel();



        $api_url = $serviceManager->get('Config')['api_url']['value'];
        $session_apiurl = new Container('api_url');
        $session_apiurl->apiurl = $api_url;
        $apiCall = new Api();
        $getCommonData = $apiCall->getCommonData($api_url);
        foreach ($getCommonData as $key => $val) {
            if (!empty($val)) {
                $viewModel->$key = $val;
            }
        }

        // Hybrid view for ajax calls (disable layout for xmlHttpRequests)
        $eventManager->getSharedManager()->attach('Zend\Mvc\Controller\AbstractController', MvcEvent::EVENT_DISPATCH, function(MvcEvent $event) {

            /**
             * @var Request $request
             */
            $request = $event->getRequest();
            $viewModel = $event->getResult();
            $vModel = $event->getViewModel();
            $vModel->setVariable('api_url', $event->getApplication()->getServiceManager()->get('Config')['api_url']['value']);

            $userAuth = new FrontEndAuth;
            if ($userAuth->hasIdentity()) {
                $practitioner = new Practitioners();
                $userSession = new Container('frontend');
                $userType = ($userSession->user_type_id == '3') ? 'practitioner' : 'consumer';
                $vModel->setVariable('notifications', $practitioner->getNotifications($event->getApplication()->getServiceManager()->get('Config')['api_url']['value'], $userType));
            }

            if ($request->isXmlHttpRequest()) {
                $viewModel->setTerminal(true);
            }

            return $viewModel;
        }, -95);

        $eventManager->attach('route', array($this, 'doHttpsRedirect'));
    }

    //Bootstarp Session handler 
    public function bootstrapSession($e)
    {
        $session = $e->getApplication()
                ->getServiceManager()
                ->get('Zend\Session\SessionManager');
        $session->start();

        $container = new Container('initialized');
        if (!isset($container->init)) {
            $session->regenerateId(true);
            $container->init = 1;
        }
    }

    // 
    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'Application\Model\Common' => function($sm) {
            $model = new \Application\Model\Common();
            $model->setServiceLocator($sm);

            return $model;
        },
                'Application\Model\MyAuthStorage' => function($sm) {
            return new \Application\Model\MyAuthStorage('zf_tutorial');
        },
                'AuthService' => function($sm) {
            $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
            $dbTableAuthAdapter = new DbTableAuthAdapter($dbAdapter, 'users', 'user_name', 'pass', 'MD5(?)');

            $authService = new AuthenticationService();
            $authService->setAdapter($dbTableAuthAdapter);
            $authService->setStorage($sm->get('Application\Model\MyAuthStorage'));

            return $authService;
        },
                'Zend\Session\SessionManager' => function ($sm) {
            $config = $sm->get('config');
            if (isset($config['session'])) {
                $session = $config['session'];

                $sessionConfig = null;
                if (isset($session['config'])) {
                    $class = isset($session['config']['class']) ? $session['config']['class'] : 'Zend\Session\Config\SessionConfig';
                    $options = isset($session['config']['options']) ? $session['config']['options'] : array();
                    $sessionConfig = new $class();
                    $sessionConfig->setOptions($options);
                }

                $sessionStorage = null;
                if (isset($session['storage'])) {
                    $class = $session['storage'];
                    $sessionStorage = new $class();
                }

                $sessionSaveHandler = null;
                if (isset($session['save_handler'])) {
                    // class should be fetched from service manager since it will require constructor arguments
                    $sessionSaveHandler = $sm->get($session['save_handler']);
                }

                $sessionManager = new SessionManager($sessionConfig, $sessionStorage, $sessionSaveHandler);

                if (isset($session['validator'])) {
                    $chain = $sessionManager->getValidatorChain();
                    foreach ($session['validator'] as $validator) {
                        $validator = new $validator();
                        $chain->attach('session.validate', array($validator, 'isValid'));
                    }
                }
            } else {
                $sessionManager = new SessionManager();
            }
            Container::setDefaultManager($sessionManager);
            return $sessionManager;
        },
            ),
        );
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function doHttpsRedirect(MvcEvent $e)
    {
        $sm = $e->getApplication()->getServiceManager();
        $uri = $e->getRequest()->getUri();
        $controller = $e->getRouteMatch()->getParam('controller');
        $action =  $e->getRouteMatch()->getParam('action');
        $securedContrrollers = array('Application\Controller\Login', 'Application\Controller\Register', 'Application\Controller\Booking', 'Application\Controller\Membership');
        $securedActions = array('Application\Controller\Login' => array('index'), 'Application\Controller\Register' => array('index'), 'Application\Controller\Booking' => array('checkout', 'payment'), 'Application\Controller\Membership' => array('checkout', 'payment'));
        
        //$securedPages = array();
        //echo $action; exit;
        if (in_array($controller, $securedContrrollers) && in_array($action, $securedActions[$controller])) {
            $scheme = $uri->getScheme();
            if ($scheme != 'https') {
                $uri->setScheme('https');
                $response = $e->getResponse();
                $response->getHeaders()->addHeaderLine('Location', $uri);
                $response->setStatusCode(302);
                $response->sendHeaders();
                return $response;
            }
        } else {
            if ($_SERVER["HTTPS"] == "on") {
                $uri->setScheme('http');
                $response = $e->getResponse();
                $response->getHeaders()->addHeaderLine('Location', $uri);
                $response->setStatusCode(302);
                $response->sendHeaders();
                return $response;
            }
        }
    }

}
