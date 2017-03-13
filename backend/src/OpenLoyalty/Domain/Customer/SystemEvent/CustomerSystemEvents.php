<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Domain\Customer\SystemEvent;

/**
 * Class CustomerSystemEvents.
 */
class CustomerSystemEvents
{
    const CUSTOMER_REGISTERED = 'oloy.customer.registered';
    const CUSTOMER_DEACTIVATED = 'oloy.customer.deactivated';
    const CUSTOMER_ACTIVATED = 'oloy.customer.activated';
    const CUSTOMER_UPDATED = 'oloy.customer.updated';
    const CUSTOMER_AGREEMENTS_UPDATED = 'oloy.customer.agreements_updated';
    const CUSTOMER_LOGGED_IN = 'oloy.customer.logged_in';
    const CUSTOMER_REFERRAL = 'oloy.customer.referral';
    const NEWSLETTER_SUBSCRIPTION = 'oloy.customer.newsletter_subscription';
    const CUSTOMER_LEVEL_CHANGED = 'oloy.customer.level_changed';
}
