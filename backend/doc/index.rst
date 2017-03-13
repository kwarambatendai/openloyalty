OpenLoyalty Documentation
=========================
Open Loyalty is technology for loyalty solutions.
It's a loyalty platform in open source, with ready-to-use gamification and loyalty features, easy to setup and customize, ready to work on-line and off-line.

Getting started
===============

Requirements
------------
This project has full support for running in `Docker <https://www.docker.com/>`_.

Best way to run it is to execute the following command

  docker-compose up
(docker must be installed on your machine).

There is also an option to run this without docker, but in such case following a set of the tool will be needed:

* php7
* MySql or PosstgreSql
* Elasticsearch 2.2

Installation
------------
Simply clone this repository, run

  composer install

and fill up all required parameters.
Then use Phing to setup database, elastcsearch and load some demo data


  phing setup

(if you are using docker, remember to run those `command inside container <./run_command_inside_docker.rst>`_)

Architecture
============
This project is based on CQRS, DDD and event sourcing, whole code is split into components and bundles. More info about each component and bundle can be found in `Architecture <./architecture/index.rst>`_.

Customization
=============
There is some possibility to customize whole app for personal needs.
Complete guide can be found in `customization <./customization.rst>`_ section of this documentation.


Cron tasks
==========
There are two tasks that should be run periodically.

1. Segmenting customers

    bin/console oloy:segment:recreate

2. Expiring points

    ol:points:transfers:expire

