from api.models import NewsLetterSubscription,Rate,Feedback,EmailTemplate,LookupRating,Booking,SiteFeature,Subscription,SpUserService
from api.models import Invoice,InvoiceDetails,PaymentHistory,SubscriptionDuration,UserSubscriptions,PractitionerOrganization
from api.models import AdvertisementPlan,Partners,BannerBooking,PublisherBanner,Media,SubscriptionFeature,Messages,SiteSettings,SpUserAvailability,availabilityDays
from api.models import WishList,SPAppointmentDelay,Newsletter,Newsletter,NewsletterSend,ServiceProviderReference,PaymentRefundHistory
from api.models import InvoiceDetailsWithoutRelation,PaymentHistoryWithoutRelation,BookingSuggestionHistory,SmsHistory,Sms
from api.models import PractitionerOrganizationLookup,Testimonials,UserCardDetails,searchCityZip, banners, bannerPageLocation, faqIndex, faqs, ResponseRate

from api.all_serializer import NewsLetterSubscriptionSerializer,RateSerializer,RateInsertSerializer,FeedbackSerializer
from api.all_serializer import EmailTemplateSerializer,LookupRatingSerializer,BookingSerializer,SiteFeatureSerializer,SubscriptionSerializer
from api.all_serializer import InvoiceSerializer,InvoiceDetailsSerializer,PaymentHistorySerializer,UserSubscriptionsSerializer
from api.all_serializer import PartnersSerializer,PractitionerOrganizationSerializer,BannerBookingSerializer,PublisherBannerSerializer,MediaSerializer
from api.all_serializer import MessagesSerializer,SiteSettingsSerializer,WishListSerializer,NewsletterSerializer,NewsletterSendSerializer
from api.all_serializer import ServiceProviderReferenceSerializer,InvoiceDetailsWSerializer,PaymentHistoryWSerializer,BookingSuggestionHistorySerializer
from api.all_serializer import SmsHistorySerializer,SmsSerializer,PractitionerOrganizationLookupSerializer,\
TestimonialsSerializer,ManualBookingsSerializer,UserCardDetailsSerializer,searchCityZipSerializer,  bannerPageLocationSerializer, bannersSerializer, faqIndexSerializer, faqsSerializer, ResponseRateSerializer

from api.serviceprovider_serializer import SpUserAvailabilitySerializer,availabilityDaysSerializer,SPAppointmentDelaySerializer

from rest_framework import status
from rest_framework.decorators import api_view,authentication_classes
from rest_framework.response import Response
from rest_framework import viewsets

from healthservices.authentication import APIAuthentication
import itertools
from itertools import chain
import simplejson as json
from django.core import serializers
from rest_framework import filters
from django.db import connection, transaction
from django.db.models import Avg, Max, Min,Sum
from django.db.models import Q
from datetime import date
# Class and function

class NewsLetterSubscriptionSerializerViewSet(viewsets.ModelViewSet):
    #authentication_classes = APIAuthentication,
    queryset = NewsLetterSubscription.objects.filter()
    serializer_class = NewsLetterSubscriptionSerializer
    #paginate_by = 2
    #paginate_by_param = 'page_size'
    #max_paginate_by = 100
    #def create(self, request, *args, **kwargs):
    #    return Response(status=status.HTTP_400_BAD_REQUEST)
    #def update(self, request, *args, **kwargs): 
    #    return Response(status=status.HTTP_400_BAD_REQUEST)    
    def destroy(self, request, *args, **kwargs):
        return Response(status=status.HTTP_400_BAD_REQUEST)
    def get_queryset(self):
        queryset = self.queryset
        status_id = self.request.QUERY_PARAMS.get('status_id', None)
        email = self.request.QUERY_PARAMS.get('email', None)
        if status_id is not None:
           queryset = queryset.filter(status_id=status_id) 
        if email is not None:
           queryset = queryset.filter(email=email)           
        return queryset 
    def update(self, request, pk=None):
         try:
           messages = NewsLetterSubscription.objects.get(id=pk)
         except:
           return Response(status=status.HTTP_404_NOT_FOUND)
       
         serializer = NewsLetterSubscriptionSerializer(messages,data=request.DATA,partial=True)
         if serializer.is_valid():
            serializer.save()
            return Response(serializer.data)
         else:
            return Response(serializer.errors, status=status.HTTP_400_BAD_REQUEST)    
#---- Rating List and Insert the rating given by consumer ---------------#    
class RateSerializerViewSet(viewsets.ModelViewSet):
    #authentication_classes = APIAuthentication,
    queryset = Rate.objects.filter()
    serializer_class = RateSerializer
    def get_queryset(self):
        queryset = self.queryset
        service_id = self.request.QUERY_PARAMS.get('service_id', None)
        users_id = self.request.QUERY_PARAMS.get('users_id', None)
        created_by = self.request.QUERY_PARAMS.get('created_by', None)
        if service_id is not None and users_id is not None and  created_by is not None:
           queryset = queryset.filter(service_id=service_id,users_id=users_id,created_by=created_by)
        if service_id is not None and users_id is not None:
           queryset = queryset.filter(service_id=service_id,users_id=users_id)           
        elif service_id is not None and created_by is not None:
           queryset = queryset.filter(service_id=service_id,created_by=created_by)
        elif users_id is not None and created_by is not None:  
           queryset = queryset.filter(users_id=users_id,created_by=created_by) 
        elif users_id is not None:
           queryset = queryset.filter(users_id=users_id) 
        elif service_id is not None:
           queryset = queryset.filter(service_id=service_id)           
        return queryset  
    
class AvgRateSerializerViewSet(viewsets.ModelViewSet):
    #authentication_classes = APIAuthentication,
    queryset = Rate.objects.values('users_id','service_id').annotate(rate=Avg('rate')).filter(rate__gte=2)
    serializer_class = RateSerializer
  
class LookupRatingViewSet(viewsets.ModelViewSet):
    #authentication_classes = APIAuthentication,
    queryset = LookupRating.objects.filter()
    serializer_class = LookupRatingSerializer
    def get_queryset(self):
        queryset = self.queryset
        status_id = self.request.QUERY_PARAMS.get('status_id', None)
        if status_id is not None:
           queryset = queryset.filter(status_id=status_id) 
        return queryset    
    def update(self, request, pk=None):
         try:
           messages = LookupRating.objects.get(id=pk)
         except:
           return Response(status=status.HTTP_404_NOT_FOUND)
       
         serializer = LookupRatingSerializer(messages,data=request.DATA,partial=True)
         if serializer.is_valid():
            serializer.save()
            return Response(serializer.data)
         else:
            return Response(serializer.errors, status=status.HTTP_400_BAD_REQUEST)    

def sql_select(sql):
    cursor = connection.cursor()
    cursor.execute(sql)
    results = cursor.fetchall()
    list = []
    i = 0
    for row in results:
        dict = {} 
        field = 0
        while True:
           try:
                dict[cursor.description[field][0]] = str(results[i][field])
                field = field +1
           except IndexError as e:
                break
        i = i + 1
        list.append(dict) 
    return list

@api_view(['GET','POST'])
def ratinginsert_list(request):

    if request.method == 'GET':
    
            return Response({'status':'fail','msg':'This service support only Post Method'},status=status.HTTP_400_BAD_REQUEST)
    
    elif request.method == 'POST':
         try: 
           if request.DATA['rating']:
             try:
               rating_obj = json.loads(request.DATA['rating'])
             except:
               return Response({'status':'fail','msg':'Please provide correct Json Format for rating'},status=status.HTTP_400_BAD_REQUEST)
            
             serializer = RateInsertSerializer(data=rating_obj) 
             if serializer.is_valid():
               serializer.save()
               return Response(serializer.data,status=status.HTTP_201_CREATED)
             else:
               return Response(serializer.errors, status=status.HTTP_400_BAD_REQUEST)        
           return Response({'msg':'Please provide correct Json Format for rating'}) 
         except: 
           return Response({'msg':'Please provide rating data'},status=status.HTTP_400_BAD_REQUEST)   
