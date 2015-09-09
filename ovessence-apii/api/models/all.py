from django.db import models

#----------------- News Letter Subscription -----------#
   
class NewsLetterSubscription(models.Model):
    class Meta:
        app_label = 'api'
        db_table = 'newsletter_subscription'
    email = models.CharField(max_length=60,unique=True)
    status_id = models.SmallIntegerField(default=0)
    
#-----------  End ---------------------#

#----------------- Rate & Feedback Models -----------#
class RateManager(models.Manager):
    
    def filterdata(self,sp='',sid=''):
        from django.db import connection
        cursor = connection.cursor()
        cursor.execute("""
            SELECT r.service_id,r.rating_type_id,r.rate,r.created_date,r.created_by,r.users_id,scat.category_name as category_name,u.first_name,u.last_name   FROM rating r inner join service_category scat on r.service_id=scat.id inner join users u on r.created_by=u.id""")
        result_list = []
        for row in cursor.fetchall():
            p = self.model(service_id=row[0], rating_type_id=row[1], rate=row[2], created_date=row[3], created_by=row[4],users_id=row[5])
            p.category_name = row[6]
            p.first_name = row[7]
            p.last_name = row[8]
            result_list.append(p)
        return result_list


class LookupRating(models.Model):
    class Meta:
        app_label = 'api'
        db_table = 'lookup_rating'
    rating_type = models.CharField(max_length=100)
    status_id = models.SmallIntegerField(default=1)
    def __unicode__(self):
        return self.rating_type   
    
class Rate(models.Model):
    class Meta:
        app_label = 'api'
        db_table = 'rating'
    rating_type = models.ForeignKey(LookupRating, related_name='lookuprating',null=False)  
    service = models.ForeignKey('Service', related_name='lookupService')
    created_by = models.ForeignKey('Users', db_column='created_by')
    users_id = models.IntegerField()
    #service_id = models.IntegerField() 
    #rating_type_id = models.IntegerField(default=0)
    rate = models.SmallIntegerField()
    created_date = models.DateTimeField(auto_now_add=True)
    #created_by = models.IntegerField()
    
class RateInsert(models.Model):
    class Meta:
        app_label = 'api'
        db_table = 'rating'
    users_id = models.IntegerField()
    service_id = models.IntegerField() 
    rating_type_id = models.IntegerField(default=0)
    rate = models.SmallIntegerField()
    created_date = models.DateTimeField(auto_now_add=True)
    created_by = models.IntegerField()
    
    
class Feedback(models.Model):
    class Meta:
        app_label = 'api'
        db_table = 'feedback'
        unique_together = ('users_id', 'service_id','created_by')
    users_id = models.IntegerField()
    service_id = models.IntegerField() 
    comments = models.CharField(max_length=1000)
    created_date = models.DateTimeField(auto_now_add=True)
    created_by = models.IntegerField()
    status_id = models.SmallIntegerField(default=5)
    view_status = models.SmallIntegerField(default=0) # added on 17-11-2014 by R
#-------------------- End -----------------------#

#----------------- Email Templates --------------#

class EmailTemplate(models.Model):
    class Meta:
        app_label = 'api'
        db_table = 'emailtemplates'
    subject = models.CharField(max_length=255)
    content = models.TextField() 
    status = models.BooleanField()
    fromEmail = models.CharField(max_length=255)
    created_date = models.DateTimeField(auto_now_add=True)
    modified_date = models.DateTimeField(auto_now=True)
    modified_by = models.IntegerField()

#------------------ End -------------------------#

#------------------ Service Booking by Consumer ---------------------#

class Invoice(models.Model):
    class Meta:
        app_label = 'api'
        db_table = 'invoice'
    
    user_id = models.IntegerField()
    sale_type = models.SmallIntegerField() 
    created_date = models.DateTimeField(auto_now_add=True)
    created_by = models.IntegerField()
    invoice_total = models.DecimalField(max_digits=11, decimal_places=2)
    site_commision = models.DecimalField(max_digits=11, decimal_places=2,default='0.00')
    status_id = models.SmallIntegerField(default=0)
    
