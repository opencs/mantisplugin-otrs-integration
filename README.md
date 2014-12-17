# mantisplugin-otrs-integration


## Introduction

The OpenCS's MantisBT OTRS Integration Plugin is a MantisBT (https://www.mantisbt.org) plugin designed to automatically update OTRS instances about MantisBT bug/issue activities.

Every time a bug associated with a given OTRS ticket is created or has its state changed, this plugin notifies the OTRS instance using the Webservice interface provided by OTRS.

## How it works

It relies on the existence of a custom field that holds the OTRS ticket. If this field is found and it is not empty, the activities in this ticket will be monitored and OTRS will be notified accordingly. 

## Requirements

  - MantisBT 1.2 or greater (not tested on 1.3 series);
  - Python 2.7 or later with SUDS (future releases should remove this dependency);

## Installation

### OTRS Configuration

   - Create a Webservice interface that exposes the method TicketUpdate;
   - Get the WSDL for this Webservice or use the existing WSDL file;
   - Create a new user for mantis on OTRS. This user must be able to add tickets on all queues;

### MantisBT Configuration

   - Create a custom field to hold the OTRS ticket. It must be numeric and may have any name (defaults to 'OTRS Ticket');
   - Adds this field to to the desired projects;

### Plugin installation

   - Download the source code from GitHub;
   - Copy the directory OTRSIntegration to the MantisBT plugin directory;
   - Goto to the MantisBT plugin management and active the plugin;

## Configuration

In order to make the OTRS connection work:

  - Go to the &lt;plugin directory&gt;/otrsconnect;
  - Copy the file otrsconnect.config.template to otrsconnect.config;
  - Edit the file otrsconnect.config and set the username and password created for mantisbt
  - Make sure that this directory is not accessible from the outside world (by default, this directory contains an .htaccess that should do this for you);

#### Using the existing WSDL file

If you want to use the existing WSDL file, set the namespace to "http://www.otrs.org/TicketConnector/" during the creation of the service. Edit the file otrsconnect.wsdl and change the soap:address location to the actual OTRS Webservice URL.

## License

This software is licensed under the terms of GNU GPL 2.0.