@api_view(['GET','POST'])
def feedback_list(request):

    if request.method == 'GET':
        query_str=''
        service_id =request.QUERY_PARAMS.get('service_id', None)
        user_id = request.QUERY_PARAMS.get('user_id', None) 
        status_id = request.QUERY_PARAMS.get('status_id', '')
        view_status = request.QUERY_PARAMS.get('view_status', None) # added on 17-11-2014 by R

        if service_id is not None and user_id is not None:
            query_str = ' where feedback.service_id = '+service_id+' and feedback.users_id='+user_id+' ' 
        # Condition added on 17-11-2014 by R starts here #
        elif  user_id is not None and view_status is not None:
            query_str = ' where feedback.users_id='+user_id+' and feedback.view_status = '+ view_status+''
        # Condition added on 17-11-2014 by R ends here #
        elif  user_id is not None:
            query_str = ' where feedback.users_id='+user_id+' '
            
        if query_str and  status_id:
            query_str = query_str + ' and feedback.status_id='+status_id
        elif status_id :
            query_str = ' where feedback.status_id='+status_id   
          
          
        # Paging Start Here #
        page = int(request.QUERY_PARAMS.get('page', 0))
        no_of_records = int(request.QUERY_PARAMS.get('no_of_records', 10))              
        if page: 
         starting_point = (page-1)*no_of_records
        else:
         starting_point = 0
        total = sql_select("SELECT count(feedback.id) as total  from feedback inner join users on users.id=feedback.created_by "+query_str+" order by feedback.id DESC")
        limit_str = ''
        if total[0]['total']>no_of_records:
            limit_str =  '  limit '+str(starting_point)+','+str(no_of_records)         
        #---- End ----------#          

        feedback = sql_select("SELECT feedback.id,feedback.users_id,feedback.status_id,users.user_name,users.first_name,users.last_name,users.avtar_url,feedback.service_id,feedback.comments,feedback.created_date,feedback.created_by,feedback.view_status from feedback inner join users on users.id=feedback.created_by "+query_str+" order by feedback.id DESC "+limit_str+"")   
        
        results = {}
        results['results']=feedback
        results['count']=total[0]['total']
        #serializer = FeedbackSerializer(feedback)
        return Response(results)
    
    elif request.method == 'POST':
         view_status = request.DATA.get('view_status', None)
         user_id = request.DATA.get('user_id', None)
         #assert False, user_id
         if user_id is not None and view_status is not None:
            feedbacks = Feedback.objects.filter(users_id=user_id)
            results = []
            for feedback in feedbacks:
                serializer = FeedbackSerializer(feedback,data=request.DATA,partial=True)
                if serializer.is_valid():
                    serializer.save()
                    results.append(serializer.data)
                else:
                    return Response(serializer.errors, status=status.HTTP_400_BAD_REQUEST)
            return Response(results)
         else:
            serializer = FeedbackSerializer(data=request.DATA)
            if serializer.is_valid():
               serializer.save()
               return Response(serializer.data,status=status.HTTP_201_CREATED)
            else:
               return Response(serializer.errors, status=status.HTTP_400_BAD_REQUEST)    
#-------------------End-----------------------------------#    

#----------------- Email Templates --------------#
class EmailTemplateViewSet(viewsets.ModelViewSet):
    #authentication_classes = APIAuthentication,
    queryset = EmailTemplate.objects.filter()
    serializer_class = EmailTemplateSerializer
    # Make EmailTemplate readable only
    def create(self, request, *args, **kwargs):
        return Response(status=status.HTTP_400_BAD_REQUEST)
    def update(self, request, *args, **kwargs): 
        return Response(status=status.HTTP_400_BAD_REQUEST) 
    def destroy(self, request, *args, **kwargs):
        return Response(status=status.HTTP_400_BAD_REQUEST)    
#------------------ End -------------------------#

#----------------- Search Templates --------------#
class searchCityZip(viewsets.ModelViewSet):
    """ Search world city zip database for suggestion """
    queryset = searchCityZip.objects.filter()
   # queryset.query.group_by = ['country_name','zip_code']
    serializer_class = searchCityZipSerializer
    paginate_by = 10
    paginate_by_param = 'page_size'
    max_paginate_by = 100
    def get_queryset(self):
        queryset = self.queryset
        param = self.request.QUERY_PARAMS.get("param",None)
        #zip_code = self.request.QUERY_PARAMS.get("zip_code",None)
        if param is not None:
            queryset = queryset.filter(Q(country_name__icontains= param)|Q(region_name__icontains=param)|Q(county_name__icontains=param)|Q(city_name__icontains=param)|Q(zip_code__icontains=param)).order_by('city_name')
        else:
            queryset = queryset.filter().order_by('city_name')
        #print queryset.query
        return queryset
    
    # Let Make API readable only
    def create(self, request, *args, **kwargs):
        return Response(status=status.HTTP_400_BAD_REQUEST)
    def update(self, request, *args, **kwargs): 
        return Response(status=status.HTTP_400_BAD_REQUEST) 
    def destroy(self, request, *args, **kwargs):
        return Response(status=status.HTTP_400_BAD_REQUEST)    
#------------------ End -------------------------#

#----------------- Search Templates --------------#
class banners(viewsets.ModelViewSet):
    """ Manage banners """
    queryset = banners.objects.filter()
    serializer_class = bannersSerializer
    paginate_by = 10
    paginate_by_param = 'page_size'
    max_paginate_by = 100
    def get_queryset(self):
        queryset = self.queryset
        page_location_id = self.request.QUERY_PARAMS.get('page_location_id',None)
        status_id = self.request.QUERY_PARAMS.get('status_id',None)
        if page_location_id is not None:
            queryset = queryset.filter(page_location_id=page_location_id).order_by('id')
        if status_id is not None:
            queryset = queryset.filter(status_id=status_id).order_by('id')
        #print queryset.query
        return queryset
    
#------------------ End -------------------------#
#----------------- Search Templates --------------#
class bannerPageLocation(viewsets.ModelViewSet):
    """ Manage Bannerpage locations """
    queryset = bannerPageLocation.objects.filter()
    serializer_class = bannerPageLocationSerializer
    paginate_by = 10
    paginate_by_param = 'page_size'
    max_paginate_by = 100
    def get_queryset(self):
        queryset = self.queryset
        #print queryset.query
        return queryset
    
    
#------------------ End -------------------------#

#----------------- Booking --------------#    
def durationinText(no,mode=0):
     if mode==1:
        if no==1:
          str='Days'
        elif no==2:
          str='Months'
        elif no==3:
          str='Years'
     else:
        if no==1:
          str='Years'
        elif no==2:
          str='Months'
        elif no==3:
          str='Days'         
     return str
