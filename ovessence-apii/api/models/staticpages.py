from django.db import models

# Create your models here.
   
class Page(models.Model):
    class Meta:
        app_label = 'api'
        db_table = 'page'
    page_id= models.IntegerField(primary_key=True,null=False,unique=True)     
    title = models.CharField(max_length=100)
    slug = models.CharField(max_length=100)
    content = models.TextField()    
    page_status = models.IntegerField(default=1,null=True, blank=True)
    created_date = models.DateTimeField(default='2013-01-01 00:00:00',null=False)
    created_by= models.IntegerField(max_length=11,default=0)
    updated_date=models.DateTimeField(auto_now_add=True)
    updated_by=models.IntegerField(max_length=11,default=0)
    
    
   
  
  
