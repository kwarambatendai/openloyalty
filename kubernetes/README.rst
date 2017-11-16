======================================================================
Kubernetes environment
======================================================================

***************
Overview
***************
Kubernetes run all necessary images as a docker containers to provide an application.
All images are standalone and provide a services like web server, database, elasticsearch and few more.

***************
Services:
***************
The list of all services running in cloud under the kubernetes

1. Database
===============
Uses default postgres v.9 database image. It's available under port 5432.

2. Web
===============
Uses prebuilt image containing nginx as a proxy server which handles requests to API from clients. It also contains compiled angular application. Admin panel is available on port 3001, client is on port 3002, pos is on 3003, and backend API listens on port 8081. Uses PHP-FPM service.

3. PHP-FPM
===============
Provides an interpreter for PHP scripts. It bases on prebuild image with all necessary backend's api code. This service is available on port 9000. Uses database, mail, elasticsearch services.

3. Elasticsearch
=================
Provides a data provider for read model in application. Available on port 9200.

********************
Used docker images:
********************
These images above are just official docker images extended with necessary features that application needs.

openloyalty/web:latest
=======================
Nginx serving application on four different ports: 3001-3003 frontend clients, and 8081 backend API.

    ``3001`` - on this port nginx provides admin cockpit

    ``3002`` - client cockpit

    ``3003`` - pos cockpit

    ``8081`` - backend API


openloyalty/fpm:latest
=======================
Official PHP-FPM image with all necessary extension and modules enabled to run php scripts, has a cron service to run some tasks periodically. This container contains also backend API code.

    ``9000`` - PHP-fpm service


********************
Configuration files:
********************
storage.yml
=======================
Description of used storages in kubernetes environment.

claims.yml
=======================
This file contains rules how kubernetes should deal with storage volumes.

config.yml
=======================
There is some config in this file. In this case it's only configuration file ``config.js`` which tells to frontend client how run an application and which endpoint should be used to communicatre with backend API.

deployment.yml
=======================
Core configuration of whole environment. Provides all necessary information for kubernetes to run all containers as a services, on which IP's and ports, and how to communicate each other. This configuration needs all configuration above to be applied before you apply this file.