@api_view(['GET','POST','PUT']) 
def booking_list(request):

    if request.method == 'GET':
        
        # Here we returned booking_suggestion_history data
        
        booking_ids = request.QUERY_PARAMS.get('booking_ids', '')
        
        i=0
        if booking_ids:
         from collections import defaultdict   
         booking_suggestion_history =defaultdict(defaultdict)
         all_booking_suggestion_history = sql_select("SELECT booking_suggestion_history.*  from booking_suggestion_history where booking_id in("+booking_ids+")")
         for temp in all_booking_suggestion_history:
                  booking_suggestion_history[temp['booking_id']][temp['id']]= temp          
         return Response(booking_suggestion_history)         
                  
        # End 
        
        booking_type = request.QUERY_PARAMS.get('booking_type', '')
        
        if booking_type=='':            
            query_str=''
            #service_id =request.QUERY_PARAMS.get('service_id', None)
            service_provider_id = request.QUERY_PARAMS.get('service_provider_id', None)
            id = request.QUERY_PARAMS.get('id', None)
            service_provider_service_id = request.QUERY_PARAMS.get('service_provider_service_id', None)
            user_id = request.QUERY_PARAMS.get('user_id', None)
            status_id = request.QUERY_PARAMS.get('status_id', '')
            
            booking_status = request.QUERY_PARAMS.get('booking_status', None)
            
            booking_time = request.QUERY_PARAMS.get('booking_time', None)
            manual = request.QUERY_PARAMS.get('manual', 1) # Added by R on 12-11-2014 #
            
            if service_provider_id is not None and user_id is not None and id is not None:
                query_str = ' where booking.service_provider_id='+service_provider_id+' and booking.user_id='+user_id+' and booking.id='+id+' '            
            
            #----- Condition parameter given by adarsh -------- # 
            elif service_provider_id is not None and user_id is not None and service_provider_service_id is not None: 
                query_str = ' where booking.service_provider_id='+service_provider_id+' and booking.user_id='+user_id+' and booking.service_provider_service_id='+service_provider_service_id+' '
            # --- end -----#
            
            elif service_provider_id is not None and user_id is not None: 
                query_str = ' where booking.service_provider_id='+service_provider_id+' and booking.user_id='+user_id+' ' 
                
            elif service_provider_id is not None and id is not None: 
                query_str = ' where booking.service_provider_id='+service_provider_id+' and booking.id='+id+' ' 
                
            elif user_id is not None and id is not None: 
                query_str = ' where booking.user_id='+user_id+' and booking.id='+id+' '
            # Condition added on 12-11-2014 by R starts here #
            elif service_provider_id is not None and manual == "0":
                query_str = ' where booking.service_provider_id='+service_provider_id+' and booking.user_id is not null '
            # Condition added on 12-11-2014 by R ends here#
            elif service_provider_id is not None:
                query_str = ' where booking.service_provider_id='+service_provider_id+' '
            elif service_provider_service_id is not None:
                query_str = ' where booking.service_provider_service_id='+service_provider_service_id+' '
            elif user_id is not None:
                query_str = ' where booking.user_id='+user_id+' '   
            # fetch the booking based on booking id
            elif id is not None :
                query_str = ' where booking.id='+id
            # end
            if query_str and  status_id:
                query_str = query_str + ' and booking.status_id='+status_id
            elif status_id :
                query_str = ' where booking.status_id='+status_id
                
            if booking_time is not None:
                booking_param = " AND booking_time like '"+booking_time+"%' "
            else:
                booking_param = ""
            
            
                

            
            # Paging Start Here #
            page = int(request.QUERY_PARAMS.get('page', 0))
            no_of_records = int(request.QUERY_PARAMS.get('no_of_records', 10))
            #---- End ----------#
            # Here we are checking booking status in booking_suggestion_history
            if booking_status is not None :
                
                
             total = sql_select("SELECT count(*) as total  from booking INNER join (select id,booking_id from booking_suggestion_history WHERE booking_status = "+booking_status+booking_param+" AND id IN (SELECT MAX(id) FROM booking_suggestion_history GROUP BY booking_id ORDER BY id DESC))x  on x.booking_id=booking.id left join service_provider_service as sps on booking.service_provider_service_id = sps.id left join service_category as scat on sps.service_id = scat.id left join users on  booking.user_id=users.id left join users as susers on  booking.service_provider_id=susers.id  LEFT JOIN mannual_booking_details as mb on booking.id =  mb.booking_id "+query_str+" order by booking.id DESC")
             
             
            else:
             
             total = sql_select("SELECT count(booking.user_id) as total  from booking left join service_provider_service as sps on booking.service_provider_service_id = sps.id left join service_category as scat on sps.service_id = scat.id left join users on  booking.user_id=users.id left join users as susers on  booking.service_provider_id=susers.id  LEFT JOIN mannual_booking_details mb on booking.id =  mb.booking_id "+query_str+" order by booking.id DESC")
             
            
            
            if page: 
             starting_point = (page-1)*no_of_records
            else:
             starting_point = 0   

            limit_str = ''
            if total[0]['total']>no_of_records and page>0:
                limit_str =  '  limit '+str(starting_point)+','+str(no_of_records)
            results = {}
            booking_ids = ''
            if booking_status is not None :

             booking = sql_select("SELECT booking.*,sps.duration,sps.price,scat.category_name,users.first_name as consumer_first_name, users.last_name as consumer_last_name,users.avtar_url as consumer_avtar_url,users.email as consumer_email,susers.first_name as sp_first_name, susers.last_name as sp_last_name,susers.avtar_url as sp_avtar_url,susers.email as sp_email "+
                                 ", invdts.sale_item_details,invdts.amount, sps.service_id "+
                                 " ,ph.payment_method_id as payment_history_payment_method_id,ph.payment_date as payment_history_payment_date,ph.amount_paid as payment_history_amount_paid, ph.currency as payment_history_currency, ph.status_id as payment_history_status_id "+
                                 " ,prh.payment_method_id as prefund_payment_method_id ,prh.payment_date  as prefund_payment_date,prh.amount_refunded as prefund_amount_refunded, prh.currency as prefund_currency , prh.status_id as prefund_status_id, mb.first_name as mb_first_name,mb.last_name as mb_last_name "+
                                 " from booking INNER join  (select id,booking_id,booking_time from booking_suggestion_history WHERE booking_status = "+booking_status+booking_param+" AND id IN (SELECT MAX(id) FROM booking_suggestion_history GROUP BY booking_id ORDER BY id DESC) )x  on x.booking_id=booking.id "+
                                 " LEFT join service_provider_service as sps on booking.service_provider_service_id = sps.id LEFT join service_category as scat on sps.service_id = scat.id LEFT join users on  booking.user_id=users.id LEFT join users as susers on  booking.service_provider_id=susers.id "+
                                 " LEFT join invoice on booking.invoice_id = invoice.id  left join invoice_details as invdts on invoice.id =invdts.invoice_id left join payment_history as ph on ph.invoice_id = invoice.id left join payment_refund_history as prh on  prh.payment_history_id = ph.id LEFT JOIN mannual_booking_details as mb on booking.id =  mb.booking_id "+
                                 query_str+" order by id DESC "+limit_str+"")                                 
            else:
             booking = sql_select("SELECT booking.*,sps.duration,sps.price,scat.category_name,users.first_name as consumer_first_name, users.last_name as consumer_last_name,users.avtar_url as consumer_avtar_url,users.email as consumer_email,susers.first_name as sp_first_name, susers.last_name as sp_last_name,susers.avtar_url as sp_avtar_url,susers.email as sp_email "+
                                 ", invdts.sale_item_details,invdts.amount ,sps.service_id "+
                                 " ,ph.payment_method_id as payment_history_payment_method_id,ph.payment_date as payment_history_payment_date,ph.amount_paid as payment_history_amount_paid, ph.currency as payment_history_currency, ph.status_id as payment_history_status_id "+
                                 " ,prh.payment_method_id as prefund_payment_method_id ,prh.payment_date  as prefund_payment_date,prh.amount_refunded as prefund_amount_refunded, prh.currency as prefund_currency , prh.status_id as prefund_status_id, mb.first_name as mb_first_name,mb.last_name as mb_last_name  "+
                                 " from booking LEFT join service_provider_service as sps on booking.service_provider_service_id = sps.id LEFT join service_category as scat on sps.service_id = scat.id LEFT join users on  booking.user_id=users.id LEFT join users as susers on  booking.service_provider_id=susers.id "
                                 " LEFT join invoice on booking.invoice_id = invoice.id  LEFT join invoice_details as invdts on invoice.id =invdts.invoice_id left join payment_history as ph on ph.invoice_id = invoice.id left join payment_refund_history as prh on  prh.payment_history_id = ph.id LEFT JOIN mannual_booking_details mb on booking.id =  mb.booking_id "+
                                 query_str+" order by id DESC "+limit_str+"")                
            
            #print 'booking_status',booking_status
            #assert False, locals()
            results['results']=booking
            for temp in booking:
                   booking_ids = temp['id']+','+booking_ids
            results['booking_ids']=booking_ids[:-1].strip()      
            results['count']=total[0]['total']
            
            return Response(results)
        
        elif booking_type=='subscription':
            query_str=''
            #service_id =request.QUERY_PARAMS.get('service_id', None)
            user_id      = request.QUERY_PARAMS.get('user_id', None)
            id = request.QUERY_PARAMS.get('id', None)
            if user_id is not None:
                query_str = ' where usubs.user_id='+user_id+' '
            # fetch the booking based on booking id
            elif id is not None :
                query_str = ' where usubs.id='+id    
            # Paging Start Here #
            page = int(request.QUERY_PARAMS.get('page', 0))
            no_of_records = int(request.QUERY_PARAMS.get('no_of_records', 10))
            #---- End ----------#    
            total = sql_select("SELECT count(usubs.user_id) as total  from user_subscriptions as usubs inner join users on  usubs.user_id=users.id  "+query_str+" order by usubs.id DESC")

            if page: 
             starting_point = (page-1)*no_of_records
            else:
             starting_point = 0   

            limit_str = ''
            if total[0]['total']>no_of_records:
                limit_str =  '  limit '+str(starting_point)+','+str(no_of_records)
            results = {}
            booking = sql_select("SELECT usubs.*,s_duration.duration as s_duration_duration,s_duration.duration_in as s_duration_duration_in,s_duration.price as s_duration_price,s_duration.currency as s_duration_currency,s_duration.status_id  as s_duration_status_id  "+
                                 ", subscription.subscription_name as subscription_subscription_name,subscription.status_id as  subscription_status_id  "+
                                 ", invdts.sale_item_details as invoice_details_sale_item_details ,invdts.amount as invoice_details_sale_amount "+
                                 " ,ph.payment_method_id as payment_history_payment_method_id,ph.payment_date as payment_history_payment_date,ph.amount_paid as payment_history_amount_paid, ph.currency as payment_history_currency, ph.status_id as payment_history_status_id "+
                                 
                                 " from user_subscriptions as usubs inner join users on  usubs.user_id=users.id "+
                                 " inner join invoice on usubs.invoice_id = invoice.id  left join invoice_details as invdts on invoice.id =invdts.invoice_id left join payment_history as ph on ph.invoice_id = invoice.id  "+
                                 " inner join subscription_duration as s_duration on s_duration.id = invdts.subscription_duration_id " +
                                 " inner join subscription on subscription.id = s_duration.subscription_id " +                          
                                 query_str+" order by id DESC "+limit_str+"")
            results['results']=booking
            results['count']=total[0]['total']
            
            return Response(results)         
    
    elif request.method == 'POST':
         # get the service provider service name
         subscription_duration_id=request.POST.get('subscription_duration_id','')
         service_provider_id=request.POST.get('service_provider_id','')
         service_provider_service_id=request.POST.get('service_provider_service_id','')
         advertisement_plan_id=request.POST.get('advertisement_plan_id','')
         invoice_status = request.POST.get('invoice_status','')
         payment_status_id= request.POST.get('payment_status_id','')
         booked_date= request.POST.get('booked_date','')
         booking_status= request.POST.get('booking_status','')
         service_address_id = request.POST.get('service_address_id','')
         
         serviceName='' 
         sale_type='' 
                        
         end_date=request.POST.get('subscription_end_date','')
         
         if  service_provider_id and service_provider_service_id and payment_status_id:
           try: 
            service = SpUserService.objects.get(id=request.DATA['service_provider_service_id'])  
            serviceName = "Service - "+service.service.category_name+" "+str(service.duration)+" mins"
            sale_type = 3
           except SpUserService.DoesNotExist:   
            serviceName='' 
            sale_type=''
         elif subscription_duration_id and payment_status_id:
            try: 
              subscription = SubscriptionDuration.objects.get(id=subscription_duration_id) 
              subscription.duration_in=durationinText(subscription.duration_in,mode=1)               
              serviceName = "Subscription Plan - "+subscription.subscription.subscription_name+" "+str(subscription.duration)+" "+str(subscription.duration_in)
              sale_type=1
              """from datetime import date
              from dateutil.relativedelta import relativedelta                
              if subscription.duration == 1 :
                         end_date = date.today() + relativedelta(years = +subscription.duration)
              elif subscription.duration == 2 :
                         end_date = date.today() + relativedelta(months = +subscription.duration)
              elif subscription.duration == 3 :
                         end_date = date.today() + relativedelta(days = +subscription.duration)"""              
            except SpUserService.DoesNotExist: 
              return Response({"Error":"Please provide required fields like subscription_duration_id,payment_status_id"}, status=status.HTTP_400_BAD_REQUEST)       
         elif advertisement_plan_id:

            try:  
              banner = AdvertisementPlan.objects.get(id=advertisement_plan_id)
              banner.duration_in=durationinText(banner.duration_in) 
              serviceName = "Advertisement Plan - "+banner.plan_name+" "+str(banner.duration)+" "+str(banner.duration_in)             
              sale_type=2
            except:
              return Response({"Error":"Please provide correct advertisement_plan_id"}, status=status.HTTP_400_BAD_REQUEST)   
         else:
            return Response({"Error":"Please provide required fields like payment_status_id,invoice_status"}, status=status.HTTP_400_BAD_REQUEST)       
            #subscription = UserSubscriptions.objects.filter(user_id=request.DATA['user_id'],subscription_duration_id=request.DATA['subscription_duration_id'],status_id=1)
            #if subscription.count() > 0:

         
         # make the bookin,invoice,invoice_details,payment history data list
         postData={
         
                  "booking":{
                    'user_id' : request.POST.get('user_id',''),
                    'service_provider_id' : request.POST.get('service_provider_id',''),
                    'service_provider_service_id' : service_provider_service_id,
                    'booked_date'  : booked_date,
                    'payment_status' : request.POST.get('payment_status',''),
                    'status_id'  : request.POST.get('status_id',''),
                    'created_date' : request.POST.get('created_date',''),
                    'service_address_id': request.POST.get('service_address_id','')
                  },
                  "subscription":{
                    'user_id' : request.POST.get('user_id',''),
                    'subscription_duration_id' : subscription_duration_id,
                    'subscription_start_date' : request.POST.get('subscription_start_date',''),
                    'subscription_end_date' : end_date,
                    'status_id' : request.POST.get('status_id',''),
                    'user_card_id' : request.POST.get('user_card_id','')  
                  },
                  "banner_booking":{
                    'user_id' : request.POST.get('user_id',''),
                    'advertisement_plan_id' : request.POST.get('advertisement_plan_id',''),
                    'booking_date' : booked_date,
                    'invoice_id' : '',                
                  },
                  "invoice_data":{
                    'user_id' : request.POST.get('user_id',''),
                    'sale_type' : sale_type,
                    'invoice_total' : request.POST.get('invoice_total',''),
                    'created_by' : request.POST.get('created_by',''),
                    'status_id' : request.POST.get('invoice_status',''),
                    'created_date': request.POST.get('created_date',''),
                    'site_commision': request.POST.get('site_commision','')
                  },
                  "invoice_details_data":{
                    'sale_item_details' : serviceName,
                    'amount' : request.POST.get('amount',''),
                    'invoice_id':'',
                    'service_provider_service_id':service_provider_service_id,
                    'subscription_duration_id' : subscription_duration_id
                  }, 
                  "ph_data":{
                    'payment_method_id' : request.POST.get('payment_method_id',''),
                    'payment_instrument_no' : request.POST.get('payment_instrument_no',''),
                    'amount_paid' : request.POST.get('amount_paid',''),
                    'status_id' : request.POST.get('payment_status_id',''),
                    'invoice_id':'',
                    'payment_date':request.POST.get('payment_date',''),
                    'transaction_id':request.POST.get('transaction_id',''),
                    'currency': request.POST.get('currency','')
                  },
                  "booking_suggestion_history":{
                    'user_id' : request.POST.get('user_id',''),
                    'booking_id' : '',
                    'booking_time': booked_date,
                    'booking_status':booking_status
                  },                  

                }
         
         #invoice_id = request.QUERY_PARAMS.get('invoice_id',None)         
         #if invoice_id is None:
         
         invoiceserializer = InvoiceSerializer(data=postData["invoice_data"])
         invoicedetailsserializer = InvoiceDetailsWSerializer(data=postData["invoice_details_data"])
         phistoryserializer = PaymentHistoryWSerializer(data=postData["ph_data"])
         bshserializer = BookingSuggestionHistorySerializer(data=postData["booking_suggestion_history"])
 
         if subscription_duration_id:
             bookingType = UserSubscriptionsSerializer(data=postData["subscription"])
         elif advertisement_plan_id:
             bookingType = BannerBookingSerializer(data=postData["banner_booking"])
         else:             
             bookingType = BookingSerializer(data=postData["booking"])
  
         if invoiceserializer.is_valid():
            if invoicedetailsserializer.is_valid():
                if phistoryserializer.is_valid():
                   if bookingType.is_valid(): 
                        #------ insert into invoice table --------#
                        invoiceserializer.save()
                        invoice=Invoice.objects.latest('id')
                        #------- Again call serializer to assign invoice.di -------------#
                        invoicedetailsserializer.object.invoice_id =invoice.id
                        phistoryserializer.object.invoice_id=invoice.id 

                        #------ insert into Invoice detail table --------#

                        invoicedetailsserializer.save()
                        #------ insert into PaymentHistory table --------#

                        phistoryserializer.save()
                        
                        if subscription_duration_id:
                             #check subscription already exist
                             try:
                                listing=UserSubscriptions.objects.get(user_id=postData["subscription"]['user_id'],subscription_duration_id=postData["subscription"]['subscription_duration_id'])
                                listing.subscription_start_date=postData["subscription"]['subscription_start_date']
                                listing.subscription_end_date=end_date
                                listing.invoice_id = invoice.id
                                listing.status_id = 1
                                listing.save()
                                user_sub_id = listing.id
                                   
                             except:
                                bookingType.object.invoice_id =invoice.id  
                                bookingType.save()
                                user_sub_id = bookingType.data['id']
                             #try:
                             temp = UserSubscriptions.objects.filter(user_id=postData["subscription"]['user_id']).values('id').exclude(id=user_sub_id)
                             user_subs_ids=''
                             for user_subs in temp:
                                 user_subs_ids = str(user_subs['id'])+','+user_subs_ids
                             user_subs_ids=user_subs_ids[:-1].strip()
                             if user_subs_ids:
                                sql_select("update user_subscriptions set status_id=2 where id in ("+user_subs_ids+")")
                             bookingType.data['id']=user_sub_id
                             ## temp=""  
                        elif advertisement_plan_id:
                             bookingType.object.invoice_id=invoice.id 
                             #bookingType.object.subscription_end_date=end_date
                             bookingType.save()
                        else:    
                             bookingType.object.invoice_id=invoice.id                          
                             bookingType.save()
                             booking_id=Booking.objects.latest('id')
                             # save the data in booking_suggestion_history table too 
                             bshserializer.is_valid()
                             bshserializer.object.booking_id = booking_id.id
                             bshserializer.save()                           
                             
                        return Response(bookingType.data)      
                   else:          
                        return Response(bookingType.errors, status=status.HTTP_400_BAD_REQUEST)          
                else:
                   return Response(phistoryserializer.errors, status=status.HTTP_400_BAD_REQUEST)          
            else:
                  return Response(invoicedetailsserializer.errors, status=status.HTTP_400_BAD_REQUEST)
                             
         else:
            return Response(invoiceserializer.errors, status=status.HTTP_400_BAD_REQUEST)
    elif request.method == 'PUT':
         #booking = Booking.objects.get(id=request.DATA['id'])
         #return Response(data=booking)
         
         try:
           booking = Booking.objects.get(id=request.DATA['id'])
         except:
           return Response(status=status.HTTP_404_NOT_FOUND)
       
         serializer = BookingSerializer(booking,data=request.DATA,partial=True)
         if serializer.is_valid():
            serializer.save()
            return Response(serializer.data)
         else:
            return Response(serializer.errors, status=status.HTTP_400_BAD_REQUEST)

