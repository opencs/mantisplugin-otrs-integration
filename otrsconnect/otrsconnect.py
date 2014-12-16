# -*- coding: utf-8 -*-
import suds.client
import urllib
import os
import json
import sys

class OTRSConnect:
    def __init__(self):
        """
        Creates a new instance 
        """
        self._loadConfig()
        self.client = self._createClient()
    
    def _loadConfig(self):
        f = os.path.join(os.path.dirname(os.path.realpath(__file__)), 'otrsconnect.config')
        try:
            with open(f, "r") as inp:
                cfg = json.load(inp, encoding='utf-8')
        except Exception as e:
            raise Exception('Invalid configuration.', e)
        self._user = cfg['user']
        if self._user == None:
            raise Exception('Invalid configuration. Missing user.')
        self._password = cfg['password']
        if self._password == None:
            raise Exception('Invalid configuration. Missing password.')
        self._wsdl = cfg['wsdl']
        if self._wsdl == None:
            self._wsdl = self._getDefaultWSDLURL()
        
    def _getDefaultWSDLURL(self): 
        """
        @return: The URI of the file.
        @rtype: string
        """
        return 'file://' + urllib.pathname2url(os.path.join(os.path.dirname(os.path.realpath(__file__)), 'otrsconnect.wsdl'))
        
    def _createClient(self):
        """
        Creates the webservice client based on the WSDL.
        @return: The client.
        @rtype: suds.client.Client        
        """
        return suds.client.Client(self._wsdl)
 
    def addNote(self, ticketNumber, subject, body):
        req = self.client.factory.create('OTRS_TicketUpdate')
        req.UserLogin = self._user
        req.Password = self._password
        req.TicketNumber = ticketNumber
        req.Article.ArticleType = 'note-internal'
        req.Article.SenderType = 'agent'
        req.Article.From = self._user
        req.Article.Subject = subject.decode('utf-8')
        req.Article.Body = body.decode('utf-8')
        req.Article.Charset = 'UTF8'
        req.Article.MimeType = 'text/plain'
        
        ret = self.client.service.TicketUpdate(req)
        if ret.TicketNumber != ticketNumber:
            raise Exception('Unable to post the message.')

if len(sys.argv) != 4:
    print('Usage: {0} <otrs ticket number> <subject> <body>'.format(sys.argv[0]))
    sys.exit(1)
else:
    connect = OTRSConnect()
    connect.addNote(sys.argv[1], sys.argv[2], sys.argv[3])
         
