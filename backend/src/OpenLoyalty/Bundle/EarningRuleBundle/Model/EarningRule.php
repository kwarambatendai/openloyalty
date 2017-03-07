<?php

namespace OpenLoyalty\Bundle\EarningRuleBundle\Model;

use OpenLoyalty\Domain\EarningRule\EarningRule as BaseEarningRule;
use OpenLoyalty\Domain\Model\Label;
use OpenLoyalty\Domain\Model\SKU;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\GroupSequenceProviderInterface;
use OpenLoyalty\Domain\EarningRule\EarningRule as DomainEarningRule;

/**
 * Class EarningRule.
 *
 * @Assert\GroupSequenceProvider
 */
class EarningRule extends BaseEarningRule implements GroupSequenceProviderInterface
{
    /**
     * @var string
     */
    protected $type;

    /**
     * @var string
     * @Assert\Regex(pattern="/^([a-z0-9\_\.])+$/", groups={"custom_event"}, message="Allowed characters: 'a-z', '0-9', '_', '.'")
     */
    protected $eventName;

    /**
     * @var int
     */
    protected $pointsAmount;

    /**
     * @var float
     */
    protected $pointValue;

    /**
     * @var SKU[]
     */
    protected $excludedSKUs = [];

    /**
     * @var Label[]
     */
    protected $excludedLabels = [];

    /**
     * @var bool
     */
    protected $excludeDeliveryCost = true;

    /**
     * @var float
     */
    protected $minOrderValue = 0;

    /**
     * @var array
     */
    protected $skuIds;

    /**
     * @var array;
     */
    protected $labels = [];

    /**
     * @var float
     */
    protected $multiplier;

    /**
     * @Assert\Valid()
     *
     * @var EarningRuleLimit
     */
    protected $limit;

    public function __construct()
    {
    }

    public function toArray()
    {
        $exSkus = array_map(
            function ($sku) {
                if (!$sku instanceof SKU) {
                    return;
                }

                return $sku->serialize();
            },
            $this->excludedSKUs
        );

        $exLabels = array_map(
            function ($label) {
                if (!$label instanceof Label) {
                    return;
                }

                return $label->serialize();
            },
            $this->excludedLabels
        );

        $labels = array_map(
            function ($label) {
                if (!$label instanceof Label) {
                    return;
                }

                return $label->serialize();
            },
            $this->labels
        );

        $data = [
            'name' => $this->getName(),
            'description' => $this->getDescription(),
            'active' => $this->isActive(),
            'startAt' => $this->startAt ? $this->startAt->getTimestamp() : null,
            'endAt' => $this->endAt ? $this->endAt->getTimestamp() : null,
            'allTimeActive' => $this->isAllTimeActive(),
            'eventName' => $this->getEventName(),
            'pointValue' => $this->pointValue,
            'pointsAmount' => $this->getPointsAmount(),
            'excludedSKUs' => $exSkus,
            'excludedLabels' => $exLabels,
            'excludeDeliveryCost' => $this->isExcludeDeliveryCost(),
            'minOrderValue' => $this->getMinOrderValue(),
            'skuIds' => $this->getSkuIds(),
            'multiplier' => $this->multiplier,
            'labels' => $labels,
        ];
        if ($this->limit && $this->type == self::TYPE_CUSTOM_EVENT) {
            $data['limit'] = [
                'period' => $this->limit->getPeriod(),
                'active' => $this->limit->isActive(),
                'limit' => $this->limit->getLimit(),
            ];
        }

        return $data;
    }

    /**
     * @return string
     */
    public function getEventName()
    {
        return $this->eventName;
    }

    /**
     * @param string $eventName
     */
    public function setEventName($eventName)
    {
        $this->eventName = $eventName;
    }

    /**
     * @return int
     */
    public function getPointsAmount()
    {
        return $this->pointsAmount;
    }

    /**
     * @param int $pointsAmount
     */
    public function setPointsAmount($pointsAmount)
    {
        $this->pointsAmount = $pointsAmount;
    }

