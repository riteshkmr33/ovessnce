from django.db import models
from django.contrib.auth.models import User,AbstractBaseUser,UserManager,BaseUserManager
from api.models import UserType,UserStatus
# Create your models here.

######### LOOKUP TABLES ##############
    
class Education(models.Model):
    class Meta:
        db_table = 'educations' 
        app_label = 'api'
    education_label = models.CharField(max_length=100)
    status_id = models.IntegerField(default=1)
    def __unicode__(self): 
        return  '{"id":%d,"education_label":"%s"}' %(self.id,self.education_label)
    
class UserActivity(models.Model):
    class Meta:
        db_table = 'lookup_activity' 
        app_label = 'api'
    activity = models.CharField(max_length=100)
    def __unicode__(self): 
        return self.activity  
    
class Service(models.Model):
    class Meta:
        db_table = 'service_category'
        app_label = 'api'
    category_name = models.CharField(max_length=100)
    parent_id = models.IntegerField(default=0)
    def __unicode__(self): 
        return  '{"id":%d,"category_name":"%s"}' %(self.id,self.category_name)
    
class Language(models.Model):
    class Meta:
        db_table = 'service_language' 
        app_label = 'api'
    language_name = models.CharField(max_length=70)
    status_id        = models.SmallIntegerField(default=1)
    def __unicode__(self): 
        return '{"id":%d,"service_language":"%s","status_id":%d}' %(self.id,self.language_name,self.status_id)
    
class availabilityDays(models.Model):
    class Meta:
        db_table = 'availability_days' 
        app_label = 'api'
    day = models.CharField(max_length=10)
    def __unicode__(self): 
        return '{"id":%d,"day":"%s"}' %(self.id,self.day) 
        
class verification(models.Model):
    class Meta:
        db_table = 'user_verification' 
        app_label = 'api'
    user = models.ForeignKey("SpUsers",related_name='user_verification')
    verification_type_id = models.IntegerField()
    verification_code = models.CharField(max_length=50)
    verification_status = models.IntegerField()
    created_date = models.DateTimeField(auto_now_add=True)
    
    def __unicode__(self): 
        return '{"id":%d,"verification_type_id":"%d","verification_code":"%s","verification_status":"%d"}' %(self.id,self.verification_type_id,self.verification_code,self.verification_status)    
        
        
class SpLocationType(models.Model):
    class Meta:
        db_table = 'lookup_location_type' 
        app_label = 'api'
    
    location_type = models.CharField(max_length=100)
     
    def __unicode__(self): 
        return '{"id":%d,"location_type":"%s"}' %(self.id,self.location_type)    
################# END LOOKUP TABLES ######################

class SpUsers(AbstractBaseUser):
    class Meta:
        db_table = 'users'
        app_label = 'api'
    # related field data 
    #user_type = models.ForeignKey(UserType, related_name='user_type_id')
    activity = models.ManyToManyField('UserActivity', through = 'SpUserActivity')
    #address = models.ManyToManyField('Address', through = 'UserAddressForSp',related_name='address')
    work_address = models.ManyToManyField('Address', through = 'SpUserAddress',related_name='work_address')
    education = models.ManyToManyField('Education', through = 'SpUserEducation')
    service = models.ManyToManyField('Service', through = 'SpUserService',related_name='service')
    language = models.ManyToManyField('Language', through = 'SpUserLanguage',related_name='language')
    availability = models.ManyToManyField('availabilityDays', through = 'SpUserAvailability',related_name='availability')
    location = models.ManyToManyField('LookupLocationType', through = 'SpLocation',related_name='location')
    #location_type =  models.ForeignKey('Address', related_name='location_type')
    
    
    # related field data end
    register_from = models.IntegerField(default=0)
    first_name = models.CharField(max_length=50,null=True, blank=True)
    last_name = models.CharField(max_length=50,null=True, blank=True)
    user_name = models.CharField(max_length=50,unique=True,null=True, blank=True)
    email = models.CharField(max_length=70,unique=True,null=True, blank=True)
    
    Pass = models.CharField(max_length=100,null=True, blank=True)
    user_type_id = models.IntegerField(blank=False)
    social_media_id= models.CharField(max_length=100)
    created_date=models.DateTimeField(auto_now_add=True)
    #last_login=models.DateTimeField(default='2013-01-01 00:00:00')
    expiration_date=models.DateField(null=True, blank=True)
    status =models.ForeignKey(UserStatus),
    status_id = models.IntegerField(default=5) 
    avtar_url = models.CharField(max_length=255,null=True, blank=True)
    age     = models.IntegerField(null=True, blank=True)
    gender  = models.CharField(max_length=1,default='M')
    
    
    USERNAME_FIELD = 'user_name'
    REQUIRED_FIELDS = ['email','Pass']
    objects = UserManager()
    
    #last_login=models.DateTimeField()
    #def save(self,force_insert=True,force_update=False):
            #self.Pass = self.Pass
            #super(Users, self).save()    
    def __unicode__(self):
        return '{"user_id":%d,"user_name":"%s","first_name":"%s","last_name":"%s","avtar_url":"%s"}' %(self.id,self.user_name,self.first_name,self.last_name,self.avtar_url)   
    
    @property 
    def is_staff(self):
        return True 

