from rest_framework import serializers
from api.models import SpUsers,SpUserContact,SpUserDetails,Education,UserActivity,Service,Language,SpUserService,SpUserAvailability,availabilityDays
from api.models import SPAppointmentDelay,ip2locationInfo,AccountDeactivateReasons,DeactivatedAccountList
from api.models import LookupLocationType,SpLocation
        
class SpUsersSerializer(serializers.ModelSerializer):
    
    activity = serializers.RelatedField(many=True,source='activity',read_only=True)
    #address = serializers.RelatedField(many=True,source='address',read_only=True)
    work_address = serializers.RelatedField(many=True,source='work_address',read_only=True)
    contact = serializers.RelatedField(many=True,source='contact',read_only=True)
    details = serializers.RelatedField(many=True,source='details',read_only=True)
    education = serializers.RelatedField(many=True,source='education',read_only=True)
    service = serializers.RelatedField(many=True,source='spusersservice',read_only=True)
    language = serializers.RelatedField(many=True,source='language',read_only=True)
    sp_commision = serializers.RelatedField(many=True,source='spcommision',read_only=True)
    location = serializers.RelatedField(many=True,source='location',read_only=True)
    #location_type = serializers.RelatedField(many=True,source='location_type',read_only=True)
    verification = serializers.RelatedField(many=True,source='user_verification',read_only=True)
    
    #min_price = serializers.Field()
    class Meta:
        model = SpUsers
        fields = ('id','first_name', 'last_name', 'user_name', 'email','activity','work_address','contact','details','service','language','avtar_url','education','sp_commision','user_type_id','status_id','age','gender','location','status_id','verification')

class SpUserContactSerializer(serializers.ModelSerializer):
    user_id = serializers.PrimaryKeyRelatedField(source='user')
    class Meta:
        model = SpUserContact
        fields = ('id','user_id', 'first_name', 'last_name', 'phone_number','cellphone')
        #required_fields =('first_name', 'phone_number','cellphone')
        
    def validate_user_id(self, attrs, source):
        value = attrs[source]
        if value == 0:
           raise serializers.ValidationError("user_id must not be zero")
        return attrs
    
class SpUserDetailsSerializer(serializers.ModelSerializer):
    user_id = serializers.PrimaryKeyRelatedField(source='user')
    education = serializers.RelatedField(source='user.education',many=True)
    class Meta:
        model = SpUserDetails
        fields = ('id','user_id','designation', 'company_name', 'description','dob','years_of_experience','prof_membership','awards_and_publication','auth_to_issue_insurence_rem_receipt','auth_to_bill_insurence_copany','professional_license_number','degrees','treatment_for_physically_disabled_person','education','specialties','offering_at_home','offering_at_work_office')
        #required_fields =('first_name', 'phone_number','cellphone')
        
    def validate_user_id(self, attrs, source):
        value = attrs[source]
        if value == 0:
           raise serializers.ValidationError("user_id must not be zero")
        return attrs
    def validate_sex(self, attrs, source):
        value = attrs[source]
        if value=='M' or value=='F':
            return attrs
        else:
           raise serializers.ValidationError("gender value must be M or F")
       

class EducationsSerializer(serializers.ModelSerializer): 
    
    class Meta:
        model = Education
        fields = ('id', 'education_label', 'status_id')
        read_only_fields = ('education_label','status_id')
        
class UserActivitySerializer(serializers.ModelSerializer): 
    activity = serializers.RelatedField(read_only=True)
    class Meta:
        model = UserActivity
        fields = ('id', 'activity')

class ServiceSerializer(serializers.ModelSerializer): 
    
    class Meta:
        model = Service
        fields = ('id', 'category_name', 'parent_id')
        read_only_fields = ('category_name', 'parent_id')
        
class LanguageSerializer(serializers.ModelSerializer): 
    
    class Meta:
        model = Language
        fields = ('id', 'language_name', 'status_id') 
        read_only_fields = ('language_name', 'status_id')
        
class SpUserServiceSerializer(serializers.ModelSerializer): 
    user = serializers.RelatedField(read_only=True,source='user')
    user_id = serializers.PrimaryKeyRelatedField(source='user')
    service = serializers.RelatedField(read_only=True,source='service')
    service_id = serializers.PrimaryKeyRelatedField(source='service')
    class Meta:
        model = SpUserService
        fields = ('id', 'user_id', 'service_id','duration','price','status_id','user','service') 

        
# ------------ Sp users SpUserAvailability ------------------#

class SpUserAvailabilitySerializer(serializers.ModelSerializer): 
    user_id = serializers.PrimaryKeyRelatedField(source='user')
    days_id = serializers.PrimaryKeyRelatedField(source='days')
    class Meta:
        model = SpUserAvailability
        fields = ('id', 'user_id', 'days_id','address_id','start_time','end_time','lunch_start_time','lunch_end_time')  

class availabilityDaysSerializer(serializers.ModelSerializer): 
    class Meta:
        model = availabilityDays
        fields = ('id', 'day')
        
class SPAppointmentDelaySerializer(serializers.ModelSerializer): 
    user_id = serializers.PrimaryKeyRelatedField(source='user')
    class Meta:
        model = SPAppointmentDelay
        fields = ('id','user_id','delay_time')   
        
class ip2locationInfoSerializer(serializers.ModelSerializer): 
    class Meta:
        model = ip2locationInfo
        fields = ('ip_from','ip_to','country_code','country_name','city_name','zip_code')        
        
       
class AccountDeactivateReasonsserializer(serializers.ModelSerializer):
    class Meta:
        model = AccountDeactivateReasons
        fields = ('id','reason')    
        
class DeactivatedAccountListSerializer(serializers.ModelSerializer): 
    user_id = serializers.PrimaryKeyRelatedField(source='dal_user')
    user = serializers.RelatedField(source='dal_user')
    reason_id = serializers.PrimaryKeyRelatedField(source='d_reason')
    reason = serializers.RelatedField(source='d_reason')
    class Meta:
        model = DeactivatedAccountList
        fields = ('id','user_id','reason','reason_id','user_id','user','comment','created_date')     
        
        
class LookupLocationTypeSerializer(serializers.ModelSerializer):
    class Meta:
        model = LookupLocationType
        fields = ('id','location_type') 
        
class SpLocationSerializer(serializers.ModelSerializer):
    user_id = serializers.PrimaryKeyRelatedField(source='user')
    user = serializers.RelatedField(source='user')
    location = serializers.PrimaryKeyRelatedField(source='location_type')
    location_type_id = serializers.RelatedField(source='location_type')    
    class Meta:
        model = SpLocation
        fields = ('location_type_id','user_id','location')         
