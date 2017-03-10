Segment component
=================
Contains all information related to segment. It allows to manage existing segments and define set of rules.

System events dispatched by component
-------------------------------------

.. code:: php

    const CUSTOMER_ADDED_TO_SEGMENT = 'oloy.segment.customer_added_to_segment';
    const CUSTOMER_REMOVED_FROM_SEGMENT = 'oloy.segment.customer_removed_from_segment';
    const SEGMENT_CHANGED = 'oloy.segment.changed';