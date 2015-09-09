<?php

namespace Admin;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Adapter\DbTable as DbTableAuthAdapter;
use Admin\Model\Page;
use Admin\Model\PageTable;
use Admin\Model\Usertype;
use Admin\Model\UsertypeTable;
use Admin\Model\Users;
use Admin\Model\UsersTable;
use Admin\Model\UserCertifications;
use Admin\Model\UserCertificationsTable;
use Admin\Model\PractitionerOrganizations;
use Admin\Model\PractitionerOrganizationsTable;
use Admin\Model\PractitionerOrganization;
use Admin\Model\PractitionerOrganizationTable;
use Admin\Model\Testimonials;
use Admin\Model\TestimonialsTable;
use Admin\Model\SiteModules;
use Admin\Model\SiteModulesTable;
use Admin\Model\Countries;
use Admin\Model\CountriesTable;
use Admin\Model\States;
use Admin\Model\StatesTable;
use Admin\Model\ServiceCategory;
use Admin\Model\ServiceCategoryTable;
use Admin\Model\Services;
use Admin\Model\ServicesTable;
use Admin\Model\ServiceProvider;
use Admin\Model\ServiceProviderTable;
use Admin\Model\Activity;
use Admin\Model\ActivityTable;
use Admin\Model\Status;
use Admin\Model\StatusTable;
use Admin\Model\Ratings;
use Admin\Model\RatingsTable;
use Admin\Model\Feedbacks;
use Admin\Model\FeedbacksTable;
use Admin\Model\Bookings;
use Admin\Model\BookingsTable;
use Admin\Model\SubscriptionPlans;
use Admin\Model\SubscriptionPlansTable;
use Admin\Model\Newsletters;
use Admin\Model\NewslettersTable;
use Admin\Model\NewsletterSubscribers;
use Admin\Model\NewsletterSubscribersTable;
use Admin\Model\Partners;
use Admin\Model\PartnersTable;
use Admin\Model\RatingType;
use Admin\Model\RatingTypeTable;
use Admin\Model\Educations;
use Admin\Model\EducationsTable;
use Admin\Model\ServiceLanguages;
use Admin\Model\ServiceLanguagesTable;
use Admin\Model\ServiceProviderServices;
use Admin\Model\ServiceProviderServicesTable;
use Admin\Model\GroupRights;
use Admin\Model\GroupRightsTable;
use Admin\Model\UserRights;
use Admin\Model\UserRightsTable;;
use Admin\Model\Emailtemplates;
use Admin\Model\EmailtemplatesTable;
use Admin\Model\BannerBookings;
use Admin\Model\BannerBookingsTable;
use Admin\Model\Revenues;
use Admin\Model\RevenuesTable;
use Admin\Model\Messages;
use Admin\Model\MessagesTable;
use Admin\Model\SubscriptionDurations;
use Admin\Model\SubscriptionDurationsTable;
use Admin\Model\Subscriptions;
use Admin\Model\SubscriptionsTable;
use Admin\Model\BannerUploads;
use Admin\Model\BannerUploadsTable;
use Admin\Model\Sms;
use Admin\Model\SmsTable;
use Admin\Model\SmsHistory;
use Admin\Model\SmsHistoryTable;
use Admin\Model\ServiceProviderCommisions;
use Admin\Model\ServiceProviderCommisionsTable;
use Admin\Model\Media;
use Admin\Model\MediaTable;
use Admin\Model\VideoViews;
use Admin\Model\VideoViewsTable;
use Admin\Model\SiteMeta;
use Admin\Model\SiteMetaTable;
use Admin\Model\SiteActions;
use Admin\Model\SiteActionsTable;
use Admin\Model\NotificationSettings;
use Admin\Model\NotificationSettingsTable;
use Admin\Model\UsersMedia;
use Admin\Model\UsersMediaTable;
use Admin\Model\ServiceProviderMedia;
use Admin\Model\ServiceProviderMediaTable;
use Admin\Model\ServiceProviderAvailability;
use Admin\Model\ServiceProviderAvailabilityTable;
use Admin\Model\SiteSettings;
use Admin\Model\SiteSettingsTable;
use Admin\Model\Test;
use Admin\Model\TestTable;
use Admin\Model\UserFeatureSetting;
use Admin\Model\UserFeatureSettingTable;
use Admin\Model\BookingSuggestionHistory;
use Admin\Model\BookingSuggestionHistoryTable;
use Admin\Model\SiteBanners;
use Admin\Model\SiteBannersTable;
use Admin\Model\BannersPageLocation;
use Admin\Model\BannersPageLocationTable;
use Admin\Model\SubscriptionFeatures;
use Admin\Model\SubscriptionFeaturesTable;
use Admin\Model\FaqIndex;
use Admin\Model\FaqIndexTable;
use Admin\Model\Faqs;
use Admin\Model\FaqsTable;
use Admin\Model\AdvertisementPage;
use Admin\Model\AdvertisementPageTable;
use Admin\Model\Advertisement;
use Admin\Model\AdvertisementTable;
use Admin\Model\AdvertisementPlan;
use Admin\Model\AdvertisementPlanTable;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;

