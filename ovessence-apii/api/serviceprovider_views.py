from api.models import SpUsers,SpUserContact,SpUserDetails,Education,UserActivity,Service,Language,SpUserActivity,SpUserEducation,SpUserLanguage
from api.models import SpUserService,Rate,Booking,Feedback,ip2locationInfo,PractitionerOrganizationLookup,AccountDeactivateReasons
from api.models import DeactivatedAccountList,LookupLocationType,SpLocation,BookingSuggestionHistory

from api.serviceprovider_serializer import SpUsersSerializer,SpUserContactSerializer,SpUserDetailsSerializer,EducationsSerializer,UserActivitySerializer,ServiceSerializer,LanguageSerializer
from api.serviceprovider_serializer import SpUserServiceSerializer,ip2locationInfoSerializer,AccountDeactivateReasonsserializer
from api.serviceprovider_serializer import DeactivatedAccountListSerializer, LookupLocationTypeSerializer, SpLocationSerializer
from rest_framework import status
from rest_framework.decorators import api_view
from rest_framework.response import Response
from rest_framework import viewsets

from healthservices.authentication import APIAuthentication
import itertools
from itertools import chain
import simplejson as json
from django.db.models import Avg, Max, Min,Sum,Count
from api.functions import sql_select
from django.db.models import Q
########################################## Classes ##

# User View Class and function

