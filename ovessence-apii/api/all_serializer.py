from rest_framework import serializers
from api.models import NewsLetterSubscription,LookupRating,Rate,Service,RateInsert,Feedback,EmailTemplate,Booking,SiteFeature,Subscription
from api.models import Invoice,InvoiceDetails,PaymentHistory,UserSubscriptions,Partners,PractitionerOrganization,BannerBooking,PublisherBanner,Media
from api.models import Messages,SiteSettings,WishList,Newsletter,NewsletterSend,ServiceProviderReference,PaymentRefundHistory
from api.models import InvoiceDetailsWithoutRelation,PaymentHistoryWithoutRelation,BookingSuggestionHistory,SmsHistory,Sms
from api.models import PractitionerOrganizationLookup,Testimonials,FeatureVideoLimit,ManualBookings,UserCardDetails,searchCityZip,banners,bannerPageLocation,faqIndex,faqs,ResponseRate
#---- NewsLetter ---------------#          
class NewsLetterSubscriptionSerializer(serializers.ModelSerializer):
   
    class Meta:
        model = NewsLetterSubscription
        fields = ('id', 'email', 'status_id')
    def validate_email(self, attrs, source):
        from django.core.validators import validate_email
        from django.core.exceptions import ValidationError
        try:
            validate_email(attrs[source])
            return attrs
        except ValidationError:
            raise serializers.ValidationError("Incorrect email.") 
        
# ------    End ---------------#     

#---- Rating & Feedback Serializer ---------------#  
class LookupRatingSerializer(serializers.ModelSerializer):
   
    class Meta:
        model = LookupRating
        fields = ('id','rating_type','status_id')
    read_only_fields = ('id','rating_type','status_id') 
    
class RateSerializer(serializers.ModelSerializer):
    
    rating_type = serializers.RelatedField(source='rating_type') 
    service = serializers.RelatedField(source='service')
    createdby = serializers.RelatedField(source='createdby') 
    class Meta:
        model = Rate
        #fields = ('users_id','service_id','rating_type_id','rate','created_date','created_by','rating_type','service','createdby')        
        read_only_fields = ('users_id','service_id','rating_type_id','rate','created_date','created_by')# #Commented by R on 12-11-2014#
        
        #Added by R on 12-11-2014#
        fields = ('users_id','rate','created_date','created_by','rating_type','service','created_by')        
        read_only_fields = ('users_id','rate','created_date','created_by')
        
        
class RateInsertSerializer(serializers.ModelSerializer):
    class Meta:
        model = RateInsert
        fields = ('users_id','service_id','rating_type_id','rate','created_date','created_by') 
        
class FeedbackSerializer(serializers.ModelSerializer):

    class Meta:
        model = Feedback
        fields = ('users_id','service_id','comments','created_date','created_by','status_id','view_status')        
     
#--------------End---------------------------------#    

#----------------- Email Templates ----------------#
class EmailTemplateSerializer(serializers.ModelSerializer):

    class Meta:
        model = EmailTemplate
        fields = ('id','subject','content','status','fromEmail')
        read_only_fields = ('id','subject','content','status','fromEmail')
        
#----------------- End  ----------------#  

#----------------- Booking  -----------------------#
class InvoiceSerializer(serializers.ModelSerializer):
    invoice_details = serializers.RelatedField(source='invoice_details',many=True)
    payment_history = serializers.RelatedField(source='payment_history')
    payment_refund_history = serializers.RelatedField(source='payment_history.payment_refund_history',many=True)
    class Meta:
        model = Invoice
        fields = ('id','user_id','sale_type','created_date','created_by','invoice_total','status_id','invoice_details','payment_history','payment_refund_history','site_commision')
        
class InvoiceDetailsSerializer(serializers.ModelSerializer):
    invoice_id = serializers.RelatedField(source='invoice_details')
    #invoice_id = serializers.PrimaryKeyRelatedField(source='invoice_details')
    class Meta:
        model = InvoiceDetails
        fields = ('invoice_id','sale_item_details','amount')
# invoice details without relation        
class InvoiceDetailsWSerializer(serializers.ModelSerializer):
    class Meta:
        model = InvoiceDetailsWithoutRelation
        fields = ('invoice_id','sale_item_details','amount','subscription_duration_id')        
