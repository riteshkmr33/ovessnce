from django.shortcuts import render
from django.http import HttpResponse
from django.contrib.auth import authenticate,login as auth_login

from api.models import Page,Users,UserContact,UserCertification,Address ,UserAddress,Country,State,SpUserAddress
from api.models import UserFeatureSetting,UserVerification,Booking,CUsersLanguage

from api.serializers import PageSerializer,UsersSerializer,UserContactSerializer,UserCertificationSerializer,AddressSerializer,CountrySerializer,StateSerializer
from api.serializers import UserFeatureSettingSerializer,UserVerificationSerializer,CUsersLanguageSerializer

from rest_framework import status
from rest_framework.decorators import api_view
from rest_framework.response import Response
from rest_framework import viewsets
from django.core import serializers
from healthservices.authentication import APIAuthentication

import simplejson as json
import hashlib
import string
from django.db.models import Q
from django.db.models import Avg, Max, Min,Sum,Count
########################################## Classes ###################################################################
# Static Page class to get page content

class PagesViewSet(viewsets.ModelViewSet):
    queryset = Page.objects.all()
    serializer_class = PageSerializer
    
    def get_queryset(self):
        queryset = Page.objects.all()
        slug = self.request.QUERY_PARAMS.get('slug', None)
        page_id = self.request.QUERY_PARAMS.get('id', None)
        if page_id is not None:
           queryset = queryset.filter(pk__in=[int(x) for x in page_id.split(",")],page_status=1) 
        elif slug is not None:
           queryset = queryset.filter(slug__in=[str(x) for x in slug.split(",")],page_status=1) 
        else:
           queryset = queryset.filter(page_status=1) 
        return queryset    
    
    # Make Pages readable only
    def create(self, request, *args, **kwargs):
        return Response(status=status.HTTP_400_BAD_REQUEST)
    def update(self, request, *args, **kwargs): 
        return Response(status=status.HTTP_400_BAD_REQUEST) 
    def destroy(self, request, *args, **kwargs):
        return Response(status=status.HTTP_400_BAD_REQUEST) 
# end static page 

# User View Class and function

class UsersViewSet(viewsets.ModelViewSet):
    #authentication_classes = APIAuthentication,
    queryset = Users.objects.all()
    serializer_class = UsersSerializer
    ordering = ('first_name',)

    paginate_by = 10
    paginate_by_param = 'page_size'
    max_paginate_by = 100    
    def get_queryset(self):
        # overwrite the pagination parameter #
        no_of_records = self.request.QUERY_PARAMS.get('no_of_records', 0)
        if no_of_records:
            self.paginate_by = no_of_records
        ######################################
        queryset = Users.objects.exclude(user_type_id=3).exclude(user_type_id=7).exclude(user_type_id=0)
        check_booking = self.request.QUERY_PARAMS.get('check_booking', None)
        sp_provider = self.request.QUERY_PARAMS.get('sp_provider', None)
        exclude_suspended = self.request.QUERY_PARAMS.get('suspended', 0) # added by R on 14-11-2014#
        check_perm = self.request.QUERY_PARAMS.get('check_perm', 0) # added by R on 14-11-2014#
        
        # ---- Check the consumer who have orders ------------ #
        if check_booking is not None and check_booking=='1' and sp_provider is not None:
           result = Booking.objects.values('user_id').annotate(Sum('invoice_id')).filter(service_provider_id=sp_provider)
           user_ids = []
           for item in result:
              user_ids.append(item['user_id'])
           queryset = queryset.filter(id__in=user_ids) 
           # Condition added by R on 14-11-2014 starts here #
           if exclude_suspended == "1":
              queryset = queryset.exclude(Q(status_id=3))
           if check_perm == "1":
               
               permsns = [4,2]
               results = UserFeatureSetting.objects.values('user_id').filter(user__in=user_ids, newsletter__in=permsns)
               #assert False, results
               allowed_users = []
               for item in results:
                   allowed_users.append(item['user_id'])
               queryset = queryset.filter(id__in=allowed_users)
           # Condition added by R on 14-11-2014 ends here #
        #-------------------------------------------------------#
        
        return queryset
    
    def destroy(self, request, *args, **kwargs):
        return Response(status=status.HTTP_400_BAD_REQUEST)
    
    def update(self, request, pk=None):
         try:
           messages = Users.objects.get(id=pk)
         except:
           return Response(status=status.HTTP_404_NOT_FOUND)
       
         serializer =  UsersSerializer(messages,data=request.DATA,partial=True)
         if serializer.is_valid():
            serializer.save()
            return Response(serializer.data)
         else:
            return Response(serializer.errors, status=status.HTTP_400_BAD_REQUEST)    
    