#----------- Service Provider Subscription ----------# 

class SiteFeatureViewSet(viewsets.ModelViewSet):

    queryset = SiteFeature.objects.filter()
    serializer_class = SiteFeatureSerializer
    # Make service readable only
    def create(self, request, *args, **kwargs):
        return Response(status=status.HTTP_400_BAD_REQUEST)
    def update(self, request, *args, **kwargs): 
        return Response(status=status.HTTP_400_BAD_REQUEST)
 
class SubscriptionViewSet(viewsets.ModelViewSet):

    queryset = Subscription.objects.filter(status_id=1)
    serializer_class = SubscriptionSerializer
    #filter_backends = (filters.SearchFilter,)
    #search_fields = ('^subscription_name',)
    # Make service readable only
    def create(self, request, *args, **kwargs):
        return Response(status=status.HTTP_400_BAD_REQUEST)
    def update(self, request, *args, **kwargs): 
        return Response(status=status.HTTP_400_BAD_REQUEST)
#-------------------- End ---------------------------#


#------------  Practitioner Organization  ----------------#
class PractitionerOrganizationViewSet(viewsets.ModelViewSet):
    queryset = PractitionerOrganization.objects.filter()
    serializer_class = PractitionerOrganizationSerializer
    
    def get_queryset(self):
        queryset= self.queryset
        status_id = self.request.QUERY_PARAMS.get('status_id', None)
        if status_id is not None:
            queryset = queryset.filter(status_id=status_id)
        return queryset
        
    
    # Make EmailTemplate readable only
    def destroy(self, request, *args, **kwargs):
        return Response(status=status.HTTP_400_BAD_REQUEST) 