class SpUserViewSet(viewsets.ModelViewSet):
    #authentication_classes = APIAuthentication,
    queryset = SpUsers.objects.filter(user_type_id=3)
    serializer_class = SpUsersSerializer
    ordering = ('first_name',)
    paginate_by = 10
    paginate_by_param = 'page_size'
    max_paginate_by = 100
    
    def get_queryset(self):
           
           queryset = self.queryset
           status = self.request.QUERY_PARAMS.get('status_id',None)
           
           if status is not None:
               queryset = queryset.filter(status_id=status)

           queryset_new = queryset    
           #-- Filter by  ID --- 
           id = self.request.QUERY_PARAMS.get('id', '') 
           params = self.request.QUERY_PARAMS.get('params',None)
           zip_codes = self.request.QUERY_PARAMS.get('zip_code',None)
           
           if id!="":
                queryset = queryset.filter(id=id)
           #-------------------------#
           #-- Search on avg_rating --- #
           avg_rating = self.request.QUERY_PARAMS.get('avg_rating', None)
           try:
               avg_rating=int(avg_rating)
               avg_rating = 'round_rating == '+str(avg_rating)
           except:
               try:
                 avg_rating = [int(x) for x in filter(None,avg_rating.split(","))]
                 string = []
                 for item in avg_rating:
                     string.append("round_rating == "+str(item))
                 avg_rating = ' or '.join(string)    
               except:  
                 avg_rating=0           
           if avg_rating is not None and avg_rating!=0 and avg_rating!="":
              result = Rate.objects.values('users_id').annotate(rate=Avg('rate'))
              user_ids = []
              for item in result:
                  round_rating = int(round(item['rate']))
                  if eval(avg_rating):
                     user_ids.append(item['users_id'])
                     
              queryset = queryset.filter(id__in=user_ids)
           #-----------------------------#
           
           #-- Search on service_id --- #
           service_id = self.request.QUERY_PARAMS.get('service_id', None)
           if service_id is not None and service_id!="":
            try:
                service_id=int(service_id)
                service_id =[service_id]
            except:
                try:
                  service_id = [int(x) for x in filter(None,service_id.split(","))]
                except:  
                  service_id=0           
            if service_id is not 0:
               result = SpUserService.objects.values('user_id','service_id').annotate(Sum('price')).filter(service_id__in=service_id)
               user_ids = []
               for item in result:
                   user_ids.append(item['user_id'])
               queryset = queryset.filter(id__in=user_ids)               
           #-----------------------------#
           
           #-- Search on auth_to_issue_insurence_rem_receipt --- #
           auth_to_issue_insurence_rem_receipt = self.request.QUERY_PARAMS.get('auth_to_issue_insurence_rem_receipt', None)
           try:
               auth_to_issue_insurence_rem_receipt=str(auth_to_issue_insurence_rem_receipt)
           except:
               auth_to_issue_insurence_rem_receipt=None           
           if auth_to_issue_insurence_rem_receipt is not None:
               if auth_to_issue_insurence_rem_receipt == '1': 
                queryset = queryset.filter(details__auth_to_issue_insurence_rem_receipt=1)
               elif auth_to_issue_insurence_rem_receipt == '0': 
                queryset = queryset.exclude(details__auth_to_issue_insurence_rem_receipt=1)   
           #-----------------------------------------------------#
           
           #-- Search on days_id --- #
           days_id = self.request.QUERY_PARAMS.get('days_id', None)
           if days_id is not None and days_id!="":
            try:
                days_id=int(days_id)
                days_id =[days_id]
            except:
                try:
                   days_id = [int(x) for x in filter(None,days_id.split(","))]
                except:
                   days_id=0 # When we assign 0,It wont check the condition           
            if days_id is not 0:
               queryset = queryset.filter(availability_days__days_id__in=days_id).distinct()
           #-------------------------#  
           

           #-- Search on practitioners_name --- #
           practitioners_name = self.request.QUERY_PARAMS.get('practitioners_name', None) 
           if practitioners_name is not None and practitioners_name!="":
              queryset_temp = queryset
              ## explode the string
              practitioners_name=[str(x) for x in filter(None,practitioners_name.strip().split(' '))]
              try:
                  if practitioners_name[0] and practitioners_name[1]:  
                    queryset_temp = queryset_temp.filter(first_name__icontains=practitioners_name[0],last_name__icontains=practitioners_name[1])
              except:
                  queryset_temp = queryset_temp.filter(first_name__icontains=practitioners_name[0])
              
              #-- Search on company name --- #
              if  queryset_temp.exists()== False:
                  queryset = queryset.filter(details__company_name__contains=practitioners_name[0])
              else:
                  queryset = queryset_temp
           #-------------------------#       
           
           
           #-- Filter by  city --- 
           #city = self.request.QUERY_PARAMS.get('city', '') 
           #if city!="":
           #    queryset = queryset.filter(Q(work_address__city__icontains=city)|Q(address__city__icontains=city)).distinct()
           #-------------------------#
           
           #-- Filter by  State --- 
           state_id = self.request.QUERY_PARAMS.get('state_id', '') 
           if state_id!="":
                queryset = queryset.filter(work_address__state_id=state_id).distinct()
           #-------------------------#
           
           #-- Filter by  Country --- 
           country_id = self.request.QUERY_PARAMS.get('country_id', '') 
           if country_id!="":
                queryset = queryset.filter(work_address__country_id=country_id).distinct()
           #-------------------------#
           
           #-- Filter by  Zip Code --- 
           #zip_code = self.request.QUERY_PARAMS.get('zip_code', '')
           #if zip_code!="":
           #    queryset = queryset.filter(Q(work_address__zip_code=zip_code)|Q(address__zip_code=zip_code))
           #-------------------------#   
           
           #-- Lets add parameter for city name province name and country name ---
           params = self.request.QUERY_PARAMS.get("params",None)
           if params is not None:
              #queryset = queryset.filter(Q(work_address__country__country_name__icontains=params)|Q(address__country__country_name__icontains=params)|Q(work_address__state__state_name__icontains=params)|Q(address__state__state_name__icontains=params)|Q(work_address__city__icontains=params)|Q(address__city__icontains=params)).distinct()
              queryset = queryset.filter(Q(work_address__country__country_name__icontains=params)|Q(work_address__state__state_name__icontains=params)|Q(work_address__city__icontains=params)).distinct()
            #-------------------------#
           #-- Lets add parameter for country name for the same ---
           country_name = self.request.QUERY_PARAMS.get("country_name",'')
           if country_name!='':
              #queryset = queryset.filter(Q(work_address__country__country_name__icontains=country_name)|Q(address__country__country_name__icontains=country_name)).distinct()
               queryset = queryset.filter(Q(work_address__country__country_name__icontains=country_name)).distinct()
           #-------------------------#
           #-- Lets add parameter for province name for the same ---
           state_name = self.request.QUERY_PARAMS.get("state_name", '')
           if state_name!='':
              #queryset = queryset.filter(Q(work_address__state__state_name__icontains=state_name)|Q(address__state__state_name__icontains=state_name)).distinct()
               queryset = queryset.filter(Q(work_address__state__state_name__icontains=state_name)).distinct()
            #-------------------------#
           
            #-- Location type filter go here ---#
           loc_type = self.request.QUERY_PARAMS.get("location_type",'')
           
           if loc_type:
               location_types = str(loc_type)
               location_type = location_types.split(',')
               
               if '4' in location_type:
                   queryset = queryset.filter(details__offering_at_home='1')
                   location_type.remove('4')
                   
               if '5' in location_type:
                   queryset = queryset.filter(details__offering_at_work_office='1')
                   location_type.remove('5')
               if location_type:
                     queryset = queryset.filter(work_address__location_type_id__in=location_type)
                
           
           #--------- Filter by distance-------------------#
           city = self.request.QUERY_PARAMS.get('city', '')
           zip_code = self.request.QUERY_PARAMS.get('zip_code', '')
           ip = self.request.QUERY_PARAMS.get('ipNumber', '')
           distance = self.request.QUERY_PARAMS.get('distance', '')
           
           if city!="" and zip_code!="":
              result = []
              zip_code_data=[]
              zip_all=[]
              result =sql_select("select Latitude,Longitude  from myips where PostalCode like '%"+zip_code+"%' limit 1")
              if result and distance!='':
                 zip_code_data =sql_select("select DISTINCT PostalCode  from myips inner join address on address.zip_code = myips.PostalCode WHERE round(3959 * acos(cos(radians("+result[0]['Latitude']+")) * cos(radians(Latitude)) * cos( radians(Longitude) - radians("+result[0]['Longitude']+")) + sin(radians("+result[0]['Latitude']+")) * sin(radians(Latitude)))) <= "+distance+" order by  (3959 * acos(cos(radians("+result[0]['Latitude']+")) * cos(radians(Latitude)) * cos( radians(Longitude) - radians("+result[0]['Longitude']+")) + sin(radians("+result[0]['Latitude']+")) * sin(radians(Latitude)))) ASC limit 20")
              elif result:
                 zip_code_data =sql_select("select DISTINCT PostalCode  from myips inner join address on address.zip_code = myips.PostalCode order by  (3959 * acos(cos(radians("+result[0]['Latitude']+")) * cos(radians(Latitude)) * cos( radians(Longitude) - radians("+result[0]['Longitude']+")) + sin(radians("+result[0]['Latitude']+")) * sin(radians(Latitude)))) ASC limit 20") 
              if zip_code_data:
                  for zip in zip_code_data:
                      zip_all.append(zip['PostalCode'])
              #assert False, zip_all
              if distance !='':
                  queryset = queryset.filter(Q(work_address__zip_code__in=zip_all)) 
              else:
                  queryset = queryset.filter(Q(work_address__city__icontains=city)|Q(work_address__zip_code__in=zip_all)) 
              #assert False, queryset.query
           #-----------------------------------------------# 
           
           #------------- Filter by association_member --------------#
           association_member  = self.request.QUERY_PARAMS.get('association_member', None)
           if association_member and association_member is not None:
              queryset_org=PractitionerOrganizationLookup.objects.filter(organization__status_id =1)
              user_ids_all = []
              for item_all in queryset_org:
                  user_ids_all.append(item_all.practitioner_id)
              if user_ids_all:
                  if association_member == '1':
                     queryset = queryset.filter(id__in=user_ids_all)
                  elif association_member == '0':
                     queryset = queryset.exclude(id__in=user_ids_all) 
           #------------------ End -------------------------#
           
           #------------- Filter by Price Range ------------#
           minPrice = self.request.QUERY_PARAMS.get('minPrice', 0) 
           maxPrice = self.request.QUERY_PARAMS.get('maxPrice', 0)
           if maxPrice and maxPrice != '0':
               queryset = queryset.filter(spusersservice__price__range=[minPrice,maxPrice]).distinct()
               
           #------------------------------------------------#
           #------------- Filter by Price Range ------------#
           treatmentLength = self.request.QUERY_PARAMS.get('treatmentLength', 0) 
           if treatmentLength and treatmentLength != '0':
               queryset = queryset.filter(spusersservice__duration__range=[0,treatmentLength]).distinct() 
               
           #------------- Filter by treatment for physically disabled person ------------#
           treatment = self.request.QUERY_PARAMS.get('treatment_for_physically_disabled_person', 0) 
           if treatment and treatment != '0':
               queryset = queryset.filter(details__treatment_for_physically_disabled_person=treatment).distinct() 
               
           # --------------- Filter by sp_availability --------------------------#
           sp_availability = self.request.QUERY_PARAMS.get('sp_availability', '')
           date_range_filter = self.request.QUERY_PARAMS.get('date_range_filter', '')
           
           if sp_availability == '' and date_range_filter == '1' :
              return queryset.none()
           if sp_availability and sp_availability != '':
               queryset = queryset.filter(id__in=[int(x) for x in filter(None,sp_availability.split(","))])            
           #---------------- End ------------------------------------------------#
           
           exclude_user = self.request.QUERY_PARAMS.get('exclude_user', '') 
           if exclude_user:
               queryset = queryset.exclude(id=exclude_user)             
           #-- Order by price  --- 
           price = self.request.QUERY_PARAMS.get('price', None) 
           if price=='1':
                  queryset = queryset.annotate(min_price=Min('spusersservice__price')).exclude(spusersservice__price__isnull=True).order_by('-min_price') # Higher to lower
           elif price=='2':
                  queryset = queryset.annotate(min_price=Min('spusersservice__price')).exclude(spusersservice__price__isnull=True).order_by('min_price')  # lower to Higher
           #-------------------------#           
           #-- Order by  booking_no --- 
           booking_no = self.request.QUERY_PARAMS.get('booking_no', '') 
           
           if booking_no == '1' or  booking_no == '2':
 
                  total_booking = Booking.objects.values('service_provider_id').annotate(total_booking=Count('service_provider')).filter(user_id__isnull=False).order_by('total_booking')
                  #total_booking = BookingSuggestionHistory.objects.values('user').annotate(total_booking=Count('user')).filter(booking_status=4,user__isnull=False).order_by('total_booking')
                  #print total_booking.query
                  user_ids_all = []
                  for item_all in queryset:
                      user_ids_all.append(item_all.id)
                     
                  user_ids = []
                  for item in total_booking:
                      #user_ids.append(item['user'])
                      user_ids.append(item['service_provider_id'])
                  ordering = 'FIELD(`id`, %s)' % ','.join(str(id) for id in user_ids)                  
                  if booking_no == '1': 
                     queryset = queryset_new.filter(id__in=user_ids_all).extra(select={'ordering': ordering}, order_by=('-ordering',))
                  else:
                     queryset = queryset_new.filter(id__in=user_ids_all).extra(select={'ordering': ordering}, order_by=('ordering',))                  
           #-------------------------#
           
           #-- Order by  feedback_filter --- 
           feedback_filter = self.request.QUERY_PARAMS.get('feedback_filter', '') 
           
           if feedback_filter == '1' or  feedback_filter == '2':
                  total_feedback = Feedback.objects.values('users_id').annotate(total_feedback=Count('users_id')).filter(status_id=9).order_by('total_feedback')
                  user_ids_all = []
                  for item_all in queryset:
                      user_ids_all.append(item_all.id)
                     
                  user_ids = []
                  for item in total_feedback:
                   user_ids.append(item['users_id'])
                  ordering = 'FIELD(`id`, %s)' % ','.join(str(id) for id in user_ids)
                  if feedback_filter == '1': 
                     queryset = queryset_new.filter(id__in=user_ids_all).extra(select={'ordering': ordering}, order_by=('-ordering',))
                  else:
                     queryset = queryset_new.filter(id__in=user_ids_all).extra(select={'ordering': ordering}, order_by=('ordering',)) 
           #-------------------------# 
           
           #assert False, queryset.query
           queryset = queryset.distinct()
           return queryset    
    # Make sp readable only
    def destroy(self, request, *args, **kwargs):
        return Response(status=status.HTTP_400_BAD_REQUEST)
    
    def update(self, request, pk=None):
         try:
           messages = SpUsers.objects.get(id=pk)
         except:
           return Response(status=status.HTTP_404_NOT_FOUND)
       
         serializer =  SpUsersSerializer(messages,data=request.DATA,partial=True)
         if serializer.is_valid():
            serializer.save()
            return Response(serializer.data)
         else:
            return Response(serializer.errors, status=status.HTTP_400_BAD_REQUEST)     
    