class InvoiceDetails(models.Model):
    class Meta:
        app_label = 'api'
        db_table = 'invoice_details'
    invoice = models.ForeignKey(Invoice,null=True, blank=True,related_name='invoice_details', db_column='invoice_id')
    #invoice_id = models.IntegerField(default=0,null=True, blank=True)
    sale_item_details = models.CharField(max_length=100) 
    amount = models.DecimalField(max_digits=11, decimal_places=2)
    def __unicode__(self):
        return '{"id":"%d","sale_item_details":"%s","amount":"%s"}' % (self.id,self.sale_item_details,self.amount)

#user card details mode to manage user card details
class UserCardDetails(models.Model):
    class Meta:
        app_label = 'api'
        db_table = 'users_card_details'
    
    user_id = models.IntegerField()
    creditCardDetails_token = models.CharField(max_length=150) 
    customerDetails_id = models.CharField(max_length=150)
    use_for_renew = models.SmallIntegerField(default=0)
    card_expiration_hash = models.CharField(max_length=100)
    def __unicode__(self):
        return '{"id":"%d","user_id":"%d","creditCardDetails_token":"%s","customerDetails_id":"%s""use_for_renew":"%d","card_expiration_hash":"%s"}' % (self.id,self.user_id,self.creditCardDetails_token,self.customerDetails_id,self.use_for_renew, self.card_expiration_hash)

    
class InvoiceDetailsWithoutRelation(models.Model):
    class Meta:
        app_label = 'api'
        db_table = 'invoice_details'
    invoice_id = models.IntegerField(default=0,null=True, blank=True)
    advertisement_plan_id = models.IntegerField(null=True, blank=True)
    service_provider_service_id = models.IntegerField(null=True, blank=True)
    subscription_duration_id = models.IntegerField(null=True, blank=True)
    sale_item_details = models.CharField(max_length=100) 
    amount = models.DecimalField(max_digits=11, decimal_places=2)
    def __unicode__(self):
        return '{"id":"%d","sale_item_details":"%s","amount":"%s"}' % (self.id,self.sale_item_details,self.amount)
    
class PaymentHistory(models.Model):
    class Meta:
        app_label = 'api'
        db_table = 'payment_history'
    invoice = models.OneToOneField(Invoice,null=True, blank=True,related_name='payment_history',db_column='invoice_id')
    payment_method_id = models.IntegerField()
    payment_instrument_no = models.CharField(max_length=25) 
    payment_date = models.DateTimeField()
    amount_paid = models.DecimalField(max_digits=11, decimal_places=2)
    currency = models.CharField(max_length=10,default='CAD')
    transaction_charge = models.DecimalField(max_digits=11, decimal_places=2,default='0.00')
    response_code = models.CharField(max_length=10,null=True, blank=True)
    auth_code = models.CharField(max_length=50,null=True, blank=True)
    transaction_id =  models.CharField(max_length=70,null=True, blank=True)
    avs_response = models.CharField(max_length=10,null=True, blank=True)
    response_message = models.CharField(max_length=255,null=True, blank=True)
    status_id = models.SmallIntegerField(default=0)
    def __unicode__(self):
        return '{"id":"%d","payment_method_id":"%s","payment_instrument_no":"%s","payment_date":"%s","amount_paid":"%s","currency":"%s","transaction_charge":"%s","response_code":"%s","auth_code":"%s","transaction_id":"%s","avs_response":"%s","response_message":"%s","status_id":"%s"}' % (self.id,self.payment_method_id,self.payment_instrument_no,self.payment_date,self.amount_paid,self.currency,self.transaction_charge,self.response_code,self.auth_code,self.transaction_id,self.avs_response,self.response_message,self.status_id)

class PaymentHistoryWithoutRelation(models.Model):
    class Meta:
        app_label = 'api'
        db_table = 'payment_history'
    invoice_id = models.IntegerField(default=0,null=True, blank=True)
    payment_method_id = models.IntegerField()
    payment_instrument_no = models.CharField(max_length=25) 
    payment_date = models.DateTimeField()
    amount_paid = models.DecimalField(max_digits=11, decimal_places=2)
    currency = models.CharField(max_length=10,default='CAD')
    transaction_charge = models.DecimalField(max_digits=11, decimal_places=2,default='0.00')
    response_code = models.CharField(max_length=10,null=True, blank=True)
    auth_code = models.CharField(max_length=50,null=True, blank=True)
    transaction_id =  models.CharField(max_length=70,null=True, blank=True)
    avs_response = models.CharField(max_length=10,null=True, blank=True)
    response_message = models.CharField(max_length=255,null=True, blank=True)
    status_id = models.SmallIntegerField(default=0)
    def __unicode__(self):
        return '{"id":"%d","payment_method_id":"%s","payment_instrument_no":"%s","payment_date":"%s","amount_paid":"%s","currency":"%s","transaction_charge":"%s","response_code":"%s","auth_code":"%s","transaction_id":"%s","avs_response":"%s","response_message":"%s","status_id":"%s"}' % (self.id,self.payment_method_id,self.payment_instrument_no,self.payment_date,self.amount_paid,self.currency,self.transaction_charge,self.response_code,self.auth_code,self.transaction_id,self.avs_response,self.response_message,self.status_id)
    
