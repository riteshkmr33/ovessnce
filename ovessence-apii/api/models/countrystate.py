from django.db import models

# Create your models here.
   
class Country(models.Model):
    class Meta:
        app_label = 'api'
        db_table = 'country'
        
    country_code = models.CharField(max_length=4)
    country_name = models.CharField(max_length=70)
    status_id = models.IntegerField(default=1)
    def __unicode__(self):
        return '{"id":%d,"country_name":"%s"}' % (self.id, self.country_name)    
    
class State(models.Model):
    class Meta:
        app_label = 'api'
        db_table = 'state'
    # foreighn key
    #cid = models.ForeignKey(Country, related_name='country') 
    state_code = models.CharField(max_length=4)
    country_id = models.IntegerField()
    state_name = models.CharField(max_length=70)
    status_id = models.IntegerField(default=1)
    def __unicode__(self):
        return '{"id":%d,"state_name":"%s"}' % (self.id, self.state_name)    
    
    
   
  
  