class SpUserContactViewSet(viewsets.ModelViewSet):
    #authentication_classes = APIAuthentication,
    queryset = SpUserContact.objects.all()
    serializer_class = SpUserContactSerializer
    def get_queryset(self):
        queryset = self.queryset
        user_id = self.request.QUERY_PARAMS.get('user_id', None)
        if user_id is not None:
           queryset = queryset.filter(user_id=user_id)
        return queryset    
    
class SpUserDetailsViewSet(viewsets.ModelViewSet):
    #authentication_classes = APIAuthentication,
    queryset = SpUserDetails.objects.all()
    serializer_class = SpUserDetailsSerializer 
    def destroy(self, request, *args, **kwargs):
        return Response(status=status.HTTP_400_BAD_REQUEST)    
####### Fetch Combined Result Education,UserActivity,Service,Language  Not possible in django#########

class EducationViewSet(viewsets.ModelViewSet):
    #authentication_classes = APIAuthentication,
    queryset = Education.objects.all()
    serializer_class = EducationsSerializer
    def get_queryset(self):
        queryset = self.queryset
        status_id = self.request.QUERY_PARAMS.get('status_id',None)
        if status_id is not None:
            queryset = queryset.filter(status_id=status_id)
        return queryset
        
    # Make Education readable only
    def create(self, request, *args, **kwargs):
        return Response(status=status.HTTP_400_BAD_REQUEST)
    def update(self, request, *args, **kwargs): 
        return Response(status=status.HTTP_400_BAD_REQUEST)    
    def destroy(self, request, *args, **kwargs):
        return Response(status=status.HTTP_400_BAD_REQUEST)     
    