class UserContactViewSet(viewsets.ModelViewSet):
    #authentication_classes = APIAuthentication,
    queryset = UserContact.objects.all()
    serializer_class = UserContactSerializer
    #paginate_by = 2
    #paginate_by_param = 'page_size'
    #max_paginate_by = 100 
    def get_queryset(self):
        queryset = self.queryset
        user_id = self.request.QUERY_PARAMS.get('user_id', None)
        if user_id is not None:
           queryset = queryset.filter(user_id=user_id)
        return queryset    
    def update(self, request, pk=None):
         try:
           messages = UserContact.objects.get(id=pk)
         except:
           return Response(status=status.HTTP_404_NOT_FOUND)
       
         serializer = UserContactSerializer(messages,data=request.DATA,partial=True)
         if serializer.is_valid():
            serializer.save()
            return Response(serializer.data)
         else:
            return Response(serializer.errors, status=status.HTTP_400_BAD_REQUEST)    
    def destroy(self, request, *args, **kwargs):
        return Response(status=status.HTTP_400_BAD_REQUEST)
    
class UserCertificationViewSet(viewsets.ModelViewSet):
    #authentication_classes = APIAuthentication,
    queryset = UserCertification.objects.all()
    serializer_class = UserCertificationSerializer
    # condition added on 24-11-2014 by R starts here #
    def get_queryset(self):
        queryset = self.queryset
        user_id = self.request.QUERY_PARAMS.get('user_id', None)
        if user_id is not None:
           queryset = queryset.filter(user_id=user_id)
        return queryset
    # condition added on 24-11-2014 by R starts here #
    #paginate_by = 2
    #paginate_by_param = 'page_size'
    #max_paginate_by = 100   
    def destroy(self, request, *args, **kwargs):
        return Response(status=status.HTTP_400_BAD_REQUEST)

# Not using following functionality    
class UserAddressViewSet(viewsets.ModelViewSet):
    #authentication_classes = APIAuthentication,
    queryset = Address.objects.all()
    serializer_class = AddressSerializer
    #print queryset.query
    def post(self, request, *args, **kwargs):
        """ return profile of current user if authenticated otherwise 401 """
        return Response({ 'detail': serializer(request.user, context=self.get_serializer_context()).data })
            
        if self.pk is None:
                   id = Address.objects.latest('id')
                   user_id = getattr(self, 'user_id')
                   new_address=UserAddress(user_id=self.user_id_new,address_id=id.id)
                   new_address.save()  
        
        if request.user.is_authenticated():
            return Response({ 'detail': serializer(request.user, context=self.get_serializer_context()).data })
        else:
            return Response({ 'detail': _('Authentication credentials were not provided') }, status=401)        
    #paginate_by = 2
    #paginate_by_param = 'page_size'
    #max_paginate_by = 100    
    def destroy(self, request, *args, **kwargs):
        return Response(status=status.HTTP_400_BAD_REQUEST)  
# End

class CountryViewSet(viewsets.ModelViewSet):
    #authentication_classes = APIAuthentication,
    queryset = Country.objects.all()
    serializer_class = CountrySerializer
    def get_queryset(self):
        queryset = Country.objects.all()
        queryset = queryset.filter(status_id=1).order_by('country_name')
        return queryset    
    #paginate_by = 2
    #paginate_by_param = 'page_size'
    #max_paginate_by = 100     
    # Make country readable only
    def create(self, request, *args, **kwargs):
        return Response(status=status.HTTP_400_BAD_REQUEST)
    def update(self, request, *args, **kwargs): 
        return Response(status=status.HTTP_400_BAD_REQUEST)
    def destroy(self, request, *args, **kwargs):
        return Response(status=status.HTTP_400_BAD_REQUEST)
    
