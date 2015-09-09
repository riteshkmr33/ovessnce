from django.db import models
from django.contrib.auth.models import User,AbstractBaseUser,UserManager,BaseUserManager
# Create your models here.

class UserType(models.Model):
    class Meta:
        db_table = 'lookup_user_type' 
        app_label = 'api'
    user_type = models.CharField(max_length=50)
    def __unicode__(self): 
        return self.user_type    
    
class UserStatus(models.Model):
    class Meta:
        db_table = 'lookup_status' 
        app_label = 'api'
    status = models.CharField(max_length=50)
    def __unicode__(self): 
        return self.status    
     
class Users(AbstractBaseUser):
    class Meta:
        db_table = 'users'
        app_label = 'api'
    # related field data 
     #user_type = models.ForeignKey(UserType, related_name='user_type_id')
    address = models.ManyToManyField('Address', through = 'UserAddress')
    language = models.ManyToManyField('Language', through = 'CUsersLanguage',related_name='Clanguage')
    # related field data end
    register_from = models.IntegerField(default=0)
    first_name = models.CharField(max_length=50)
    last_name = models.CharField(max_length=50)
    user_name = models.CharField(max_length=50,unique=True)
    email = models.CharField(max_length=70,unique=True)
    age = models.SmallIntegerField(default=0)
    gender = models.CharField(max_length=1,default='M')    
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
        return '{"user_id":%d,"user_name":"%s","email":"%s","first_name":"%s","last_name":"%s","avtar_url":"%s","user_type_id":"%d"}' %(self.id,self.user_name,self.email,self.first_name,self.last_name,self.avtar_url,self.user_type_id)   
    
    @property 
    def is_staff(self):
        return True 
#-------------Consumer Language -----------------------#
class CUsersLanguage(models.Model):
    class Meta:
        db_table = 'consumer_service_language' 
        app_label = 'api'
    user = models.ForeignKey('Users', related_name='cuserlanguage')    
    service_language = models.ForeignKey('Language', related_name='cuserlanguage')
#------------------------------------------------#    
    
class UserContact(models.Model):
    class Meta:
        db_table = 'user_contact' 
        app_label = 'api'
    user = models.ForeignKey(Users, related_name='contact',unique=True)    
    home_phone = models.CharField(max_length=20,null=True, blank=True)
    work_phone = models.CharField(max_length=20,null=True, blank=True)
    cell_phone = models.CharField(max_length=20,null=True, blank=True)
    fax = models.IntegerField(max_length=20,null=True, blank=True)
    
    def __unicode__(self):
        return '{"id":%d,"home_phone":"%s","work_phone":"%s","cell_phone":"%s","fax":"%s"}' % (self.id, self.home_phone,self.work_phone,self.cell_phone,self.fax)
    
class UserCertification(models.Model):
    class Meta:
        db_table = 'user_certification' 
        app_label = 'api'
    id = models.IntegerField(primary_key=True)    
    user = models.ForeignKey(Users, related_name='certification')    
    title = models.CharField(max_length=100)
    logo = models.CharField(max_length=200)
    organization_name = models.CharField(max_length=100)
    certification_date = models.DateField(null=True, blank=True)
    validity = models.DateField(null=True, blank=True)
    # Fields added on 24-11-2014 by R starts here #
    professional_licence_number = models.CharField(max_length=50)
    status_id = models.IntegerField(default=5)
    # Fields added on 24-11-2014 by R ends here #

    
    def __unicode__(self):
        return '{"id":%d,"title":"%s","logo":"%s","organization_name":"%s","certification_date":"%s","validity":"%s","professional_licence_number":"%s"}' % (self.id, self.title,self.logo,self.organization_name,self.certification_date,self.validity, self.professional_licence_number)    
 
class UserAddress(models.Model):
    class Meta:
        db_table = 'user_address' 
        app_label = 'api'
    user = models.ForeignKey('Users',related_name='useraddress')
    address = models.ForeignKey('Address',related_name='useraddress')    
    
class Address(models.Model):
    class Meta:
        db_table = 'address' 
        app_label = 'api'
        
    
    @property
    def user_id(self):
        return None
    street1_address = models.CharField(max_length=100)
    street2_address = models.CharField(max_length=100,null=True, blank=True)
    city = models.CharField(max_length=100)
    zip_code = models.CharField(max_length=10)
    state = models.ForeignKey('State')
    country = models.ForeignKey('Country')
    #location_type_id = models.SmallIntegerField(null=True, blank=True)
    location_type_id = models.ForeignKey("SpLocationType",db_column="location_type_id",related_name='location_type_id',null=True, blank=True)
    
    # this will be inserted

    """def save(self,force_insert=True,force_update=False):
                super(Address, self).save()
                
                if self.pk is None:
                   id = Address.objects.latest('id')
                   user_id = getattr(self, 'user_id')
                   new_address=UserAddress(user_id=self.user_id,address_id=id.id)
                   new_address.save()"""                   
                
    def __unicode__(self):
        
            loc_id =0
            loc_name='none'
            if self.location_type_id is not None:
                loc_id = self.location_type_id.id
                loc_name = self.location_type_id.location_type
            
            return       '{"id":%d,"street1_address":"%s","street2_address":"%s","city":"%s","zip_code":"%s","state_id":%s,"state_name":"%s","country_id":%s,"country_name":"%s","location_id":"%d","location_type":"%s"}' % (self.id,self.street1_address, self.street2_address,self.city,self.zip_code,self.state_id,self.state.state_name,self.country_id,self.country.country_name,loc_id,loc_name)
        
    
# -------------- user_feature_setting -------------------#
class UserFeatureSetting(models.Model):
    class Meta:
        db_table = 'user_feature_setting' 
        app_label = 'api'
    user = models.ForeignKey('Users',related_name='feature_user') 
    newsletter = models.SmallIntegerField(default=1)
    chat = models.SmallIntegerField(default=0) 
    sms  = models.SmallIntegerField(default=0)
    email= models.SmallIntegerField(default=0)
#--------------- End ------------------------------------#

# -------------- user_verification -------------------#
class UserVerification(models.Model):
    class Meta:
        db_table = 'user_verification' 
        app_label = 'api'
    user_id = models.IntegerField() 
    verification_type = models.ForeignKey('UserVerificationType') 
    verification_code  = models.CharField(max_length=50)
    verification_status= models.SmallIntegerField(default=0)
    created_date= models.DateTimeField()
       
#--------------- End ------------------------------------#

# -------------- verification_type -------------------#
class UserVerificationType(models.Model):
    class Meta:
        db_table = 'verification_type' 
        app_label = 'api'
    title = models.CharField(max_length=100)
    def __unicode__(self):
        return '{"id":"%d","title":"%s"}' % (self.id,self.title)   
#--------------- End ------------------------------------#