class PractitionerOrganizationLookupViewSet(viewsets.ModelViewSet):

    queryset = PractitionerOrganizationLookup.objects.filter()
    serializer_class = PractitionerOrganizationLookupSerializer
    # Make EmailTemplate readable only
    def get_queryset(self):
        queryset= self.queryset
        practitioner_id = self.request.QUERY_PARAMS.get('practitioner_id', None)
        organization_id = self.request.QUERY_PARAMS.get('organization_id', None)
        if organization_id is not None and practitioner_id is not None:        
           queryset = queryset.filter(organization_id=organization_id,practitioner_id=practitioner_id)
        elif organization_id is not None:
           queryset = queryset.filter(organization_id=organization_id)
        elif practitioner_id is not None:  
           queryset = queryset.filter(practitioner_id=practitioner_id) 
        return queryset  
#------------------ End -------------------------#

#------------  Partners ----------------#
class PartnersViewSet(viewsets.ModelViewSet):

    queryset = Partners.objects.filter()
    serializer_class = PartnersSerializer
    filter_backends = (filters.SearchFilter,)
    #search_fields = ('status_id',)
    def get_queryset(self):
        queryset= self.queryset
        status_id = self.request.QUERY_PARAMS.get('status_id', None)
        if status_id is not None:        
         queryset = queryset.filter(status_id=status_id)
        return queryset    
    # Make EmailTemplate readable only
    def create(self, request, *args, **kwargs):
        return Response(status=status.HTTP_400_BAD_REQUEST)
    def update(self, request, *args, **kwargs): 
        return Response(status=status.HTTP_400_BAD_REQUEST) 
    def destroy(self, request, *args, **kwargs):
        return Response(status=status.HTTP_400_BAD_REQUEST)    
#------------------ End -------------------------#


#------------  Media ----------------#
class MediaViewSet(viewsets.ModelViewSet):

    queryset = Media.objects.filter()
    serializer_class = MediaSerializer
    paginate_by = 10
    paginate_by_param = 'page_size'
    max_paginate_by = 100
    def get_queryset(self):
        
        user_id = self.request.QUERY_PARAMS.get('user_id', None)
        media_type = self.request.QUERY_PARAMS.get('media_type', None)
        
        status_id = self.request.QUERY_PARAMS.get('status_id', None)
             
        queryset = Media.objects.all().order_by('-created_date')
        if user_id is not None and media_type is not None and status_id is not None:      
         queryset = queryset.filter(user_id=user_id,media_type=media_type,status_id=status_id)
        elif user_id is not None and media_type is not None:
         queryset = queryset.filter(user_id=user_id,media_type=media_type)
        elif user_id is not None and status_id is not None:
         queryset = queryset.filter(user_id=user_id,status_id=status_id)         
        elif media_type is not None and status_id is not None:
         queryset = queryset.filter(media_type=media_type,status_id=status_id)
        elif media_type is not None:
         queryset = queryset.filter(media_type=media_type)       
        elif user_id is not None:
         queryset = queryset.filter(user_id=user_id)         
        elif status_id is not None:
         queryset = queryset.filter(status_id=status_id)   
        return queryset    
    # Make EmailTemplate readable only
   
#------------------ End -------------------------#

#------------  Media ----------------#
class MessagesViewSet(viewsets.ModelViewSet):

    queryset = Messages.objects.filter()
    serializer_class = MessagesSerializer
    paginate_by = 10
    paginate_by_param = 'page_size'
    max_paginate_by = 100    
    def get_queryset(self):
        # code added by R #
        # overwrite the pagination parameter #
        no_of_records = self.request.QUERY_PARAMS.get('no_of_records', 0)
        self.paginate_by = no_of_records # Added on 18-11-2014 by R

        # Commented on 18-11-2014 by R #
        #if no_of_records:
            #self.paginate_by = no_of_records
        # code added by R #
        
        queryset = self.queryset.order_by("-id")
        
        from_user_id = self.request.QUERY_PARAMS.get('from_user_id', None)
        to_user_id = self.request.QUERY_PARAMS.get('to_user_id', None)        
        deleteFlag = self.request.QUERY_PARAMS.get('deleteFlag', None)
        topLevel_id = self.request.QUERY_PARAMS.get('topLevel_id', None)   
        readFlag = self.request.QUERY_PARAMS.get('readFlag', None)
        deleteFlag_p = self.request.QUERY_PARAMS.get('deleteFlag_p', None)
        deleteFlag_c = self.request.QUERY_PARAMS.get('deleteFlag_c', None)        
        readFlag_c   = self.request.QUERY_PARAMS.get('readFlag_c', None)
        readFlag_p   = self.request.QUERY_PARAMS.get('readFlag_p', None)
        exclude_suspended   = self.request.QUERY_PARAMS.get('suspended', 0) # added by R on 13-11-2014#
        

        # Condition added by R on 13-11-2014 starts here #
        if from_user_id and to_user_id and deleteFlag is not None and exclude_suspended == "1":
           queryset = queryset.filter(Q(from_user_id=from_user_id,deleteFlag=deleteFlag) | Q(to_user_id=to_user_id,deleteFlag=deleteFlag)).exclude(Q(to_user__status_id=3) | Q(from_user__status_id=3))
        # Condition added by R on 13-11-2014 ends here #
        elif from_user_id and to_user_id and deleteFlag is not None:
           queryset = queryset.filter(Q(from_user_id=from_user_id,deleteFlag=deleteFlag) | Q(to_user_id=to_user_id,deleteFlag=deleteFlag))        
        elif from_user_id and to_user_id and deleteFlag_p is not None:
           queryset = queryset.filter(Q(from_user_id=from_user_id,deleteFlag_p=deleteFlag_p) | Q(to_user_id=to_user_id,deleteFlag_p=deleteFlag_p))        
        elif from_user_id and to_user_id and deleteFlag_c is not None:
           queryset = queryset.filter(Q(from_user_id=from_user_id,deleteFlag_c=deleteFlag_c) | Q(to_user_id=to_user_id,deleteFlag_c=deleteFlag_c))           
        elif from_user_id and to_user_id:
           queryset = queryset.filter(Q(to_user_id=to_user_id) | Q(from_user_id=from_user_id))           
                
        
        elif from_user_id and deleteFlag is not None:      
         queryset = queryset.filter(from_user_id=from_user_id,deleteFlag=deleteFlag)
        elif to_user_id and deleteFlag is not None:
         queryset = queryset.filter(to_user_id=to_user_id,deleteFlag=deleteFlag)   
        elif from_user_id is not None:
         queryset = queryset.filter(from_user_id=from_user_id)
        elif to_user_id is not None:
         queryset = queryset.filter(to_user_id=to_user_id)        
         

         
        if topLevel_id is not None:
           queryset = queryset.filter(topLevel_id=topLevel_id)
        if readFlag is not None:
           queryset = queryset.filter(readFlag=readFlag)
        if deleteFlag_p is not None:
           queryset = queryset.filter(deleteFlag_p=deleteFlag_p)
        if readFlag_c is not None:
           queryset = queryset.filter(readFlag_c=readFlag_c)  
        if readFlag_p is not None:
           queryset = queryset.filter(readFlag_p=readFlag_p)
        if deleteFlag_c is not None:
           queryset = queryset.filter(deleteFlag_c=deleteFlag_c)           
           
        return queryset
    
    def update(self, request, pk=None):
         try:
           messages = Messages.objects.get(id=pk)
         except:
           return Response(status=status.HTTP_404_NOT_FOUND)
       
         serializer = MessagesSerializer(messages,data=request.DATA,partial=True)
         if serializer.is_valid():
            serializer.save()
            return Response(serializer.data)
         else:
            return Response(serializer.errors, status=status.HTTP_400_BAD_REQUEST)        