class StateViewSet(viewsets.ModelViewSet):
    #authentication_classes = APIAuthentication,
    queryset = State.objects.all()
    serializer_class = StateSerializer
    def get_queryset(self):
        queryset = State.objects.all()
        queryset = queryset.filter(status_id=1).order_by('state_name')
        country_id = self.request.QUERY_PARAMS.get('country_id', None)
        if country_id is not None:
           queryset = queryset.filter(status_id=1,country_id=country_id) 
        return queryset    
    # Make state readable only
    def create(self, request, *args, **kwargs):
        return Response(status=status.HTTP_400_BAD_REQUEST)
    def update(self, request, *args, **kwargs): 
        return Response(status=status.HTTP_400_BAD_REQUEST)
    def destroy(self, request, *args, **kwargs):
        return Response(status=status.HTTP_400_BAD_REQUEST)    

class UserFeatureSettingViewSet(viewsets.ModelViewSet):
    queryset = UserFeatureSetting.objects.all()
    serializer_class = UserFeatureSettingSerializer
    def get_queryset(self):
        queryset = UserFeatureSetting.objects.all()
        user_id = self.request.QUERY_PARAMS.get('user_id', None)
        if user_id is not None:
           queryset = queryset.filter(user_id=user_id) 
        return queryset    
    # Make API readable only
    def destroy(self, request, *args, **kwargs):
        return Response(status=status.HTTP_400_BAD_REQUEST)
    
    def update(self, request, pk=None):
         try:
           messages = UserFeatureSetting.objects.get(id=pk)
         except:
           return Response(status=status.HTTP_404_NOT_FOUND)
       
         serializer = UserFeatureSettingSerializer(messages,data=request.DATA,partial=True)
         if serializer.is_valid():
            serializer.save()
            return Response(serializer.data)
         else:
            return Response(serializer.errors, status=status.HTTP_400_BAD_REQUEST)    
    
class UserVerificationViewSet(viewsets.ModelViewSet):
    queryset = UserVerification.objects.all()
    serializer_class = UserVerificationSerializer
    
    def get_queryset(self):
        queryset = UserVerification.objects.all()
        user_id = self.request.QUERY_PARAMS.get('user_id', None)
        verification_type_id = self.request.QUERY_PARAMS.get('verification_type_id', None)
        if user_id is not None and verification_type_id is not None:
           queryset = queryset.filter(user_id=user_id,verification_type_id=verification_type_id)
        elif  user_id is not None:
           queryset = queryset.filter(user_id=user_id) 
        return queryset    
    # Make API readable only
    
    def update(self, request, pk=None):
         try:
           messages = UserVerification.objects.get(id=pk)
         except:
           return Response(status=status.HTTP_404_NOT_FOUND)
       
         serializer = UserVerificationSerializer(messages,data=request.DATA,partial=True)
         if serializer.is_valid():
            serializer.save()
            return Response(serializer.data)
         else:
            return Response(serializer.errors, status=status.HTTP_400_BAD_REQUEST)    
    
######################################################### End Classes ###############################################

######################################################### Functions ################################################# 
@api_view(['GET','POST'])
def address_list(request):
    "List all address and create new"
    if request.method == 'GET':
        address = Address.objects.all()
        serializer = AddressSerializer(address)
        return Response(serializer.data)
    elif request.method == 'POST':
         serializer = AddressSerializer(data=request.DATA)
         if serializer.is_valid() and request.DATA['user_id']:
            serializer.save()
            address=Address.objects.latest('id')
            # insert address id in user reference table
            if address.id  and request.DATA['user_type']=='sp':
                   new_address=SpUserAddress(user_id=request.DATA['user_id'],address_id=address.id)
                   new_address.save()
            elif address.id:
                   new_address=UserAddress(user_id=request.DATA['user_id'],address_id=address.id)
                   new_address.save()
            return Response(serializer.data)
         else:
            return Response(serializer.errors, status=status.HTTP_400_BAD_REQUEST)
        