# end        
class PaymentHistorySerializer(serializers.ModelSerializer):
    invoice_id = serializers.RelatedField(source='payment_history')
    class Meta:
        model = PaymentHistory
        fields = ('invoice_id','payment_method_id','payment_instrument_no','payment_date','amount_paid','currency','transaction_charge','response_code','auth_code','transaction_id','avs_response','response_message','status_id')

# invoice details without relation       
class PaymentHistoryWSerializer(serializers.ModelSerializer):
    class Meta:
        model = PaymentHistoryWithoutRelation
        fields = ('invoice_id','payment_method_id','payment_instrument_no','payment_date','amount_paid','currency','transaction_charge','response_code','auth_code','transaction_id','avs_response','response_message','status_id')
# end 
class PaymentRefundHistorySerializer(serializers.ModelSerializer):
    payment_history_id = serializers.PrimaryKeyRelatedField(source='payment_history')
    #payment_history = serializers.RelatedField(source='payment_history')
    #booking_history = serializers.RelatedField(source='payment_history.invoice.lookipInvoice',many=True)
    class Meta:
        model = PaymentRefundHistory
        fields = ('payment_history_id','payment_method_id','payment_instrument_no','payment_date','currency','response_code','auth_code','transaction_id','avs_response','response_message','status_id')
                                   
class BookingSerializer(serializers.ModelSerializer):
    #user_id = serializers.PrimaryKeyRelatedField(source='user')
    service_provider_id = serializers.PrimaryKeyRelatedField(source='service_provider')
    service_provider_service_id = serializers.PrimaryKeyRelatedField(source='service_provider_service')
    
    class Meta:
        model = Booking
        
        #fields = ('id','user_id','service_provider_id','service_provider_service_id','invoice_id','created_date','parent_booking_id','booked_date','status_id','payment_status')
        fields = ('id','user_id','service_provider_id','service_provider_service_id','service_address_id','created_date','parent_booking_id','booked_date','status_id','payment_status','invoice_id')

class UserSubscriptionsSerializer(serializers.ModelSerializer):
    user_id = serializers.PrimaryKeyRelatedField(source='user')
    subscription_duration_id = serializers.PrimaryKeyRelatedField(source='subscription_duration')
    subscription_duration = serializers.RelatedField(source='subscription_duration')
    class Meta:
        model = UserSubscriptions
        fields = ('id','user_id','subscription_duration_id','subscription_duration','subscription_start_date','subscription_end_date','invoice_id','status_id','auto_renewal','user_card_id')

class BookingSuggestionHistorySerializer(serializers.ModelSerializer):
    user_id = serializers.PrimaryKeyRelatedField(source='user',required=False)
    user = serializers.RelatedField(source='user')
    class Meta:
        model = BookingSuggestionHistory
        fields = ('id','user_id','user','booking_id','booking_time','booking_status')
      
                
#----------------- Email Templates ----------------# 

#----------------- SiteFeature  -----------------------#
class SiteFeatureSerializer(serializers.ModelSerializer):

    class Meta:
        model = SiteFeature
        fields = ('id','feature_name','description','status_id')
        read_only_fields = ('feature_name','description','status_id')
        
class SubscriptionSerializer(serializers.ModelSerializer):
    duration = serializers.RelatedField(source='subscriptionduration',many=True)
    site_feature = serializers.RelatedField(source='subscription',many=True)
    subscription_video_limit = serializers.RelatedField(source='subscription_video_limit',many=True)
    class Meta:
        model = Subscription
        fields = ('id','subscription_name','duration','site_feature','subscription_video_limit')
#----------------- End --------------------------------#         

#------------------ Banner Booking by Consumer --------------------#
class BannerBookingSerializer(serializers.ModelSerializer):
    user_id = serializers.PrimaryKeyRelatedField(source='user')
    advertisement_plan_id = serializers.PrimaryKeyRelatedField(source='advertisement_plan')
    class Meta:
        model = BannerBooking
        fields = ('id','user_id','advertisement_plan_id','start_date','end_date','booking_date','invoice_id','status_id')

#------------------------ End -------------------------------------#

#------------  Practitioner Organization  ----------------#
class PartnersSerializer(serializers.ModelSerializer):

    class Meta:
        model = Partners
        fields = ('id','title','desc','url','logo','status_id')
        read_only_fields = ('id','title','desc','url','logo','status_id')
        