class PaymentRefundHistory(models.Model):
    class Meta:
        app_label = 'api'
        db_table = 'payment_refund_history'
    payment_history = models.ForeignKey('PaymentHistory',related_name='payment_refund_history')
    payment_method_id = models.IntegerField()
    payment_instrument_no = models.CharField(max_length=25) 
    payment_date = models.DateTimeField()
    #amount_paid = models.DecimalField(max_digits=11, decimal_places=2)
    currency = models.CharField(max_length=10,default='CAD')
    #transaction_charge = models.DecimalField(max_digits=11, decimal_places=2,default='0.00')
    response_code = models.CharField(max_length=10,null=True, blank=True)
    auth_code = models.CharField(max_length=50,null=True, blank=True)
    transaction_id =  models.CharField(max_length=70,null=True, blank=True)
    avs_response = models.CharField(max_length=10,null=True, blank=True)
    response_message = models.CharField(max_length=255,null=True, blank=True)
    status_id = models.SmallIntegerField(default=0)
    amount_refunded = models.DecimalField(max_digits=11, decimal_places=2,default='0.00')
    def __unicode__(self):
        return '{"id":"%d","payment_history_id":"%s","payment_method_id":"%s","payment_instrument_no":"%s","payment_date":"%s","currency":"%s","response_code":"%s","auth_code":"%s","transaction_id":"%s","avs_response":"%s","response_message":"%s","status_id":"%s","amount_refunded":%s}' % (self.id,self.payment_history.id,self.payment_method_id,self.payment_instrument_no,self.payment_date,self.currency,self.response_code,self.auth_code,self.transaction_id,self.avs_response,self.response_message,self.status_id,self.amount_refunded)
        
    
class Booking(models.Model):
    class Meta:
        app_label = 'api'
        db_table = 'booking'
        
    user_id = models.IntegerField(null=True, blank=True)  
    service_provider = models.ForeignKey('SpUsers', related_name='lookupSp')
    service_provider_service = models.ForeignKey('SpUserService')
    
    invoice_id = models.IntegerField(null=True, blank=True)
    
    service_address_id = models.IntegerField(null=True, blank=True)
    
    created_date = models.DateTimeField(auto_now_add=True)
    modified_date = models.DateTimeField(auto_now=True)
    modified_by = models.IntegerField(default=0)
    #lookipInvoice = models.ForeignKey('Invoice',null=True, blank=True,related_name='lookipInvoice',db_column='lookipInvoice')
    parent_booking_id = models.IntegerField(default=0)
    booked_date =  models.DateTimeField()
    status_id = models.SmallIntegerField(default=0)
    payment_status = models.IntegerField(default=0)
    def __unicode__(self):
        return '{"id":"%d","user_id":"%s","service_provider_id":"%s","service_provider_service_id":"%s","created_date":"%s","modified_date":"%s","modified_by":"%s","parent_booking_id":"%s","booked_date":"%s","status_id":"%s","payment_status":"%s","invoice_id":"%s","service_address_id":"%d"}' % (self.id,self.user_id,self.service_provider_id,self.service_provider_service_id,self.created_date,self.modified_date,self.modified_by,self.parent_booking_id,self.booked_date,self.status_id,self.payment_status,self.invoice_id,self.service_address_id)
    
class UserSubscriptions(models.Model):
    class Meta:
        app_label = 'api'
        db_table = 'user_subscriptions'
        #unique_together = ('user','subscription_duration')
    user = models.ForeignKey('Users')  
    subscription_duration = models.ForeignKey('SubscriptionDuration')
    invoice_id = models.IntegerField(default=0,null=True, blank=True)
    subscription_start_date = models.DateField()
    subscription_end_date = models.DateField(null=True, blank=True)
    auto_renewal= models.BooleanField(default=0)
    status_id = models.SmallIntegerField()
    user_card_id = models.CharField(max_length=100,null=True, blank=True)