@api_view(['GET','PUT','DELETE'])        
def address_detail(request,pk):
    "GET, update, or delete a specific task"
    try:
        address= Address.objects.get(pk=pk)
    except Address.DoesNotExist:
        return Response(status=status.HTTP_404_NOT_FOUND)
    
    if request.method == 'GET':
        serializer = AddressSerializer(address)
        return Response(serializer.data)
    elif request.method == 'PUT':
         serializer = AddressSerializer(address,data=request.DATA, partial=True)
         
         if serializer.is_valid():
            serializer.save()
            return Response(serializer.data,status=status.HTTP_201_CREATED)
         else:
            return Response(serializer.errors, status=status.HTTP_400_BAD_REQUEST)
    elif request.method == 'DELETE':
          address.delete()
          if pk and request.DATA['user_type']=='sp':
                user_address = SpUserAddress.objects.filter(address_id=pk)
                user_address.delete()
          elif pk:
                user_address = UserAddress.objects.filter(address_id=pk)
                user_address.delete()
                
          return Response(status=status.HTTP_204_NO_CONTENT)
      
#----------User Login | Forgot Password | Reset Password -----------------#      
@api_view(['GET','POST'])       
def useractivity_list(request):
    msg = {}
    if request.method == 'GET':
        email = request.QUERY_PARAMS.get('email',None)
        if email is not None:
            try:
                user= list(Users.objects.filter(email=email).values('id','first_name','last_name','user_name','email','user_type_id','status_id'))
                if user:
                    return Response(user)
                else:
                    Response(status=status.HTTP_404_NOT_FOUND)
            except Users.DoesNotExist:
                return Response(status=status.HTTP_404_NOT_FOUND)  
        else:
        
            return Response(json.dumps('Required some inputs'))
        
    if request.method == 'POST':
        op = request.POST.get('op','')
        if op == 'login':
            
            username = request.POST.get('user_name','')
            password = request.POST.get('Pass','')
            user_type_id = request.POST.get('user_type_id',0)
            
            password = hashlib.md5(password).hexdigest() 
            user = list(Users.objects.filter(user_name=username,Pass=password,user_type_id=user_type_id).values('id','first_name','last_name','user_name','email','user_type_id','status_id','last_login'))
            
            data={} 
            if user > 0:
             from django.forms.models import model_to_dict   
             import time  
             user_new = Users.objects.get(pk=user[0]['id']) 
             user_new.last_login = time.strftime("%Y-%m-%d %H:%M:%S")
             user_new.save()             
             temp = model_to_dict(user_new)
             user[0]['last_login_prev'] = user[0]['last_login']
             user[0]['last_login'] = temp['last_login']
             
             return Response(user[0])
            else:
             return Response('Not Found',status=status.HTTP_404_NOT_FOUND)
        
        if op == 'forgotpassword':

            """email = request.POST.get('email','')
            password = request.POST.get('password','')
            password_org = request.POST.get('password','')
            if password:
               password = hashlib.md5(password).hexdigest()
            else:
               return Response("Please provide password",status=status.HTTP_404_NOT_FOUND)  
            try:
                user = Users.objects.get(email=email)
                user.Pass = password
                user.save()         
                user_new = Users.objects.values('id','first_name','last_name','user_name','email','Pass').filter(email=email).exclude(user_type_id=1)
                data={}
                for key,value in user_new[0].items():
                    data[key]=value
                data['Pass']= password_org   
                return Response(data)
            except:
                return Response(status=status.HTTP_404_NOT_FOUND)"""
            email = request.POST.get('email','')
            if email:
               user = Users.objects.filter(email=email).values('id','first_name','last_name','user_name','email','user_type_id')                
               if user.count() > 0:                
                return Response(user[0])
               else:
                return Response('Not Found',status=status.HTTP_404_NOT_FOUND)
            else:
                return Response('Please provide email',status=status.HTTP_406_NOT_ACCEPTABLE)       
        if op == 'resetpassword': # work as a forgotpassword functionality
            email = request.POST.get('email','')
            password = request.POST.get('password','')
            #repassword = request.POST.get('repassword','')
            if password is not '' and email is not '':                
                try:
                 user = Users.objects.get(email=email)
                except:
                 return Response(status=status.HTTP_400_BAD_REQUEST)
                password = hashlib.md5(password).hexdigest()
                user.Pass = password
                user.save()
                return Response(status=status.HTTP_200_OK)
            else:
                return Response(status=status.HTTP_400_BAD_REQUEST)
            
        if op == 'check_exist_user':
            email = request.POST.get('email','')
            user_name = request.POST.get('user_name','')
            if email or user_name:
               user = Users.objects.filter(Q(email=email) | Q(user_name=user_name)).values('id','first_name','last_name','user_name','email','user_type_id','status_id')                
               if user.count() > 0:                
                return Response(user[0])
               else:
                return Response('Not Found',status=status.HTTP_404_NOT_FOUND)
            else:
                return Response('Please provide email',status=status.HTTP_406_NOT_ACCEPTABLE)            
            
        if op == 'changepassword': #change password by users
            old_password = request.POST.get('old_password','')
            new_password = request.POST.get('new_password','')
            user_id = request.POST.get('user_id','')
            #repassword = request.POST.get('repassword','')
            if old_password is not '' and new_password is not '' and user_id is not '':                
                try:
                 user = Users.objects.get(id=user_id,Pass=old_password)
                except: 
                 return Response(status=status.HTTP_404_NOT_FOUND)   
                user.Pass = new_password
                user.save()
                return Response(status=status.HTTP_200_OK)
            else:
                return Response(status=status.HTTP_400_BAD_REQUEST)            
         
    return Response(status=status.HTTP_400_BAD_REQUEST)       

    