class UserActivityViewSet(viewsets.ModelViewSet):
    #authentication_classes = APIAuthentication,
    #api_view=['GET','POST']
    queryset = UserActivity.objects.all()
    serializer_class = UserActivitySerializer
    # Make UserActivity readable only
    def create(self, request, *args, **kwargs):
        return Response(status=status.HTTP_400_BAD_REQUEST)
    def update(self, request, *args, **kwargs): 
        return Response(status=status.HTTP_400_BAD_REQUEST)    
    def destroy(self, request, *args, **kwargs):
        return Response(status=status.HTTP_400_BAD_REQUEST)    

class ServiceViewSet(viewsets.ModelViewSet):
    #authentication_classes = APIAuthentication,
    queryset = Service.objects.all()
    serializer_class = ServiceSerializer
    def get_queryset(self):
       queryset = self.queryset.all()
       parent_id = self.request.QUERY_PARAMS.get('parent_id', None)
       if parent_id is not None:
          queryset = queryset.filter(parent_id=parent_id)
       return queryset   
    #Make Service readable only
    def update(self, request, *args, **kwargs): 
       return Response(status=status.HTTP_400_BAD_REQUEST)    
    def destroy(self, request, *args, **kwargs):
       return Response(status=status.HTTP_400_BAD_REQUEST)
    