#------------------ End -------------------------#

#------------------ Banner Booking by Consumer ---------------------#
class PublisherBanner(models.Model):
    class Meta:
        app_label = 'api'
        db_table = 'publisher_banner'
    booking = models.ForeignKey('BannerBooking')
    banner_title = models.CharField(max_length=100)
    banner_type = models.SmallIntegerField(default=0)
    banner_content = models.CharField(max_length=2500)
    target_url = models.CharField(max_length=100)
    status = models.ForeignKey('Status')

class BannerBooking(models.Model):
    class Meta:
        app_label = 'api'
        db_table = 'banner_booking'
    user = models.ForeignKey('Users')
    advertisement_plan = models.ForeignKey('AdvertisementPlan')
    start_date = models.DateTimeField(null=True, blank=True)
    end_date = models.DateTimeField(null=True, blank=True)
    booking_date = models.DateTimeField(null=True, blank=True)
    invoice_id = models.IntegerField(null=True, blank=True)
    status_id = models.SmallIntegerField(default=0)

class AdvertisementPlan(models.Model):
    class Meta:
        app_label = 'api'
        db_table = 'advertisement_plan'
    plan_name = models.CharField(max_length=100)
    advertisement = models.ForeignKey('Advertisement')
    advertisement_page = models.ForeignKey('AdvertisementPage')
    duration = models.IntegerField(null=True, blank=True)
    duration_in = models.SmallIntegerField(null=True, blank=True)
    price = models.IntegerField(null=True, blank=True)

class Advertisement(models.Model):
    class Meta:
        app_label = 'api'
        db_table = 'advertisement'
    banner_name = models.CharField(max_length=100)
    banner_height = models.IntegerField(null=True, blank=True)
    banenr_width = models.IntegerField(null=True, blank=True)
    status = models.ForeignKey('Status')

class AdvertisementPage(models.Model):
    class Meta:
        app_label = 'api'
        db_table = 'advertisement_page'
    page_name = models.CharField(max_length=100)
    status = models.ForeignKey('Status')

class Status(models.Model):
    class Meta:
        app_label = 'api'
        db_table = 'lookup_status'
    status = models.CharField(max_length=100)
    

#------------------- End --------------------------------------------#

#------------------ Subscription ---------------------#
    
class SiteFeature(models.Model):
    class Meta:
        app_label = 'api'
        db_table = 'site_feature'
   
    feature_name = models.CharField(max_length=100)
    description = models.CharField(max_length=255)
    status_id = models.SmallIntegerField()
    def __unicode__(self):
        return "[{'id':%d,'feature_name':%s,'description':%s,'status_id':%s}]" %(self.id,self.feature_name,self.description,self.status_id)    

class Subscription(models.Model):
    class Meta:
        app_label = 'api'
        db_table = 'subscription'
   
    subscription_name = models.CharField(max_length=50)
    status_id = models.SmallIntegerField() 
    
class SubscriptionDuration(models.Model):
    class Meta:
        app_label = 'api'
        db_table = 'subscription_duration'
   
    subscription = models.ForeignKey('Subscription' , related_name='subscriptionduration')  
    duration = models.SmallIntegerField()   
    duration_in = models.SmallIntegerField()
    price=models.DecimalField(max_digits=11, decimal_places=2) 
    currency = models.CharField(max_length=10,default='CAD')
    status_id = models.SmallIntegerField()
    def __unicode__(self):
        return '{"id":%d,"duration":%d,"duration_in":%d,"price":"%s","currency":"%s","subscription_name":"%s","subscription_id":"%d"}' %(self.id,self.duration,self.duration_in,self.price,self.currency,self.subscription.subscription_name,self.subscription.id)    
    
    
class SubscriptionFeature(models.Model):
    class Meta:
        app_label = 'api'
        db_table = 'subscription_feature'
        unique_together = ('subscription','site_feature')
        
    subscription = models.ForeignKey('Subscription', related_name='subscription')
    site_feature = models.ForeignKey('SiteFeature', related_name='feature') 
    def __unicode__(self):
        return '{"id":%d,"feature_name":"%s","description":"%s","status_id":%s}' %(self.site_feature.id,self.site_feature.feature_name,self.site_feature.description,self.site_feature.status_id)    

