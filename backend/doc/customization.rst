Campaign photos storage
=======================

Currently all campaign photos are stored in app/uploads directory.

It can be easily changed to other directory or even to cloud storage.

In order to do that just edit config.yml and add new adapter under knp_gaufrette section.

Next change (in parameters.yml) `campaign_photos_adapter` to your adapter.

Complete reference on how to define adapter can be found in `KnpGaufretterBundle documentation <https://github.com/KnpLabs/KnpGaufretteBundle>`_

Listening for system events
===========================

In many places of this system some system events are dispatched, e.g. `oloy.account.available_points_amount_changed` is dispatched when available points
amount is changed.

It is possible to write listener for that events and perform some custom actions.

Defining listener:

.. code:: php

    oloy.my_custom_listener
        class: 'OpenLoyalty\Bundle\MyBundle\EventListener\MyCustomListener
        tags:
          - { name: broadway.event_listener, event: `oloy.account.available_points_amount_changed`, method: onPointsChanged}