    /**
     * @return float
     */
    public function getPointValue()
    {
        return $this->pointValue;
    }

    /**
     * @param float $pointValue
     */
    public function setPointValue($pointValue)
    {
        $this->pointValue = $pointValue;
    }

    /**
     * @return \OpenLoyalty\Domain\Model\SKU[]
     */
    public function getExcludedSKUs()
    {
        return $this->excludedSKUs;
    }

    /**
     * @param \OpenLoyalty\Domain\Model\SKU[] $excludedSKUs
     */
    public function setExcludedSKUs($excludedSKUs)
    {
        $this->excludedSKUs = $excludedSKUs;
    }

    /**
     * @return \OpenLoyalty\Domain\Model\Label[]
     */
    public function getExcludedLabels()
    {
        return $this->excludedLabels;
    }

    /**
     * @param \OpenLoyalty\Domain\Model\Label[] $excludedLabels
     */
    public function setExcludedLabels($excludedLabels)
    {
        $this->excludedLabels = $excludedLabels;
    }

    /**
     * @return bool
     */
    public function isExcludeDeliveryCost()
    {
        return $this->excludeDeliveryCost;
    }

    /**
     * @param bool $excludeDeliveryCost
     */
    public function setExcludeDeliveryCost($excludeDeliveryCost)
    {
        $this->excludeDeliveryCost = $excludeDeliveryCost;
    }

    /**
     * @return float
     */
    public function getMinOrderValue()
    {
        return $this->minOrderValue;
    }

    /**
     * @param float $minOrderValue
     */
    public function setMinOrderValue($minOrderValue)
    {
        $this->minOrderValue = $minOrderValue;
    }

    /**
     * @return array
     */
    public function getSkuIds()
    {
        return $this->skuIds;
    }

    /**
     * @param array $skuIds
     */
    public function setSkuIds($skuIds)
    {
        $this->skuIds = $skuIds;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @param ExecutionContextInterface $context
     *
     * @Assert\Callback(groups={"default", "custom_event"})
     */
    public function validateAllTimeActive(ExecutionContextInterface $context)
    {
        if (!$this->allTimeActive) {
            if (!$this->startAt) {
                $context->buildViolation('This value should not be blank.')->atPath('startAt')->addViolation();
            }
            if (!$this->endAt) {
                $context->buildViolation('This value should not be blank.')->atPath('endAt')->addViolation();
            }

            if ($this->startAt && $this->endAt) {
                if ($this->endAt <= $this->startAt) {
                    $context->buildViolation('This date must be later than Start at.')->atPath('endAt')->addViolation();
                }
            }
        }
    }

    /**
     * @return float
     */
    public function getMultiplier()
    {
        return $this->multiplier;
    }

    /**
     * @param float $multiplier
     */
    public function setMultiplier($multiplier)
    {
        $this->multiplier = $multiplier;
    }

    /**
     * @return \OpenLoyalty\Domain\Model\Label[]
     */
    public function getLabels()
    {
        return $this->labels;
    }

    /**
     * @param \OpenLoyalty\Domain\Model\Label[] $labels
     */
    public function setLabels(array $labels)
    {
        $this->labels = $labels;
    }

    /**
     * {@inheritdoc}
     */
    public function getGroupSequence()
    {
        $groups = ['default'];
        if ($this->type == DomainEarningRule::TYPE_CUSTOM_EVENT) {
            $groups[] = 'custom_event';
        }

        return $groups;
    }

    public static function createFromDomain(DomainEarningRule $rule)
    {
        $model = new self();
        foreach (DomainEarningRule::TYPE_MAP as $key => $val) {
            if ($rule instanceof $val) {
                $model->setType($key);

                break;
            }
        }

        return $model;
    }

    /**
     * @return EarningRuleLimit
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * @param EarningRuleLimit $limit
     */
    public function setLimit($limit)
    {
        $this->limit = $limit;
    }
}
