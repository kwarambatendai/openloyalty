<?php

namespace OpenLoyalty\Domain\Customer\Event;

use OpenLoyalty\Domain\Customer\CustomerId;

/**
 * Class CustomerAddressWasUpdated.
 */
class CustomerAddressWasUpdated extends CustomerEvent
{
    protected $addressData;

    /**
     * @var \DateTime
     */
    protected $updateAt;

    /**
     * CustomerAddressWasUpdated constructor.
     *
     * @param CustomerId $customerId
     * @param $addressData
     */
    public function __construct(CustomerId $customerId, $addressData)
    {
        parent::__construct($customerId);
        $this->addressData = $addressData;
        $this->updateAt = new \DateTime();
    }

    /**
     * @return mixed
     */
    public function getAddressData()
    {
        return $this->addressData;
    }

    public function serialize()
    {
        return array_merge(parent::serialize(), array(
            'addressData' => $this->addressData,
            'updatedAt' => $this->updateAt ? $this->updateAt->getTimestamp() : null,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public static function deserialize(array $data)
    {
        $event = new self(
            new CustomerId($data['customerId']),
            $data['addressData']
        );

        if (isset($data['updatedAt'])) {
            $date = new \DateTime();
            $date->setTimestamp($data['updatedAt']);
            $event->setUpdateAt($date);
        }

        return $event;
    }

    /**
     * @return \DateTime
     */
    public function getUpdateAt()
    {
        return $this->updateAt;
    }

    /**
     * @param \DateTime $updateAt
     */
    public function setUpdateAt($updateAt)
    {
        $this->updateAt = $updateAt;
    }
}