class FeatureVideoLimit(models.Model):
    class Meta:
        app_label = 'api'
        db_table = 'feature_video_limit'   
    
    subscription_plan = models.ForeignKey('Subscription', related_name='subscription_video_limit')
    site_feature = models.ForeignKey('SiteFeature', related_name='subscription_video_limit')
    limit        = models.SmallIntegerField()
    created_date = models.DateTimeField(auto_now_add=True)
    
    def __unicode__(self):
        return '{"id":%d,"subscription_plan_id":%d,"subscription_plan_name":"%s","site_feature_id":%d,"site_feature_name":"%s","limit":%d,"created_date":"%s"}' %(self.id,self.subscription_plan.id,self.subscription_plan.subscription_name,self.site_feature.id,self.site_feature.feature_name,self.limit,self.created_date) 
#------------------ End -------------------------#

#------------  Practitioner Organization ----------------#
class PractitionerOrganization(models.Model):
    class Meta:
        app_label = 'api'
        db_table = 'practitioner_organization_list'
        
    organization_id = models.IntegerField(primary_key=True)
    organization_name = models.CharField(max_length=150)
    logo = models.CharField(max_length=150)
    phone_no = models.CharField(max_length=15)
    email = models.CharField(max_length=100)
    status_id = models.SmallIntegerField()
    def __unicode__(self):
        return '{"organization_id":%d,"organization_name":"%s","logo":"%s","phone_no":"%s","email":"%s","status_id":%s}' %(self.organization_id,self.organization_name,self.logo, self.phone_no, self.email, self.status_id)        
class PractitionerOrganizationLookup(models.Model):
    class Meta:
        app_label = 'api'
        db_table = 'practitioner_organization'
        
    practitioner = models.ForeignKey('SpUsers',related_name='practitioner')
    organization = models.ForeignKey('PractitionerOrganization',related_name='organization')
#-------------- End ------------------------------------------#

#------------  Partners ----------------#
class Partners(models.Model):
    class Meta:
        app_label = 'api'
        db_table = 'partners'
    title = models.CharField(max_length=50)
    desc = models.TextField(max_length=150)
    url = models.CharField(max_length=100)
    logo = models.CharField(max_length=100)
    status_id = models.SmallIntegerField(default=1)
#-------------- End ------------------------------------------#

#------------  Media ----------------#
class Media(models.Model):
    class Meta:
        app_label = 'api'
        db_table = 'media'
    user = models.ForeignKey('Users')    
    media_url = models.CharField(max_length=255)
    media_title = models.TextField(max_length=100)
    media_description = models.CharField(max_length=500)
    media_type = models.SmallIntegerField(default=0)
    created_date = models.DateTimeField(auto_now_add=True)
    created_by = models.IntegerField()
    updated_date = models.DateTimeField()
    updated_by =models.IntegerField()
    status_id = models.SmallIntegerField(default=5)

#-------------- End ------------------------------------------#

#------------  Messages ----------------#
class Messages(models.Model):
    class Meta:
        app_label = 'api'
        db_table = 'messages'
    from_user = models.ForeignKey('Users',related_name='from_user')    
    from_name = models.CharField(max_length=255,null=True, blank=True,default='')
    to_user   = models.ForeignKey('SpUsers',related_name='to_user')
    subject = models.TextField()
    message = models.TextField()
    replyId = models.IntegerField(default=0)
    topLevel_id = models.IntegerField(default=0)
    readFlag    = models.SmallIntegerField(default=0)
    deleteFlag    = models.SmallIntegerField(default=0)
    created_date = models.DateTimeField(auto_now_add=True)
    deleteFlag_c    = models.SmallIntegerField(default=0)
    deleteFlag_p    = models.SmallIntegerField(default=0)
    readFlag_c    = models.SmallIntegerField(default=0)
    readFlag_p    = models.SmallIntegerField(default=0)
#-------------- End ------------------------------------------#

#------------  Site Settings ----------------#
class SiteSettings(models.Model):
    class Meta:
        app_label = 'api'
        db_table = 'site_settings'
    setting_key = models.CharField(max_length=100)
    setting_value = models.CharField(max_length=100)
#-------------- End ------------------------------------------# 

