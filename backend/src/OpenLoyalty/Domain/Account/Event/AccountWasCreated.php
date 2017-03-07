<?php

namespace OpenLoyalty\Domain\Account\Event;

use OpenLoyalty\Domain\Account\AccountId;
use OpenLoyalty\Domain\Account\CustomerId;

/**
 * Class AccountWasCreated.
 */
class AccountWasCreated extends AccountEvent
{
    /**
     * @var CustomerId
     */
    protected $customerId;

    /**
     * AccountWasCreated constructor.
     *
     * @param AccountId  $accountId
     * @param CustomerId $customerId
     */
    public function __construct(AccountId $accountId, CustomerId $customerId)
    {
        parent::__construct($accountId);
        $this->customerId = $customerId;
    }

    /**
     * @return CustomerId
     */
    public function getCustomerId()
    {
        return $this->customerId;
    }

    public function serialize()
    {
        return array_merge(
            parent::serialize(),
            [
                'customerId' => $this->customerId->__toString(),
            ]
        );
    }

    /**
     * @return mixed The object instance
     */
    public static function deserialize(array $data)
    {
        return new self(new AccountId($data['accountId']), new CustomerId($data['customerId']));
    }
}
