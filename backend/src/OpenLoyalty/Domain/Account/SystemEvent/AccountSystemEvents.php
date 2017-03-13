<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Domain\Account\SystemEvent;

/**
 * Class AccountSystemEvents.
 */
class AccountSystemEvents
{
    const AVAILABLE_POINTS_AMOUNT_CHANGED = 'oloy.account.available_points_amount_changed';
    const ACCOUNT_CREATED = 'oloy.account.created';
    const CUSTOM_EVENT_OCCURRED = 'oloy.account.custom_event_occured';
}