class UserAddressForSp(models.Model):
    class Meta:
        db_table = 'user_address' 
        app_label = 'api'
    user = models.ForeignKey('SpUsers')
    address = models.ForeignKey('Address') 
    
############### INTERMEDIATRY TABLES OR CROSS REFERENCE TABLES ##########################    
class SpUserActivity(models.Model):
    class Meta:
        db_table = 'service_provider_activity' 
        app_label = 'api'
    #users_id     = models.IntegerField(blank=False)
    #activity_id  = models.IntegerField(blank=False)
    created_date = models.DateTimeField(auto_now_add=True)
    users = models.ForeignKey('SpUsers',related_name='spusersactivity')
    activity = models.ForeignKey('UserActivity',related_name='spusersactivity')
    
class SpUserEducation(models.Model):
    class Meta:
        db_table = 'service_provider_educations' 
        app_label = 'api'
    user = models.ForeignKey('SpUsers',related_name='spuserseducation')
    education = models.ForeignKey('Education',related_name='spuserseducation')
    
class SpUserService(models.Model):
    class Meta:
        db_table = 'service_provider_service' 
        app_label = 'api'
    duration = models.IntegerField()
    price = models.DecimalField(max_digits=15, decimal_places=2, null=True)
    status_id = models.IntegerField(default=0)
    user = models.ForeignKey('SpUsers',related_name='spusersservice')
    service = models.ForeignKey('Service',related_name='spusersservice')
    
    def __unicode__(self):
        return '{"id":%s,"service_id":%s,"service_name":"%s","duration":"%s","price":"%s","status_id":%d}' % (self.id,self.service.id,self.service.category_name,self.duration,self.price, self.status_id)
     
class SpUserAddress(models.Model):
    class Meta:
        db_table = 'service_provider_address' 
        app_label = 'api'
        
    user = models.ForeignKey('SpUsers',related_name='spusersaddress')
    address = models.ForeignKey('Address',related_name='spusersaddress')  
    
class SpUserLanguage(models.Model):
    class Meta:
        db_table = 'service_provider_service_language' 
        app_label = 'api'
    user = models.ForeignKey('SpUsers',related_name='spuserslanguage')
    service_language = models.ForeignKey('Language',related_name='spuserslanguage')
    
class SpUserAvailability(models.Model):
    class Meta:
        db_table = 'service_provider_availability' 
        app_label = 'api'
        unique_together = ('user','days')
  
    user = models.ForeignKey('SpUsers',related_name='availability_days')
    days = models.ForeignKey('availabilityDays',related_name='availability_days')
    address_id = models.IntegerField(blank=True,null=True)
    start_time = models.TimeField(blank=True,null=True)
    end_time   = models.TimeField(blank=True,null=True)
    lunch_start_time = models.TimeField(blank=True,null=True)
    lunch_end_time   = models.TimeField(blank=True,null=True)            
##################################################################################

class SpUserContact(models.Model):
    class Meta:
        db_table = 'service_provider_contact' 
        app_label = 'api'
    user = models.ForeignKey(SpUsers, related_name='contact',null=True, blank=True)    
    first_name = models.CharField(max_length=50,null=True, blank=True)
    last_name = models.CharField(max_length=50,null=True, blank=True)
    phone_number = models.CharField(max_length=20,null=True, blank=True)
    cellphone = models.IntegerField(max_length=20,null=True, blank=True)
    
    def __unicode__(self):
        return '{"id":%d,"user_id":%d,"first_name":"%s","last_name":"%s","phone_number":"%s","cellphone":"%s"}' % (self.id,self.user.id, self.first_name,self.last_name,self.phone_number,self.cellphone)

