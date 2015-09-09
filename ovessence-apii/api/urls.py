from django.conf.urls import patterns, url

from api import views,serviceprovider_views,all_views,tests

urlpatterns = patterns(
    '',    
    url(r'^address/$','api.views.address_list',name='address_list'),
    url(r'^address/(?P<pk>[0-9]+)/$','api.views.address_detail',name='address_detail'),
    url(r'^sprelateddata/$','api.serviceprovider_views.sprelateddata_list',name='sprelateddata_list'),
    url(r'^ratinginsert/$','api.all_views.ratinginsert_list',name='ratinginsert_list'),
    url(r'^feedback/$','api.all_views.feedback_list',name='feedback_list'),
    url(r'^booking/$','api.all_views.booking_list',name='booking_list'),
    url(r'^useractivity/$','api.views.useractivity_list',name='useractivity_list'),
    url(r'^sp_availability/$','api.all_views.sp_availability_list',name='sp_availability_list'),
    url(r'^consumerrelateddata/$','api.views.consumerrelateddata_list',name='consumerrelateddata_list'),
    #url(r'^appointment_delay_list/$','api.all_views.sp_appointment_delay_list',name='sp_appointment_delay_list'),
    #url(r'^test/$','api.tests.test_list',name='test_list'),
    url(r'^distance/$','api.all_views.distance_list',name='distance_list'),
    url(r'^manualbooking/$','api.all_views.manual_booking_list',name='manual_booking_list'),
    url(r'^response_rate/$','api.all_views.response_rate',name='response_rate'),  # added on 27-11-2014 by R
    
    
)