class LanguageViewSet(viewsets.ModelViewSet):
    #authentication_classes = APIAuthentication,
    queryset = Language.objects.all()
    serializer_class = LanguageSerializer   
    def get_queryset(self):
        queryset = self.queryset
        status_id = self.request.QUERY_PARAMS.get("status_id",None) 
        if status_id is not None:
            queryset=queryset.filter(status_id=status_id)
            
        return queryset

class SpUserServiceViewSet(viewsets.ModelViewSet):
    paginate_by = 5
    paginate_by_param = 'page_size'
    max_paginate_by = 100    
    #authentication_classes = APIAuthentication,
    queryset = SpUserService.objects.all()
    serializer_class = SpUserServiceSerializer 
    
    def get_queryset(self):
        # overwrite the pagination parameter #
        no_of_records = self.request.QUERY_PARAMS.get('no_of_records', 0)
        all = self.request.QUERY_PARAMS.get('all', 0)

        if no_of_records:
            self.paginate_by = no_of_records
        if all:
           self.paginate_by = None
           self.paginate_by_param = None
           self.max_paginate_by = None            
        ######################################
        queryset = self.queryset
        service_id = self.request.QUERY_PARAMS.get('service_id', None)
        user_id = self.request.QUERY_PARAMS.get('user_id', None)
        duration = self.request.QUERY_PARAMS.get('duration', None)
        if service_id is not None and user_id is not None and duration is not None:
           queryset = queryset.filter(service_id=service_id,user_id=user_id,duration=duration)
        elif service_id is not None and user_id is not None:
           queryset = queryset.filter(service_id=service_id,user_id=user_id) 
        elif service_id is not None:
           queryset = queryset.filter(service_id=service_id)
        elif user_id is not None:   
           queryset = queryset.filter(user_id=user_id) 
        return queryset
    
