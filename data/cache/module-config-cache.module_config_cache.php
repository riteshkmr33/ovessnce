<?php
return array (
  'router' => 
  array (
    'routes' => 
    array (
      'home' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Literal',
        'options' => 
        array (
          'route' => '/',
          'defaults' => 
          array (
            'controller' => 'Application\\Controller\\Index',
            'action' => 'index',
          ),
        ),
      ),
      'application' => 
      array (
        'type' => 'Literal',
        'options' => 
        array (
          'route' => '/',
          'defaults' => 
          array (
            '__NAMESPACE__' => 'Application\\Controller',
            'controller' => 'Index',
            'action' => 'index',
          ),
        ),
        'may_terminate' => true,
        'child_routes' => 
        array (
          'default' => 
          array (
            'type' => 'Segment',
            'options' => 
            array (
              'route' => '[:controller[/:action]]',
              'constraints' => 
              array (
                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
              ),
              'defaults' => 
              array (
              ),
            ),
          ),
        ),
      ),
      'resetpassword' => 
      array (
        'type' => 'Segment',
        'options' => 
        array (
          'route' => '/resetpassword[/:resettoken]',
          'constraints' => 
          array (
            'resettoken' => '[a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'Application\\Controller\\Resetpassword',
            'action' => 'index',
          ),
        ),
      ),
      'page' => 
      array (
        'type' => 'Segment',
        'options' => 
        array (
          'route' => '/page[/:slug]',
          'constraints' => 
          array (
            'slug' => '[a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'Application\\Controller\\Page',
            'action' => 'index',
          ),
        ),
      ),
      'partners' => 
      array (
        'type' => 'Segment',
        'options' => 
        array (
          'route' => '/partners[/:action][/]',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'Application\\Controller\\Partners',
            'action' => 'index',
          ),
        ),
      ),
      'practitioner' => 
      array (
        'type' => 'Segment',
        'options' => 
        array (
          'route' => '/practitioner[/:action][/:id][/]',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'Application\\Controller\\Practitioner',
            'action' => 'index',
          ),
        ),
      ),
      'consumer' => 
      array (
        'type' => 'Segment',
        'options' => 
        array (
          'route' => '/consumer[/:action][/:id][/]',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'Application\\Controller\\Consumer',
            'action' => 'index',
          ),
        ),
      ),
      'forgetpassword' => 
      array (
        'type' => 'Segment',
        'options' => 
        array (
          'route' => '/Forgetpassword[/:action][/:id][/]',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'Application\\Controller\\Forgetpassword',
            'action' => 'index',
          ),
        ),
      ),
      'forgetusername' => 
      array (
        'type' => 'Segment',
        'options' => 
        array (
          'route' => '/forgetusername[/:action][/:id][/]',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'Application\\Controller\\ForgetUsername',
            'action' => 'index',
          ),
        ),
      ),
      'verification' => 
      array (
        'type' => 'Segment',
        'options' => 
        array (
          'route' => '/verification[/:userid]',
          'constraints' => 
          array (
            'userid' => '[0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'Application\\Controller\\Verification',
            'action' => 'index',
          ),
        ),
      ),
      'facebooklogin' => 
      array (
        'type' => 'Segment',
        'options' => 
        array (
          'route' => '/Facebooklogin[/:action][/:id][/]',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'Application\\Controller\\Facebooklogin',
            'action' => 'index',
          ),
        ),
      ),
      'googlelogin' => 
      array (
        'type' => 'Segment',
        'options' => 
        array (
          'route' => '/Googlelogin[/:action][/:id][/]',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'Application\\Controller\\Googlelogin',
            'action' => 'index',
          ),
        ),
      ),
      'linkedinlogin' => 
      array (
        'type' => 'Segment',
        'options' => 
        array (
          'route' => '/linkedinlogin[/:flag]',
          'constraints' => 
          array (
            'flag' => '[a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'Application\\Controller\\Linkedinlogin',
            'action' => 'index',
          ),
        ),
      ),
      'membership' => 
      array (
        'type' => 'Segment',
        'options' => 
        array (
          'route' => '/membership[/:action][/:id][/]',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'Application\\Controller\\Membership',
            'action' => 'index',
          ),
        ),
      ),
      'testimonials' => 
      array (
        'type' => 'Segment',
        'options' => 
        array (
          'route' => '/testimonials[/:action][/:id][/]',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'Application\\Controller\\Testimonials',
            'action' => 'index',
          ),
        ),
      ),
      'contact' => 
      array (
        'type' => 'Segment',
        'options' => 
        array (
          'route' => '/contact[/:action][/:id][/]',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'Application\\Controller\\Contactus',
            'action' => 'index',
          ),
        ),
      ),
      'booking' => 
      array (
        'type' => 'Segment',
        'options' => 
        array (
          'route' => '/booking[/:action][/:id][/]',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'Application\\Controller\\Booking',
            'action' => 'index',
          ),
        ),
      ),
      'login' => 
      array (
        'type' => 'Segment',
        'options' => 
        array (
          'route' => '/login[/:action][/:id][/]',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'Application\\Controller\\Login',
            'action' => 'index',
          ),
        ),
      ),
      'register' => 
      array (
        'type' => 'Segment',
        'options' => 
        array (
          'route' => '/register[/:action][/:id][/]',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'Application\\Controller\\Register',
            'action' => 'index',
          ),
        ),
      ),
      'cron' => 
      array (
        'type' => 'Segment',
        'options' => 
        array (
          'route' => '/cron[/:action][/:id][/]',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'Application\\Controller\\Cron',
            'action' => 'index',
          ),
        ),
      ),
      'helpcenter' => 
      array (
        'type' => 'Segment',
        'options' => 
        array (
          'route' => '/helpcenter[/:action][/:id][/]',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'Application\\Controller\\HelpCenter',
            'action' => 'index',
          ),
        ),
      ),
      'admin' => 
      array (
        'type' => 'Literal',
        'options' => 
        array (
          'route' => '/admin',
          'defaults' => 
          array (
            '__NAMESPACE__' => 'Admin\\Controller',
            'controller' => 'Admin',
            'action' => 'index',
          ),
        ),
        'may_terminate' => true,
        'child_routes' => 
        array (
          'default' => 
          array (
            'type' => 'Segment',
            'options' => 
            array (
              'route' => '[/:controller][/:action][/:id][/]',
              'constraints' => 
              array (
                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
              ),
              'defaults' => 
              array (
              ),
            ),
          ),
          'page' => 
          array (
            'type' => 'Segment',
            'options' => 
            array (
              'route' => '/page[/:action][/:id][/]',
              'constraints' => 
              array (
                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                'id' => '[0-9]+',
              ),
              'defaults' => 
              array (
                'controller' => 'Admin\\Controller\\Page',
                'action' => 'index',
              ),
            ),
          ),
          'sitemeta' => 
          array (
            'type' => 'Segment',
            'options' => 
            array (
              'route' => '/sitemeta[/:action][/:id][/]',
              'constraints' => 
              array (
                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                'id' => '[0-9]+',
              ),
              'defaults' => 
              array (
                'controller' => 'Admin\\Controller\\SiteMeta',
                'action' => 'index',
              ),
            ),
          ),
          'siteactions' => 
          array (
            'type' => 'Segment',
            'options' => 
            array (
              'route' => '/siteactions[/:action][/:id][/]',
              'constraints' => 
              array (
                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                'id' => '[0-9]+',
              ),
              'defaults' => 
              array (
                'controller' => 'Admin\\Controller\\SiteActions',
                'action' => 'index',
              ),
            ),
          ),
          'notificationsettings' => 
          array (
            'type' => 'Segment',
            'options' => 
            array (
              'route' => '/notificationsettings[/:action][/:id][/]',
              'constraints' => 
              array (
                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                'id' => '[0-9]+',
              ),
              'defaults' => 
              array (
                'controller' => 'Admin\\Controller\\NotificationSettings',
                'action' => 'index',
              ),
            ),
          ),
          'messages' => 
          array (
            'type' => 'Segment',
            'options' => 
            array (
              'route' => '/messages[/:action][/:id][/]',
              'constraints' => 
              array (
                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                'id' => '[0-9]+',
              ),
              'defaults' => 
              array (
                'controller' => 'Admin\\Controller\\Messages',
                'action' => 'index',
              ),
            ),
          ),
          'emailtemplates' => 
          array (
            'type' => 'Segment',
            'options' => 
            array (
              'route' => '/emailtemplates[/:action][/:id][/]',
              'constraints' => 
              array (
                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                'id' => '[0-9]+',
              ),
              'defaults' => 
              array (
                'controller' => 'Admin\\Controller\\Emailtemplates',
                'action' => 'index',
              ),
            ),
          ),
          'sms' => 
          array (
            'type' => 'Segment',
            'options' => 
            array (
              'route' => '/sms[/:action][/:id][/]',
              'constraints' => 
              array (
                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                'id' => '[0-9]+',
              ),
              'defaults' => 
              array (
                'controller' => 'Admin\\Controller\\Sms',
                'action' => 'index',
              ),
            ),
          ),
          'consumers' => 
          array (
            'type' => 'Segment',
            'options' => 
            array (
              'route' => '/consumers[/:action][/:id][/]',
              'constraints' => 
              array (
                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                'id' => '[0-9]+',
              ),
              'defaults' => 
              array (
                'controller' => 'Admin\\Controller\\Consumers',
                'action' => 'index',
              ),
            ),
          ),
          'testimonials' => 
          array (
            'type' => 'Segment',
            'options' => 
            array (
              'route' => '/testimonials[/:action][/:id][/]',
              'constraints' => 
              array (
                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                'id' => '[0-9]+',
              ),
              'defaults' => 
              array (
                'controller' => 'Admin\\Controller\\Testimonials',
                'action' => 'index',
              ),
            ),
          ),
          'sitemodules' => 
          array (
            'type' => 'Segment',
            'options' => 
            array (
              'route' => '/SiteModules[/:action][/:id][/]',
              'constraints' => 
              array (
                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                'id' => '[0-9]+',
              ),
              'defaults' => 
              array (
                'controller' => 'Admin\\Controller\\SiteModules',
                'action' => 'index',
              ),
            ),
          ),
          'organizations' => 
          array (
            'type' => 'Segment',
            'options' => 
            array (
              'route' => '/PractitionerOrganizations[/:action][/:id][/]',
              'constraints' => 
              array (
                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                'id' => '[0-9]+',
              ),
              'defaults' => 
              array (
                'controller' => 'Admin\\Controller\\PractitionerOrganizations',
                'action' => 'index',
              ),
            ),
          ),
          'certifications' => 
          array (
            'type' => 'Segment',
            'options' => 
            array (
              'route' => '/UserCertifications[/:action][/:id][/]',
              'constraints' => 
              array (
                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                'id' => '[0-9]+',
              ),
              'defaults' => 
              array (
                'controller' => 'Admin\\Controller\\UserCertifications',
                'action' => 'index',
              ),
            ),
          ),
          'usertype' => 
          array (
            'type' => 'Segment',
            'options' => 
            array (
              'route' => '/usertype[/:action][/:id][/]',
              'constraints' => 
              array (
                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                'id' => '[0-9]+',
              ),
              'defaults' => 
              array (
                'controller' => 'Admin\\Controller\\Usertype',
                'action' => 'index',
              ),
            ),
          ),
          'users' => 
          array (
            'type' => 'Segment',
            'options' => 
            array (
              'route' => '/users[/:action][/:id][/]',
              'constraints' => 
              array (
                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                'id' => '[0-9]+',
              ),
              'defaults' => 
              array (
                'controller' => 'Admin\\Controller\\Users',
                'action' => 'index',
              ),
            ),
          ),
          'countries' => 
          array (
            'type' => 'Segment',
            'options' => 
            array (
              'route' => '/countries[/:action][/:id][/]',
              'constraints' => 
              array (
                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                'id' => '[0-9]+',
              ),
              'defaults' => 
              array (
                'controller' => 'Admin\\Controller\\Countries',
                'action' => 'index',
              ),
            ),
          ),
          'states' => 
          array (
            'type' => 'Segment',
            'options' => 
            array (
              'route' => '/states[/:action][/:id][/]',
              'constraints' => 
              array (
                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                'id' => '[0-9]+',
              ),
              'defaults' => 
              array (
                'controller' => 'Admin\\Controller\\States',
                'action' => 'index',
              ),
            ),
          ),
          'servicecategory' => 
          array (
            'type' => 'Segment',
            'options' => 
            array (
              'route' => '/servicecategory[/:action][/:id][/]',
              'constraints' => 
              array (
                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                'id' => '[0-9]+',
              ),
              'defaults' => 
              array (
                'controller' => 'Admin\\Controller\\ServiceCategory',
                'action' => 'index',
              ),
            ),
          ),
          'services' => 
          array (
            'type' => 'Segment',
            'options' => 
            array (
              'route' => '/services[/:action][/:id][/]',
              'constraints' => 
              array (
                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                'id' => '[0-9]+',
              ),
              'defaults' => 
              array (
                'controller' => 'Admin\\Controller\\Services',
                'action' => 'index',
              ),
            ),
          ),
          'activity' => 
          array (
            'type' => 'Segment',
            'options' => 
            array (
              'route' => '/activity[/:action][/:id][/]',
              'constraints' => 
              array (
                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                'id' => '[0-9]+',
              ),
              'defaults' => 
              array (
                'controller' => 'Admin\\Controller\\Activity',
                'action' => 'index',
              ),
            ),
          ),
          'serviceproviders' => 
          array (
            'type' => 'Segment',
            'options' => 
            array (
              'route' => '/serviceproviders[/:action][/:id][/]',
              'constraints' => 
              array (
                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                'id' => '[0-9]+',
              ),
              'defaults' => 
              array (
                'controller' => 'Admin\\Controller\\ServiceProvider',
                'action' => 'index',
              ),
            ),
          ),
          'ratings' => 
          array (
            'type' => 'Segment',
            'options' => 
            array (
              'route' => '/ratings[/:action][/:user][/:createdby][/:ratingtypeid][/]',
              'constraints' => 
              array (
                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                'user' => '[0-9]+',
                'createdby' => '[0-9]+',
                'ratingtypeid' => '[0-9]+',
              ),
              'defaults' => 
              array (
                'controller' => 'Admin\\Controller\\Ratings',
                'action' => 'index',
              ),
            ),
          ),
          'feedback' => 
          array (
            'type' => 'Segment',
            'options' => 
            array (
              'route' => '/feedback[/:action][/:user][/:service][/]',
              'constraints' => 
              array (
                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                'user' => '[0-9]+',
                'service' => '[0-9]+',
              ),
              'defaults' => 
              array (
                'controller' => 'Admin\\Controller\\Feedback',
                'action' => 'index',
              ),
            ),
          ),
          'bookings' => 
          array (
            'type' => 'Segment',
            'options' => 
            array (
              'route' => '/bookings[/:action][/:id][/]',
              'constraints' => 
              array (
                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                'id' => '[0-9]+',
              ),
              'defaults' => 
              array (
                'controller' => 'Admin\\Controller\\Booking',
                'action' => 'index',
              ),
            ),
          ),
          'subscriptions' => 
          array (
            'type' => 'Segment',
            'options' => 
            array (
              'route' => '/subscriptions[/:action][/:id][/]',
              'constraints' => 
              array (
                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                'id' => '[0-9]+',
              ),
              'defaults' => 
              array (
                'controller' => 'Admin\\Controller\\Subscription',
                'action' => 'index',
              ),
            ),
          ),
          'grouprights' => 
          array (
            'type' => 'Segment',
            'options' => 
            array (
              'route' => '/GroupRights[/:action][/:id][/]',
              'constraints' => 
              array (
                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                'id' => '[0-9]+',
              ),
              'defaults' => 
              array (
                'controller' => 'Admin\\Controller\\GroupRights',
                'action' => 'index',
              ),
            ),
          ),
          'userrights' => 
          array (
            'type' => 'Segment',
            'options' => 
            array (
              'route' => '/UserRights[/:action][/:id][/]',
              'constraints' => 
              array (
                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                'id' => '[0-9]+',
              ),
              'defaults' => 
              array (
                'controller' => 'Admin\\Controller\\UserRights',
                'action' => 'index',
              ),
            ),
          ),
          'subscriptionplans' => 
          array (
            'type' => 'Segment',
            'options' => 
            array (
              'route' => '/subscriptionplans[/:action][/:id][/]',
              'constraints' => 
              array (
                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                'id' => '[0-9]+',
              ),
              'defaults' => 
              array (
                'controller' => 'Admin\\Controller\\SubscriptionPlan',
                'action' => 'index',
              ),
            ),
          ),
          'newsletters' => 
          array (
            'type' => 'Segment',
            'options' => 
            array (
              'route' => '/newsletters[/:action][/:id][/]',
              'constraints' => 
              array (
                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                'id' => '[0-9]+',
              ),
              'defaults' => 
              array (
                'controller' => 'Admin\\Controller\\Newsletter',
                'action' => 'index',
              ),
            ),
          ),
          'newslettersubscribers' => 
          array (
            'type' => 'Segment',
            'options' => 
            array (
              'route' => '/newslettersubscribers[/:action][/:id][/]',
              'constraints' => 
              array (
                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                'id' => '[0-9]+',
              ),
              'defaults' => 
              array (
                'controller' => 'Admin\\Controller\\NewsletterSubscriber',
                'action' => 'index',
              ),
            ),
          ),
          'partners' => 
          array (
            'type' => 'Segment',
            'options' => 
            array (
              'route' => '/partners[/:action][/:id][/]',
              'constraints' => 
              array (
                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                'id' => '[0-9]+',
              ),
              'defaults' => 
              array (
                'controller' => 'Admin\\Controller\\Partners',
                'action' => 'index',
              ),
            ),
          ),
          'ratingtypes' => 
          array (
            'type' => 'Segment',
            'options' => 
            array (
              'route' => '/ratingtypes[/:action][/:id][/]',
              'constraints' => 
              array (
                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                'id' => '[0-9]+',
              ),
              'defaults' => 
              array (
                'controller' => 'Admin\\Controller\\RatingType',
                'action' => 'index',
              ),
            ),
          ),
          'schools' => 
          array (
            'type' => 'Segment',
            'options' => 
            array (
              'route' => '/schools[/:action][/:id][/]',
              'constraints' => 
              array (
                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                'id' => '[0-9]+',
              ),
              'defaults' => 
              array (
                'controller' => 'Admin\\Controller\\Education',
                'action' => 'index',
              ),
            ),
          ),
          'servicelanguages' => 
          array (
            'type' => 'Segment',
            'options' => 
            array (
              'route' => '/servicelanguages[/:action][/:id][/]',
              'constraints' => 
              array (
                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                'id' => '[0-9]+',
              ),
              'defaults' => 
              array (
                'controller' => 'Admin\\Controller\\ServiceLanguage',
                'action' => 'index',
              ),
            ),
          ),
          'serviceproviderservices' => 
          array (
            'type' => 'Segment',
            'options' => 
            array (
              'route' => '/serviceproviderservices[/:action][/:id][/:service_id][/]',
              'constraints' => 
              array (
                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                'id' => '[0-9]+',
                'service_id' => '[0-9]+',
              ),
              'defaults' => 
              array (
                'controller' => 'Admin\\Controller\\ServiceProviderService',
                'action' => 'index',
              ),
            ),
          ),
          'userfeaturesetting' => 
          array (
            'type' => 'Segment',
            'options' => 
            array (
              'route' => '/userfeaturesetting[/:action][/:id][/]',
              'constraints' => 
              array (
                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                'id' => '[0-9]+',
              ),
              'defaults' => 
              array (
                'controller' => 'Admin\\Controller\\UserFeatureSetting',
                'action' => 'index',
              ),
            ),
          ),
          'advertisement' => 
          array (
            'type' => 'Segment',
            'options' => 
            array (
              'route' => '/advertisement[/:action][/:id][/]',
              'constraints' => 
              array (
                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                'id' => '[0-9]+',
              ),
              'defaults' => 
              array (
                'controller' => 'Admin\\Controller\\Advertisement',
                'action' => 'index',
              ),
            ),
          ),
          'advertisementplan' => 
          array (
            'type' => 'Segment',
            'options' => 
            array (
              'route' => '/advertisementplan[/:action][/:id][/]',
              'constraints' => 
              array (
                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                'id' => '[0-9]+',
              ),
              'defaults' => 
              array (
                'controller' => 'Admin\\Controller\\AdvertisementPlan',
                'action' => 'index',
              ),
            ),
          ),
          'bannerbookings' => 
          array (
            'type' => 'Segment',
            'options' => 
            array (
              'route' => '/bannerbookings[/:action][/:id][/]',
              'constraints' => 
              array (
                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                'id' => '[0-9]+',
              ),
              'defaults' => 
              array (
                'controller' => 'Admin\\Controller\\BannerBooking',
                'action' => 'index',
              ),
            ),
          ),
          'revenues' => 
          array (
            'type' => 'Segment',
            'options' => 
            array (
              'route' => '/revenues[/:action][/:id][/]',
              'constraints' => 
              array (
                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                'id' => '[0-9]+',
              ),
              'defaults' => 
              array (
                'controller' => 'Admin\\Controller\\Revenue',
                'action' => 'index',
              ),
            ),
          ),
          'subscriptiondurations' => 
          array (
            'type' => 'Segment',
            'options' => 
            array (
              'route' => '/subscriptiondurations[/:action][/:subscription_id][/:id][/]',
              'constraints' => 
              array (
                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                'subscription_id' => '[0-9]+',
                'id' => '[0-9]+',
              ),
              'defaults' => 
              array (
                'controller' => 'Admin\\Controller\\SubscriptionDuration',
                'action' => 'index',
              ),
            ),
          ),
          'banneruploads' => 
          array (
            'type' => 'Segment',
            'options' => 
            array (
              'route' => '/banneruploads[/:action][/:booking_id][/:id][/]',
              'constraints' => 
              array (
                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                'booking_id' => '[0-9]+',
                'id' => '[0-9]+',
              ),
              'defaults' => 
              array (
                'controller' => 'Admin\\Controller\\BannerUpload',
                'action' => 'index',
              ),
            ),
          ),
          'serviceprovidercommisions' => 
          array (
            'type' => 'Segment',
            'options' => 
            array (
              'route' => '/serviceprovidercommisions[/:action][/:user_id][/:id][/]',
              'constraints' => 
              array (
                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                'user_id' => '[0-9]+',
                'id' => '[0-9]+',
              ),
              'defaults' => 
              array (
                'controller' => 'Admin\\Controller\\ServiceProviderCommision',
                'action' => 'index',
              ),
            ),
          ),
          'media' => 
          array (
            'type' => 'Segment',
            'options' => 
            array (
              'route' => '/media[/:action][/:id][/]',
              'constraints' => 
              array (
                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                'id' => '[0-9]+',
              ),
              'defaults' => 
              array (
                'controller' => 'Admin\\Controller\\Media',
                'action' => 'index',
              ),
            ),
          ),
          'usersmedia' => 
          array (
            'type' => 'Segment',
            'options' => 
            array (
              'route' => '/usersmedia[/:action][/:user_id][/:id][/]',
              'constraints' => 
              array (
                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                'user_id' => '[0-9]+',
                'id' => '[0-9]+',
              ),
              'defaults' => 
              array (
                'controller' => 'Admin\\Controller\\UsersMedia',
                'action' => 'index',
              ),
            ),
          ),
          'serviceprovidermedia' => 
          array (
            'type' => 'Segment',
            'options' => 
            array (
              'route' => '/serviceprovidermedia[/:action][/:user_id][/:id][/]',
              'constraints' => 
              array (
                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                'user_id' => '[0-9]+',
                'id' => '[0-9]+',
              ),
              'defaults' => 
              array (
                'controller' => 'Admin\\Controller\\ServiceProviderMedia',
                'action' => 'index',
              ),
            ),
          ),
          'serviceprovideravailability' => 
          array (
            'type' => 'Segment',
            'options' => 
            array (
              'route' => '/serviceprovideravailability[/:action][/:user_id][/:id][/]',
              'constraints' => 
              array (
                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                'user_id' => '[0-9]+',
                'id' => '[0-9]+',
              ),
              'defaults' => 
              array (
                'controller' => 'Admin\\Controller\\ServiceProviderAvailability',
                'action' => 'index',
              ),
            ),
          ),
          'sitesettings' => 
          array (
            'type' => 'Segment',
            'options' => 
            array (
              'route' => '/sitesettings[/:action][/:id][/]',
              'constraints' => 
              array (
                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                'id' => '[0-9]+',
              ),
              'defaults' => 
              array (
                'controller' => 'Admin\\Controller\\SiteSettings',
                'action' => 'index',
              ),
            ),
          ),
          'sitebanner' => 
          array (
            'type' => 'Segment',
            'options' => 
            array (
              'route' => '/sitebanner[/:action][/:id][/]',
              'constraints' => 
              array (
                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                'id' => '[0-9]+',
              ),
              'defaults' => 
              array (
                'controller' => 'Admin\\Controller\\SiteBanner',
                'action' => 'index',
              ),
            ),
          ),
          'subscriptionfeatures' => 
          array (
            'type' => 'Segment',
            'options' => 
            array (
              'route' => '/subscriptionfeatures[/:action][/:id][/]',
              'constraints' => 
              array (
                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                'id' => '[0-9]+',
              ),
              'defaults' => 
              array (
                'controller' => 'Admin\\Controller\\SubscriptionFeatures',
                'action' => 'index',
              ),
            ),
          ),
          'faqindex' => 
          array (
            'type' => 'Segment',
            'options' => 
            array (
              'route' => '/faqindex[/:action][/:id][/]',
              'constraints' => 
              array (
                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                'id' => '[0-9]+',
              ),
              'defaults' => 
              array (
                'controller' => 'Admin\\Controller\\FaqIndex',
                'action' => 'index',
              ),
            ),
          ),
          'faqs' => 
          array (
            'type' => 'Segment',
            'options' => 
            array (
              'route' => '/faqs[/:action][/:id][/]',
              'constraints' => 
              array (
                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                'id' => '[0-9]+',
              ),
              'defaults' => 
              array (
                'controller' => 'Admin\\Controller\\Faqs',
                'action' => 'index',
              ),
            ),
          ),
          'login' => 
          array (
            'type' => 'Segment',
            'options' => 
            array (
              'route' => '/login[/:action][/]',
              'constraints' => 
              array (
                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                'id' => '[0-9]+',
              ),
              'defaults' => 
              array (
                'controller' => 'Admin\\Controller\\Auth',
                'action' => 'login',
              ),
            ),
          ),
          'logout' => 
          array (
            'type' => 'Segment',
            'options' => 
            array (
              'route' => '/logout[/]',
              'constraints' => 
              array (
                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                'id' => '[0-9]+',
              ),
              'defaults' => 
              array (
                'controller' => 'Admin\\Controller\\Auth',
                'action' => 'logout',
              ),
            ),
          ),
          'test' => 
          array (
            'type' => 'Segment',
            'options' => 
            array (
              'route' => '/test[/:action][/:id][/]',
              'constraints' => 
              array (
                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                'id' => '[0-9]+',
              ),
              'defaults' => 
              array (
                'controller' => 'Admin\\Controller\\Test',
                'action' => 'index',
              ),
            ),
          ),
        ),
      ),
    ),
  ),
  'service_manager' => 
  array (
    'abstract_factories' => 
    array (
      0 => 'Zend\\Cache\\Service\\StorageCacheAbstractServiceFactory',
      1 => 'Zend\\Log\\LoggerAbstractServiceFactory',
    ),
    'aliases' => 
    array (
      'translator' => 'MvcTranslator',
      'ScnSocialAuth_ZendDbAdapter' => 'Zend\\Db\\Adapter\\Adapter',
      'ScnSocialAuth_ZendSessionManager' => 'Zend\\Session\\SessionManager',
      'zfcuser_zend_db_adapter' => 'Zend\\Db\\Adapter\\Adapter',
    ),
    'factories' => 
    array (
      'navigation' => 'Zend\\Navigation\\Service\\DefaultNavigationFactory',
      'Zend\\Db\\Adapter\\Adapter' => 'Zend\\Db\\Adapter\\AdapterServiceFactory',
    ),
  ),
  'translator' => 
  array (
    'locale' => 'en_US',
    'translation_file_patterns' => 
    array (
      0 => 
      array (
        'type' => 'gettext',
        'base_dir' => '/var/www/html/ovessence/module/Application/config/../language',
        'pattern' => '%s.mo',
      ),
    ),
  ),
  'controllers' => 
  array (
    'invokables' => 
    array (
      'Application\\Controller\\Index' => 'Application\\Controller\\IndexController',
      'Application\\Controller\\Test' => 'Application\\Controller\\TestController',
      'Application\\Controller\\Page' => 'Application\\Controller\\PageController',
      'Application\\Controller\\Login' => 'Application\\Controller\\LoginController',
      'Application\\Controller\\Register' => 'Application\\Controller\\RegisterController',
      'Application\\Controller\\Logout' => 'Application\\Controller\\LogoutController',
      'Application\\Controller\\Resetpassword' => 'Application\\Controller\\ResetpasswordController',
      'Application\\Controller\\Forgetpassword' => 'Application\\Controller\\ForgetpasswordController',
      'Application\\Controller\\Partners' => 'Application\\Controller\\PartnersController',
      'Application\\Controller\\Practitioner' => 'Application\\Controller\\PractitionerController',
      'Application\\Controller\\Membership' => 'Application\\Controller\\MembershipController',
      'Application\\Controller\\Testimonials' => 'Application\\Controller\\testimonialsController',
      'Application\\Controller\\Contactus' => 'Application\\Controller\\ContactusController',
      'Application\\Controller\\Booking' => 'Application\\Controller\\BookingController',
      'Application\\Controller\\Facebooklogin' => 'Application\\Controller\\FacebookloginController',
      'Application\\Controller\\Googlelogin' => 'Application\\Controller\\GoogleloginController',
      'Application\\Controller\\Linkedinlogin' => 'Application\\Controller\\LinkedinloginController',
      'Application\\Controller\\Consumer' => 'Application\\Controller\\ConsumerController',
      'Application\\Controller\\Wishlist' => 'Application\\Controller\\WishlistController',
      'Application\\Controller\\Verification' => 'Application\\Controller\\VerificationController',
      'Application\\Controller\\Cron' => 'Application\\Controller\\CronController',
      'Application\\Controller\\ForgetUsername' => 'Application\\Controller\\ForgetUsernameController',
      'Application\\Controller\\HelpCenter' => 'Application\\Controller\\HelpCenterController',
      'Admin\\Controller\\Auth' => 'Admin\\Controller\\AuthController',
      'Admin\\Controller\\Admin' => 'Admin\\Controller\\AdminController',
      'Admin\\Controller\\Page' => 'Admin\\Controller\\PageController',
      'Admin\\Controller\\Usertype' => 'Admin\\Controller\\UsertypeController',
      'Admin\\Controller\\Users' => 'Admin\\Controller\\UsersController',
      'Admin\\Controller\\Countries' => 'Admin\\Controller\\CountriesController',
      'Admin\\Controller\\States' => 'Admin\\Controller\\StatesController',
      'Admin\\Controller\\ServiceCategory' => 'Admin\\Controller\\ServiceCategoryController',
      'Admin\\Controller\\Services' => 'Admin\\Controller\\ServicesController',
      'Admin\\Controller\\Activity' => 'Admin\\Controller\\ActivityController',
      'Admin\\Controller\\ServiceProvider' => 'Admin\\Controller\\ServiceProviderController',
      'Admin\\Controller\\Feedback' => 'Admin\\Controller\\FeedbackController',
      'Admin\\Controller\\Ratings' => 'Admin\\Controller\\RatingsController',
      'Admin\\Controller\\Consumers' => 'Admin\\Controller\\ConsumersController',
      'Admin\\Controller\\Booking' => 'Admin\\Controller\\BookingController',
      'Admin\\Controller\\Subscription' => 'Admin\\Controller\\SubscriptionController',
      'Admin\\Controller\\UserCertifications' => 'Admin\\Controller\\UserCertificationsController',
      'Admin\\Controller\\SubscriptionPlan' => 'Admin\\Controller\\SubscriptionPlanController',
      'Admin\\Controller\\Newsletter' => 'Admin\\Controller\\NewsletterController',
      'Admin\\Controller\\NewsletterSubscriber' => 'Admin\\Controller\\NewsletterSubscriberController',
      'Admin\\Controller\\Partners' => 'Admin\\Controller\\PartnersController',
      'Admin\\Controller\\PractitionerOrganizations' => 'Admin\\Controller\\PractitionerOrganizationsController',
      'Admin\\Controller\\Testimonials' => 'Admin\\Controller\\TestimonialsController',
      'Admin\\Controller\\RatingType' => 'Admin\\Controller\\RatingTypeController',
      'Admin\\Controller\\SiteModules' => 'Admin\\Controller\\SiteModulesController',
      'Admin\\Controller\\Education' => 'Admin\\Controller\\EducationController',
      'Admin\\Controller\\ServiceLanguage' => 'Admin\\Controller\\ServiceLanguageController',
      'Admin\\Controller\\ServiceProviderService' => 'Admin\\Controller\\ServiceProviderServiceController',
      'Admin\\Controller\\GroupRights' => 'Admin\\Controller\\GroupRightsController',
      'Admin\\Controller\\UserRights' => 'Admin\\Controller\\UserRightsController',
      'Admin\\Controller\\Emailtemplates' => 'Admin\\Controller\\EmailtemplatesController',
      'Admin\\Controller\\BannerBooking' => 'Admin\\Controller\\BannerBookingController',
      'Admin\\Controller\\Revenue' => 'Admin\\Controller\\RevenueController',
      'Admin\\Controller\\Messages' => 'Admin\\Controller\\MessagesController',
      'Admin\\Controller\\SubscriptionDuration' => 'Admin\\Controller\\SubscriptionDurationController',
      'Admin\\Controller\\BannerUpload' => 'Admin\\Controller\\BannerUploadController',
      'Admin\\Controller\\Sms' => 'Admin\\Controller\\SmsController',
      'Admin\\Controller\\ServiceProviderCommision' => 'Admin\\Controller\\ServiceProviderCommisionController',
      'Admin\\Controller\\SiteMeta' => 'Admin\\Controller\\SiteMetaController',
      'Admin\\Controller\\SiteActions' => 'Admin\\Controller\\SiteActionsController',
      'Admin\\Controller\\NotificationSettings' => 'Admin\\Controller\\NotificationSettingsController',
      'Admin\\Controller\\Media' => 'Admin\\Controller\\MediaController',
      'Admin\\Controller\\UsersMedia' => 'Admin\\Controller\\UsersMediaController',
      'Admin\\Controller\\ServiceProviderMedia' => 'Admin\\Controller\\ServiceProviderMediaController',
      'Admin\\Controller\\ServiceProviderAvailability' => 'Admin\\Controller\\ServiceProviderAvailabilityController',
      'Admin\\Controller\\SiteSettings' => 'Admin\\Controller\\SiteSettingsController',
      'Admin\\Controller\\Test' => 'Admin\\Controller\\TestController',
      'Admin\\Controller\\UserFeatureSetting' => 'Admin\\Controller\\UserFeatureSettingController',
      'Admin\\Controller\\SiteBanner' => 'Admin\\Controller\\SiteBannerController',
      'Admin\\Controller\\SubscriptionFeatures' => 'Admin\\Controller\\SubscriptionFeaturesController',
      'Admin\\Controller\\FaqIndex' => 'Admin\\Controller\\FaqIndexController',
      'Admin\\Controller\\Faqs' => 'Admin\\Controller\\FaqsController',
      'Admin\\Controller\\Advertisement' => 'Admin\\Controller\\AdvertisementController',
      'Admin\\Controller\\AdvertisementPlan' => 'Admin\\Controller\\AdvertisementPlanController',
    ),
  ),
  'view_manager' => 
  array (
    'display_not_found_reason' => true,
    'display_exceptions' => true,
    'doctype' => 'HTML5',
    'not_found_template' => 'error/404',
    'exception_template' => 'error/index',
    'template_map' => 
    array (
      'layout/layout' => '/var/www/html/ovessence/module/Application/config/../view/layout/layout.phtml',
      'application/index/index' => '/var/www/html/ovessence/module/Application/config/../view/application/index/index.phtml',
      'error/404' => '/var/www/html/ovessence/module/Application/config/../view/error/404.phtml',
      'error/index' => '/var/www/html/ovessence/module/Application/config/../view/error/index.phtml',
    ),
    'template_path_stack' => 
    array (
      0 => '/var/www/html/ovessence/module/Application/config/../view',
      'Admin' => '/var/www/html/ovessence/module/Admin/config/../view',
      'pages' => '/var/www/html/ovessence/module/Admin/config/../view',
    ),
    'base_path' => '//ovessence.loc/',
  ),
  'console' => 
  array (
    'router' => 
    array (
      'routes' => 
      array (
      ),
    ),
  ),
  'GMaps' => 
  array (
    'api_key' => 'AIzaSyC4541bKcpmGkAB_s-aTDvX1mQdfPtfnJA',
  ),
  'navigation' => 
  array (
    'default' => 
    array (
      0 => 
      array (
        'label' => 'Admin',
        'route' => 'admin',
      ),
      1 => 
      array (
        'label' => 'Admin',
        'route' => 'admin/default',
      ),
      2 => 
      array (
        'label' => 'Admin',
        'route' => 'admin/page',
        'pages' => 
        array (
          0 => 
          array (
            'label' => 'Pages',
            'route' => 'admin/page',
            'pages' => 
            array (
              0 => 
              array (
                'label' => 'Add Page',
                'route' => 'admin/page',
                'action' => 'add',
              ),
              1 => 
              array (
                'label' => 'Edit Page',
                'route' => 'admin/page',
                'action' => 'edit',
              ),
            ),
          ),
        ),
      ),
      3 => 
      array (
        'label' => 'Admin',
        'route' => 'admin/sitemeta',
        'pages' => 
        array (
          0 => 
          array (
            'label' => 'Site Meta',
            'route' => 'admin/sitemeta',
            'pages' => 
            array (
              0 => 
              array (
                'label' => 'Add Meta',
                'route' => 'admin/sitemeta',
                'action' => 'add',
              ),
              1 => 
              array (
                'label' => 'Edit Meta',
                'route' => 'admin/sitemeta',
                'action' => 'edit',
              ),
            ),
          ),
        ),
      ),
      4 => 
      array (
        'label' => 'Admin',
        'route' => 'admin/sitactions',
        'pages' => 
        array (
          0 => 
          array (
            'label' => 'Site Actions',
            'route' => 'admin/siteactions',
            'pages' => 
            array (
              0 => 
              array (
                'label' => 'Add Actions',
                'route' => 'admin/siteactions',
                'action' => 'add',
              ),
              1 => 
              array (
                'label' => 'Edit Actions',
                'route' => 'admin/siteactions',
                'action' => 'edit',
              ),
            ),
          ),
        ),
      ),
      5 => 
      array (
        'label' => 'Admin',
        'route' => 'admin/notificationsettings',
        'pages' => 
        array (
          0 => 
          array (
            'label' => 'Notification Settings',
            'route' => 'admin/notificationsettings',
            'pages' => 
            array (
              0 => 
              array (
                'label' => 'Add Notification',
                'route' => 'admin/notificationsettings',
                'action' => 'add',
              ),
              1 => 
              array (
                'label' => 'Edit Notifications',
                'route' => 'admin/notificationsettings',
                'action' => 'edit',
              ),
            ),
          ),
        ),
      ),
      6 => 
      array (
        'label' => 'Admin',
        'route' => 'admin/sms',
        'pages' => 
        array (
          0 => 
          array (
            'label' => 'Sms Templates',
            'route' => 'admin/sms',
            'pages' => 
            array (
              0 => 
              array (
                'label' => 'Add sms Templates',
                'route' => 'admin/sms',
                'action' => 'add',
              ),
              1 => 
              array (
                'label' => 'Edit Sms Templates',
                'route' => 'admin/sms',
                'action' => 'edit',
              ),
            ),
          ),
        ),
      ),
      7 => 
      array (
        'label' => 'Admin',
        'route' => 'admin/emailtemplates',
        'pages' => 
        array (
          0 => 
          array (
            'label' => 'Email Templates',
            'route' => 'admin/emailtemplates',
            'pages' => 
            array (
              0 => 
              array (
                'label' => 'Add Email Templates',
                'route' => 'admin/emailtemplates',
                'action' => 'add',
              ),
              1 => 
              array (
                'label' => 'Edit Email Templates',
                'route' => 'admin/emailtemplates',
                'action' => 'edit',
              ),
            ),
          ),
        ),
      ),
      8 => 
      array (
        'label' => 'Admin',
        'route' => 'admin/consumers',
        'pages' => 
        array (
          0 => 
          array (
            'label' => 'Consumers',
            'route' => 'admin/consumers',
            'pages' => 
            array (
              0 => 
              array (
                'label' => 'Add Consumer',
                'route' => 'admin/consumer',
                'action' => 'add',
              ),
              1 => 
              array (
                'label' => 'Edit Consumer',
                'route' => 'admin/consumer',
                'action' => 'edit',
              ),
            ),
          ),
        ),
      ),
      9 => 
      array (
        'label' => 'Admin',
        'route' => 'admin/certifications',
        'pages' => 
        array (
          0 => 
          array (
            'label' => 'User Certifications',
            'route' => 'admin/certifications',
            'pages' => 
            array (
              0 => 
              array (
                'label' => 'Add Certifications',
                'route' => 'admin/certifications',
                'action' => 'add',
              ),
              1 => 
              array (
                'label' => 'Edit Certifications',
                'route' => 'admin/certifications',
                'action' => 'edit',
              ),
            ),
          ),
        ),
      ),
      10 => 
      array (
        'label' => 'Admin',
        'route' => 'admin/testimonials',
        'pages' => 
        array (
          0 => 
          array (
            'label' => 'Testimonials',
            'route' => 'admin/testimonials',
            'pages' => 
            array (
              0 => 
              array (
                'label' => 'Add Testimonial',
                'route' => 'admin/testimonials',
                'action' => 'add',
              ),
              1 => 
              array (
                'label' => 'Edit Testimonials',
                'route' => 'admin/testimonials',
                'action' => 'edit',
              ),
            ),
          ),
        ),
      ),
      11 => 
      array (
        'label' => 'Admin',
        'route' => 'admin/sitemodules',
        'pages' => 
        array (
          0 => 
          array (
            'label' => 'Site Modules',
            'route' => 'admin/sitemodules',
            'pages' => 
            array (
              0 => 
              array (
                'label' => 'Add Module',
                'route' => 'admin/sitemodules',
                'action' => 'add',
              ),
              1 => 
              array (
                'label' => 'Edit Module',
                'route' => 'admin/sitemodules',
                'action' => 'edit',
              ),
            ),
          ),
        ),
      ),
      12 => 
      array (
        'label' => 'Admin',
        'route' => 'admin/organizations',
        'pages' => 
        array (
          0 => 
          array (
            'label' => 'Practitioner\'s Organization',
            'route' => 'admin/organizations',
            'pages' => 
            array (
              0 => 
              array (
                'label' => 'Add Organization',
                'route' => 'admin/organizations',
                'action' => 'add',
              ),
              1 => 
              array (
                'label' => 'Edit Organizations',
                'route' => 'admin/organization',
                'action' => 'edit',
              ),
            ),
          ),
        ),
      ),
      13 => 
      array (
        'label' => 'Admin',
        'route' => 'admin/usertype',
        'pages' => 
        array (
          0 => 
          array (
            'label' => 'User Types',
            'route' => 'admin/usertype',
            'pages' => 
            array (
              0 => 
              array (
                'label' => 'Add User Type',
                'route' => 'admin/usertype',
                'action' => 'add',
              ),
              1 => 
              array (
                'label' => 'Edit User Type',
                'route' => 'admin/usertype',
                'action' => 'edit',
              ),
            ),
          ),
        ),
      ),
      14 => 
      array (
        'label' => 'Admin',
        'route' => 'admin/users',
        'pages' => 
        array (
          0 => 
          array (
            'label' => 'Users',
            'route' => 'admin/users',
            'pages' => 
            array (
              0 => 
              array (
                'label' => 'Add User',
                'route' => 'admin/users',
                'action' => 'add',
              ),
              1 => 
              array (
                'label' => 'Edit User',
                'route' => 'admin/users',
                'action' => 'edit',
              ),
            ),
          ),
        ),
      ),
      15 => 
      array (
        'label' => 'Admin',
        'route' => 'admin/countries',
        'pages' => 
        array (
          0 => 
          array (
            'label' => 'Countries',
            'route' => 'admin/countries',
            'pages' => 
            array (
              0 => 
              array (
                'label' => 'Add Country',
                'route' => 'admin/countries',
                'action' => 'add',
              ),
              1 => 
              array (
                'label' => 'Edit Country',
                'route' => 'admin/countries',
                'action' => 'edit',
              ),
            ),
          ),
        ),
      ),
      16 => 
      array (
        'label' => 'Admin',
        'route' => 'admin/states',
        'pages' => 
        array (
          0 => 
          array (
            'label' => 'States',
            'route' => 'admin/states',
            'pages' => 
            array (
              0 => 
              array (
                'label' => 'Add State',
                'route' => 'admin/states',
                'action' => 'add',
              ),
              1 => 
              array (
                'label' => 'Edit State',
                'route' => 'admin/states',
                'action' => 'edit',
              ),
            ),
          ),
        ),
      ),
      17 => 
      array (
        'label' => 'Admin',
        'route' => 'admin/servicecategory',
        'pages' => 
        array (
          0 => 
          array (
            'label' => 'Service Categories',
            'route' => 'admin/servicecategory',
            'pages' => 
            array (
              0 => 
              array (
                'label' => 'Add Service Category',
                'route' => 'admin/servicecategory',
                'action' => 'add',
              ),
              1 => 
              array (
                'label' => 'Edit Service Category',
                'route' => 'admin/servicecategory',
                'action' => 'edit',
              ),
            ),
          ),
        ),
      ),
      18 => 
      array (
        'label' => 'Admin',
        'route' => 'admin/services',
        'pages' => 
        array (
          0 => 
          array (
            'label' => 'Services',
            'route' => 'admin/services',
            'pages' => 
            array (
              0 => 
              array (
                'label' => 'Add Service',
                'route' => 'admin/services',
                'action' => 'add',
              ),
              1 => 
              array (
                'label' => 'Edit Service',
                'route' => 'admin/services',
                'action' => 'edit',
              ),
            ),
          ),
        ),
      ),
      19 => 
      array (
        'label' => 'Admin',
        'route' => 'admin/activity',
        'pages' => 
        array (
          0 => 
          array (
            'label' => 'Activities',
            'route' => 'admin/activity',
            'pages' => 
            array (
              0 => 
              array (
                'label' => 'Add Activity',
                'route' => 'admin/activity',
                'action' => 'add',
              ),
              1 => 
              array (
                'label' => 'Edit Activity',
                'route' => 'admin/activity',
                'action' => 'edit',
              ),
            ),
          ),
        ),
      ),
      20 => 
      array (
        'label' => 'Admin',
        'route' => 'admin/serviceproviders',
        'pages' => 
        array (
          0 => 
          array (
            'label' => 'Service Providers',
            'route' => 'admin/serviceproviders',
            'pages' => 
            array (
              0 => 
              array (
                'label' => 'Add Service Provider',
                'route' => 'admin/serviceproviders',
                'action' => 'add',
              ),
              1 => 
              array (
                'label' => 'Edit Service Provider',
                'route' => 'admin/serviceproviders',
                'action' => 'edit',
              ),
            ),
          ),
        ),
      ),
      21 => 
      array (
        'label' => 'Admin',
        'route' => 'admin/userfeaturesetting',
        'pages' => 
        array (
          0 => 
          array (
            'label' => 'Feature Setting',
            'route' => 'admin/userfeaturesetting',
            'pages' => 
            array (
              0 => 
              array (
                'label' => 'Add Service Provider',
                'route' => 'admin/serviceproviders',
                'action' => 'add',
              ),
              1 => 
              array (
                'label' => 'Edit Service Provider',
                'route' => 'admin/serviceproviders',
                'action' => 'edit',
              ),
            ),
          ),
        ),
      ),
      22 => 
      array (
        'label' => 'Admin',
        'route' => 'admin/grouprights',
        'pages' => 
        array (
          0 => 
          array (
            'label' => 'Group Rights',
            'route' => 'admin/grouprighs',
            'pages' => 
            array (
              0 => 
              array (
                'label' => 'Add gropu Rights',
                'route' => 'admin/grouprights',
                'action' => 'add',
              ),
              1 => 
              array (
                'label' => 'Edit Group Rights',
                'route' => 'admin/grouprights',
                'action' => 'edit',
              ),
            ),
          ),
        ),
      ),
      23 => 
      array (
        'label' => 'Admin',
        'route' => 'admin/userrights',
        'pages' => 
        array (
          0 => 
          array (
            'label' => 'User Rights',
            'route' => 'admin/userrights',
            'pages' => 
            array (
              0 => 
              array (
                'label' => 'Manage User Rights',
                'route' => 'admin/userrights',
                'action' => 'assign',
              ),
            ),
          ),
        ),
      ),
      24 => 
      array (
        'label' => 'Admin',
        'route' => 'admin/ratings',
        'pages' => 
        array (
          0 => 
          array (
            'label' => 'Ratings',
            'route' => 'admin/ratings',
            'pages' => 
            array (
              0 => 
              array (
                'label' => 'Add Rating',
                'route' => 'admin/ratings',
                'action' => 'add',
              ),
              1 => 
              array (
                'label' => 'Edit Rating',
                'route' => 'admin/ratings',
                'action' => 'edit',
              ),
            ),
          ),
        ),
      ),
      25 => 
      array (
        'label' => 'Admin',
        'route' => 'admin/feedback',
        'pages' => 
        array (
          0 => 
          array (
            'label' => 'Feedbacks',
            'route' => 'admin/feedback',
            'pages' => 
            array (
              0 => 
              array (
                'label' => 'Add Feedback',
                'route' => 'admin/feedback',
                'action' => 'add',
              ),
              1 => 
              array (
                'label' => 'Edit Feedback',
                'route' => 'admin/feedback',
                'action' => 'edit',
              ),
            ),
          ),
        ),
      ),
      26 => 
      array (
        'label' => 'Admin',
        'route' => 'admin/bookings',
        'pages' => 
        array (
          0 => 
          array (
            'label' => 'Bookings',
            'route' => 'admin/bookings',
            'pages' => 
            array (
              0 => 
              array (
                'label' => 'Add Booking',
                'route' => 'admin/bookings',
                'action' => 'add',
              ),
              1 => 
              array (
                'label' => 'Edit Booking',
                'route' => 'admin/bookings',
                'action' => 'edit',
              ),
              2 => 
              array (
                'label' => 'Reschedule Booking',
                'route' => 'admin/bookings',
                'action' => 'reschedule',
              ),
            ),
          ),
        ),
      ),
      27 => 
      array (
        'label' => 'Admin',
        'route' => 'admin/subscriptions',
        'pages' => 
        array (
          0 => 
          array (
            'label' => 'Subscriptions',
            'route' => 'admin/subscriptions',
            'pages' => 
            array (
              0 => 
              array (
                'label' => 'Add Subscription',
                'route' => 'admin/subscriptions',
                'action' => 'add',
              ),
              1 => 
              array (
                'label' => 'Edit Subscription',
                'route' => 'admin/subscriptions',
                'action' => 'edit',
              ),
            ),
          ),
        ),
      ),
      28 => 
      array (
        'label' => 'Admin',
        'route' => 'admin/subscriptionplans',
        'pages' => 
        array (
          0 => 
          array (
            'label' => 'Subscription Plans',
            'route' => 'admin/subscriptionplans',
            'pages' => 
            array (
              0 => 
              array (
                'label' => 'Add Subscription Plan',
                'route' => 'admin/subscriptionplans',
                'action' => 'add',
              ),
              1 => 
              array (
                'label' => 'Edit Subscription Plan',
                'route' => 'admin/subscriptionplans',
                'action' => 'edit',
              ),
            ),
          ),
        ),
      ),
      29 => 
      array (
        'label' => 'Admin',
        'route' => 'admin/newsletters',
        'pages' => 
        array (
          0 => 
          array (
            'label' => 'Newsletters',
            'route' => 'admin/newsletters',
            'pages' => 
            array (
              0 => 
              array (
                'label' => 'Add Newsletter',
                'route' => 'admin/newsletters',
                'action' => 'add',
              ),
              1 => 
              array (
                'label' => 'Edit Newsletter',
                'route' => 'admin/newsletters',
                'action' => 'edit',
              ),
            ),
          ),
        ),
      ),
      30 => 
      array (
        'label' => 'Admin',
        'route' => 'admin/newslettersubscribers',
        'pages' => 
        array (
          0 => 
          array (
            'label' => 'Newsletter Subscribers',
            'route' => 'admin/newslettersubscribers',
            'pages' => 
            array (
              0 => 
              array (
                'label' => 'Add Newsletter Subscriber',
                'route' => 'admin/newslettersubscribers',
                'action' => 'add',
              ),
              1 => 
              array (
                'label' => 'Edit Newsletter Subscriber',
                'route' => 'admin/newslettersubscribers',
                'action' => 'edit',
              ),
            ),
          ),
        ),
      ),
      31 => 
      array (
        'label' => 'Admin',
        'route' => 'admin/partners',
        'pages' => 
        array (
          0 => 
          array (
            'label' => 'Partners',
            'route' => 'admin/partners',
            'pages' => 
            array (
              0 => 
              array (
                'label' => 'Add Parner',
                'route' => 'admin/parners',
                'action' => 'add',
              ),
              1 => 
              array (
                'label' => 'Edit Partner',
                'route' => 'admin/partners',
                'action' => 'edit',
              ),
            ),
          ),
        ),
      ),
      32 => 
      array (
        'label' => 'Admin',
        'route' => 'admin/ratingtypes',
        'pages' => 
        array (
          0 => 
          array (
            'label' => 'Rating Types',
            'route' => 'admin/ratingtypes',
            'pages' => 
            array (
              0 => 
              array (
                'label' => 'Add Rating Type',
                'route' => 'admin/ratingtypes',
                'action' => 'add',
              ),
              1 => 
              array (
                'label' => 'Edit Rating Type',
                'route' => 'admin/ratingtypes',
                'action' => 'edit',
              ),
            ),
          ),
        ),
      ),
      33 => 
      array (
        'label' => 'Admin',
        'route' => 'admin/schools',
        'pages' => 
        array (
          0 => 
          array (
            'label' => 'Schools',
            'route' => 'admin/schools',
            'pages' => 
            array (
              0 => 
              array (
                'label' => 'Add Schools',
                'route' => 'admin/schools',
                'action' => 'add',
              ),
              1 => 
              array (
                'label' => 'Edit Schools',
                'route' => 'admin/schools',
                'action' => 'edit',
              ),
            ),
          ),
        ),
      ),
      34 => 
      array (
        'label' => 'Admin',
        'route' => 'admin/servicelanguages',
        'pages' => 
        array (
          0 => 
          array (
            'label' => 'Service Languages',
            'route' => 'admin/servicelanguages',
            'pages' => 
            array (
              0 => 
              array (
                'label' => 'Add Service Language',
                'route' => 'admin/servicelanguages',
                'action' => 'add',
              ),
              1 => 
              array (
                'label' => 'Edit Service Language',
                'route' => 'admin/servicelanguages',
                'action' => 'edit',
              ),
            ),
          ),
        ),
      ),
      35 => 
      array (
        'label' => 'Admin',
        'route' => 'admin/serviceproviderservices',
        'pages' => 
        array (
          0 => 
          array (
            'label' => 'Service Provider Services',
            'route' => 'admin/serviceproviderservices',
            'pages' => 
            array (
              0 => 
              array (
                'label' => 'Add Service Provider Service',
                'route' => 'admin/serviceproviderservices',
                'action' => 'add',
              ),
              1 => 
              array (
                'label' => 'Edit Service Provider Service',
                'route' => 'admin/serviceproviderservices',
                'action' => 'edit',
              ),
            ),
          ),
        ),
      ),
      36 => 
      array (
        'label' => 'Admin',
        'route' => 'admin/messages',
        'pages' => 
        array (
          0 => 
          array (
            'label' => 'Messages',
            'route' => 'admin/messages',
            'pages' => 
            array (
              0 => 
              array (
                'label' => 'Index Messages',
                'route' => 'admin/messages',
                'action' => 'index',
              ),
              1 => 
              array (
                'label' => 'Inbox Messages',
                'route' => 'admin/messages',
                'action' => 'inbox',
              ),
              2 => 
              array (
                'label' => 'Outbox Messages',
                'route' => 'admin/messages',
                'action' => 'outbox',
              ),
              3 => 
              array (
                'label' => 'Trash Messages',
                'route' => 'admin/messages',
                'action' => 'trash',
              ),
              4 => 
              array (
                'label' => 'Compose Messages',
                'route' => 'admin/messages',
                'action' => 'compose',
              ),
            ),
          ),
        ),
      ),
      37 => 
      array (
        'label' => 'Admin',
        'route' => 'admin/advertisementplan',
        'pages' => 
        array (
          0 => 
          array (
            'label' => 'Advertisement Plans',
            'route' => 'admin/advertisementplan',
            'pages' => 
            array (
              0 => 
              array (
                'label' => 'Add Advertisement Plan',
                'route' => 'admin/advertisementplan',
                'action' => 'add',
              ),
              1 => 
              array (
                'label' => 'Edit Advertisement Plan',
                'route' => 'admin/advertisementplan',
                'action' => 'edit',
              ),
            ),
          ),
        ),
      ),
      38 => 
      array (
        'label' => 'Admin',
        'route' => 'admin/advertisement',
        'pages' => 
        array (
          0 => 
          array (
            'label' => 'Advertisements',
            'route' => 'admin/advertisement',
            'pages' => 
            array (
              0 => 
              array (
                'label' => 'Add Advertisement',
                'route' => 'admin/advertisement',
                'action' => 'add',
              ),
              1 => 
              array (
                'label' => 'Edit Advertisement',
                'route' => 'admin/advertisement',
                'action' => 'edit',
              ),
            ),
          ),
        ),
      ),
      39 => 
      array (
        'label' => 'Admin',
        'route' => 'admin/bannerbookings',
        'pages' => 
        array (
          0 => 
          array (
            'label' => 'Banner Bookings',
            'route' => 'admin/bannerbookings',
            'pages' => 
            array (
              0 => 
              array (
                'label' => 'Add Banner Booking',
                'route' => 'admin/bannerbookings',
                'action' => 'add',
              ),
              1 => 
              array (
                'label' => 'Edit Banner Booking',
                'route' => 'admin/bannerbookings',
                'action' => 'edit',
              ),
            ),
          ),
        ),
      ),
      40 => 
      array (
        'label' => 'Admin',
        'route' => 'admin/revenues',
        'pages' => 
        array (
          0 => 
          array (
            'label' => 'Payment History',
            'route' => 'admin/revenues',
            'pages' => 
            array (
            ),
          ),
        ),
      ),
      41 => 
      array (
        'label' => 'Admin',
        'route' => 'admin/subscriptiondurations',
        'pages' => 
        array (
          0 => 
          array (
            'label' => 'Subscription Durations',
            'route' => 'admin/subscriptiondurations',
            'pages' => 
            array (
              0 => 
              array (
                'label' => 'Add Subscription Duration',
                'route' => 'admin/subscriptiondurations',
                'action' => 'add',
              ),
              1 => 
              array (
                'label' => 'Edit Subscription Duration',
                'route' => 'admin/subscriptiondurations',
                'action' => 'edit',
              ),
            ),
          ),
        ),
      ),
      42 => 
      array (
        'label' => 'Admin',
        'route' => 'admin/banneruploads',
        'pages' => 
        array (
          0 => 
          array (
            'label' => 'Uploaded Banners',
            'route' => 'admin/banneruploads',
            'pages' => 
            array (
              0 => 
              array (
                'label' => 'Upload Banner',
                'route' => 'admin/banneruploads',
                'action' => 'add',
              ),
              1 => 
              array (
                'label' => 'Edit Banner',
                'route' => 'admin/banneruploads',
                'action' => 'edit',
              ),
            ),
          ),
        ),
      ),
      43 => 
      array (
        'label' => 'Admin',
        'route' => 'admin/serviceprovidercommisions',
        'pages' => 
        array (
          0 => 
          array (
            'label' => 'Service Provider Commissions',
            'route' => 'admin/serviceprovidercommisions',
            'pages' => 
            array (
              0 => 
              array (
                'label' => 'Add Service Provider Commission',
                'route' => 'admin/serviceprovidercommisions',
                'action' => 'add',
              ),
              1 => 
              array (
                'label' => 'Edit Service Provider Commission',
                'route' => 'admin/serviceprovidercommisionss',
                'action' => 'edit',
              ),
            ),
          ),
        ),
      ),
      44 => 
      array (
        'label' => 'Admin',
        'route' => 'admin/media',
        'pages' => 
        array (
          0 => 
          array (
            'label' => 'Media',
            'route' => 'admin/media',
            'pages' => 
            array (
              0 => 
              array (
                'label' => 'Add Media',
                'route' => 'admin/media',
                'action' => 'add',
              ),
              1 => 
              array (
                'label' => 'Edit Media',
                'route' => 'admin/media',
                'action' => 'edit',
              ),
            ),
          ),
        ),
      ),
      45 => 
      array (
        'label' => 'Admin',
        'route' => 'admin/usersmedia',
        'pages' => 
        array (
          0 => 
          array (
            'label' => 'Users Media',
            'route' => 'admin/usersmedia',
            'pages' => 
            array (
              0 => 
              array (
                'label' => 'Add Users Media',
                'route' => 'admin/usersmedia',
                'action' => 'add',
              ),
              1 => 
              array (
                'label' => 'Edit Users Media',
                'route' => 'admin/usersmedia',
                'action' => 'edit',
              ),
            ),
          ),
        ),
      ),
      46 => 
      array (
        'label' => 'Admin',
        'route' => 'admin/serviceprovidermedia',
        'pages' => 
        array (
          0 => 
          array (
            'label' => 'Service Provider Media',
            'route' => 'admin/serviceprovidermedia',
            'pages' => 
            array (
              0 => 
              array (
                'label' => 'Add Service Provider Media',
                'route' => 'admin/serviceprovidermedia',
                'action' => 'add',
              ),
              1 => 
              array (
                'label' => 'Edit Service Provider Media',
                'route' => 'admin/serviceprovidermedia',
                'action' => 'edit',
              ),
            ),
          ),
        ),
      ),
      47 => 
      array (
        'label' => 'Admin',
        'route' => 'admin/serviceprovideravailability',
        'pages' => 
        array (
          0 => 
          array (
            'label' => 'Service Provider Availability',
            'route' => 'admin/serviceprovideravailability',
            'pages' => 
            array (
              0 => 
              array (
                'label' => 'Add Service Provider Availability',
                'route' => 'admin/serviceprovideravailability',
                'action' => 'add',
              ),
              1 => 
              array (
                'label' => 'Edit Service Provider Availability',
                'route' => 'admin/serviceprovideravailability',
                'action' => 'edit',
              ),
            ),
          ),
        ),
      ),
      48 => 
      array (
        'label' => 'Admin',
        'route' => 'admin/sitesettings',
        'pages' => 
        array (
          0 => 
          array (
            'label' => 'Site Setting',
            'route' => 'admin/sitesettings',
            'pages' => 
            array (
              0 => 
              array (
                'label' => 'Add Site Setting',
                'route' => 'admin/sitesettings',
                'action' => 'add',
              ),
              1 => 
              array (
                'label' => 'Edit Site Setting',
                'route' => 'admin/sitesettings',
                'action' => 'edit',
              ),
            ),
          ),
        ),
      ),
      49 => 
      array (
        'label' => 'Admin',
        'route' => 'admin/sitebanner',
        'pages' => 
        array (
          0 => 
          array (
            'label' => 'Site Banners',
            'route' => 'admin/sitebanner',
            'pages' => 
            array (
              0 => 
              array (
                'label' => 'Add Site Banner',
                'route' => 'admin/sitebanner',
                'action' => 'add',
              ),
              1 => 
              array (
                'label' => 'Edit Site Banner',
                'route' => 'admin/sitebanner',
                'action' => 'edit',
              ),
            ),
          ),
        ),
      ),
      50 => 
      array (
        'label' => 'Admin',
        'route' => 'admin/subscriptionfeatures',
        'pages' => 
        array (
          0 => 
          array (
            'label' => 'Subscription Features',
            'route' => 'admin/subscriptionfeatures',
            'pages' => 
            array (
              0 => 
              array (
                'label' => 'Add Subscription Feature',
                'route' => 'admin/subscriptionfeatures',
                'action' => 'add',
              ),
              1 => 
              array (
                'label' => 'Edit Subscription Feature',
                'route' => 'admin/subscriptionfeatures',
                'action' => 'edit',
              ),
            ),
          ),
        ),
      ),
      51 => 
      array (
        'label' => 'Admin',
        'route' => 'admin/faqindex',
        'pages' => 
        array (
          0 => 
          array (
            'label' => 'Faq Index',
            'route' => 'admin/faqindex',
            'pages' => 
            array (
              0 => 
              array (
                'label' => 'Add Faq Index',
                'route' => 'admin/faqindex',
                'action' => 'add',
              ),
              1 => 
              array (
                'label' => 'Edit Faq Index',
                'route' => 'admin/faqindex',
                'action' => 'edit',
              ),
            ),
          ),
        ),
      ),
      52 => 
      array (
        'label' => 'Admin',
        'route' => 'admin/faqs',
        'pages' => 
        array (
          0 => 
          array (
            'label' => 'Faqs',
            'route' => 'admin/faqs',
            'pages' => 
            array (
              0 => 
              array (
                'label' => 'Add Faq',
                'route' => 'admin/faqs',
                'action' => 'add',
              ),
              1 => 
              array (
                'label' => 'Edit Faq',
                'route' => 'admin/faqs',
                'action' => 'edit',
              ),
            ),
          ),
        ),
      ),
      53 => 
      array (
        'label' => 'Admin',
        'route' => 'admin/test',
        'pages' => 
        array (
          0 => 
          array (
            'label' => 'Test',
            'route' => 'admin/test',
            'pages' => 
            array (
              0 => 
              array (
                'label' => 'Add Test',
                'route' => 'admin/test',
                'action' => 'add',
              ),
              1 => 
              array (
                'label' => 'Edit Test',
                'route' => 'admin/test',
                'action' => 'edit',
              ),
            ),
          ),
        ),
      ),
    ),
  ),
  'db' => 
  array (
    'driver' => 'Pdo',
    'dsn' => 'mysql:dbname=OvEssenCe;host=localhost',
    'driver_options' => 
    array (
      1002 => 'SET NAMES \'UTF8\'',
    ),
    'username' => 'root',
    'password' => 'tech',
  ),
  'payment_methods' => 
  array (
    1 => 'Visa',
    2 => 'Mastercard',
    3 => 'Amex',
  ),
  'module_layouts' => 
  array (
    'Admin' => 'layout/layout.phtml',
  ),
  'session' => 
  array (
    'config' => 
    array (
      'class' => 'Zend\\Session\\Config\\SessionConfig',
      'options' => 
      array (
        'name' => 'myapp',
      ),
    ),
    'storage' => 'Zend\\Session\\Storage\\SessionArrayStorage',
    'validators' => 
    array (
      0 => 
      array (
        0 => 'Zend\\Session\\Validator\\RemoteAddr',
        1 => 'Zend\\Session\\Validator\\HttpUserAgent',
      ),
    ),
  ),
  'Twilio' => 
  array (
    'sid' => 'AC74f128f90e7423cb8fe553c3dd3c25de',
    'token' => '74d76e9e12b1d5935a9a031fffa5d62c',
    'fromNumber' => '+14387939015',
  ),
  'Vimeo' => 
  array (
    'clientId' => 'e19e9bce5bb95d7b8e0fc5ef61feb6582d3c9e19',
    'clientSecrate' => 'cb64548284bd805d4a5286b9fa731d3c124d98dc',
  ),
  'payment_gateway' => 
  array (
    'tree_env' => 'sandbox',
    'merchant_id' => 'spny72gz8fjpnnzd',
    'public_key' => 'nkbzbdzxcsq3cndk',
    'private_key' => 'fa337621fe7eb9e1542349c383de66d3',
  ),
  'api_url' => 
  array (
    'value' => 'http://localhost:8000',
  ),
  'fb_keys' => 
  array (
    'appId' => '224663037743916',
    'secret' => '3c900e668a44869092ab049fcf718cc9',
    'cookie' => true,
  ),
  'gplus_keys' => 
  array (
    'google_client_id' => '942495162904-biub13g4nljjcqfoe1m8qm7uq2udks7j.apps.googleusercontent.com',
    'google_client_secret' => 'c2PZhAw_LBjJvUYpCM0wr8hm',
    'google_redirect_url' => 'http://dev.clavax.us/ovessence/public/googlelogin',
    'google_developer_key' => 'AIzaSyBkdptmFQ88B0ruH-ZLxgfqOH4FW3DzPmQ',
  ),
  'linkedin_keys' => 
  array (
    'aapId' => '753odaa2ldz7ij',
    'app_secret' => 'qzcRPIHc830RfcT7',
  ),
  'basepath' => 
  array (
    'url' => 'http://ovessence.loc/',
  ),
  'chatpath' => 
  array (
    'url' => 'http://ovessence.loc/livechat/',
  ),
  'scn-social-auth' => 
  array (
    'facebook_enabled' => true,
    'facebook_scope' => 'public_profile, email',
    'facebook_display' => 'popup',
    'linkedIn_enabled' => true,
    'twitter_enabled' => true,
  ),
  'google_analytics' => 
  array (
    'id' => 'UA-50945302-1',
    'domain_name' => 'http://dev.clavax.us/ovessence',
    'allow_linker' => true,
    'enable' => true,
  ),
  'zfcuser' => 
  array (
    'auth_adapters' => 
    array (
      100 => 'ZfcUser\\Authentication\\Adapter\\Db',
    ),
  ),
);