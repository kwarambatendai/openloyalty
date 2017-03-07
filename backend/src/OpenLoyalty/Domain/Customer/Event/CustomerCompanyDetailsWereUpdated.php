<?php

namespace OpenLoyalty\Domain\Customer\Event;

use OpenLoyalty\Domain\Customer\CustomerId;

/**
 * Class CustomerCompanyDetailsWereUpdated.
 */
class CustomerCompanyDetailsWereUpdated extends CustomerEvent
{
    protected $companyData;

    /**
     * @var \DateTime
     */
    protected $updateAt;

    /**
     * CustomerCompanyDetailsWasUpdated constructor.
     *
     * @param CustomerId $customerId
     * @param $companyData
     */
    public function __construct(CustomerId $customerId, $companyData)
    {
        parent::__construct($customerId);
        $this->companyData = $companyData;
        $this->updateAt = new \DateTime();
    }

    /**
     * @return mixed
     */
    public function getCompanyData()
    {
        return $this->companyData;
    }

    public function serialize()
    {
        return array_merge(parent::serialize(), array(
            'companyData' => $this->companyData,
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
            $data['companyData']
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