# ----------------End----------------------------------#
#------------  Practitioner Organization ----------------#
class PractitionerOrganizationSerializer(serializers.ModelSerializer):

    class Meta:
        model = PractitionerOrganization
        fields = ('organization_id','organization_name','logo','phone_no','email','status_id')

class PractitionerOrganizationLookupSerializer(serializers.ModelSerializer):
    practitioner_id = serializers.PrimaryKeyRelatedField(source='practitioner')
    organization_id = serializers.PrimaryKeyRelatedField(source='organization')
    practitioner = serializers.RelatedField(source='practitioner')
    organization = serializers.RelatedField(source='organization')    
    class Meta:
        model = PractitionerOrganizationLookup
        fields = ('id','practitioner_id','organization_id','practitioner','organization')
     
        
#------------------------------------------------------#  

#------------  Media ----------------#
class MediaSerializer(serializers.ModelSerializer):
    user_id = serializers.PrimaryKeyRelatedField(source='user')
    class Meta:
        model = Media
        fields = ('id','user_id','media_url','media_title','media_description','media_type','created_date','updated_date','created_by','updated_by','status_id')
#------------------------------------------------------# 

#------------  Media ----------------#
class MessagesSerializer(serializers.ModelSerializer):
    from_user_id = serializers.PrimaryKeyRelatedField(source='from_user')
    from_avtar_url = serializers.RelatedField(source='from_user.avtar_url')
    from_user_details = serializers.RelatedField(source='from_user')
    from_user_status = serializers.RelatedField(source='from_user.status_id')
    
    to_user_id = serializers.PrimaryKeyRelatedField(source='to_user')
    to_avtar_url = serializers.RelatedField(source='to_user.avtar_url')
    to_user_details = serializers.RelatedField(source='to_user')
    to_user_status = serializers.RelatedField(source='to_user.status_id')
    
    to_user_services = serializers.RelatedField(source='to_user.service',many=True)
    class Meta:
        model = Messages
        fields = ('id','from_user_id','from_name','to_user_id','subject','message','replyId','topLevel_id','readFlag','readFlag_c','readFlag_p','deleteFlag','deleteFlag_c','deleteFlag_p','created_date','from_avtar_url','from_user_details','to_avtar_url','to_user_details','to_user_services','from_user_status','to_user_status')
#------------------------------------------------------# 

#------------  SiteSettings ----------------#
class SiteSettingsSerializer(serializers.ModelSerializer):
    class Meta:
        model = SiteSettings
        fields = ('id','setting_key','setting_value')
#------------------------------------------------------# 

#------------  wish_list ----------------#
class WishListSerializer(serializers.ModelSerializer):
    user_id = serializers.PrimaryKeyRelatedField(source='user')
    user = serializers.RelatedField(source='user')
    service_id = serializers.PrimaryKeyRelatedField(source='service')
    service = serializers.RelatedField(source='service')
    service_duration_id = serializers.PrimaryKeyRelatedField(source='service_duration')
    service_duration = serializers.RelatedField(source='service_duration')    
    class Meta:
        model = WishList
        fields = ('id','user_id','created_by','created_date','service_id','service_duration','service_duration_id','current_price','status_id','user','service')
#------------------------------------------------------# 


#------------  Newsletter Serializer ----------------#
class NewsletterSerializer(serializers.ModelSerializer):
    created_by = serializers.PrimaryKeyRelatedField(source='created_by')
    created_by_info = serializers.RelatedField(source='created_by')
    class Meta:
        model = Newsletter
        fields = ('id','created_by','created_by_info','subject','message','attachment','date_created','send_date','status_id')
#------------------------------------------------------# 

#------------  Newsletter Send Serializer ----------------#
class NewsletterSendSerializer(serializers.ModelSerializer):
    newsletter_id = serializers.PrimaryKeyRelatedField(source='newsletter')
    newsletter = serializers.RelatedField(source='newsletter')
    user_id = serializers.PrimaryKeyRelatedField(source='user')
    user = serializers.RelatedField(source='user')
    class Meta:
        model = NewsletterSend
        fields = ('id','newsletter_id','newsletter','user_id','user','added_date','sent_date','status')
#------------------------------------------------------# 

