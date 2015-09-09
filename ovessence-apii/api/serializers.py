from rest_framework import serializers
from api.models import Users,UserContact,UserCertification,UserAddress,Address,Page,Country,State
from api.models import UserFeatureSetting, UserVerification, UserVerificationType,CUsersLanguage
class PageSerializer(serializers.ModelSerializer):
    class Meta:
         model = Page
         fields = ('page_id','title', 'slug', 'content','page_status')
        
class UserContactSerializer(serializers.ModelSerializer):
    user_id = serializers.PrimaryKeyRelatedField(source='user')
    class Meta:
        model = UserContact
        fields = ('id','user_id', 'home_phone', 'work_phone', 'cell_phone','fax')
        
class UserCertificationSerializer(serializers.ModelSerializer):
    user_id = serializers.PrimaryKeyRelatedField(source='user')
    class Meta:
        model = UserCertification
        fields = ('id','user_id', 'title', 'logo', 'organization_name','certification_date','validity','professional_licence_number', 'status_id')  
    def validate_user_id(self, attrs, source):
        value = attrs[source]
        if value == 0:
           raise serializers.ValidationError("user_id must not be zero")
        return attrs 
    
class AddressSerializer(serializers.ModelSerializer):
    
    user_id = serializers.WritableField()
    state_id = serializers.PrimaryKeyRelatedField(source='state')
    state = serializers.RelatedField(source='state')
    country_id = serializers.PrimaryKeyRelatedField(source='country')
    country = serializers.RelatedField(source='country')
    location_type_id = serializers.PrimaryKeyRelatedField(source='location_type_id')
    class Meta:
        model = Address
        fields = ('id','street1_address', 'street2_address', 'city','zip_code','state_id','country_id','state','country','location_type_id')
        #read_only_fields=('id',)
        
        
    def validate_user_id(self, attrs, source):
         value = attrs[source]
         del attrs[source]
         return attrs
    
    """def restore_object(self, attrs, instance=None):
        if instance is not None:
            instance.email = attrs.get('street1_address', instance.street1_address)
            instance.author = attrs.get('street2_address', instance.street2_address)
            instance.url = attrs.get('city', instance.city)
            instance.content = attrs.get('zip_code', instance.zip_code)
            instance.ip = attrs.get('state_id', instance.state_id)
            instance.post_title = attrs.get('country_id', instance.country_id)
            return instance

        user_id = attrs.get('user_id')
        del attrs['user_id']

        address = Address(**attrs)
        address.user_id = user_id

        return address """   
 
        
class UsersSerializer(serializers.ModelSerializer):
    #user_type = serializers.RelatedField(source='user_type')
    contact = serializers.RelatedField(many=True,source='contact')
    certification = serializers.RelatedField(many=True,source='certification') 
    address = serializers.RelatedField(many=True,source='address',read_only=True)
    language = serializers.RelatedField(many=True,source='language',read_only=True)
    class Meta:
        model = Users
        fields = ('id','first_name', 'last_name', 'user_name', 'email','age','gender','Pass','user_type_id','address','contact','certification','avtar_url','language','status_id','age','gender')
        write_only_fields = ('Pass',)
    
    def validate_Pass(self, attrs, source):
         value = attrs[source]
         import hashlib
         if value is not None:
            password = hashlib.md5(value).hexdigest()
            attrs[source]=password
         return attrs        
    def validate_email(self, attrs, source):
        from django.core.validators import validate_email
        from django.core.exceptions import ValidationError
        try:
            validate_email(attrs[source])
            return attrs
        except ValidationError:
            raise serializers.ValidationError("Incorrect email.")
        
    """def validate_user_type_id(self, attrs, source):
        value = attrs[source]
        if value == 0:
           raise serializers.ValidationError("user_type_id must not be zero")
        return attrs """        
        
class CountrySerializer(serializers.ModelSerializer):
    class Meta:
        model = Country 
        fields = ('id','country_code', 'country_name', 'status_id') 
        read_only_fields = ('id','country_code','country_name','status_id')
        ordering_fields = ('country_name',)
        
class StateSerializer(serializers.ModelSerializer):
    class Meta:
        model = State 
        fields = ('id','state_code', 'country_id', 'state_name') 
        read_only_fields = ('id','state_code','country_id','state_name') 
        ordering_fields = ('state_name',)
        
        
# -------------- user_feature_setting Serializer -------------------#
class UserFeatureSettingSerializer(serializers.ModelSerializer):
    user_id = serializers.PrimaryKeyRelatedField(source='user')
    class Meta:
        model = UserFeatureSetting 
        fields = ('id','user_id','chat', 'sms', 'email','newsletter')
        
# -------------- user_verification Serializer  ---------------------#
class UserVerificationSerializer(serializers.ModelSerializer):
    verification_type_id = serializers.PrimaryKeyRelatedField(source='verification_type')
    verification_type = serializers.RelatedField(source='verification_type')
    class Meta:
        model = UserVerification 
        fields = ('id','user_id','verification_type','verification_type_id', 'verification_code', 'verification_status','created_date') 
        
# -------------- verification_type  Serializer---------------------------------#
class VerificationTypeSerializer(serializers.ModelSerializer):
    class Meta:
        model = UserVerificationType 
        fields = ('id','title') 
#--------------- End -----------------------------------------------#


# -------------- CUsersLanguage  Serializer---------------------------------#
class CUsersLanguageSerializer(serializers.ModelSerializer):
    user_id = serializers.PrimaryKeyRelatedField(source='cuserlanguage')
    service_language_id = serializers.PrimaryKeyRelatedField(source='cuserlanguage')
    class Meta:
        model = UserVerificationType 
        fields = ('user_id','service_language_id') 
#--------------- End -----------------------------------------------#
