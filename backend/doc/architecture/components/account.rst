Account component
=================

Account component contains all information about points, points transfers and also it is responsible for adding, spending and expiring points.

System events dispatched by component
-------------------------------------
.. code:: php

    const AVAILABLE_POINTS_AMOUNT_CHANGED = 'oloy.account.available_points_amount_changed';
    const ACCOUNT_CREATED = 'oloy.account.created';
    const CUSTOM_EVENT_OCCURRED = 'oloy.account.custom_event_occured';

