# api login
## jwt token structure
```
{
 exp: 1470299383,
 username: "admin",
 roles: [
  "ROLE_ADMIN"
 ],
 iat: "1470212983"
}
```
## obtaining jwt token
admin url: /api/admin/login_check
customer url: /api/customer/login_check
seller url: /api/seller/login_check

method: POST

body:
- _username: admin
- _password: open
    
response:
```
{
"token":"eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXUyJ9.eyJleHA",
"refresh_token":"44588da231e2f480bd4"
}
```
## refreshing jwt token
url: /api/token/refresh

method: POST

body:
- refresh_token: 'token'

response:
```
{
"token":"eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXUyJ9.eyJleHA",
"refresh_token":"44588da231e2f480bd4"
}
```

# events

## system events
Proper listener should be created to handle system events.
Example definition of such listener:
```
    oloy.listener:
        class: 'OpenLoyalty\Listener.php'
        tags:
          - { name: broadway.event_listener, event: oloy.customer.registered, method: handle }
```
### 'oloy.customer.registered' - customer added to PL
Event class: OpenLoyalty\Domain\Customer\SystemEvent\CustomerRegisteredSystemEvent

### 'oloy.customer.updated'
Event class: OpenLoyalty\Domain\Customer\SystemEvent\CustomerUpdatedSystemEvent

### 'oloy.customer.agreements_updated'
Event class: OpenLoyalty\Domain\Customer\SystemEvent\CustomerAgreementsUpdatedSystemEvent

### 'oloy.customer.deactivated'
Event class: OpenLoyalty\Domain\Customer\SystemEvent\CustomerDeactivatedSystemEvent

### 'oloy.segment.customer_added_to_segment'
Event class: OpenLoyalty\Domain\Segment\SystemEvent\CustomerAddedToSegmentSystemEvent

### 'oloy.segment.customer_removed_from_segment'
Event class: OpenLoyalty\Domain\Segment\SystemEvent\CustomerRemovedFromSegmentSystemEvent

## domain events
Proper listener should be created to handle domain events.
This listener must implement Broadway\EventHandling\EventListenerInterface

Example definition of such listener:
```
    oloy.listener:
        class: 'OpenLoyalty\Listener.php'
        lazy: true
        tags:
          - { name: broadway.domain.event_listener }
```

### OpenLoyalty\Domain\Customer\Event\CampaignWasBoughtByCustomer - dispatched when campaign was bought by customer