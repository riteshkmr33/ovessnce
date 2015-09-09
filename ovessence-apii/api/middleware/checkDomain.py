from django.http import HttpResponse
from django.http import HttpRequest
from rest_framework import status
class allowDomain(object):  
   def process_request(self, request):  
        allowHost=['localhost:8000','127.0.0.1','ovessenceapi.clavax.us:81','182.71.165.220','69.64.88.84']
        """try:
           host=request.META['REMOTE_ADDR']
        except:
           host=request.META['REMOTE_ADDR'] 
        if any(host in item for item in allowHost):
            return None
            #return HttpResponse(escape(repr(request)))
        else:
            return HttpResponse('You are not allowed domain',status=status.HTTP_400_BAD_REQUEST)"""
        