def login_old(request):
    msg = {}
    if request.method == 'POST':
        username = request.POST['username']
        password = request.POST['password']
        user = authenticate(username=username, password=password)
        if user is not None:
            if user.is_active:
                auth_login(request, user)
                msg['status'] = 'success'
                msg['msg'] = 'login successful'
                return HttpResponse(json.dumps(msg), content_type="application/json")
            else:
                msg['status'] = 'fail'
                msg['msg'] = 'disabled account'                
                return HttpResponse(json.dumps(msg), content_type="application/json")
        else:
            msg['status'] = 'fail'
            msg['msg'] = 'invalid login'            
            return HttpResponse(json.dumps(msg), content_type="application/json")
    return HttpResponse(json.dumps('Please login'), content_type="application/json")


######################################################### End Functions #################################################

@api_view(['GET','POST','DELETE'])
def consumerrelateddata_list(request):
    if request.method == 'GET':
        return Response({'status':'fail','msg':'Support only POST Method.'})
    elif request.method == 'POST':          
         try:
           language=request.DATA['language']
         except:
           language=''           
        
         if language:
            try:
               language_obj = json.loads(language)
            except:
               return Response({'status':'fail','msg':'Please provide correct Json Format for language'}) 
         msg=''     
         if language:
            status=0
            for val in language_obj: 
                if val['user_id'] and val['language_id']:
                   listing = CUsersLanguage.objects.filter(user_id=val['user_id'],service_language_id=val['language_id'])
                   
                   if listing.count() <= 0:     
                    new_activity=CUsersLanguage(user_id=val['user_id'],service_language_id=val['language_id'])
                    new_activity.save()
                    status=1
            if status:
               msg = msg + 'Language inserted successfully'               
          
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
        if op=='language' and users_id!='':
                   CUsersLanguage.objects.filter(user_id = users_id ).delete()
                   return Response({'msg':'Language deleted successfully.'})
              
        return Response({'msg':'Please provide correct inputs'})  

   
    