#------------  wish_list ----------------#
class WishList(models.Model):
    class Meta:
        app_label = 'api'
        db_table = 'wish_list'
        unique_together = ('user','created_by','service','service_duration','current_price')
    user = models.ForeignKey('SpUsers',related_name='sp_user_id')
    created_by = models.IntegerField()
    created_date = models.DateTimeField(auto_now_add=True)
    service = models.ForeignKey('Service')
    service_duration = models.ForeignKey('SpUserService',related_name='service_duration')
    current_price    = models.DecimalField(max_digits=15, decimal_places=2)
    status_id = models.SmallIntegerField(default=0)
#-------------- End ------------------------------------------#

#------------  newsletter ----------------#
class Newsletter(models.Model):
    class Meta:
        app_label = 'api'
        db_table = 'newsletter'
    created_by = models.ForeignKey('Users', db_column='created_by')
    subject = models.CharField(max_length=255)
    message   = models.CharField(max_length=2500)
    attachment = models.CharField(max_length=100,null=True, blank=True)
    date_created = models.DateField(auto_now_add=True,null=True, blank=True)
    send_date =  models.DateField(null=True, blank=True)
    status_id = models.SmallIntegerField(default=0)
    def __unicode__(self):
        return '{"id":"%d","created_by":"%s","subject":"%s","message":"%s","attachment":"%s","date_created":"%s","send_date":"%s","status_id":"%s"}' %(self.id,self.created_by.id,self.subject,self.message,self.attachment,self.date_created,self.send_date,self.status_id)
#-------------- End ------------------------------------------#

#------------  newsletter_send ----------------#
class NewsletterSend(models.Model):
    class Meta:
        app_label = 'api'
        db_table = 'newsletter_send'
    newsletter =  models.ForeignKey('Newsletter',related_name='newsletter')   
    user = models.ForeignKey('Users',related_name='user')
    status = models.SmallIntegerField(default=0)
    added_date = models.DateTimeField(auto_now_add=True)
    sent_date = models.DateTimeField(null=True, blank=True)
    def __unicode__(self):
        return '{"id":%d,"newsletter_id":"%d","newsletter":"%s","user_id":"%d","user":"%s","added_date":%s,"sent_date":%s,"status":%d}' %(self.id,self.newsletter_id,self.newsletter,self.user_id,self.user, self.added_date,self.send_date,self.status)
#-------------- End ------------------------------------------#

#------------  service_provider_reference ----------------#
class ServiceProviderReference(models.Model):
    class Meta:
        app_label = 'api'
        db_table = 'service_provider_reference'
    user =  models.ForeignKey('Users')   
    referred_by = models.ForeignKey('Users',related_name='referred_by',db_column='referred_by')
    service = models.ForeignKey('Service')
    created_date = models.DateTimeField(auto_now_add=True)
    status_id = models.SmallIntegerField(default=0)
    view_status = models.SmallIntegerField(default=0) # added on 17-11-2014 by R
#-------------- End ------------------------------------------#

#------------  booking_suggestion_history  ----------------#
class BookingSuggestionHistory(models.Model):
    class Meta:
        app_label = 'api'
        db_table = 'booking_suggestion_history'
    user =  models.ForeignKey('Users',null=True, blank=True)   
    booking_id =  models.IntegerField(null=True, blank=True)
    booking_time = models.DateTimeField(null=True, blank=True)
    booking_status = models.SmallIntegerField(null=True, blank=True)
#-------------- End ------------------------------------------#

#------------  SMS History ----------------#
class SmsHistory(models.Model):
    class Meta:
        app_label = 'api'
        db_table = 'sms_history'
    history_to_user =  models.ForeignKey('Users',null=True, blank=True,db_column='to_user_id',related_name='history_to_user') 
    history_from_user =  models.ForeignKey('Users',null=True, blank=True,db_column='from_user_id',related_name='history_from_user') 
    subject =  models.CharField(max_length=100)
    message = models.CharField(max_length=170)
    sent_date = models.DateTimeField(auto_now_add=True)
    status  = models.SmallIntegerField(null=True, blank=True)
    
class Sms(models.Model):
    class Meta:
        app_label = 'api'
        db_table = 'sms'
    subject =  models.CharField(max_length=100)
    message = models.CharField(max_length=170)
    created_date = models.DateTimeField(auto_now_add=True)
    sms_created_by   = models.ForeignKey('Users',null=True, blank=True,db_column='created_by',related_name='sms_created_by')
    updated_date     = models.DateTimeField(null=True, blank=True)
    updated_by       = models.IntegerField(null=True, blank=True)
    status_id  = models.SmallIntegerField(default=1)    
