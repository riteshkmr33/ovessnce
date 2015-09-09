"""
WSGI config for healthservices project.

It exposes the WSGI callable as a module-level variable named ``application``.

For more information on this file, see
https://docs.djangoproject.com/en/1.6/howto/deployment/wsgi/
"""

import os
import sys

path = '/var/www/websites/ovessence/public_html/healthservices/healthservices'
if path not in sys.path:
    sys.path.append(path)

#os.environ.setdefault("DJANGO_SETTINGS_MODULE", "healthservices.settings")
os.environ['DJANGO_SETTINGS_MODULE'] = 'healthservices.settings'
#from django.core.wsgi import get_wsgi_application
import django.core.handlers.wsgi
application = django.core.handlers.wsgi.WSGIHandler()
#application = get_wsgi_application()
#import healthservices.monitor
#healthservices.monitor.start(interval=1.0)