#------------  ServiceProviderReference Serializer ----------------#
class ServiceProviderReferenceSerializer(serializers.ModelSerializer):
    user_id = serializers.PrimaryKeyRelatedField(source='user')
    user_info = serializers.RelatedField(source='user')
    referred_by = serializers.PrimaryKeyRelatedField(source='referred_by')
    referred_by_info = serializers.RelatedField(source='referred_by')
    service_id = serializers.PrimaryKeyRelatedField(source='service')
    service = serializers.RelatedField(source='service')
    class Meta:
        model = ServiceProviderReference
        fields = ('id','user_id','referred_by','service_id','created_date','user_info','referred_by_info','service','status_id','view_status')
#------------------------------------------------------# S0erviceProviderReference


#------------  SMS History Serializer ----------------#
class SmsHistorySerializer(serializers.ModelSerializer):
    to_user_id = serializers.PrimaryKeyRelatedField(source='history_to_user')
    from_user_id = serializers.PrimaryKeyRelatedField(source='history_from_user')
    class Meta:
        model = SmsHistory
        fields = ('id','to_user_id','from_user_id','subject','message','sent_date','status')
        
class SmsSerializer(serializers.ModelSerializer):
    created_by = serializers.PrimaryKeyRelatedField(source='sms_created_by')
    class Meta:
        model = Sms
        fields = ('id','subject','message','created_date','created_by','updated_date','updated_by','status_id')        
#------------------------------------------------------# 

class TestimonialsSerializer(serializers.ModelSerializer):
    created_by = serializers.PrimaryKeyRelatedField(source='created_by')
    created_by_user = serializers.RelatedField(source='created_by')
    class Meta:
        model = Testimonials
        fields = ('id','text','created_by','created_on','status_id','created_by_user')
        
        
#-----------------------ManualBookings---------------------------------------
class ManualBookingsSerializer(serializers.ModelSerializer):
    class Meta:
        model = ManualBookings
        fields = ('booking_id','first_name','last_name')

#-----------------------User card details---------------------------------------
class UserCardDetailsSerializer(serializers.ModelSerializer):
    class Meta:
        model = UserCardDetails
        fields = ('id','user_id','creditCardDetails_token','customerDetails_id','use_for_renew','card_expiration_hash')
#-----------------------search banner Serializer---------------------------------------
class bannersSerializer(serializers.ModelSerializer):
    class Meta:
        model = banners
        fields = ("id","banner_url","title","page_location_id","status_id")
        
#-----------------------search page location for banner Serializer---------------------------------------
class bannerPageLocationSerializer(serializers.ModelSerializer):
    class Meta:
        model = bannerPageLocation
        fields = ("id","page_name")
        
#-----------------------search City Zip Serializer---------------------------------------
class searchCityZipSerializer(serializers.ModelSerializer):
    class Meta:
        model = searchCityZip
        fields = ("country_name","region_name","county_name","city_name","zip_code")
        read_only_fields = ("country_name","region_name","county_name","city_name","zip_code")

#---------------------Faq index Serializer added on 26-11-2014 by R-----------------------------#
class faqIndexSerializer(serializers.ModelSerializer):
    class Meta:
        model = faqIndex
        fields = ("id","index_name","order_by","status_id")
        read_only_fields = ("id","index_name","order_by")

#---------------------Faqs Serializer added on 26-11-2014 by R-----------------------------#
class faqsSerializer(serializers.ModelSerializer):
    index = serializers.RelatedField(source='index')
    user_type = serializers.RelatedField(source='user_type')

    class Meta:
        model = faqs
        fields = ("id","index","user_type","question","answer","status_id","order_by")
        read_only_fields = ("id","question","answer","status_id")

#---------------------Response Rate Serializer added on 27-11-2014 by R-----------------------------#
class ResponseRateSerializer(serializers.ModelSerializer):
    #index = serializers.RelatedField(source='index')

    class Meta:
        model = ResponseRate
        fields = ("id","booking_time","booking_status")
        read_only_fields = ("id","booking_time","booking_status")

#----------------------Publisher Banner Serializer added on 15-12-2014 by R-----------------------------------------#
class PublisherBannerSerializer(serializers.ModelSerializer):
    class Meta:
        model = PublisherBanner
        fields = ("id","booking","banner_title","banner_type","banner_content","target_url","status")
        read_only_fields = ("id","booking","banner_title","banner_type","banner_content","target_url","status")