#-------------- End ------------------------------------------#

#  --------------- Testimonila -------------------------------#
class Testimonials(models.Model):
    class Meta:
        app_label = 'api'
        db_table = 'testimonials'
        ordering = ('-id',)
    text =  models.TextField()
    created_by = models.ForeignKey('Users',null=True, blank=True,db_column='created_by',related_name='testimonials_created_by')
    created_on = models.DateTimeField(auto_now_add=True)
    status_id  = models.SmallIntegerField(default=1) 

#------------------ End --------------------------------------#

#  --------------- Manual Bookings -------------------------------#
class ManualBookings(models.Model):
    class Meta:
        app_label = 'api'
        db_table = 'mannual_booking_details'        
    
    booking_id = models.IntegerField(null=True, blank=True)
    first_name = models.CharField(max_length=50,null=True, blank=True)
    last_name = models.CharField(max_length=50,null=True, blank=True)
     

#------------------ End --------------------------------------#
#  --------------- Manual Bookings -------------------------------#
class searchCityZip(models.Model):
    class Meta:
        app_label = 'api'
        db_table = 'city_zip_code' 
        
    country_name = models.CharField(max_length=50,null=True, blank=True)
    region_name = models.CharField(max_length=50,null=True, blank=True)
    county_name = models.CharField(max_length=50,null=True, blank=True)
    city_name = models.CharField(max_length=50,null=True, blank=True)
    zip_code = models.CharField(max_length=10,null=True, blank=True)
    def __unicode__(self):
        return '{"country_name":"%s","region_name":"%s","county_name":"%s","city_name":"%s","zip_code":"%s",}' %(self.county_code, self.country_name, self.region_name,self.county_name,self.city_name,self.zip_code)
     

#------------------ End --------------------------------------#
#  --------------- Manual Bookings -------------------------------#
class banners(models.Model):
    class Meta:
        app_label = 'api'
        db_table = 'banners' 
        
    banner_url = models.CharField(max_length=150)
    title = models.CharField(max_length=100)
    page_location_id   = models.IntegerField()
    status_id = models.SmallIntegerField(default=0)
    def __unicode__(self):
        return '{"id":"%d","banner_url":"%s","title":"%s","page_location_id":"%s","status_id":"%s"}' %(self.id,self.banner_url,self.title,self.page_location_id,self.status_id)
     

#------------------ End --------------------------------------#
#  --------------- Manual Bookings -------------------------------#
class bannerPageLocation(models.Model):
    class Meta:
        app_label = 'api'
        db_table = 'banners_page_location' 
        
    page_name = models.CharField(max_length=50)
    
    def __unicode__(self):
        return '{"id":"%d","page_name":"%s"}' %(self.id,self.page_name)
     

#------------------ End --------------------------------------#

#-----------------Faq index added on 26-11-2014 by R start----------#
class faqIndex(models.Model):
    class Meta:
        app_label = 'api'
        db_table = 'faq_index'
    
    index_name = models.CharField(max_length=100)
    order_by = models.IntegerField(default=0)
    status_id = models.IntegerField(default=1)

    def __unicode__(self):
        return '{"id":"%d","index_name":"%s","order_by":"%d","status_id":"%d"}' %(self.id,self.index_name,self.order_by,self.status_id)
#-----------------Faq index added on 26-11-2014 ny R end----------#

#-----------------Faqs added on 26-11-2014 by R start----------#
class faqs(models.Model):
    class Meta:
        app_label = 'api'
        db_table = 'faqs'
    
    index = models.ForeignKey('faqIndex', related_name='faq_index')
    user_type = models.ForeignKey('UserType', related_name='userType')
    question = models.CharField(max_length=2500)
    answer = models.CharField(max_length=2500)
    status_id = models.IntegerField(default=1)
    order_by = models.IntegerField(default=0)
#-----------------Faqs added on 26-11-2014 by R end----------#

#-----------------Response rate added on 27-11-2014 by R start---------------------#
class ResponseRate(models.Model):
    class Meta:
        app_label = 'api'
        db_table = 'booking_suggestion_history'
    user =  models.ForeignKey('Users',null=True, blank=True)   
    booking =  models.ForeignKey('Booking', related_name='booking')
    booking_time = models.DateTimeField(null=True, blank=True)
    booking_status = models.SmallIntegerField(null=True, blank=True)
#-----------------Response added on 27-11-2014 by R end---------------------#
