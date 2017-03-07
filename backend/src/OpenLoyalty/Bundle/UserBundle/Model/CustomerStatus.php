<?php

namespace OpenLoyalty\Bundle\UserBundle\Model;

use OpenLoyalty\Domain\Customer\CustomerId;
use JMS\Serializer\Annotation as JMS;

/**
 * Class CustomerStatus.
 */
class CustomerStatus
{
    /**
     * @var string
     */
    protected $firstName;

    /**
     * @var string
     */
    protected $lastName;

    /**
     * @var CustomerId
     * @JMS\Inline()
     */
    protected $customerId;

    /**
     * @var int
     */
    protected $points = 0;

    /**
     * @var int
     */
    protected $usedPoints = 0;

    /**
     * @var int
     */
    protected $expiredPoints = 0;

    /**
     * @var string
     * @JMS\SerializedName("level")
     */
    protected $levelPercent;

    /**
     * @var string
     */
    protected $levelName;

    /**
     * @var string
     * @JMS\SerializedName("nextLevel")
     */
    protected $nextLevelPercent;

    /**
     * @var string
     */
    protected $nextLevelName;

    /**
     * @var float
     */
    protected $transactionsAmountToNextLevelWithoutDeliveryCosts;

    /**
     * @var float
     */
    protected $transactionsAmountWithoutDeliveryCosts;

    /**
     * @var float
     */
    protected $transactionsAmountToNextLevel;

    /**
     * @var float
     */
    protected $averageTransactionsAmount;

    /**
     * @var int
     */
    protected $transactionsCount;

    /**
     * @var float
     */
    protected $transactionsAmount;

    /**
     * @var float
     */
    protected $pointsToNextLevel;

    /**
     * @var string
     */
    protected $currency;

    /**
     * CustomerStatus constructor.
     *
     * @param CustomerId $customerId
     */
    public function __construct(CustomerId $customerId)
    {
        $this->customerId = $customerId;
    }

    /**
     * @return CustomerId
     */
    public function getCustomerId()
    {
        return $this->customerId;
    }

    /**
     * @param CustomerId $customerId
     */
    public function setCustomerId($customerId)
    {
        $this->customerId = $customerId;
    }

    /**
     * @return int
     */
    public function getPoints()
    {
        return $this->points;
    }

    /**
     * @param int $points
     */
    public function setPoints($points)
    {
        $this->points = $points;
    }

    /**
     * @return string
     */
    public function getLevelPercent()
    {
        return $this->levelPercent;
    }

    /**
     * @param string $levelPercent
     */
    public function setLevelPercent($levelPercent)
    {
        $this->levelPercent = $levelPercent;
    }

    /**
     * @return string
     */
    public function getNextLevelPercent()
    {
        return $this->nextLevelPercent;
    }

    /**
     * @param string $nextLevelPercent
     */
    public function setNextLevelPercent($nextLevelPercent)
    {
        $this->nextLevelPercent = $nextLevelPercent;
    }

    /**
     * @return float
     */
    public function getTransactionsAmountToNextLevelWithoutDeliveryCosts()
    {
        return $this->transactionsAmountToNextLevelWithoutDeliveryCosts;
    }

    /**
     * @param float $transactionsAmountToNextLevelWithoutDeliveryCosts
     */
    public function setTransactionsAmountToNextLevelWithoutDeliveryCosts(
        $transactionsAmountToNextLevelWithoutDeliveryCosts
    ) {
        $this->transactionsAmountToNextLevelWithoutDeliveryCosts = $transactionsAmountToNextLevelWithoutDeliveryCosts;
    }

    /**
     * @return float
     */
    public function getTransactionsAmountWithoutDeliveryCosts()
    {
        return $this->transactionsAmountWithoutDeliveryCosts;
    }

    /**
     * @param float $transactionsAmountWithoutDeliveryCosts
     */
    public function setTransactionsAmountWithoutDeliveryCosts($transactionsAmountWithoutDeliveryCosts)
    {
        $this->transactionsAmountWithoutDeliveryCosts = $transactionsAmountWithoutDeliveryCosts;
    }

    /**
     * @return float
     */
    public function getTransactionsAmountToNextLevel()
    {
        return $this->transactionsAmountToNextLevel;
    }

    /**
     * @param float $transactionsAmountToNextLevel
     */
    public function setTransactionsAmountToNextLevel($transactionsAmountToNextLevel)
    {
        $this->transactionsAmountToNextLevel = $transactionsAmountToNextLevel;
    }

    /**
     * @return float
     */
    public function getTransactionsAmount()
    {
        return $this->transactionsAmount;
    }

    /**
     * @param float $transactionsAmount
     */
    public function setTransactionsAmount($transactionsAmount)
    {
        $this->transactionsAmount = $transactionsAmount;
    }

    /**
     * @return float
     */
    public function getPointsToNextLevel()
    {
        return $this->pointsToNextLevel;
    }

    /**
     * @param float $pointsToNextLevel
     */
    public function setPointsToNextLevel($pointsToNextLevel)
    {
        $this->pointsToNextLevel = $pointsToNextLevel;
    }

    /**
     * @return int
     */
    public function getUsedPoints()
    {
        return $this->usedPoints;
    }

    /**
     * @param int $usedPoints
     */
    public function setUsedPoints($usedPoints)
    {
        $this->usedPoints = $usedPoints;
    }

    /**
     * @return int
     */
    public function getExpiredPoints()
    {
        return $this->expiredPoints;
    }

    /**
     * @param int $expiredPoints
     */
    public function setExpiredPoints($expiredPoints)
    {
        $this->expiredPoints = $expiredPoints;
    }

    /**
     * @return string
     */
    public function getLevelName()
    {
        return $this->levelName;
    }

    /**
     * @param string $levelName
     */
    public function setLevelName($levelName)
    {
        $this->levelName = $levelName;
    }

    /**
     * @return string
     */
    public function getNextLevelName()
    {
        return $this->nextLevelName;
    }

    /**
     * @param string $nextLevelName
     */
    public function setNextLevelName($nextLevelName)
    {
        $this->nextLevelName = $nextLevelName;
    }

    /**
     * @return float
     */
    public function getAverageTransactionsAmount()
    {
        return $this->averageTransactionsAmount;
    }

    /**
     * @param float $averageTransactionsAmount
     */
    public function setAverageTransactionsAmount($averageTransactionsAmount)
    {
        $this->averageTransactionsAmount = $averageTransactionsAmount;
    }

    /**
     * @return int
     */
    public function getTransactionsCount()
    {
        return $this->transactionsCount;
    }

    /**
     * @param int $transactionsCount
     */
    public function setTransactionsCount($transactionsCount)
    {
        $this->transactionsCount = $transactionsCount;
    }

    /**
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @param string $currency
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }
}