#------------------ End -------------------------#
#---- Service Provider Availabilty --------------# 
@api_view(['GET','POST','PUT','DELETE']) 
def  sp_availability_list(request):

    if request.method == 'GET':
        query_str=''
        #service_id =request.QUERY_PARAMS.get('service_id', None)
        user_id = request.QUERY_PARAMS.get('user_id', None)
        start_time = request.QUERY_PARAMS.get('start_time', '')
        end_time = request.QUERY_PARAMS.get('end_time', '')
        days_id = request.QUERY_PARAMS.get('days_id', None)
        address_id = request.QUERY_PARAMS.get('address_id', None)
        availability = []
        if user_id is not None and address_id  is not None:
           availability = sql_select("SELECT service_provider_availability.*, availability_days.day AS day FROM service_provider_availability INNER JOIN availability_days ON availability_days.id = service_provider_availability.days_id WHERE service_provider_availability.user_id = "+user_id+" and address_id="+address_id+" ORDER BY availability_days.id ASC")
           
        elif user_id is not None:
           availability = sql_select("SELECT service_provider_availability.*, availability_days.day AS day FROM service_provider_availability INNER JOIN availability_days ON availability_days.id = service_provider_availability.days_id WHERE service_provider_availability.user_id = "+user_id+" ORDER BY availability_days.id ASC")
           
        elif address_id is not None:
           availability = sql_select("SELECT service_provider_availability.*, availability_days.day AS day FROM service_provider_availability INNER JOIN availability_days ON availability_days.id = service_provider_availability.days_id WHERE service_provider_availability.address_id = "+address_id+" ORDER BY availability_days.id ASC")
           
        elif start_time !="" and end_time  !="" and days_id is not None:
           availability = sql_select("SELECT * FROM service_provider_availability WHERE days_id in ("+days_id+")"+
           " AND start_time IS NOT null AND end_time IS NOT null AND time_to_sec('"+start_time+"') >= time_to_sec(start_time) AND time_to_sec('"+end_time+"') <= time_to_sec(end_time) "+
           " AND ((lunch_start_time IS null OR lunch_end_time IS null) OR time_to_sec('"+start_time+"') "+
           " >= time_to_sec(lunch_end_time) OR (time_to_sec('"+start_time+"') <= time_to_sec(lunch_start_time) AND time_to_sec('"+end_time+"') "
           " <= time_to_sec(lunch_start_time)))")
        elif days_id is not None and start_time =="" and end_time  =="":
           availability = sql_select("SELECT * FROM service_provider_availability WHERE days_id in ("+days_id+") AND start_time is not null")           
        else:
           return Response(availability,status=status.HTTP_400_BAD_REQUEST) 
            
        return Response(availability)
    elif request.method == 'POST':
         serializer = SpUserAvailabilitySerializer(data=request.DATA)
         if serializer.is_valid():
            serializer.save()
            return Response(serializer.data,status=status.HTTP_201_CREATED)
         else:
            return Response(serializer.errors, status=status.HTTP_400_BAD_REQUEST)
        
    elif request.method == 'PUT':

         try:
             availability = SpUserAvailability.objects.get(id=request.DATA['id'])
         except:
           return Response(status=status.HTTP_404_NOT_FOUND)
       
         serializer = SpUserAvailabilitySerializer(availability,data=request.DATA,partial=True)
         if serializer.is_valid():
            serializer.save()
            return Response(serializer.data)
         else:
            return Response(serializer.errors, status=status.HTTP_400_BAD_REQUEST)
    elif request.method == 'DELETE':
         try:
           availability = SpUserAvailability.objects.get(id=request.DATA['id'])
         except:
           return Response(status=status.HTTP_404_NOT_FOUND)        
         availability.delete()
         return Response({'msg':'Deleted successfully.'})        
    
#---- Service Provider Availabilty --------------# 
class SPAppointmentDelayViewSet(viewsets.ModelViewSet):
       queryset = SPAppointmentDelay.objects.filter()
       serializer_class = SPAppointmentDelaySerializer

#-------- availabilityDays -----------------------------------#
class availabilityDaysViewSet(viewsets.ModelViewSet):

    queryset = availabilityDays.objects.filter()
    serializer_class = availabilityDaysSerializer
    def create(self, request, *args, **kwargs):
        return Response(status=status.HTTP_400_BAD_REQUEST)
    def update(self, request, *args, **kwargs): 
        return Response(status=status.HTTP_400_BAD_REQUEST) 
    def destroy(self, request, *args, **kwargs):
        return Response(status=status.HTTP_400_BAD_REQUEST)
 
#---------------- SiteSettings --------------------#
class SiteSettingsViewSet(viewsets.ModelViewSet):

    queryset = SiteSettings.objects.filter()
    serializer_class = SiteSettingsSerializer
    def create(self, request, *args, **kwargs):
        return Response(status=status.HTTP_400_BAD_REQUEST)
    def update(self, request, *args, **kwargs): 
        return Response(status=status.HTTP_400_BAD_REQUEST) 
    def destroy(self, request, *args, **kwargs):
        return Response(status=status.HTTP_400_BAD_REQUEST) 
    
#----------------  --------------------#
class UserSubscriptionsViewSet(viewsets.ModelViewSet):

    queryset = UserSubscriptions.objects.filter(status_id=1)
    serializer_class = UserSubscriptionsSerializer
    def get_queryset(self):
        user_id = self.request.QUERY_PARAMS.get('user_id', None)
        subscription_end_date = self.request.QUERY_PARAMS.get('subscription_end_date', None)
        status_id = self.request.QUERY_PARAMS.get('status_id', None)
        
        if status_id is None:
            status_id = 1
            
        
        queryset = self.queryset
        
        if user_id is not None and subscription_end_date is not None: 
           queryset = queryset.filter(user_id=user_id,subscription_end_date=subscription_end_date,status_id=status_id)
        elif user_id is not None: 
           queryset = queryset.filter(user_id=user_id,status_id=status_id)
        elif subscription_end_date is not None: 
           queryset = queryset.filter(subscription_end_date=subscription_end_date,status_id=status_id)
          
        return queryset 
    
    def update(self, request, pk=None):
         try:
           messages = UserSubscriptions.objects.get(id=pk)
         except:
           return Response(status=status.HTTP_404_NOT_FOUND)
       
         serializer = UserSubscriptionsSerializer(messages,data=request.DATA,partial=True)
         if serializer.is_valid():
            serializer.save()
            return Response(serializer.data)
         else:
            return Response(serializer.errors, status=status.HTTP_400_BAD_REQUEST) 
        
    #def create(self, request, *args, **kwargs):
        #return Response(status=status.HTTP_400_BAD_REQUEST)
    def destroy(self, request, *args, **kwargs):
        return Response(status=status.HTTP_400_BAD_REQUEST)   
    
#---------------- WishList --------------------#    
class WishListViewSet(viewsets.ModelViewSet):
    #authentication_classes = APIAuthentication,
    queryset = WishList.objects.filter()
    serializer_class = WishListSerializer
    paginate_by = 10
    paginate_by_param = 'page_size'
    max_paginate_by = 100     
    def get_queryset(self):
        # overwrite the pagination parameter #
        no_of_records = self.request.QUERY_PARAMS.get('no_of_records', 0)
        if no_of_records:
            self.paginate_by = no_of_records
        ######################################        
        user_id = self.request.QUERY_PARAMS.get('created_by', None)
        queryset = self.queryset
        if user_id is not None: 
           queryset = queryset.filter(created_by=user_id)  
        return queryset    
    def create(self, request, *args, **kwargs): 
        try:
          user_id =  request.DATA['user_id'] 
        except:
          user_id=0
        try:
          created_by = request.DATA['created_by']
        except:
          created_by=0
        try:  
          service_id = request.DATA['service_id']
        except:
          service_id=0
        try:  
          service_duration_id = request.DATA['service_duration_id']
        except:
          service_duration_id = 0 
        try:  
          current_price = request.DATA['current_price']
        except:
          current_price =0 
          
        try:  
          created_date = request.DATA['created_date']
        except:
          created_date ='0000-00-00 00:00:00'         
          
        if user_id and created_by and service_id and service_duration_id and current_price:
            try:
              import datetime
              result = WishList.objects.get(user_id=user_id,created_by=created_by,service_id=service_id,service_duration_id=service_duration_id,current_price=current_price,status_id=0)
              result.status_id=1
              result.created_date=datetime.datetime.now()
              result.save()
              return Response(status=status.HTTP_200_OK) 
            except:
              try:
                result = WishList.objects.filter(user_id=user_id,created_by=created_by,service_id=service_id,service_duration_id=service_duration_id,current_price=current_price)[0]
                if result:
                   return Response({'msg':'Already exist this record'},status=status.HTTP_400_BAD_REQUEST)
              except:  
                try:
                  instance=WishList(user_id=user_id,created_by=created_by,service_id=service_id,service_duration_id=service_duration_id,current_price=current_price,status_id=1,created_date=created_date)  
                  instance.save()
                except:
                  return Response({'msg':'Bad post data'},status=status.HTTP_400_BAD_REQUEST)  
              return Response(status=status.HTTP_201_CREATED)
          
        return Response(status=status.HTTP_400_BAD_REQUEST) 
    
    
