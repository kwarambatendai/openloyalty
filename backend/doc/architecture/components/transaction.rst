Segment component
=================
Contains all information related to transaction. Registering transaction and assign it to customer.

System events dispatched by component
-------------------------------------

.. code:: php
    const TRANSACTION_REGISTERED = 'oloy.transaction.registered';
    const CUSTOMER_ASSIGNED_TO_TRANSACTION = 'oloy.transaction.customer_assigned';
    const CUSTOMER_FIRST_TRANSACTION = 'oloy.transaction.customer_first_transaction';