class Ip2locationInfoViewSet(viewsets.ModelViewSet):
    
    queryset = ip2locationInfo.objects.all()
    serializer_class = ip2locationInfoSerializer
    
    def get_queryset(self):
        queryset = self.queryset
         #--------- Filter by distance-------------------#
        ip = self.request.QUERY_PARAMS.get('ipNumber', '')
        distance = self.request.QUERY_PARAMS.get('distance', '')
        if ip and distance:
          result = []
          zip_code_data=[]
          zip_all=[]
          result =sql_select("select latitude,longitude  from ip2location where "+ip+" between  ip_from and ip_to limit 1")
          if result:
             zip_code_data =sql_select("select zip_code  from ip2location where  (3959 * acos(cos(radians("+result[0]['latitude']+")) * cos(radians(latitude)) * cos( radians(longitude) - radians("+result[0]['longitude']+")) + sin(radians("+result[0]['latitude']+")) * sin(radians(latitude)))) < "+distance+" group by zip_code") 
          if zip_code_data:
              for zip in zip_code_data:
                  zip_all.append(zip['zip_code'])
          queryset = queryset.filter(zip_code__in=zip_all)[:10]        
        elif ip is not None:
           queryset = queryset.filter(ip_from__lte=ip,ip_to__gte=ip)
        else:
           queryset = ip2locationInfo.objects.none() 
        return queryset
    
class AccountDeactivateReasonsViewSet(viewsets.ModelViewSet):
    
    queryset = AccountDeactivateReasons.objects.all()
    serializer_class = AccountDeactivateReasonsserializer
    def create(self, request, *args, **kwargs):
        return Response(status=status.HTTP_400_BAD_REQUEST)
    def update(self, request, *args, **kwargs): 
        return Response(status=status.HTTP_400_BAD_REQUEST)
    def destroy(self, request, *args, **kwargs):
        return Response(status=status.HTTP_400_BAD_REQUEST) 

class DeactivatedAccountListViewSet(viewsets.ModelViewSet):    
    queryset = DeactivatedAccountList.objects.all()
    serializer_class = DeactivatedAccountListSerializer
    
class LookupLocationTypeViewSet(viewsets.ModelViewSet):    
    queryset = LookupLocationType.objects.all()
    serializer_class = LookupLocationTypeSerializer
    def create(self, request, *args, **kwargs):
        return Response(status=status.HTTP_400_BAD_REQUEST)
    def update(self, request, *args, **kwargs): 
        return Response(status=status.HTTP_400_BAD_REQUEST)
    def destroy(self, request, *args, **kwargs):
        return Response(status=status.HTTP_400_BAD_REQUEST)     
    
  
######### End ################################################################


######################################################### Functions ########## 
#Insert, Update, Delete on service_provider_activity,service_provider_educations,
# service_provider_service and service_provider_service_language #############

