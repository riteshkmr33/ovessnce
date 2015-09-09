import os
if os.environ['mod_wsgi.process_group'] != '':
    import signal, os
    os.kill(os.getpid(), signal.SIGINT)