class SpUserDetails(models.Model):
    class Meta:
        db_table = 'service_provider_details' 
        app_label = 'api'
    user = models.ForeignKey(SpUsers, related_name='details') 
    designation = models.CharField(max_length=100,null=True, blank=True)
    company_name = models.CharField(max_length=50,null=True, blank=True)
    description = models.CharField(max_length=2000,null=True, blank=True)
    #sex = models.CharField(max_length=1,null=True, blank=True)
    dob = models.DateField(null=True, blank=True)
    years_of_experience = models.IntegerField(null=True, blank=True)
    prof_membership = models.CharField(max_length=255,null=True, blank=True)
    professional_license_number = models.CharField(max_length=50,null=True, blank=True)
    degrees = models.CharField(max_length=255,null=True, blank=True)
    awards_and_publication = models.CharField(max_length=255,null=True, blank=True)
    auth_to_issue_insurence_rem_receipt = models.SmallIntegerField(null=True, blank=True)
    auth_to_bill_insurence_copany = models.SmallIntegerField(null=True, blank=True)

    treatment_for_physically_disabled_person = models.SmallIntegerField(null=True, blank=True)
    specialties = models.CharField(max_length=255,null=True, blank=True)
    offering_at_work_office = models.SmallIntegerField(null=True, blank=True)
    offering_at_home = models.SmallIntegerField(null=True, blank=True)
    
    def __unicode__(self):
        if self.description is not None:
            self.description = self.description.replace("\\", "")
        return '{"id":%d,"user_id":%s,"designation":"%s","company_name":"%s","description":"%s","dob":"%s","years_of_experience":"%s","prof_membership":"%s","awards_and_publication":"%s","auth_to_issue_insurence_rem_receipt":"%s","auth_to_bill_insurence_copany":"%s","degrees":"%s","treatment_for_physically_disabled_person":"%s","professional_license_number":"%s","specialties":"%s","offering_at_home":"%s","offering_at_work_office":"%s"}' % (self.id,self.user.id,self.designation, self.company_name,self.description,self.dob,self.years_of_experience,self.prof_membership,self.awards_and_publication,self.auth_to_issue_insurence_rem_receipt,self.auth_to_bill_insurence_copany,self.degrees,self.treatment_for_physically_disabled_person,self.professional_license_number,self.specialties,self. offering_at_home,self.offering_at_work_office)

class SpSiteCommision(models.Model):
    class Meta:
        db_table = 'service_provider_site_commision' 
        app_label = 'api'
    user = models.ForeignKey(SpUsers,related_name='spcommision')    
    commision = models.IntegerField()
    status_id = models.SmallIntegerField() 
    created_date =models.DateTimeField(auto_now_add=True)
    def __unicode__(self):
        return '{"id":%d,"commision":%d,"status_id":%d,"created_date":"%s"}' % (self.id, self.commision,self.status_id,self.created_date)

class SPAppointmentDelay(models.Model):
    class Meta:
        db_table = 'service_provider_appointment_delay' 
        app_label = 'api'
    user = models.ForeignKey(SpUsers) 
    delay_time = models.SmallIntegerField()
    
class ip2locationInfo(models.Model):
    class Meta:
        db_table = 'ip2location' 
        app_label = 'api'
    ip_from = models.IntegerField(primary_key=True)
    ip_to = models.IntegerField(unique=True)
    country_code = models.CharField(max_length=100)
    country_name = models.CharField(max_length=100)
    region_name = models.CharField(max_length=128)
    city_name = models.CharField(max_length=128)
    latitude = models.CharField(max_length=100)
    longitude = models.CharField(max_length=100)
    zip_code = models.CharField(max_length=30)
    time_zone = models.CharField(max_length=20)
    
class AccountDeactivateReasons(models.Model):
    class Meta:
        db_table = 'account_deactivate_reasons' 
        app_label = 'api'
    reason = models.CharField(max_length=255)
    def __unicode__(self):
        return '{"id":%d,"reason":"%s"}' % (self.id, self.reason)    
    
class DeactivatedAccountList(models.Model):
    class Meta:
        db_table = 'deactivated_accounts_list' 
        app_label = 'api'
    dal_user = models.ForeignKey(SpUsers,related_name='dal_user',db_column='user_id')
    d_reason = models.ForeignKey('AccountDeactivateReasons',related_name='d_reason',db_column='reason_id')
    comment = models.CharField(max_length=255,null=True, blank=True)
    created_date = models.DateTimeField(auto_now_add=True)
    
    
# -------------- Service Provider Location Type -------------------#
class LookupLocationType(models.Model):
    class Meta:
        db_table = 'lookup_location_type' 
        app_label = 'api'
    location_type = models.CharField(max_length=100,null=True, blank=True)
    def __unicode__(self):
        return '{"id":"%d","location_type":"%s"}' % (self.id,self.location_type)
    
class SpLocation(models.Model):
    class Meta:
        db_table = 'service_provider_location_type' 
        app_label = 'api'
    user = models.ForeignKey(SpUsers,related_name='location_user')
    location_type = models.ForeignKey('LookupLocationType',related_name='location_location_type')
    def __unicode__(self):
        return '{"user_id":"%d","location_type_id":"%s"}' % (self.user.id,self.location_type.id,self.location_type.location_type)    
    
#--------------- End ------------------------------------#    
          
  
