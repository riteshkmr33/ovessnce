from django.conf.urls import patterns, include, url
from django.contrib import admin
admin.autodiscover()
#from users import views
from api import views,serviceprovider_views,all_views
from rest_framework import routers

router = routers.DefaultRouter()
router.register(r'api/pages', views.PagesViewSet)
router.register(r'api/country', views.CountryViewSet)
router.register(r'api/state', views.StateViewSet)

router.register(r'api/users/contact', views.UserContactViewSet)
router.register(r'api/users/certification', views.UserCertificationViewSet)
router.register(r'api/users', views.UsersViewSet)
router.register(r'api/userverification', views.UserVerificationViewSet)
router.register(r'api/userfeaturesetting', views.UserFeatureSettingViewSet)

# sp users
router.register(r'api/spusers/details', serviceprovider_views.SpUserDetailsViewSet)
router.register(r'api/spusers/contact', serviceprovider_views.SpUserContactViewSet)
router.register(r'api/spusers/spservices', serviceprovider_views.SpUserServiceViewSet)
router.register(r'api/spusers', serviceprovider_views.SpUserViewSet)
router.register(r'api/education', serviceprovider_views.EducationViewSet)
router.register(r'api/activity', serviceprovider_views.UserActivityViewSet)
router.register(r'api/servicecategory', serviceprovider_views.ServiceViewSet)
router.register(r'api/language', serviceprovider_views.LanguageViewSet)
router.register(r'api/iptolocation', serviceprovider_views.Ip2locationInfoViewSet)
router.register(r'api/account-deactivate-reason', serviceprovider_views.AccountDeactivateReasonsViewSet)
router.register(r'api/deactivated-account-list', serviceprovider_views.DeactivatedAccountListViewSet)
router.register(r'api/location-type', serviceprovider_views.LookupLocationTypeViewSet)

# all
router.register(r'api/sitefeatures', all_views.SiteFeatureViewSet)
router.register(r'api/subscription', all_views.SubscriptionViewSet)
router.register(r'api/usersubscription', all_views.UserSubscriptionsViewSet)
router.register(r'api/sp/reference', all_views.ServiceProviderReferenceViewSet)
router.register(r'api/ratingtype', all_views.LookupRatingViewSet)
router.register(r'api/newslettersubscription', all_views.NewsLetterSubscriptionSerializerViewSet)
router.register(r'api/emailtemplate', all_views.EmailTemplateViewSet)
router.register(r'api/messages', all_views.MessagesViewSet)
router.register(r'api/avgrating', all_views.AvgRateSerializerViewSet)
router.register(r'api/rating', all_views.RateSerializerViewSet)
router.register(r'api/sitesetting', all_views.SiteSettingsViewSet) 
router.register(r'api/availability_days', all_views.availabilityDaysViewSet)
router.register(r'api/wishlist', all_views.WishListViewSet)
router.register(r'api/appointment_delay_list', all_views.SPAppointmentDelayViewSet)
router.register(r'api/newsletter/send', all_views.NewsletterSendViewSet)
router.register(r'api/newsletter', all_views.NewsletterViewSet)
router.register(r'api/invoicedetails', all_views.InvoiceViewSet)
router.register(r'api/suggestionhistory', all_views.BookingSuggestionHistoryViewSet)
router.register(r'api/sms', all_views.SmsViewSet)
router.register(r'api/smshistory', all_views.SmsHistoryViewSet)
router.register(r'api/testimonials', all_views.TestimonialsViewSet)
router.register(r'api/card_details', all_views.userCardDetails)
router.register(r'api/search_city', all_views.searchCityZip)
router.register(r'api/faq_index', all_views.faqIndex)  # added on 26-11-2014 by R
router.register(r'api/faqs', all_views.faqs)  # added on 26-11-2014 by R
#routing banners
router.register(r'api/banner_page_loc', all_views.bannerPageLocation)
router.register(r'api/banners', all_views.banners)
router.register(r'api/advertisements', all_views.Advertisements)

#router.register(r'api/payment_refund_history', all_views.PaymentRefundHistoryViewSet)
# end sp users

# partners,practitioner organization
router.register(r'api/partners', all_views.PartnersViewSet)
router.register(r'api/sporganization', all_views.PractitionerOrganizationViewSet)
router.register(r'api/sporganizationlookup', all_views.PractitionerOrganizationLookupViewSet)
router.register(r'api/media', all_views.MediaViewSet)
#end 

urlpatterns = patterns('',
    #url(r'^admin/', include(admin.site.urls)),
    #url(r'^api/login', views.UsersLoginViewSet),
    url(r'^', include(router.urls)),
    url(r'^api/', include('api.urls')),
    #url(r'^api-auth/', include('rest_framework.urls', namespace='rest_framework')),
    #url(r'^docs/', include('rest_framework_swagger.urls')),
)