class Module implements AutoloaderProviderInterface
{

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    // if we're in a namespace deeper than one level we need to fix the \ in the path
                    __NAMESPACE__ => __DIR__ . '/src/' . str_replace('\\', '/', __NAMESPACE__),
                ),
            ),
        );
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    /* public function getViewHelperConfig()
      {
      return array(
      'invokables' => array(
      'minify' => 'Admin\Helper\MinifyHelper'
      )
      );
      } */

    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'Admin\Model\MyAuthStorage' => function($sm) {
            return new \Admin\Model\MyAuthStorage('zf_tutorial');
        },
                /* Page table starts */
                'Admin\Model\PageTable' => function($sm) {
            $tableGateway = $sm->get('PageTableGateway');
            $table = new PageTable($tableGateway);
            return $table;
        },
                'PageTableGateway' => function ($sm) {
            $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
            $resultSetPrototype = new ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new Page());
            return new TableGateway('page', $dbAdapter, null, $resultSetPrototype);
        },
                /* Page table ends */

                /* Usertype table starts */
                'Admin\Model\UsertypeTable' => function($sm) {
            $tableGateway = $sm->get('UsertypeTableGateway');
            $table = new UsertypeTable($tableGateway);
            return $table;
        },
                'UsertypeTableGateway' => function ($sm) {
            $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
            $resultSetPrototype = new ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new Usertype());
            return new TableGateway('lookup_user_type', $dbAdapter, null, $resultSetPrototype);
        },
                /* Usertype table ends */

                /* Users table starts */
                'Admin\Model\UsersTable' => function($sm) {
            $tableGateway = $sm->get('UsersTableGateway');
            $table = new UsersTable($tableGateway);
            return $table;
        },
                'UsersTableGateway' => function ($sm) {
            $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
            $resultSetPrototype = new ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new Users());
            return new TableGateway('users', $dbAdapter, null, $resultSetPrototype);
        },
                /* Users table ends */

                /* UserCertification table starts */
                'Admin\Model\UserCertificationsTable' => function($sm) {
            $tableGateway = $sm->get('UserCertificationsTableGateway');
            $table = new UserCertificationsTable($tableGateway);
            return $table;
        },
                'UserCertificationsTableGateway' => function ($sm) {
            $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
            $resultSetPrototype = new ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new UserCertifications());
            return new TableGateway('user_certification', $dbAdapter, null, $resultSetPrototype);
        },
                /* UserCertification table ends */

                /* Countries table starts */
                'Admin\Model\CountriesTable' => function($sm) {
            $tableGateway = $sm->get('CountriesTableGateway');
            $table = new CountriesTable($tableGateway);
            return $table;
        },
                'CountriesTableGateway' => function ($sm) {
            $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
            $resultSetPrototype = new ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new Countries());
            return new TableGateway('country', $dbAdapter, null, $resultSetPrototype);
        },
                /* Countries table ends */

                /* States table starts */
                'Admin\Model\StatesTable' => function($sm) {
            $tableGateway = $sm->get('StatesTableGateway');
            $table = new StatesTable($tableGateway);
            return $table;
        },
                'StatesTableGateway' => function ($sm) {
            $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
            $resultSetPrototype = new ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new States());
            return new TableGateway('state', $dbAdapter, null, $resultSetPrototype);
        },
                /* States table ends */

                /* Service category table starts */
                'Admin\Model\ServiceCategoryTable' => function($sm) {
            $tableGateway = $sm->get('ServiceCategoryTableGateway');
            $table = new ServiceCategoryTable($tableGateway);
            return $table;
        },
                'ServiceCategoryTableGateway' => function ($sm) {
            $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
            $resultSetPrototype = new ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new ServiceCategory());
            return new TableGateway('service_category', $dbAdapter, null, $resultSetPrototype);
        },
                /* Service category table ends */

                /* Services table starts */
                'Admin\Model\ServicesTable' => function($sm) {
            $tableGateway = $sm->get('ServicesTableGateway');
            $table = new ServicesTable($tableGateway);
            return $table;
        },
                'ServicesTableGateway' => function ($sm) {
            $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
            $resultSetPrototype = new ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new Services());
            return new TableGateway('service_provider_service', $dbAdapter, null, $resultSetPrototype);
        },
                /* Services table ends */

                /* Service Provider table starts */
                'Admin\Model\ServiceProviderTable' => function($sm) {
            $tableGateway = $sm->get('ServiceProviderTableGateway');
            $table = new ServiceProviderTable($tableGateway);
            return $table;
        },
                'ServiceProviderTableGateway' => function ($sm) {
            $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
            $resultSetPrototype = new ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new ServiceProvider());
            return new TableGateway('users', $dbAdapter, null, $resultSetPrototype);
        },
                /* Service Provider table ends */

                /* Activity table starts */
                'Admin\Model\ActivityTable' => function($sm) {
            $tableGateway = $sm->get('ActivityTableGateway');
            $table = new ActivityTable($tableGateway);
            return $table;
        },
                'ActivityTableGateway' => function ($sm) {
            $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
            $resultSetPrototype = new ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new Activity());
            return new TableGateway('lookup_activity', $dbAdapter, null, $resultSetPrototype);
        },
                /* Activity table ends */

                /* Status table starts */
                'Admin\Model\StatusTable' => function($sm) {
            $tableGateway = $sm->get('StatusTableGateway');
            $table = new StatusTable($tableGateway);
            return $table;
        },
                'StatusTableGateway' => function ($sm) {
            $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
            $resultSetPrototype = new ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new Status());
            return new TableGateway('lookup_status', $dbAdapter, null, $resultSetPrototype);
        },
                /* Status table ends */

                /* Ratings table starts */
                'Admin\Model\RatingsTable' => function($sm) {
            $tableGateway = $sm->get('RatingsTableGateway');
            $table = new RatingsTable($tableGateway);
            return $table;
        },
                'RatingsTableGateway' => function ($sm) {
            $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
            $resultSetPrototype = new ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new Ratings());
            return new TableGateway('rating', $dbAdapter, null, $resultSetPrototype);
        },
                /* Ratings table ends */

                /* Feedbacks table starts */
                'Admin\Model\FeedbacksTable' => function($sm) {
            $tableGateway = $sm->get('FeedbacksTableGateway');
            $table = new FeedbacksTable($tableGateway);
            return $table;
        },
                'FeedbacksTableGateway' => function ($sm) {
            $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
            $resultSetPrototype = new ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new Feedbacks());
            return new TableGateway('feedback', $dbAdapter, null, $resultSetPrototype);
        },
                /* Feedbacks table ends */

                /* Bookings table starts */
                'Admin\Model\BookingsTable' => function($sm) {
            $tableGateway = $sm->get('BookingsTableGateway');
            $table = new BookingsTable($tableGateway);
            return $table;
        },
                'BookingsTableGateway' => function ($sm) {
            $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
            $resultSetPrototype = new ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new Bookings());
            return new TableGateway('booking', $dbAdapter, null, $resultSetPrototype);
        },
                /* Bookings table ends */

                /* Subscription Plans table starts */
                'Admin\Model\SubscriptionPlansTable' => function($sm) {
            $tableGateway = $sm->get('SubscriptionPlansTableGateway');
            $table = new SubscriptionPlansTable($tableGateway);
            return $table;
        },
                'SubscriptionPlansTableGateway' => function ($sm) {
            $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
            $resultSetPrototype = new ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new SubscriptionPlans());
            return new TableGateway('subscription', $dbAdapter, null, $resultSetPrototype);
        },
                /* Subscription Plans table ends */

                /* Newsletters table starts */
                'Admin\Model\NewslettersTable' => function($sm) {
            $tableGateway = $sm->get('NewslettersTableGateway');
            $table = new NewslettersTable($tableGateway);
            return $table;
        },
                'NewslettersTableGateway' => function ($sm) {
            $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
            $resultSetPrototype = new ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new Newsletters());
            return new TableGateway('newsletter', $dbAdapter, null, $resultSetPrototype);
        },
                /* Newsletters table ends */

                /* Newsletter Subscribers table starts */
                'Admin\Model\NewsletterSubscribersTable' => function($sm) {
            $tableGateway = $sm->get('NewsletterSubscribersTableGateway');
            $table = new NewsletterSubscribersTable($tableGateway);
            return $table;
        },
                'NewsletterSubscribersTableGateway' => function ($sm) {
            $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
            $resultSetPrototype = new ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new NewsletterSubscribers());
            return new TableGateway('newsletter_subscription', $dbAdapter, null, $resultSetPrototype);
        },
                /* Newsletter Subscribers table ends */

                /* Partners table starts */
                'Admin\Model\PartnersTable' => function($sm) {
            $tableGateway = $sm->get('PartnersTableGateway');
            $table = new PartnersTable($tableGateway);
            return $table;
        },
                'PartnersTableGateway' => function ($sm) {
            $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
            $resultSetPrototype = new ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new Partners());
            return new TableGateway('partners', $dbAdapter, null, $resultSetPrototype);
        },
                /* Partners table ends */

                /* Practitioner Organizations List Table starts */
                'Admin\Model\PractitionerOrganizationsTable' => function($sm) {
            $tableGateway = $sm->get('PractitionerOrganizationsTableGateway');
            $table = new PractitionerOrganizationsTable($tableGateway);
            return $table;
        },
                'PractitionerOrganizationsTableGateway' => function ($sm) {
            $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
            $resultSetPrototype = new ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new PractitionerOrganizations());
            return new TableGateway('practitioner_organization_list', $dbAdapter, null, $resultSetPrototype);
        },
                /* Practitioner Organizations List Table ends */

                /* User Feature Setting Table starts */
                'Admin\Model\UserFeatureSettingTable' => function($sm) {
            $tableGateway = $sm->get('UserFeatureSettingTableGateway');
            $table = new UserFeatureSettingTable($tableGateway);
            return $table;
        },
                'UserFeatureSettingTableGateway' => function ($sm) {
            $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
            $resultSetPrototype = new ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new UserFeatureSetting());
            return new TableGateway('user_feature_setting', $dbAdapter, null, $resultSetPrototype);
        },
                /* User Feature Setting Table ends */


                /* Practitioner Organizations Table starts */
                'Admin\Model\PractitionerOrganizationTable' => function($sm) {
            $tableGateway = $sm->get('PractitionerOrganizationTableGateway');
            $table = new PractitionerOrganizationsTable($tableGateway);
            return $table;
        },
                'PractitionerOrganizationTableGateway' => function ($sm) {
            $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
            $resultSetPrototype = new ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new PractitionerOrganization());
            return new TableGateway('practitioner_organization', $dbAdapter, null, $resultSetPrototype);
        },
                /* Practitioner Organizations Table ends */
                
                
                /* Testimonials Table starts */
                'Admin\Model\TestimonialsTable' => function($sm) {
            $tableGateway = $sm->get('TestimonialsTableGateway');
            $table = new TestimonialsTable($tableGateway);
            return $table;
        },
                'TestimonialsTableGateway' => function ($sm) {
            $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
            $resultSetPrototype = new ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new Testimonials());
            return new TableGateway('testimonials', $dbAdapter, null, $resultSetPrototype);
        },
                /* Testimonials Table ends */

                /* Site Modules Table starts */
                'Admin\Model\SiteModulesTable' => function($sm) {
            $tableGateway = $sm->get('SiteModulesTableGateway');
            $table = new SiteModulesTable($tableGateway);
            return $table;
        },
                'SiteModulesTableGateway' => function ($sm) {
            $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
            $resultSetPrototype = new ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new SiteModules());
            return new TableGateway('site_modules', $dbAdapter, null, $resultSetPrototype);
        },
                /* Testimonials Table ends */

                /* Rating type Table starts */
                'Admin\Model\RatingTypeTable' => function($sm) {
            $tableGateway = $sm->get('RatingTypeTableGateway');
            $table = new RatingTypeTable($tableGateway);
            return $table;
        },
                'RatingTypeTableGateway' => function ($sm) {
            $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
            $resultSetPrototype = new ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new RatingType());
            return new TableGateway('lookup_rating', $dbAdapter, null, $resultSetPrototype);
        },
                /* Rating type Table ends */

                /* Educations Table starts */
                'Admin\Model\EducationsTable' => function($sm) {
            $tableGateway = $sm->get('EducationsTableGateway');
            $table = new EducationsTable($tableGateway);
            return $table;
        },
                'EducationsTableGateway' => function ($sm) {
            $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
            $resultSetPrototype = new ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new Educations());
            return new TableGateway('educations', $dbAdapter, null, $resultSetPrototype);
        },
                /* Educations Table ends */

                /* Service Languages Table starts */
                'Admin\Model\ServiceLanguagesTable' => function($sm) {
            $tableGateway = $sm->get('ServiceLanguagesTableGateway');
            $table = new ServiceLanguagesTable($tableGateway);
            return $table;
        },
                'ServiceLanguagesTableGateway' => function ($sm) {
            $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
            $resultSetPrototype = new ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new ServiceLanguages());
            return new TableGateway('service_language', $dbAdapter, null, $resultSetPrototype);
        },
                /* Service Languages Table ends */


                /* Service Provider service Table starts */
                'Admin\Model\ServiceProviderServicesTable' => function($sm) {
            $tableGateway = $sm->get('ServiceProviderServicesTableGateway');
            $table = new ServiceProviderServicesTable($tableGateway);
            return $table;
        },
                'ServiceProviderServicesTableGateway' => function ($sm) {
            $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
            $resultSetPrototype = new ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new ServiceProviderServices());
            return new TableGateway('service_provider_service', $dbAdapter, null, $resultSetPrototype);
        },
                /* Service Provider service Table ends */

                /* Group Rights Table starts */
                'Admin\Model\GroupRightsTable' => function($sm) {
            $tableGateway = $sm->get('GroupRightsTableGateway');
            $table = new GroupRightsTable($tableGateway);
            return $table;
        },
                'GroupRightsTableGateway' => function ($sm) {
            $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
            $resultSetPrototype = new ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new GroupRights());
            return new TableGateway('grouprights', $dbAdapter, null, $resultSetPrototype);
        },
                /* Group Rights Table ends */

                /* User Rights Table starts */
                'Admin\Model\UserRightsTable' => function($sm) {
            $tableGateway = $sm->get('UserRightsTableGateway');
            $table = new UserRightsTable($tableGateway);
            return $table;
        },
                'UserRightsTableGateway' => function ($sm) {
            $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
            $resultSetPrototype = new ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new UserRights());
            return new TableGateway('user_rights', $dbAdapter, null, $resultSetPrototype);
        },
                /* User Rights Table ends */

                /* Advertisement Page Table starts */
                'Admin\Model\AdvertisementPageTable' => function($sm) {
            $tableGateway = $sm->get('AdvertisementPageTableGateway');
            $table = new AdvertisementPageTable($tableGateway);
            return $table;
        },
                'AdvertisementPageTableGateway' => function ($sm) {
            $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
            $resultSetPrototype = new ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new AdvertisementPage());
            return new TableGateway('advertisement_page', $dbAdapter, null, $resultSetPrototype);
        },
                /* Advertisement Page Table ends */
                
                /* Advertisement Table starts */
                'Admin\Model\AdvertisementTable' => function($sm) {
            $tableGateway = $sm->get('AdvertisementTableGateway');
            $table = new AdvertisementTable($tableGateway);
            return $table;
        },
                'AdvertisementTableGateway' => function ($sm) {
            $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
            $resultSetPrototype = new ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new Advertisement());
            return new TableGateway('advertisement', $dbAdapter, null, $resultSetPrototype);
        },
                /* Advertisement Table ends */
                
                /* Advertisement Plan Table starts */
                'Admin\Model\AdvertisementPlanTable' => function($sm) {
            $tableGateway = $sm->get('AdvertisementPlanTableGateway');
            $table = new AdvertisementPlanTable($tableGateway);
            return $table;
        },
                'AdvertisementPlanTableGateway' => function ($sm) {
            $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
            $resultSetPrototype = new ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new AdvertisementPlan());
            return new TableGateway('advertisement_plan', $dbAdapter, null, $resultSetPrototype);
        },
                /* Advertisement Plan Table ends */
                
                
                /* Email Templates Table Table starts */
                'Admin\Model\EmailtemplatesTable' => function($sm) {
            $tableGateway = $sm->get('EmailtemplatesTableGateway');
            $table = new EmailtemplatesTable($tableGateway);
            return $table;
        },
                'EmailtemplatesTableGateway' => function ($sm) {
            $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
            $resultSetPrototype = new ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new Emailtemplates());
            return new TableGateway('emailtemplates', $dbAdapter, null, $resultSetPrototype);
        },
                /* Email Templates Table ends */

                /* Banner Booking Table starts */
                'Admin\Model\BannerBookingsTable' => function($sm) {
            $tableGateway = $sm->get('BannerBookingsTableGateway');
            $table = new BannerBookingsTable($tableGateway);
            return $table;
        },
                'BannerBookingsTableGateway' => function ($sm) {
            $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
            $resultSetPrototype = new ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new BannerBookings());
            return new TableGateway('banner_booking', $dbAdapter, null, $resultSetPrototype);
        },
                /* Banner Booking Table ends */

                /* Payment History Table starts */
                'Admin\Model\RevenuesTable' => function($sm) {
            $tableGateway = $sm->get('RevenuesTableGateway');
            $table = new RevenuesTable($tableGateway);
            return $table;
        },
                'RevenuesTableGateway' => function ($sm) {
            $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
            $resultSetPrototype = new ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new Revenues());
            return new TableGateway('payment_history', $dbAdapter, null, $resultSetPrototype);
        },
                /* Payment History Table ends */

                /* Messages Table starts */
                'Admin\Model\MessagesTable' => function($sm) {
            $tableGateway = $sm->get('MessagesTableGateway');
            $table = new MessagesTable($tableGateway);
            return $table;
        },
                'MessagesTableGateway' => function ($sm) {
            $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
            $resultSetPrototype = new ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new Messages());
            return new TableGateway('messages', $dbAdapter, null, $resultSetPrototype);
        },
                /* Messages Table ends */

                /* Subscription Duration Table starts */
                'Admin\Model\SubscriptionDurationsTable' => function($sm) {
            $tableGateway = $sm->get('SubscriptionDurationsTableGateway');
            $table = new SubscriptionDurationsTable($tableGateway);
            return $table;
        },
                'SubscriptionDurationsTableGateway' => function ($sm) {
            $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
            $resultSetPrototype = new ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new SubscriptionDurations());
            return new TableGateway('subscription_duration', $dbAdapter, null, $resultSetPrototype);
        },
                /* Subscription Duration Table ends */

                /* User Subscription Table starts */
                'Admin\Model\SubscriptionsTable' => function($sm) {
            $tableGateway = $sm->get('SubscriptionsTableGateway');
            $table = new SubscriptionsTable($tableGateway);
            return $table;
        },
                'SubscriptionsTableGateway' => function ($sm) {
            $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
            $resultSetPrototype = new ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new Subscriptions());
            return new TableGateway('user_subscriptions', $dbAdapter, null, $resultSetPrototype);
        },
                /* User Subscription Table ends */

                /* Publisher banner Table starts */
                'Admin\Model\BannerUploadsTable' => function($sm) {
            $tableGateway = $sm->get('BannerUploadsTableGateway');
            $table = new BannerUploadsTable($tableGateway);
            return $table;
        },
                'BannerUploadsTableGateway' => function ($sm) {
            $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
            $resultSetPrototype = new ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new BannerUploads());
            return new TableGateway('publisher_banner', $dbAdapter, null, $resultSetPrototype);
        },
                /* Publisher banner Table ends */

                /* Sms Table starts */
                'Admin\Model\SmsTable' => function($sm) {
            $tableGateway = $sm->get('SmsTableGateway');
            $table = new SmsTable($tableGateway);
            return $table;
        },
                'SmsTableGateway' => function ($sm) {
            $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
            $resultSetPrototype = new ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new Sms());
            return new TableGateway('sms', $dbAdapter, null, $resultSetPrototype);
        },
                /* Sms Table ends */

                /* Sms History Table starts */
                'Admin\Model\SmsHistoryTable' => function($sm) {
            $tableGateway = $sm->get('SmsHistoryTableGateway');
            $table = new SmsHistoryTable($tableGateway);
            return $table;
        },
                'SmsHistoryTableGateway' => function ($sm) {
            $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
            $resultSetPrototype = new ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new SmsHistory());
            return new TableGateway('sms_history', $dbAdapter, null, $resultSetPrototype);
        },
                /* Sms History Table ends */

                /* Service provider site commision Table starts */
                'Admin\Model\ServiceProviderCommisionsTable' => function($sm) {
            $tableGateway = $sm->get('ServiceProviderCommisionsTableGateway');
            $table = new ServiceProviderCommisionsTable($tableGateway);
            return $table;
        },
                'ServiceProviderCommisionsTableGateway' => function ($sm) {
            $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
            $resultSetPrototype = new ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new ServiceProviderCommisions());
            return new TableGateway('service_provider_site_commision', $dbAdapter, null, $resultSetPrototype);
        },
                /* Service provider site commision Table ends */

                /* Media Table starts */
                'Admin\Model\MediaTable' => function($sm) {
            $tableGateway = $sm->get('MediaTableGateway');
            $table = new MediaTable($tableGateway);
            return $table;
        },
                'MediaTableGateway' => function ($sm) {
            $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
            $resultSetPrototype = new ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new Media());
            return new TableGateway('media', $dbAdapter, null, $resultSetPrototype);
        },
                /* Media Table ends */

                /* Video Views Table starts */
                'Admin\Model\VideoViewsTable' => function($sm) {
            $tableGateway = $sm->get('VideoViewsTableGateway');
            $table = new VideoViewsTable($tableGateway);
            return $table;
        },
                'VideoViewsTableGateway' => function ($sm) {
            $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
            $resultSetPrototype = new ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new VideoViews());
            return new TableGateway('video_views', $dbAdapter, null, $resultSetPrototype);
        },
                /* Video Views Table ends */

                /* Site Meta Table starts */
                'Admin\Model\SiteMetaTable' => function($sm) {
            $tableGateway = $sm->get('SiteMetaTableGateway');
            $table = new SiteMetaTable($tableGateway);
            return $table;
        },
                'SiteMetaTableGateway' => function ($sm) {
            $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
            $resultSetPrototype = new ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new SiteMeta());
            return new TableGateway('site_meta', $dbAdapter, null, $resultSetPrototype);
        },
                /* Site Meta Table ends */

                /* Site Actions Table starts */
                'Admin\Model\SiteActionsTable' => function($sm) {
            $tableGateway = $sm->get('SiteActionsTableGateway');
            $table = new SiteActionsTable($tableGateway);
            return $table;
        },
                'SiteActionsTableGateway' => function ($sm) {
            $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
            $resultSetPrototype = new ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new SiteActions());
            return new TableGateway('site_actions', $dbAdapter, null, $resultSetPrototype);
        },
                /* Site Actions Table ends */

                /* Notification Settings Table starts */
                'Admin\Model\NotificationSettingsTable' => function($sm) {
            $tableGateway = $sm->get('NotificationSettingsTableGateway');
            $table = new NotificationSettingsTable($tableGateway);
            return $table;
        },
                'NotificationSettingsTableGateway' => function ($sm) {
            $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
            $resultSetPrototype = new ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new NotificationSettings());
            return new TableGateway('notification_settings', $dbAdapter, null, $resultSetPrototype);
        },
                /* NotificationSettings Table ends */

                /* User Media starts */
                'Admin\Model\UsersMediaTable' => function($sm) {
            $tableGateway = $sm->get('UsersMediaTableGateway');
            $table = new UsersMediaTable($tableGateway);
            return $table;
        },
                'UsersMediaTableGateway' => function ($sm) {
            $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
            $resultSetPrototype = new ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new UsersMedia());
            return new TableGateway('media', $dbAdapter, null, $resultSetPrototype);
        },
                /* User Media ends */

                /* Service Provider Media starts */
                'Admin\Model\ServiceProviderMediaTable' => function($sm) {
            $tableGateway = $sm->get('ServiceProviderMediaTableGateway');
            $table = new ServiceProviderMediaTable($tableGateway);
            return $table;
        },
                'ServiceProviderMediaTableGateway' => function ($sm) {
            $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
            $resultSetPrototype = new ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new ServiceProviderMedia());
            return new TableGateway('media', $dbAdapter, null, $resultSetPrototype);
        },
                /* Service Provider Media ends */

                /* Service Provider Availability starts */
                'Admin\Model\ServiceProviderAvailabilityTable' => function($sm) {
            $tableGateway = $sm->get('ServiceProviderAvailabilityTableGateway');
            $table = new ServiceProviderAvailabilityTable($tableGateway);
            return $table;
        },
                'ServiceProviderAvailabilityTableGateway' => function ($sm) {
            $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
            $resultSetPrototype = new ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new ServiceProviderAvailability());
            return new TableGateway('service_provider_availability', $dbAdapter, null, $resultSetPrototype);
        },
                /* Service Provider Availability ends */

                /* Service Provider faq starts */
                'Admin\Model\SpfaqTable' => function($sm) {
            $tableGateway = $sm->get('SpfaqTableGateway');
            $table = new SpfaqTable($tableGateway);
            return $table;
        },
                'SpfaqTableGateway' => function ($sm) {
            $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
            $resultSetPrototype = new ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new Spfaq());
            return new TableGateway('sp_faq', $dbAdapter, null, $resultSetPrototype);
        },
                /* Service Provider faq ends */

                /* Site Setting table starts */
                'Admin\Model\SiteSettingsTable' => function($sm) {
            $tableGateway = $sm->get('SiteSettingsTableGateway');
            $table = new SiteSettingsTable($tableGateway);
            return $table;
        },
                'SiteSettingsTableGateway' => function ($sm) {
            $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
            $resultSetPrototype = new ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new SiteSettings());
            return new TableGateway('site_settings', $dbAdapter, null, $resultSetPrototype);
        },
                /* Site Setting table ends */

                /* Booking Suggestion History table starts */
                'Admin\Model\BookingSuggestionHistoryTable' => function($sm) {
            $tableGateway = $sm->get('BookingSuggestionHistoryTableGateway');
            $table = new SiteSettingsTable($tableGateway);
            return $table;
        },
                'BookingSuggestionHistoryTableGateway' => function ($sm) {
            $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
            $resultSetPrototype = new ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new BookingSuggestionHistory());
            return new TableGateway('booking_suggestion_history', $dbAdapter, null, $resultSetPrototype);
        },
                /* Booking Suggestion History table ends */
                
                /* Site Banners table starts */
                'Admin\Model\SiteBannersTable' => function($sm) {
            $tableGateway = $sm->get('SiteBannersTableGateway');
            $table = new SiteBannersTable($tableGateway);
            return $table;
        },
                'SiteBannersTableGateway' => function ($sm) {
            $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
            $resultSetPrototype = new ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new SiteBanners());
            return new TableGateway('banners', $dbAdapter, null, $resultSetPrototype);
        },
                /* Site Banners table ends */
                
                /* Subscription Feature table starts */
                'Admin\Model\SubscriptionFeaturesTable' => function($sm) {
            $tableGateway = $sm->get('SubscriptionFeaturesTableGateway');
            $table = new SubscriptionFeaturesTable($tableGateway);
            return $table;
        },
                'SubscriptionFeaturesTableGateway' => function ($sm) {
            $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
            $resultSetPrototype = new ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new SubscriptionFeatures());
            return new TableGateway('site_feature', $dbAdapter, null, $resultSetPrototype);
        },
                /* Subscription Feature table ends */
                
                /* Faq Index table starts */
                'Admin\Model\FaqIndexTable' => function($sm) {
            $tableGateway = $sm->get('FaqIndexTableGateway');
            $table = new FaqIndexTable($tableGateway);
            return $table;
        },
                'FaqIndexTableGateway' => function ($sm) {
            $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
            $resultSetPrototype = new ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new FaqIndex());
            return new TableGateway('faq_index', $dbAdapter, null, $resultSetPrototype);
        },
                /* Faq Index table ends */
                
                /* Faqs table starts */
                'Admin\Model\FaqsTable' => function($sm) {
            $tableGateway = $sm->get('FaqsTableGateway');
            $table = new FaqsTable($tableGateway);
            return $table;
        },
                'FaqsTableGateway' => function ($sm) {
            $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
            $resultSetPrototype = new ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new Faqs());
            return new TableGateway('faqs', $dbAdapter, null, $resultSetPrototype);
        },
                /* Faqs table ends */
                
                
                /* Test Setting table starts */
                'Admin\Model\TestTable' => function($sm) {
            $tableGateway = $sm->get('TestTableGateway');
            $table = new TestTable($tableGateway);
            return $table;
        },
                'TestTableGateway' => function ($sm) {
            $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
            $resultSetPrototype = new ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new Test());
            return new TableGateway('testOnly', $dbAdapter, null, $resultSetPrototype);
        },
                /* Test Setting table ends */
                'AuthService' => function($sm) {
            $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
            $dbTableAuthAdapter = new DbTableAuthAdapter($dbAdapter, 'users', 'user_name', 'pass', 'MD5(?)');

            $authService = new AuthenticationService();
            $authService->setAdapter($dbTableAuthAdapter);
            $authService->setStorage($sm->get('Admin\Model\MyAuthStorage'));

            return $authService;
        },
            ),
        );
    }

    public function onBootstrap(MvcEvent $e)
    {
        $eventManager = $e->getApplication()->getEventManager();

        $eventManager->attach('route', array($this, 'onRouteFinish'), -100);  // To get the routing details

        /* Admin Layout code starts here */
        $eventManager->getSharedManager()->attach('Zend\Mvc\Controller\AbstractActionController', 'dispatch', function($e) {
            $controller = $e->getTarget();
            $controllerClass = get_class($controller);
            $moduleNamespace = substr($controllerClass, 0, strpos($controllerClass, '\\'));
            $config = $e->getApplication()->getServiceManager()->get('config');
            if (isset($config['module_layouts'][$moduleNamespace])) {
                $controller->layout($config['module_layouts'][$moduleNamespace]);
            }
        }, 100);
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
        /* Admin Layout code ends here */

        $eventManager->attach(MvcEvent::EVENT_DISPATCH, function($e) {

            /* Controller and action name getting code starts here */
            $viewModel = $e->getViewModel();
            $viewModel->setVariable('controller', str_replace("Admin\Controller", "", $e->getRouteMatch()->getParam('controller')));
            $viewModel->setVariable('action', $e->getRouteMatch()->getParam('action'));
            /* Controller and action name getting code ends here */

            $controller = $e->getTarget();
            if ($controller instanceof Controller\Authcontroller) {
                $controller->layout('layout/login.phtml');
            }

            /* Login check starts here */
            if (stristr($e->getRouteMatch()->getParam("__NAMESPACE__"), 'admin') != false && !$controller instanceof Controller\Authcontroller) {
                if (!$e->getApplication()->getServiceManager()->get('AuthService')->hasIdentity()) {
                    return $e->getTarget()->plugin('redirect')->toRoute('admin/login');
                }
            }
            /* Login check ends here */

            /* Code for user permissions stars here */
            $controller = str_replace('Admin\Controller\\', '', $e->getRouteMatch()->getParam('controller'));

            // Array Of all the controllers who needs user authorization to perform various actions else all other controllers do not need permission
            $PermissionControllerArr = array('Countries', 'States', 'Activity', 'SiteModules', 'PractitionerOrganizations', 'Education', 'ServiceLanguages', 'Emailtemplates', 'PageLocation', 'BannerPlan', 'BannerDuration',
                'Banner', 'PageBannerLocation', 'BannerPlan', 'Revenue', 'RatingType', 'Ratings', 'Feedback', 'Booking', 'BannerBooking', 'SubscriptionType',
                'Subscription', 'ServiceCategory', 'Newsletter', 'NewsletterSubscriber', 'Page', 'Testimonials', 'Usertype', 'Users', 'UserCertifications', 'Consumers',
                'Partners', 'ServiceProvider', 'Messages', 'SubscriptionPlan', 'Sms', 'SiteMeta', 'NotificationSettings');

            $action = $e->getRouteMatch()->getParam('action');
            isset($_SESSION['user_permission']['rights']) ? $user_rights = $_SESSION['user_permission']['rights']['module'] : '';

            $controller == "Admin" ? $controller = "Dashboard" : $controller;

            if (!empty($controller) && in_array($controller, $PermissionControllerArr)) {

                if ($action == "index") {
                    if ($user_rights[$controller]['can_view'] != "1") {
                        die('Sorry this user has not sufficient permission to perform this action');
                    }
                } else if ($action == "add") {
                    if ($user_rights[$controller]['can_add'] != "1") {
                        die('Sorry this user has not sufficient permission to perform this action');
                    }
                } else if ($action == "edit") {
                    if ($user_rights[$controller]['can_edit'] != "1") {
                        die('Sorry this user has not sufficient permission to perform this action');
                    }
                } else if ($action == "delete") {
                    if ($user_rights[$controller]['can_del'] != "1") {
                        die('Sorry this user has not sufficient permission to perform this action');
                    }
                } else {
                    return true;
                }
            }
            /* Code for user permissions Ends here */
        });
    }

    public function onDispatch(MvcEvent $e)
    {
        $controller = $e->getTarget();
        $controller->layout('layout/layout');
    }

    /* Function to get the routing details */

    public function onRouteFinish($e)
    {
        $matches = $e->getRouteMatch();
        $controller = $matches->getParam('controller');
        //var_dump($matches);
    }

}
