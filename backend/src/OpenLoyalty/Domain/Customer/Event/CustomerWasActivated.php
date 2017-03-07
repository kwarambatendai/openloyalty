<?php

namespace OpenLoyalty\Domain\Customer\Event;

use OpenLoyalty\Domain\Customer\CustomerId;

/**
 * Class CustomerWasActivated.
 */
class CustomerWasActivated extends CustomerEvent
{
    /**
     * @var \DateTime
     */
    protected $activatedAt;

    public function __construct(CustomerId $customerId)
    {
        parent::__construct($customerId);
        $this->activatedAt = new \DateTime();
    }

    public function serialize()
    {
        return array_merge(parent::serialize(), array(
            'activatedAt' => $this->activatedAt ? $this->activatedAt->getTimestamp() : null,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public static function deserialize(array $data)
    {
        $id = $data['customerId'];
        $customer = new self(
            new CustomerId($id)
        );

        if (isset($data['activatedAt'])) {
            $date = new \DateTime();
            $date->setTimestamp($data['activatedAt']);
            $customer->setActivatedAt($date);
        }

        return $customer;
    }

    /**
     * @return \DateTime
     */
    public function getActivatedAt()
    {
        return $this->activatedAt;
    }

    /**
     * @param \DateTime $activatedAt
     */
    public function setActivatedAt($activatedAt)
    {
        $this->activatedAt = $activatedAt;
    }
}