#---------------- Newsletter --------------------###########
class NewsletterViewSet(viewsets.ModelViewSet):
    paginate_by = 10
    paginate_by_param = 'page_size'
    max_paginate_by = 100 
    queryset = Newsletter.objects.all()
    serializer_class = NewsletterSerializer
    def get_queryset(self):
        # overwrite the pagination parameter #
        no_of_records = self.request.QUERY_PARAMS.get('no_of_records', 0)
        
        if no_of_records:
            self.paginate_by = no_of_records
        ######################################        
        created_by = self.request.QUERY_PARAMS.get('created_by', None)
        status_id = self.request.QUERY_PARAMS.get('status_id', None)
        send_date = self.request.QUERY_PARAMS.get('send_date', None)
        
        
        queryset = self.queryset
        
        if created_by is not None and status_id is not None and send_date is not None: 
           queryset = queryset.filter(created_by=created_by,status_id=status_id, send_date=send_date)
        elif created_by is not None and send_date is not None:
           queryset = queryset.filter(created_by=created_by,send_date=send_date)
        elif  status_id is not None and send_date is not None:   
           queryset = queryset.filter(status_id=status_id ,send_date=send_date)
        elif  status_id is not None:   
           queryset = queryset.filter(status_id=status_id)
        elif  send_date is not None:   
           queryset = queryset.filter(send_date=send_date)
        elif  created_by is not None:   
           queryset = queryset.filter(created_by=created_by)
        #print queryset.query   
        return queryset 
    
    def update(self, request, pk=None):
         try:
           messages = Newsletter.objects.get(id=pk)
         except:
           return Response(status=status.HTTP_404_NOT_FOUND)
       
         serializer = NewsletterSerializer(messages,data=request.DATA,partial=True)
         if serializer.is_valid():
            serializer.save()
            return Response(serializer.data)
         else:
            return Response(serializer.errors, status=status.HTTP_400_BAD_REQUEST)     
############################################################

##################### NewsletterSend ######################
class NewsletterSendViewSet(viewsets.ModelViewSet):
    paginate_by = 10
    paginate_by_param = 'page_size'
    max_paginate_by = 100 
    queryset = NewsletterSend.objects.all()
    serializer_class = NewsletterSendSerializer
    def get_queryset(self):
        queryset = self.queryset
        user_id = self.request.QUERY_PARAMS.get("user_id",None)
        status = self.request.QUERY_PARAMS.get("status",None)
        newsletter_id = self.request.QUERY_PARAMS.get("newsletter_id",None)

        # Conditions added on 19-11-2014 by R starts here #
        if newsletter_id is not None:
            queryset = queryset.filter(newsletter_id=newsletter_id)
        if user_id is not None:
            queryset = queryset.filter(user_id=user_id)
        # Conditions added on 19-11-2014 by R ends here #
        if status is not None:
            queryset = queryset.filter(status=status)
        
        return queryset
    
    def update(self, request, pk=None):
         try:
           messages = NewsletterSend.objects.get(id=pk)
         except:
           return Response(status=status.HTTP_404_NOT_FOUND)
       
         serializer = NewsletterSendSerializer(messages,data=request.DATA,partial=True)
         if serializer.is_valid():
            serializer.save()
            return Response(serializer.data)
         else:
            return Response(serializer.errors, status=status.HTTP_400_BAD_REQUEST)     
###########################################################

#------------  ServiceProviderReferenceSend View -----------------#
class ServiceProviderReferenceViewSet(viewsets.ModelViewSet):
    paginate_by = 10
    paginate_by_param = 'page_size'
    max_paginate_by = 100 
    queryset = ServiceProviderReference.objects.all()
    serializer_class = ServiceProviderReferenceSerializer
    
    def get_queryset(self):
        # overwrite the pagination parameter #
        no_of_records = self.request.QUERY_PARAMS.get('no_of_records', 0)
        status_id = self.request.QUERY_PARAMS.get('status_id', None)
        
        
        if no_of_records:
            self.paginate_by = no_of_records
        ######################################        
        user_id = self.request.QUERY_PARAMS.get('user_id', None)
        referred_by = self.request.QUERY_PARAMS.get('referred_by', None)
        view_status = self.request.QUERY_PARAMS.get('view_status', None)
        queryset = ServiceProviderReference.objects.all()
        
        if status_id is not None:
            queryset = queryset.filter(status_id=status_id)
        if user_id is not None and referred_by is not None: 
           queryset = queryset.filter(user_id=user_id,referred_by=referred_by)
        # Condition added on 17-11-2014 by R starts here #
        elif user_id is not None and view_status is not None:
           queryset = queryset.filter(user_id=user_id, view_status=view_status)
        elif  referred_by is not None and view_status is not None:   
           queryset = queryset.filter(referred_by=referred_by, view_status=view_status)
        # Condition added on 17-11-2014 by R ends here #
        elif user_id is not None:
           queryset = queryset.filter(user_id=user_id)
        elif  referred_by is not None:   
           queryset = queryset.filter(referred_by=referred_by)
        #print queryset.query   
        return queryset

    # Function updated on 17-11-2014 by R #
    def update(self, request, pk=None):
         user_id = self.request.DATA.get('user_id', None)
         referred_by = self.request.DATA.get('referred_by', None)
         multiple = False
         
         if user_id is not None and pk == "0":
            multiple = True
            messages = ServiceProviderReference.objects.filter(user_id=user_id)
         elif referred_by is not None and pk == "0":
            multiple = True
            messages = ServiceProviderReference.objects.filter(referred_by=referred_by)
         else:
            try:
              messages = ServiceProviderReference.objects.get(id=pk)
            except:
              return Response(status=status.HTTP_404_NOT_FOUND)
         
         if multiple == True:
            results = []
            for message in messages:
                serializer = ServiceProviderReferenceSerializer(message,data=request.DATA,partial=True)
                if serializer.is_valid():
                   serializer.save()
                   results.append(serializer.data)
                else:
                   return Response(serializer.errors, status=status.HTTP_400_BAD_REQUEST)
            return Response(results)
         else:
            serializer = ServiceProviderReferenceSerializer(messages,data=request.DATA,partial=True)
            if serializer.is_valid():
               serializer.save()
               return Response(serializer.data)
            else:
               return Response(serializer.errors, status=status.HTTP_400_BAD_REQUEST)    

    #def update(self, request, pk=None):
    #     try:
    #      messages = ServiceProviderReference.objects.get(id=pk)
    #    except:
    #       return Response(status=status.HTTP_404_NOT_FOUND)
       
    #    serializer = ServiceProviderReferenceSerializer(messages,data=request.DATA,partial=True)
    #    if serializer.is_valid():
    #       serializer.save()
    #       return Response(serializer.data)
    #    else:
    #       return Response(serializer.errors, status=status.HTTP_400_BAD_REQUEST)
###################################################################

#------------   Invoice Details Service -----------------#

#------------  InvoiceView Using  invoice(payment_history, invoice_details) Read Only -----------------#
class InvoiceViewSet(viewsets.ModelViewSet):
    paginate_by = 10
    paginate_by_param = 'page_size'
    max_paginate_by = 100 
    queryset = Invoice.objects.all()
    serializer_class = InvoiceSerializer
    def get_queryset(self):
        queryset = self.queryset
        # overwrite the pagination parameter #
        no_of_records = self.request.QUERY_PARAMS.get('no_of_records', 0)
        if no_of_records:
            self.paginate_by = no_of_records
        ######################################        
        user_id = self.request.QUERY_PARAMS.get('user_id', None)
        if user_id is not None:
           queryset = queryset.filter(user_id=user_id)
        return queryset   
    def create(self, request, *args, **kwargs):
        return Response(status=status.HTTP_400_BAD_REQUEST)
    def update(self, request, *args, **kwargs): 
        return Response(status=status.HTTP_400_BAD_REQUEST) 
    def destroy(self, request, *args, **kwargs):
        return Response(status=status.HTTP_400_BAD_REQUEST) 
    
"""class PaymentRefundHistoryViewSet(viewsets.ModelViewSet):
    paginate_by = 10
    paginate_by_param = 'page_size'
    max_paginate_by = 100 
    queryset = PaymentRefundHistory.objects.filter(payment_history__invoice__lookipInvoice__status_id=5)
    serializer_class = PaymentRefundHistorySerializer
    
    
    def create(self, request, *args, **kwargs):
        return Response(status=status.HTTP_400_BAD_REQUEST)
    def update(self, request, *args, **kwargs): 
        return Response(status=status.HTTP_400_BAD_REQUEST) 
    def destroy(self, request, *args, **kwargs):
        return Response(status=status.HTTP_400_BAD_REQUEST)"""
        
        