@api_view(['GET','POST','DELETE'])
def sprelateddata_list(request):
    if request.method == 'GET':
        return Response({'status':'fail','msg':'Support only POST Method.'})
    elif request.method == 'POST':
         try:
           activity=request.DATA['activity']
         except:
           activity=''           
         try:
           language=request.DATA['language']
         except:
           language=''           
         try:
           education=request.DATA['education']
         except:
           education=''
           
         try:
           location=request.DATA['location']
         except:
           location=''           
           
         if activity:
            try:
               activity_obj = json.loads(activity)
            except:
               return Response({'status':'fail','msg':'Please provide correct Json Format for activity'})
        
         if language:
            try:
               language_obj = json.loads(language)
            except:
               return Response({'status':'fail','msg':'Please provide correct Json Format for language'}) 
           
         if education:
            try:
               education_obj = json.loads(education)
            except:
               return Response({'status':'fail','msg':'Please provide correct Json Format for education'}) 
           
         if location:
            try:
               location_obj = json.loads(location)
            except:
               return Response({'status':'fail','msg':'Please provide correct Json Format for location'})            

         msg=''
         if activity:
            status=0
            for val in activity_obj: 
                if val['user_id'] and val['activity_id']:
                   listing = SpUserActivity.objects.filter(users_id=val['user_id'],activity_id=val['activity_id'])
                   
                   if listing.count() <= 0:     
                    new_activity=SpUserActivity(users_id=val['user_id'],activity_id=val['activity_id'])
                    new_activity.save()
                    status=1
            if status:
               msg = 'Activity inserted successfully'
               
         if language:
            status=0
            for val in language_obj: 
                if val['user_id'] and val['language_id']:
                   listing = SpUserLanguage.objects.filter(user_id=val['user_id'],service_language_id=val['language_id'])
                   
                   if listing.count() <= 0:     
                    new_activity=SpUserLanguage(user_id=val['user_id'],service_language_id=val['language_id'])
                    new_activity.save()
                    status=1
            if status:
               msg = msg + 'Language inserted successfully'               
                
         if education:
            status=0
            for val in education_obj: 
                if val['user_id'] and val['education_id']:
                   listing = SpUserEducation.objects.filter(user_id=val['user_id'],education_id=val['education_id'])
                   
                   if listing.count() <= 0:     
                    new_education=SpUserEducation(user_id=val['user_id'],education_id=val['education_id'])
                    new_education.save()
                    status=1
            if status:
                msg = msg + 'Education inserted successfully'
                
         if location:
            status=0
            for val in location_obj: 
                if val['user_id'] and val['location_type_id']:
                   listing = SpLocation.objects.filter(user_id=val['user_id'],location_type_id=val['location_type_id'])
                   
                   if listing.count() <= 0:     
                    new_location=SpLocation(user_id=val['user_id'],location_type_id=val['location_type_id'])
                    new_location.save()
                    status=1
            if status:
                msg = msg + 'Location inserted successfully'                
          
         if msg:      
            return Response({'status':'success','msg':msg}) 
         else:
            return Response({'status':'fail','msg':'error occured'})
    elif request.method == 'DELETE':
        try:
          op = request.DATA['op']
        except:
          op = ''
        try:
          users_id = request.DATA['users_id']
        except:
          users_id = ''  
        if op=='activity' and users_id!='':
                   SpUserActivity.objects.filter(users_id = users_id ).delete()
                   return Response({'msg':'SpUserActivity deleted successfully.'})
        if op=='language' and users_id!='':
                   SpUserLanguage.objects.filter(user_id = users_id ).delete()
                   return Response({'msg':'SpUserLanguage deleted successfully.'})
        if op=='education' and users_id!='':
                   SpUserEducation.objects.filter(user_id = users_id ).delete()
                   return Response({'msg':'SpUserEducation deleted successfully.'})
        if op=='location' and users_id!='':
                   SpLocation.objects.filter(user_id = users_id ).delete()
                   return Response({'msg':'SpLocation deleted successfully.'})               
               
        return Response({'msg':'Please provide correct inputs'})     
        
"""@api_view(['PUT','DELETE'])        
def sprelateddata_detail(request,pk):
    "update, or delete a specific task"
    if request.method == 'PUT':
         serializer = AddressSerializer(address,data=request.DATA)
         
         if serializer.is_valid():
            serializer.save()
            return Response({'status':'success','msg':'data deleted successfully','data':serializer.data})
         else:
            return Response({'status':'fail','msg':'error occured','errors':serializer.errors})
    elif request.method == 'DELETE':
          address.delete()
          return Response({'status':'success','msg':'data deleted successfully'})"""
    
    
 
    

    

