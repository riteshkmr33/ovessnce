<?php

ini_set('display_errors', 1);
return array(
    'controllers' => array(
        'invokables' => array(
            'Admin\Controller\Auth' => 'Admin\Controller\AuthController',
            'Admin\Controller\Admin' => 'Admin\Controller\AdminController',
            'Admin\Controller\Page' => 'Admin\Controller\PageController',
            'Admin\Controller\Usertype' => 'Admin\Controller\UsertypeController',
            'Admin\Controller\Users' => 'Admin\Controller\UsersController',
            'Admin\Controller\Countries' => 'Admin\Controller\CountriesController',
            'Admin\Controller\States' => 'Admin\Controller\StatesController',
            'Admin\Controller\ServiceCategory' => 'Admin\Controller\ServiceCategoryController',
            'Admin\Controller\Services' => 'Admin\Controller\ServicesController',
            'Admin\Controller\Activity' => 'Admin\Controller\ActivityController',
            'Admin\Controller\ServiceProvider' => 'Admin\Controller\ServiceProviderController',
            'Admin\Controller\Feedback' => 'Admin\Controller\FeedbackController',
            'Admin\Controller\Ratings' => 'Admin\Controller\RatingsController',
            'Admin\Controller\Consumers' => 'Admin\Controller\ConsumersController',
            'Admin\Controller\Booking' => 'Admin\Controller\BookingController',
            'Admin\Controller\Subscription' => 'Admin\Controller\SubscriptionController',
            'Admin\Controller\UserCertifications' => 'Admin\Controller\UserCertificationsController',
            'Admin\Controller\SubscriptionPlan' => 'Admin\Controller\SubscriptionPlanController',
            'Admin\Controller\Newsletter' => 'Admin\Controller\NewsletterController',
            'Admin\Controller\NewsletterSubscriber' => 'Admin\Controller\NewsletterSubscriberController',
            'Admin\Controller\Partners' => 'Admin\Controller\PartnersController',
            'Admin\Controller\PractitionerOrganizations' => 'Admin\Controller\PractitionerOrganizationsController',
            'Admin\Controller\Testimonials' => 'Admin\Controller\TestimonialsController',
            'Admin\Controller\RatingType' => 'Admin\Controller\RatingTypeController',
            'Admin\Controller\SiteModules' => 'Admin\Controller\SiteModulesController',
            'Admin\Controller\Education' => 'Admin\Controller\EducationController',
            'Admin\Controller\ServiceLanguage' => 'Admin\Controller\ServiceLanguageController',
            'Admin\Controller\ServiceProviderService' => 'Admin\Controller\ServiceProviderServiceController',
            'Admin\Controller\ServiceLanguage' => 'Admin\Controller\ServiceLanguageController',
            'Admin\Controller\GroupRights' => 'Admin\Controller\GroupRightsController',
            'Admin\Controller\UserRights' => 'Admin\Controller\UserRightsController',
            'Admin\Controller\Emailtemplates' => 'Admin\Controller\EmailtemplatesController',
            'Admin\Controller\BannerBooking' => 'Admin\Controller\BannerBookingController',
            'Admin\Controller\Revenue' => 'Admin\Controller\RevenueController',
            'Admin\Controller\Messages' => 'Admin\Controller\MessagesController',
            'Admin\Controller\SubscriptionDuration' => 'Admin\Controller\SubscriptionDurationController',
            'Admin\Controller\BannerUpload' => 'Admin\Controller\BannerUploadController',
            'Admin\Controller\Sms' => 'Admin\Controller\SmsController',
            'Admin\Controller\ServiceProviderCommision' => 'Admin\Controller\ServiceProviderCommisionController',
            'Admin\Controller\SiteMeta' => 'Admin\Controller\SiteMetaController',
            'Admin\Controller\SiteActions' => 'Admin\Controller\SiteActionsController',
            'Admin\Controller\NotificationSettings' => 'Admin\Controller\NotificationSettingsController',
            'Admin\Controller\Media' => 'Admin\Controller\MediaController',
            'Admin\Controller\UsersMedia' => 'Admin\Controller\UsersMediaController',
            'Admin\Controller\ServiceProviderMedia' => 'Admin\Controller\ServiceProviderMediaController',
            'Admin\Controller\ServiceProviderAvailability' => 'Admin\Controller\ServiceProviderAvailabilityController',
            'Admin\Controller\SiteSettings' => 'Admin\Controller\SiteSettingsController',
            'Admin\Controller\Test' => 'Admin\Controller\TestController',
            'Admin\Controller\UserFeatureSetting' => 'Admin\Controller\UserFeatureSettingController',
            'Admin\Controller\SiteBanner' => 'Admin\Controller\SiteBannerController',
            'Admin\Controller\SubscriptionFeatures' => 'Admin\Controller\SubscriptionFeaturesController',
            'Admin\Controller\FaqIndex' => 'Admin\Controller\FaqIndexController',
            'Admin\Controller\Faqs' => 'Admin\Controller\FaqsController',
            'Admin\Controller\Advertisement' => 'Admin\Controller\AdvertisementController',
            'Admin\Controller\AdvertisementPlan' => 'Admin\Controller\AdvertisementPlanController',
        ),
    ),
    'router' => array(
        'routes' => array(
            'admin' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/admin',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Admin\Controller',
                        'controller' => 'Admin',
                        'action' => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    /* Default route */
                    'default' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '[/:controller][/:action][/:id][/]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                            ),
                        ),
                    ),
                    /* Page route */
                    'page' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/page[/:action][/:id][/]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'Admin\Controller\Page',
                                'action' => 'index',
                            ),
                        ),
                    ),
                    /* Site Meta route */
                    'sitemeta' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/sitemeta[/:action][/:id][/]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'Admin\Controller\SiteMeta',
                                'action' => 'index',
                            ),
                        ),
                    ),
                    /* Site Actions route */
                    'siteactions' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/siteactions[/:action][/:id][/]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'Admin\Controller\SiteActions',
                                'action' => 'index',
                            ),
                        ),
                    ),
                    /* Notification Settings route */
                    'notificationsettings' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/notificationsettings[/:action][/:id][/]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'Admin\Controller\NotificationSettings',
                                'action' => 'index',
                            ),
                        ),
                    ),
                    /* Messages route */
                    'messages' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/messages[/:action][/:id][/]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'Admin\Controller\Messages',
                                'action' => 'index',
                            ),
                        ),
                    ),
                    /* Emailtemplates route */
                    'emailtemplates' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/emailtemplates[/:action][/:id][/]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'Admin\Controller\Emailtemplates',
                                'action' => 'index',
                            ),
                        ),
                    ),
                    /* Sms route */
                    'sms' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/sms[/:action][/:id][/]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'Admin\Controller\Sms',
                                'action' => 'index',
                            ),
                        ),
                    ),
                    /* Consumers route */
                    'consumers' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/consumers[/:action][/:id][/]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'Admin\Controller\Consumers',
                                'action' => 'index',
                            ),
                        ),
                    ),
                    /* Testimonials route */
                    'testimonials' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/testimonials[/:action][/:id][/]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'Admin\Controller\Testimonials',
                                'action' => 'index',
                            ),
                        ),
                    ),
                    /* Site Modules route */
                    'sitemodules' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/SiteModules[/:action][/:id][/]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'Admin\Controller\SiteModules',
                                'action' => 'index',
                            ),
                        ),
                    ),
                    /* Practitioner's Organization route */
                    'organizations' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/PractitionerOrganizations[/:action][/:id][/]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'Admin\Controller\PractitionerOrganizations',
                                'action' => 'index',
                            ),
                        ),
                    ),
                    /* User Certifications route */
                    'certifications' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/UserCertifications[/:action][/:id][/]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'Admin\Controller\UserCertifications',
                                'action' => 'index',
                            ),
                        ),
                    ),
                    /* Usertype route */
                    'usertype' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/usertype[/:action][/:id][/]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'Admin\Controller\Usertype',
                                'action' => 'index',
                            ),
                        ),
                    ),
                    /* Users route */
                    'users' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/users[/:action][/:id][/]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'Admin\Controller\Users',
                                'action' => 'index',
                            ),
                        ),
                    ),
                    /* Countries route */
                    'countries' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/countries[/:action][/:id][/]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'Admin\Controller\Countries',
                                'action' => 'index',
                            ),
                        ),
                    ),
                    /* States route */
                    'states' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/states[/:action][/:id][/]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'Admin\Controller\States',
                                'action' => 'index',
                            ),
                        ),
                    ),
                    /* Service category route */
                    'servicecategory' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/servicecategory[/:action][/:id][/]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'Admin\Controller\ServiceCategory',
                                'action' => 'index',
                            ),
                        ),
                    ),
                    /* Services route */
                    'services' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/services[/:action][/:id][/]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'Admin\Controller\Services',
                                'action' => 'index',
                            ),
                        ),
                    ),
                    /* Activity route */
                    'activity' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/activity[/:action][/:id][/]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'Admin\Controller\Activity',
                                'action' => 'index',
                            ),
                        ),
                    ),
                    /* Service Provider route */
                    'serviceproviders' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/serviceproviders[/:action][/:id][/]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'Admin\Controller\ServiceProvider',
                                'action' => 'index',
                            ),
                        ),
                    ),
                    /* Ratings route */
                    'ratings' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/ratings[/:action][/:user][/:createdby][/:ratingtypeid][/]',
                            //'route'    => '/ratings[/:action][/:user][/:service][/:createdby][/:ratingtypeid][/]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'user' => '[0-9]+',
                                //'service' => '[0-9]+',
                                'createdby' => '[0-9]+',
                                'ratingtypeid' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'Admin\Controller\Ratings',
                                'action' => 'index',
                            ),
                        ),
                    ),
                    /* Feedback route */
                    'feedback' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/feedback[/:action][/:user][/:service][/]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'user' => '[0-9]+',
                                'service' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'Admin\Controller\Feedback',
                                'action' => 'index',
                            ),
                        ),
                    ),
                    /* Bookings route */
                    'bookings' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/bookings[/:action][/:id][/]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'Admin\Controller\Booking',
                                'action' => 'index',
                            ),
                        ),
                    ),
                    /* Subscriptions route */
                    'subscriptions' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/subscriptions[/:action][/:id][/]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'Admin\Controller\Subscription',
                                'action' => 'index',
                            ),
                        ),
                    ),
                    /* GroupRights route */
                    'grouprights' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/GroupRights[/:action][/:id][/]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'Admin\Controller\GroupRights',
                                'action' => 'index',
                            ),
                        ),
                    ),
                    /* UserRights route */
                    'userrights' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/UserRights[/:action][/:id][/]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'Admin\Controller\UserRights',
                                'action' => 'index',
                            ),
                        ),
                    ),
                    /* Subscription Plans route */
                    'subscriptionplans' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/subscriptionplans[/:action][/:id][/]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'Admin\Controller\SubscriptionPlan',
                                'action' => 'index',
                            ),
                        ),
                    ),
                    /* Newsletters route */
                    'newsletters' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/newsletters[/:action][/:id][/]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'Admin\Controller\Newsletter',
                                'action' => 'index',
                            ),
                        ),
                    ),
                    /* Newsletters Subscribers route */
                    'newslettersubscribers' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/newslettersubscribers[/:action][/:id][/]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'Admin\Controller\NewsletterSubscriber',
                                'action' => 'index',
                            ),
                        ),
                    ),
                    /* Partners route */
                    'partners' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/partners[/:action][/:id][/]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'Admin\Controller\Partners',
                                'action' => 'index',
                            ),
                        ),
                    ),
                    /* Rating type route */
                    'ratingtypes' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/ratingtypes[/:action][/:id][/]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'Admin\Controller\RatingType',
                                'action' => 'index',
                            ),
                        ),
                    ),
                    /* Educations type route */
                    'schools' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/schools[/:action][/:id][/]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'Admin\Controller\Education',
                                'action' => 'index',
                            ),
                        ),
                    ),
                    /* Service Language type route */
                    'servicelanguages' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/servicelanguages[/:action][/:id][/]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'Admin\Controller\ServiceLanguage',
                                'action' => 'index',
                            ),
                        ),
                    ),
                    /* Service provider service type route */
                    'serviceproviderservices' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/serviceproviderservices[/:action][/:id][/:service_id][/]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                                'service_id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'Admin\Controller\ServiceProviderService',
                                'action' => 'index',
                            ),
                        ),
                    ),
                    /* User Feature Setting  type route */
                    'userfeaturesetting' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/userfeaturesetting[/:action][/:id][/]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'Admin\Controller\UserFeatureSetting',
                                'action' => 'index',
                            ),
                        ),
                    ),
                    /* Advertisement route */
                    'advertisement' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/advertisement[/:action][/:id][/]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'Admin\Controller\Advertisement',
                                'action' => 'index',
                            ),
                        ),
                    ),
                    /* Advertisement Plan route */
                    'advertisementplan' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/advertisementplan[/:action][/:id][/]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'Admin\Controller\AdvertisementPlan',
                                'action' => 'index',
                            ),
                        ),
                    ),
                    /* Banner booking route */
                    'bannerbookings' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/bannerbookings[/:action][/:id][/]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'Admin\Controller\BannerBooking',
                                'action' => 'index',
                            ),
                        ),
                    ),
                    /* Revenues route */
                    'revenues' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/revenues[/:action][/:id][/]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'Admin\Controller\Revenue',
                                'action' => 'index',
                            ),
                        ),
                    ),
                    /* Subscription Duration route */
                    'subscriptiondurations' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/subscriptiondurations[/:action][/:subscription_id][/:id][/]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'subscription_id' => '[0-9]+',
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'Admin\Controller\SubscriptionDuration',
                                'action' => 'index',
                            ),
                        ),
                    ),
                    /* Banner Upload route */
                    'banneruploads' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/banneruploads[/:action][/:booking_id][/:id][/]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'booking_id' => '[0-9]+',
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'Admin\Controller\BannerUpload',
                                'action' => 'index',
                            ),
                        ),
                    ),
                    /* Service Provider Commisions route */
                    'serviceprovidercommisions' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/serviceprovidercommisions[/:action][/:user_id][/:id][/]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'user_id' => '[0-9]+',
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'Admin\Controller\ServiceProviderCommision',
                                'action' => 'index',
                            ),
                        ),
                    ),
                    /* Media route */
                    'media' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/media[/:action][/:id][/]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'Admin\Controller\Media',
                                'action' => 'index',
                            ),
                        ),
                    ),
                    /* Users Media route */
                    'usersmedia' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/usersmedia[/:action][/:user_id][/:id][/]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'user_id' => '[0-9]+',
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'Admin\Controller\UsersMedia',
                                'action' => 'index',
                            ),
                        ),
                    ),
                    /* Service Provider Media route */
                    'serviceprovidermedia' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/serviceprovidermedia[/:action][/:user_id][/:id][/]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'user_id' => '[0-9]+',
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'Admin\Controller\ServiceProviderMedia',
                                'action' => 'index',
                            ),
                        ),
                    ),
                    /* Service Provider Availability route */
                    'serviceprovideravailability' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/serviceprovideravailability[/:action][/:user_id][/:id][/]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'user_id' => '[0-9]+',
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'Admin\Controller\ServiceProviderAvailability',
                                'action' => 'index',
                            ),
                        ),
                    ),
                    /* Site Setting route */
                    'sitesettings' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/sitesettings[/:action][/:id][/]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'Admin\Controller\SiteSettings',
                                'action' => 'index',
                            ),
                        ),
                    ),
                    /* Site Banners route */
                    'sitebanner' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/sitebanner[/:action][/:id][/]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'Admin\Controller\SiteBanner',
                                'action' => 'index',
                            ),
                        ),
                    ),
                    /* Subscription Features route */
                    'subscriptionfeatures' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/subscriptionfeatures[/:action][/:id][/]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'Admin\Controller\SubscriptionFeatures',
                                'action' => 'index',
                            ),
                        ),
                    ),
                    
                    /* Faq Index route */
                    'faqindex' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/faqindex[/:action][/:id][/]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'Admin\Controller\FaqIndex',
                                'action' => 'index',
                            ),
                        ),
                    ),
                    
                    /* Faqs route */
                    'faqs' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/faqs[/:action][/:id][/]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'Admin\Controller\Faqs',
                                'action' => 'index',
                            ),
                        ),
                    ),
                    
                    /* Login route */
                    'login' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/login[/:action][/]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'Admin\Controller\Auth',
                                'action' => 'login',
                            ),
                        ),
                    ),
                    /* Logout route */
                    'logout' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/logout[/]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'Admin\Controller\Auth',
                                'action' => 'logout',
                            ),
                        ),
                    ),
                    /* Test Setting route */
                    'test' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/test[/:action][/:id][/]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'Admin\Controller\Test',
                                'action' => 'index',
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
    /* Navigation array for breadcrumbs */
    'navigation' => array(
        'default' => array(
            array(
                'label' => 'Admin',
                'route' => 'admin',
            ),
            array(
                'label' => 'Admin',
                'route' => 'admin/default',
            ),
            /* Page breadcrumb array */
            array(
                'label' => 'Admin',
                'route' => 'admin/page',
                'pages' => array(
                    array(
                        'label' => 'Pages',
                        'route' => 'admin/page',
                        'pages' => array(
                            array(
                                'label' => 'Add Page',
                                'route' => 'admin/page',
                                'action' => 'add',
                            ),
                            array(
                                'label' => 'Edit Page',
                                'route' => 'admin/page',
                                'action' => 'edit',
                            ),
                        ),
                    ),
                ),
            ),
            
            /* Site Meta breadcrumb array */
            array(
                'label' => 'Admin',
                'route' => 'admin/sitemeta',
                'pages' => array(
                    array(
                        'label' => 'Site Meta',
                        'route' => 'admin/sitemeta',
                        'pages' => array(
                            array(
                                'label' => 'Add Meta',
                                'route' => 'admin/sitemeta',
                                'action' => 'add',
                            ),
                            array(
                                'label' => 'Edit Meta',
                                'route' => 'admin/sitemeta',
                                'action' => 'edit',
                            ),
                        ),
                    ),
                ),
            ),
            /* Site Actions breadcrumb array */
            array(
                'label' => 'Admin',
                'route' => 'admin/sitactions',
                'pages' => array(
                    array(
                        'label' => 'Site Actions',
                        'route' => 'admin/siteactions',
                        'pages' => array(
                            array(
                                'label' => 'Add Actions',
                                'route' => 'admin/siteactions',
                                'action' => 'add',
                            ),
                            array(
                                'label' => 'Edit Actions',
                                'route' => 'admin/siteactions',
                                'action' => 'edit',
                            ),
                        ),
                    ),
                ),
            ),
            /* Notification Settings breadcrumb array */
            array(
                'label' => 'Admin',
                'route' => 'admin/notificationsettings',
                'pages' => array(
                    array(
                        'label' => 'Notification Settings',
                        'route' => 'admin/notificationsettings',
                        'pages' => array(
                            array(
                                'label' => 'Add Notification',
                                'route' => 'admin/notificationsettings',
                                'action' => 'add',
                            ),
                            array(
                                'label' => 'Edit Notifications',
                                'route' => 'admin/notificationsettings',
                                'action' => 'edit',
                            ),
                        ),
                    ),
                ),
            ),
            /* sms breadcrumb array */
            array(
                'label' => 'Admin',
                'route' => 'admin/sms',
                'pages' => array(
                    array(
                        'label' => 'Sms Templates',
                        'route' => 'admin/sms',
                        'pages' => array(
                            array(
                                'label' => 'Add sms Templates',
                                'route' => 'admin/sms',
                                'action' => 'add',
                            ),
                            array(
                                'label' => 'Edit Sms Templates',
                                'route' => 'admin/sms',
                                'action' => 'edit',
                            ),
                        ),
                    ),
                ),
            ),
            /* Emailtemplates breadcrumb array */
            array(
                'label' => 'Admin',
                'route' => 'admin/emailtemplates',
                'pages' => array(
                    array(
                        'label' => 'Email Templates',
                        'route' => 'admin/emailtemplates',
                        'pages' => array(
                            array(
                                'label' => 'Add Email Templates',
                                'route' => 'admin/emailtemplates',
                                'action' => 'add',
                            ),
                            array(
                                'label' => 'Edit Email Templates',
                                'route' => 'admin/emailtemplates',
                                'action' => 'edit',
                            ),
                        ),
                    ),
                ),
            ),
            /* Consumers breadcrumb array */
            array(
                'label' => 'Admin',
                'route' => 'admin/consumers',
                'pages' => array(
                    array(
                        'label' => 'Consumers',
                        'route' => 'admin/consumers',
                        'pages' => array(
                            array(
                                'label' => 'Add Consumer',
                                'route' => 'admin/consumer',
                                'action' => 'add',
                            ),
                            array(
                                'label' => 'Edit Consumer',
                                'route' => 'admin/consumer',
                                'action' => 'edit',
                            ),
                        ),
                    ),
                ),
            ),
            /* User Certifications breadcrumb array */
            array(
                'label' => 'Admin',
                'route' => 'admin/certifications',
                'pages' => array(
                    array(
                        'label' => 'User Certifications',
                        'route' => 'admin/certifications',
                        'pages' => array(
                            array(
                                'label' => 'Add Certifications',
                                'route' => 'admin/certifications',
                                'action' => 'add',
                            ),
                            array(
                                'label' => 'Edit Certifications',
                                'route' => 'admin/certifications',
                                'action' => 'edit',
                            ),
                        ),
                    ),
                ),
            ),
            /* Testimonials breadcrumb array */
            array(
                'label' => 'Admin',
                'route' => 'admin/testimonials',
                'pages' => array(
                    array(
                        'label' => 'Testimonials',
                        'route' => 'admin/testimonials',
                        'pages' => array(
                            array(
                                'label' => 'Add Testimonial',
                                'route' => 'admin/testimonials',
                                'action' => 'add',
                            ),
                            array(
                                'label' => 'Edit Testimonials',
                                'route' => 'admin/testimonials',
                                'action' => 'edit',
                            ),
                        ),
                    ),
                ),
            ),
            /* SiteModules breadcrumb array */
            array(
                'label' => 'Admin',
                'route' => 'admin/sitemodules',
                'pages' => array(
                    array(
                        'label' => 'Site Modules',
                        'route' => 'admin/sitemodules',
                        'pages' => array(
                            array(
                                'label' => 'Add Module',
                                'route' => 'admin/sitemodules',
                                'action' => 'add',
                            ),
                            array(
                                'label' => 'Edit Module',
                                'route' => 'admin/sitemodules',
                                'action' => 'edit',
                            ),
                        ),
                    ),
                ),
            ),
            /* Practitioner Organization breadcrumb array */
            array(
                'label' => 'Admin',
                'route' => 'admin/organizations',
                'pages' => array(
                    array(
                        'label' => 'Practitioner\'s Organization',
                        'route' => 'admin/organizations',
                        'pages' => array(
                            array(
                                'label' => 'Add Organization',
                                'route' => 'admin/organizations',
                                'action' => 'add',
                            ),
                            array(
                                'label' => 'Edit Organizations',
                                'route' => 'admin/organization',
                                'action' => 'edit',
                            ),
                        ),
                    ),
                ),
            ),
            /* Usertype breadcrumb array */
            array(
                'label' => 'Admin',
                'route' => 'admin/usertype',
                'pages' => array(
                    array(
                        'label' => 'User Types',
                        'route' => 'admin/usertype',
                        'pages' => array(
                            array(
                                'label' => 'Add User Type',
                                'route' => 'admin/usertype',
                                'action' => 'add',
                            ),
                            array(
                                'label' => 'Edit User Type',
                                'route' => 'admin/usertype',
                                'action' => 'edit',
                            ),
                        ),
                    ),
                ),
            ),
            /* Users breadcrumb array */
            array(
                'label' => 'Admin',
                'route' => 'admin/users',
                'pages' => array(
                    array(
                        'label' => 'Users',
                        'route' => 'admin/users',
                        'pages' => array(
                            array(
                                'label' => 'Add User',
                                'route' => 'admin/users',
                                'action' => 'add',
                            ),
                            array(
                                'label' => 'Edit User',
                                'route' => 'admin/users',
                                'action' => 'edit',
                            ),
                        ),
                    ),
                ),
            ),
            /* Countries breadcrumb array */
            array(
                'label' => 'Admin',
                'route' => 'admin/countries',
                'pages' => array(
                    array(
                        'label' => 'Countries',
                        'route' => 'admin/countries',
                        'pages' => array(
                            array(
                                'label' => 'Add Country',
                                'route' => 'admin/countries',
                                'action' => 'add',
                            ),
                            array(
                                'label' => 'Edit Country',
                                'route' => 'admin/countries',
                                'action' => 'edit',
                            ),
                        ),
                    ),
                ),
            ),
            /* States breadcrumb array */
            array(
                'label' => 'Admin',
                'route' => 'admin/states',
                'pages' => array(
                    array(
                        'label' => 'States',
                        'route' => 'admin/states',
                        'pages' => array(
                            array(
                                'label' => 'Add State',
                                'route' => 'admin/states',
                                'action' => 'add',
                            ),
                            array(
                                'label' => 'Edit State',
                                'route' => 'admin/states',
                                'action' => 'edit',
                            ),
                        ),
                    ),
                ),
            ),
            /* Service category breadcrumb array */
            array(
                'label' => 'Admin',
                'route' => 'admin/servicecategory',
                'pages' => array(
                    array(
                        'label' => 'Service Categories',
                        'route' => 'admin/servicecategory',
                        'pages' => array(
                            array(
                                'label' => 'Add Service Category',
                                'route' => 'admin/servicecategory',
                                'action' => 'add',
                            ),
                            array(
                                'label' => 'Edit Service Category',
                                'route' => 'admin/servicecategory',
                                'action' => 'edit',
                            ),
                        ),
                    ),
                ),
            ),
            /* Services breadcrumb array */
            array(
                'label' => 'Admin',
                'route' => 'admin/services',
                'pages' => array(
                    array(
                        'label' => 'Services',
                        'route' => 'admin/services',
                        'pages' => array(
                            array(
                                'label' => 'Add Service',
                                'route' => 'admin/services',
                                'action' => 'add',
                            ),
                            array(
                                'label' => 'Edit Service',
                                'route' => 'admin/services',
                                'action' => 'edit',
                            ),
                        ),
                    ),
                ),
            ),
            /* Activity breadcrumb array */
            array(
                'label' => 'Admin',
                'route' => 'admin/activity',
                'pages' => array(
                    array(
                        'label' => 'Activities',
                        'route' => 'admin/activity',
                        'pages' => array(
                            array(
                                'label' => 'Add Activity',
                                'route' => 'admin/activity',
                                'action' => 'add',
                            ),
                            array(
                                'label' => 'Edit Activity',
                                'route' => 'admin/activity',
                                'action' => 'edit',
                            ),
                        ),
                    ),
                ),
            ),
            /* Service Providers breadcrumb array */
            array(
                'label' => 'Admin',
                'route' => 'admin/serviceproviders',
                'pages' => array(
                    array(
                        'label' => 'Service Providers',
                        'route' => 'admin/serviceproviders',
                        'pages' => array(
                            array(
                                'label' => 'Add Service Provider',
                                'route' => 'admin/serviceproviders',
                                'action' => 'add',
                            ),
                            array(
                                'label' => 'Edit Service Provider',
                                'route' => 'admin/serviceproviders',
                                'action' => 'edit',
                            ),
                        ),
                    ),
                ),
            ),
            /* User Feature Setting breadcrumb array */
            array(
                'label' => 'Admin',
                'route' => 'admin/userfeaturesetting',
                'pages' => array(
                    array(
                        'label' => 'Feature Setting',
                        'route' => 'admin/userfeaturesetting',
                        'pages' => array(
                            array(
                                'label' => 'Add Service Provider',
                                'route' => 'admin/serviceproviders',
                                'action' => 'add',
                            ),
                            array(
                                'label' => 'Edit Service Provider',
                                'route' => 'admin/serviceproviders',
                                'action' => 'edit',
                            ),
                        ),
                    ),
                ),
            ),
            /* Group Rights breadcrumb array */
            array(
                'label' => 'Admin',
                'route' => 'admin/grouprights',
                'pages' => array(
                    array(
                        'label' => 'Group Rights',
                        'route' => 'admin/grouprighs',
                        'pages' => array(
                            array(
                                'label' => 'Add gropu Rights',
                                'route' => 'admin/grouprights',
                                'action' => 'add',
                            ),
                            array(
                                'label' => 'Edit Group Rights',
                                'route' => 'admin/grouprights',
                                'action' => 'edit',
                            ),
                        ),
                    ),
                ),
            ),
            /* User Rights breadcrumb array */
            array(
                'label' => 'Admin',
                'route' => 'admin/userrights',
                'pages' => array(
                    array(
                        'label' => 'User Rights',
                        'route' => 'admin/userrights',
                        'pages' => array(
                            array(
                                'label' => 'Manage User Rights',
                                'route' => 'admin/userrights',
                                'action' => 'assign',
                            ),
                        ),
                    ),
                ),
            ),
            /* Ratings breadcrumb array */
            array(
                'label' => 'Admin',
                'route' => 'admin/ratings',
                'pages' => array(
                    array(
                        'label' => 'Ratings',
                        'route' => 'admin/ratings',
                        'pages' => array(
                            array(
                                'label' => 'Add Rating',
                                'route' => 'admin/ratings',
                                'action' => 'add',
                            ),
                            array(
                                'label' => 'Edit Rating',
                                'route' => 'admin/ratings',
                                'action' => 'edit',
                            ),
                        ),
                    ),
                ),
            ),
            /* Feedback breadcrumb array */
            array(
                'label' => 'Admin',
                'route' => 'admin/feedback',
                'pages' => array(
                    array(
                        'label' => 'Feedbacks',
                        'route' => 'admin/feedback',
                        'pages' => array(
                            array(
                                'label' => 'Add Feedback',
                                'route' => 'admin/feedback',
                                'action' => 'add',
                            ),
                            array(
                                'label' => 'Edit Feedback',
                                'route' => 'admin/feedback',
                                'action' => 'edit',
                            ),
                        ),
                    ),
                ),
            ),
            /* Bookings breadcrumb array */
            array(
                'label' => 'Admin',
                'route' => 'admin/bookings',
                'pages' => array(
                    array(
                        'label' => 'Bookings',
                        'route' => 'admin/bookings',
                        'pages' => array(
                            array(
                                'label' => 'Add Booking',
                                'route' => 'admin/bookings',
                                'action' => 'add',
                            ),
                            array(
                                'label' => 'Edit Booking',
                                'route' => 'admin/bookings',
                                'action' => 'edit',
                            ),
                            array(
                                'label' => 'Reschedule Booking',
                                'route' => 'admin/bookings',
                                'action' => 'reschedule',
                            ),
                        ),
                    ),
                ),
            ),
            /* Subscriptions breadcrumb array */
            array(
                'label' => 'Admin',
                'route' => 'admin/subscriptions',
                'pages' => array(
                    array(
                        'label' => 'Subscriptions',
                        'route' => 'admin/subscriptions',
                        'pages' => array(
                            array(
                                'label' => 'Add Subscription',
                                'route' => 'admin/subscriptions',
                                'action' => 'add',
                            ),
                            array(
                                'label' => 'Edit Subscription',
                                'route' => 'admin/subscriptions',
                                'action' => 'edit',
                            ),
                        ),
                    ),
                ),
            ),
            /* Subscription plans breadcrumb array */
            array(
                'label' => 'Admin',
                'route' => 'admin/subscriptionplans',
                'pages' => array(
                    array(
                        'label' => 'Subscription Plans',
                        'route' => 'admin/subscriptionplans',
                        'pages' => array(
                            array(
                                'label' => 'Add Subscription Plan',
                                'route' => 'admin/subscriptionplans',
                                'action' => 'add',
                            ),
                            array(
                                'label' => 'Edit Subscription Plan',
                                'route' => 'admin/subscriptionplans',
                                'action' => 'edit',
                            ),
                        ),
                    ),
                ),
            ),
            /* Newsletters breadcrumb array */
            array(
                'label' => 'Admin',
                'route' => 'admin/newsletters',
                'pages' => array(
                    array(
                        'label' => 'Newsletters',
                        'route' => 'admin/newsletters',
                        'pages' => array(
                            array(
                                'label' => 'Add Newsletter',
                                'route' => 'admin/newsletters',
                                'action' => 'add',
                            ),
                            array(
                                'label' => 'Edit Newsletter',
                                'route' => 'admin/newsletters',
                                'action' => 'edit',
                            ),
                        ),
                    ),
                ),
            ),
            /* Newsletters Subscribers breadcrumb array */
            array(
                'label' => 'Admin',
                'route' => 'admin/newslettersubscribers',
                'pages' => array(
                    array(
                        'label' => 'Newsletter Subscribers',
                        'route' => 'admin/newslettersubscribers',
                        'pages' => array(
                            array(
                                'label' => 'Add Newsletter Subscriber',
                                'route' => 'admin/newslettersubscribers',
                                'action' => 'add',
                            ),
                            array(
                                'label' => 'Edit Newsletter Subscriber',
                                'route' => 'admin/newslettersubscribers',
                                'action' => 'edit',
                            ),
                        ),
                    ),
                ),
            ),
            /* Partners breadcrumb array */
            array(
                'label' => 'Admin',
                'route' => 'admin/partners',
                'pages' => array(
                    array(
                        'label' => 'Partners',
                        'route' => 'admin/partners',
                        'pages' => array(
                            array(
                                'label' => 'Add Parner',
                                'route' => 'admin/parners',
                                'action' => 'add',
                            ),
                            array(
                                'label' => 'Edit Partner',
                                'route' => 'admin/partners',
                                'action' => 'edit',
                            ),
                        ),
                    ),
                ),
            ),
            /* Rating type breadcrumb array */
            array(
                'label' => 'Admin',
                'route' => 'admin/ratingtypes',
                'pages' => array(
                    array(
                        'label' => 'Rating Types',
                        'route' => 'admin/ratingtypes',
                        'pages' => array(
                            array(
                                'label' => 'Add Rating Type',
                                'route' => 'admin/ratingtypes',
                                'action' => 'add',
                            ),
                            array(
                                'label' => 'Edit Rating Type',
                                'route' => 'admin/ratingtypes',
                                'action' => 'edit',
                            ),
                        ),
                    ),
                ),
            ),
            /* Educations breadcrumb array */
            array(
                'label' => 'Admin',
                'route' => 'admin/schools',
                'pages' => array(
                    array(
                        'label' => 'Schools',
                        'route' => 'admin/schools',
                        'pages' => array(
                            array(
                                'label' => 'Add Schools',
                                'route' => 'admin/schools',
                                'action' => 'add',
                            ),
                            array(
                                'label' => 'Edit Schools',
                                'route' => 'admin/schools',
                                'action' => 'edit',
                            ),
                        ),
                    ),
                ),
            ),
            /* Service Location breadcrumb array */
            array(
                'label' => 'Admin',
                'route' => 'admin/servicelanguages',
                'pages' => array(
                    array(
                        'label' => 'Service Languages',
                        'route' => 'admin/servicelanguages',
                        'pages' => array(
                            array(
                                'label' => 'Add Service Language',
                                'route' => 'admin/servicelanguages',
                                'action' => 'add',
                            ),
                            array(
                                'label' => 'Edit Service Language',
                                'route' => 'admin/servicelanguages',
                                'action' => 'edit',
                            ),
                        ),
                    ),
                ),
            ),
            /* Service Provider service breadcrumb array */
            array(
                'label' => 'Admin',
                'route' => 'admin/serviceproviderservices',
                'pages' => array(
                    array(
                        'label' => 'Service Provider Services',
                        'route' => 'admin/serviceproviderservices',
                        'pages' => array(
                            array(
                                'label' => 'Add Service Provider Service',
                                'route' => 'admin/serviceproviderservices',
                                'action' => 'add',
                            ),
                            array(
                                'label' => 'Edit Service Provider Service',
                                'route' => 'admin/serviceproviderservices',
                                'action' => 'edit',
                            ),
                        ),
                    ),
                ),
            ),
            
            /* Messgaes breadcrumb array */
            array(
                'label' => 'Admin',
                'route' => 'admin/messages',
                'pages' => array(
                    array(
                        'label' => 'Messages',
                        'route' => 'admin/messages',
                        'pages' => array(
                            array(
                                'label' => 'Index Messages',
                                'route' => 'admin/messages',
                                'action' => 'index',
                            ),
                            array(
                                'label' => 'Inbox Messages',
                                'route' => 'admin/messages',
                                'action' => 'inbox',
                            ),
                            array(
                                'label' => 'Outbox Messages',
                                'route' => 'admin/messages',
                                'action' => 'outbox',
                            ),
                            array(
                                'label' => 'Trash Messages',
                                'route' => 'admin/messages',
                                'action' => 'trash',
                            ),
                            array(
                                'label' => 'Compose Messages',
                                'route' => 'admin/messages',
                                'action' => 'compose',
                            ),
                        ),
                    ),
                ),
            ),
            /* Advertisement Plan breadcrumb array */
            array(
                'label' => 'Admin',
                'route' => 'admin/advertisementplan',
                'pages' => array(
                    array(
                        'label' => 'Advertisement Plans',
                        'route' => 'admin/advertisementplan',
                        'pages' => array(
                            array(
                                'label' => 'Add Advertisement Plan',
                                'route' => 'admin/advertisementplan',
                                'action' => 'add',
                            ),
                            array(
                                'label' => 'Edit Advertisement Plan',
                                'route' => 'admin/advertisementplan',
                                'action' => 'edit',
                            ),
                        ),
                    ),
                ),
            ),
            /* Advertisement breadcrumb array */
            array(
                'label' => 'Admin',
                'route' => 'admin/advertisement',
                'pages' => array(
                    array(
                        'label' => 'Advertisements',
                        'route' => 'admin/advertisement',
                        'pages' => array(
                            array(
                                'label' => 'Add Advertisement',
                                'route' => 'admin/advertisement',
                                'action' => 'add',
                            ),
                            array(
                                'label' => 'Edit Advertisement',
                                'route' => 'admin/advertisement',
                                'action' => 'edit',
                            ),
                        ),
                    ),
                ),
            ),
            /* Banner Booking breadcrumb array */
            array(
                'label' => 'Admin',
                'route' => 'admin/bannerbookings',
                'pages' => array(
                    array(
                        'label' => 'Banner Bookings',
                        'route' => 'admin/bannerbookings',
                        'pages' => array(
                            array(
                                'label' => 'Add Banner Booking',
                                'route' => 'admin/bannerbookings',
                                'action' => 'add',
                            ),
                            array(
                                'label' => 'Edit Banner Booking',
                                'route' => 'admin/bannerbookings',
                                'action' => 'edit',
                            ),
                        ),
                    ),
                ),
            ),
            /* Revenue breadcrumb array */
            array(
                'label' => 'Admin',
                'route' => 'admin/revenues',
                'pages' => array(
                    array(
                        'label' => 'Payment History',
                        'route' => 'admin/revenues',
                        'pages' => array(
                        ),
                    ),
                ),
            ),
            /* Subscription Duration breadcrumb array */
            array(
                'label' => 'Admin',
                'route' => 'admin/subscriptiondurations',
                'pages' => array(
                    array(
                        'label' => 'Subscription Durations',
                        'route' => 'admin/subscriptiondurations',
                        'pages' => array(
                            array(
                                'label' => 'Add Subscription Duration',
                                'route' => 'admin/subscriptiondurations',
                                'action' => 'add',
                            ),
                            array(
                                'label' => 'Edit Subscription Duration',
                                'route' => 'admin/subscriptiondurations',
                                'action' => 'edit',
                            ),
                        ),
                    ),
                ),
            ),
            /* Banner Upload breadcrumb array */
            array(
                'label' => 'Admin',
                'route' => 'admin/banneruploads',
                'pages' => array(
                    array(
                        'label' => 'Uploaded Banners',
                        'route' => 'admin/banneruploads',
                        'pages' => array(
                            array(
                                'label' => 'Upload Banner',
                                'route' => 'admin/banneruploads',
                                'action' => 'add',
                            ),
                            array(
                                'label' => 'Edit Banner',
                                'route' => 'admin/banneruploads',
                                'action' => 'edit',
                            ),
                        ),
                    ),
                ),
            ),
            /* Service Provider Commission breadcrumb array */
            array(
                'label' => 'Admin',
                'route' => 'admin/serviceprovidercommisions',
                'pages' => array(
                    array(
                        'label' => 'Service Provider Commissions',
                        'route' => 'admin/serviceprovidercommisions',
                        'pages' => array(
                            array(
                                'label' => 'Add Service Provider Commission',
                                'route' => 'admin/serviceprovidercommisions',
                                'action' => 'add',
                            ),
                            array(
                                'label' => 'Edit Service Provider Commission',
                                'route' => 'admin/serviceprovidercommisionss',
                                'action' => 'edit',
                            ),
                        ),
                    ),
                ),
            ),
            /* Media breadcrumb array */
            array(
                'label' => 'Admin',
                'route' => 'admin/media',
                'pages' => array(
                    array(
                        'label' => 'Media',
                        'route' => 'admin/media',
                        'pages' => array(
                            array(
                                'label' => 'Add Media',
                                'route' => 'admin/media',
                                'action' => 'add',
                            ),
                            array(
                                'label' => 'Edit Media',
                                'route' => 'admin/media',
                                'action' => 'edit',
                            ),
                        ),
                    ),
                ),
            ),
            /* Users Media breadcrumb array */
            array(
                'label' => 'Admin',
                'route' => 'admin/usersmedia',
                'pages' => array(
                    array(
                        'label' => 'Users Media',
                        'route' => 'admin/usersmedia',
                        'pages' => array(
                            array(
                                'label' => 'Add Users Media',
                                'route' => 'admin/usersmedia',
                                'action' => 'add',
                            ),
                            array(
                                'label' => 'Edit Users Media',
                                'route' => 'admin/usersmedia',
                                'action' => 'edit',
                            ),
                        ),
                    ),
                ),
            ),
            /* Service Provider Media breadcrumb array */
            array(
                'label' => 'Admin',
                'route' => 'admin/serviceprovidermedia',
                'pages' => array(
                    array(
                        'label' => 'Service Provider Media',
                        'route' => 'admin/serviceprovidermedia',
                        'pages' => array(
                            array(
                                'label' => 'Add Service Provider Media',
                                'route' => 'admin/serviceprovidermedia',
                                'action' => 'add',
                            ),
                            array(
                                'label' => 'Edit Service Provider Media',
                                'route' => 'admin/serviceprovidermedia',
                                'action' => 'edit',
                            ),
                        ),
                    ),
                ),
            ),
            /* Service Provider Availability breadcrumb array */
            array(
                'label' => 'Admin',
                'route' => 'admin/serviceprovideravailability',
                'pages' => array(
                    array(
                        'label' => 'Service Provider Availability',
                        'route' => 'admin/serviceprovideravailability',
                        'pages' => array(
                            array(
                                'label' => 'Add Service Provider Availability',
                                'route' => 'admin/serviceprovideravailability',
                                'action' => 'add',
                            ),
                            array(
                                'label' => 'Edit Service Provider Availability',
                                'route' => 'admin/serviceprovideravailability',
                                'action' => 'edit',
                            ),
                        ),
                    ),
                ),
            ),
            /* Site Setting breadcrumb array */
            array(
                'label' => 'Admin',
                'route' => 'admin/sitesettings',
                'pages' => array(
                    array(
                        'label' => 'Site Setting',
                        'route' => 'admin/sitesettings',
                        'pages' => array(
                            array(
                                'label' => 'Add Site Setting',
                                'route' => 'admin/sitesettings',
                                'action' => 'add',
                            ),
                            array(
                                'label' => 'Edit Site Setting',
                                'route' => 'admin/sitesettings',
                                'action' => 'edit',
                            ),
                        ),
                    ),
                ),
            ),
            /* Site Banner breadcrumb array */
            array(
                'label' => 'Admin',
                'route' => 'admin/sitebanner',
                'pages' => array(
                    array(
                        'label' => 'Site Banners',
                        'route' => 'admin/sitebanner',
                        'pages' => array(
                            array(
                                'label' => 'Add Site Banner',
                                'route' => 'admin/sitebanner',
                                'action' => 'add',
                            ),
                            array(
                                'label' => 'Edit Site Banner',
                                'route' => 'admin/sitebanner',
                                'action' => 'edit',
                            ),
                        ),
                    ),
                ),
            ),
            /* Subscription Features breadcrumb array */
            array(
                'label' => 'Admin',
                'route' => 'admin/subscriptionfeatures',
                'pages' => array(
                    array(
                        'label' => 'Subscription Features',
                        'route' => 'admin/subscriptionfeatures',
                        'pages' => array(
                            array(
                                'label' => 'Add Subscription Feature',
                                'route' => 'admin/subscriptionfeatures',
                                'action' => 'add',
                            ),
                            array(
                                'label' => 'Edit Subscription Feature',
                                'route' => 'admin/subscriptionfeatures',
                                'action' => 'edit',
                            ),
                        ),
                    ),
                ),
            ),
            /* Faq Index breadcrumb array */
            array(
                'label' => 'Admin',
                'route' => 'admin/faqindex',
                'pages' => array(
                    array(
                        'label' => 'Faq Index',
                        'route' => 'admin/faqindex',
                        'pages' => array(
                            array(
                                'label' => 'Add Faq Index',
                                'route' => 'admin/faqindex',
                                'action' => 'add',
                            ),
                            array(
                                'label' => 'Edit Faq Index',
                                'route' => 'admin/faqindex',
                                'action' => 'edit',
                            ),
                        ),
                    ),
                ),
            ),
            /* Faqs breadcrumb array */
            array(
                'label' => 'Admin',
                'route' => 'admin/faqs',
                'pages' => array(
                    array(
                        'label' => 'Faqs',
                        'route' => 'admin/faqs',
                        'pages' => array(
                            array(
                                'label' => 'Add Faq',
                                'route' => 'admin/faqs',
                                'action' => 'add',
                            ),
                            array(
                                'label' => 'Edit Faq',
                                'route' => 'admin/faqs',
                                'action' => 'edit',
                            ),
                        ),
                    ),
                ),
            ),
            /* Test Setting breadcrumb array */
            array(
                'label' => 'Admin',
                'route' => 'admin/test',
                'pages' => array(
                    array(
                        'label' => 'Test',
                        'route' => 'admin/test',
                        'pages' => array(
                            array(
                                'label' => 'Add Test',
                                'route' => 'admin/test',
                                'action' => 'add',
                            ),
                            array(
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
    'service_manager' => array(
        'factories' => array(
            'navigation' => 'Zend\Navigation\Service\DefaultNavigationFactory', // Library for navigation related methods like menu(), breadcrumbs() etc
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'Admin' => __DIR__ . '/../view',
            'pages' => __DIR__ . '/../view',
        ),
    ),
        /* 	'template_map' => array(
          include (__DIR__ . '../template_map.php')
          ) */
);