class BookingSuggestionHistoryViewSet(viewsets.ModelViewSet):
    
    queryset = BookingSuggestionHistory.objects.all()
    serializer_class = BookingSuggestionHistorySerializer    
    def get_queryset(self):
        queryset = self.queryset
        # overwrite the pagination parameter #
        user_id = self.request.QUERY_PARAMS.get('user_id', None)
        booking_id = self.request.QUERY_PARAMS.get('booking_id', None)
        if user_id is not None and  booking_id is not None:
           queryset = queryset.filter(user_id=user_id,booking_id=booking_id)
        elif user_id is not None:
           queryset = queryset.filter(user_id=user_id)
        elif booking_id is not None:
           queryset = queryset.filter(booking_id=booking_id) 
        return queryset 
    def update(self, request, pk=None):
         try:
           messages = BookingSuggestionHistory.objects.get(id=pk)
         except:
           return Response(status=status.HTTP_404_NOT_FOUND)
       
         serializer = BookingSuggestionHistorySerializer(messages,data=request.DATA,partial=True)
         if serializer.is_valid():
            serializer.save()
            return Response(serializer.data)
         else:
            return Response(serializer.errors, status=status.HTTP_400_BAD_REQUEST)    
###################################################################

class SmsHistoryViewSet(viewsets.ModelViewSet):
    
    queryset = SmsHistory.objects.all()
    serializer_class = SmsHistorySerializer    
    def update(self, request, pk=None):
         try:
           messages = SmsHistory.objects.get(id=pk)
         except:
           return Response(status=status.HTTP_404_NOT_FOUND)
       
         serializer = SmsHistorySerializer(messages,data=request.DATA,partial=True)
         if serializer.is_valid():
            serializer.save()
            return Response(serializer.data)
         else:
            return Response(serializer.errors, status=status.HTTP_400_BAD_REQUEST) 
        
class SmsViewSet(viewsets.ModelViewSet):
    
    queryset = Sms.objects.all()
    serializer_class = SmsSerializer 
    
class TestimonialsViewSet(viewsets.ModelViewSet):
    
    queryset = Testimonials.objects.all().filter(status_id=1)
    serializer_class = TestimonialsSerializer
    paginate_by = 12
    paginate_by_param = 'page_size'
    max_paginate_by = 100    


class userCardDetails(viewsets.ModelViewSet):
    """
    List user's card detail
    """
    queryset = UserCardDetails.objects.all()
    serializer_class = UserCardDetailsSerializer
    paginate_by = 20
    paginate_by_param = 'page_size'
    max_paginate_by = 100    
    
    def get_queryset(self):
        
        queryset = self.queryset
        user_id = self.request.QUERY_PARAMS.get('user_id',0)
        use_for_renew = self.request.QUERY_PARAMS.get('use_for_renew',0)
        card_expiration_hash = self.request.QUERY_PARAMS.get('card_expiration_hash',0)
        
        if user_id and use_for_renew and card_expiration_hash:
            queryset = queryset.filter(user_id=user_id,use_for_renew=use_for_renew,card_expiration_hash=card_expiration_hash)
            
        elif user_id and card_expiration_hash:
            queryset = queryset.filter(user_id=user_id,card_expiration_hash=card_expiration_hash)
            
        elif user_id and use_for_renew:
            queryset = queryset.filter(user_id=user_id,use_for_renew=use_for_renew)
            
        elif use_for_renew and card_expiration_hash:
            queryset = queryset.filter(use_for_renew=use_for_renew,card_expiration_hash=card_expiration_hash)
            
        elif user_id:
            queryset = queryset.filter(user_id=user_id)
            
        elif use_for_renew:
            queryset = queryset.filter(use_for_renew=use_for_renew)
        elif card_expiration_hash:
            queryset = queryset.filter(card_expiration_hash=card_expiration_hash)
            
        else:
            queryset = queryset.filter()
        return queryset
    
    
    
    
@api_view(['GET']) 
def  distance_list(request):
    
    if request.method == 'GET':
        query_str=''
        practitioner_zip = request.QUERY_PARAMS.get('practitioner_zip',0)
        geoLocation = request.QUERY_PARAMS.get('geoLocation', 0)
        consumer_zip = request.QUERY_PARAMS.get('consumer_zip', 0)
        distance = []
        if practitioner_zip is not 0:
           distance = sql_select("CALL getDistancePractitioner('"+str(practitioner_zip)+"',"+str(geoLocation)+",'"+str(consumer_zip)+"')")
           return Response(distance)
        else:
           return Response(status=status.HTTP_400_BAD_REQUEST) 
       
       
#------------ManualBookings ---------------------------#
@api_view(['POST',]) 
def manual_booking_list(request):
        
        
         if request.method == 'POST':
            postData={         
                  "booking":{
                    'user_id' : request.POST.get('user_id',''),
                    'service_provider_id' : request.POST.get('service_provider_id',''),
                    'service_provider_service_id' : request.POST.get('service_provider_service_id',''),
                    'booked_date'  : request.POST.get('booked_date',''),
                    'payment_status' : request.POST.get('payment_status',''),
                    'status_id'  : request.POST.get('status_id',''),
                    'created_date' : request.POST.get('created_date',''),
                    'service_address_id': request.POST.get('service_address_id',''),
                    'invoice_id':0
                  },
                  "booking_suggestion_history":{
                    'user_id' : request.POST.get('user_id',''),
                    'booking_id' : '',
                    'booking_time': request.POST.get('booked_date',''),
                    'booking_status':request.POST.get('booking_status','')
                  },
                  "mannual_booking_details":{
                    'booking_id' : '',
                    'first_name' : request.POST.get('first_name',''),
                    'last_name' : request.POST.get('last_name','')
                    
                  }                 

                }
                
            bookingserializer = BookingSerializer(data=postData["booking"])
            bshserializer = BookingSuggestionHistorySerializer(data=postData["booking_suggestion_history"])
            mbookingserializer = ManualBookingsSerializer(data=postData["mannual_booking_details"])
            if bookingserializer.is_valid():
               if bshserializer.is_valid():
                                         
                             
                             bookingserializer.save()
                             booking_id=Booking.objects.latest('id')
                             
                             bshserializer.object.booking_id = booking_id.id
                             bshserializer.save()
                             # insert the data into manula booking table
                             mbookingserializer.is_valid()
                             mbookingserializer.object.booking_id = booking_id.id
                             mbookingserializer.save()
                             return Response(bookingserializer.data)      

               else:          
                        return Response(bshserializer.errors, status=status.HTTP_400_BAD_REQUEST)
            else:          
                        return Response(bookingserializer.errors, status=status.HTTP_400_BAD_REQUEST)                    
                    
                             
                             
                
                            
    
#----------- End --------------------------------------#

#-----------------Faq index added on 26-11-2014 by R start---------------------------#
class faqIndex(viewsets.ModelViewSet):
    
    queryset = faqIndex.objects.all()
    serializer_class = faqIndexSerializer
#-----------------Faq index added on 26-11-2014 by R end---------------------------#

#-----------------Faqs added on 26-11-2014 by R start---------------------------#
class faqs(viewsets.ModelViewSet):
    
    queryset = faqs.objects.all()
    serializer_class = faqsSerializer

    def get_queryset(self):

        user_type = self.request.QUERY_PARAMS.get('user_type',None)
        id = self.request.QUERY_PARAMS.get('id',None)
        queryset = self.queryset

        if user_type is not None:
            queryset = queryset.filter(user_type=user_type)

        if id is not None:
            queryset = queryset.filter(id=id)

        queryset = queryset.filter(status_id=1, index__status_id=1)
        queryset = queryset.order_by('index__order_by', 'order_by')
        
        return queryset
#-----------------Faqs added on 26-11-2014 by R end---------------------------#

#-----------------Response Rate added on 27-11-2014 by R start---------------------------#
@api_view(['GET',]) 
def response_rate(request):
    
    serializer_class = ResponseRateSerializer
    service_provider_id = request.QUERY_PARAMS.get("service_provider_id",None)

    total = ResponseRate.objects.filter(booking__service_provider_id=service_provider_id).values_list('booking_id').distinct()
    responded = ResponseRate.objects.filter(booking__service_provider_id=service_provider_id).exclude(booking_status=5).values_list('booking_id').distinct()
    total_request = int(total.count())
    total_responded = int(responded.count())
    response_rate = 0

    if total_request != 0 and total_responded != 0:
        
        response_rate = 100*total_responded/total_request
    
    #assert False, total
    
    return Response(response_rate)
#-----------------Response Rate added on 27-11-2014 by R end---------------------------#

#-----------------Advertisement added on 15-12-2014 by R start---------------------------#
class Advertisements(viewsets.ModelViewSet):
    queryset = PublisherBanner.objects.all()
    serializer_class = PublisherBannerSerializer

    def get_queryset(self):
        
        queryset = self.queryset.filter(booking__end_date__gte=date.today()).filter(status_id=1).order_by('-booking')
        page_id = self.request.QUERY_PARAMS.get("page_id", None)
        
        if page_id is not None:
            queryset = queryset.filter(booking__advertisement_plan__advertisement_page_id=page_id)
        return queryset
#-----------------Advertisement added on 15-12-2014 by R end---------------------------#