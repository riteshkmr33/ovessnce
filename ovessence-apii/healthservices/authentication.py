from api.models import Users
from rest_framework import authentication
from rest_framework import exceptions
import hashlib
# Thi class is used for  authentication  to access api and user must be api user
class APIAuthentication(authentication.BaseAuthentication):
    
    def authenticate(self, request):
        # -------------------------#
        username = request.META.get('HTTP_USERNAME')
        password = request.META.get('HTTP_PASSWORD')

        try:
         password = hashlib.md5(password).hexdigest()
        except:
         raise exceptions.AuthenticationFailed('Please provide credential to access api')

        try:
            user = Users.objects.get(user_name=username,user_type_id=7,Pass=password)
        except Users.DoesNotExist:
            raise exceptions.AuthenticationFailed('Please provide credential to access api')

        return (user, None)