from django.test import TestCase
from rest_framework.test import APIRequestFactory
from rest_framework.decorators import api_view
from rest_framework.response import Response
# Create your tests here.
@api_view(['GET','POST'])
def test_list(request):
  factory = APIRequestFactory()
  response = factory.get('/api/users/', format='json')
  return Response(response)
