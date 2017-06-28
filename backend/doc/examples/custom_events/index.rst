Using Custom Event
------------------

Defining custom event earning points rule
=========================================

Go to administration panel and add new earning points rule.
Select "Custom event rule" as type and fill in all required fields.

.. image:: new_earning_rule.png

Custom event name has some limitation.
It can contain only following characters: 'a-z', '0-9', '_', '.'

This name will be used next in api calls.

Report custom event
===================

In order to report custom event, you should made POST request to the following endpoint

.. code::

    /api/v1/earnRule/{eventName}/customer/{customer}

Where:

- eventName - is the name you provided during creation of earning rule
- customer - is the customer id

Example request will look like this:

.. code::

    curl -X POST 'http://openloyalty/api/v1/earnRule/follow_page/customer/00000000-0000-474c-b092-b0dd880c07e1'
         -H 'Authorization: Bearer eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXUyJ9...'


And the response will look similar to this one:

.. code::

    {
        "points": 100
    }