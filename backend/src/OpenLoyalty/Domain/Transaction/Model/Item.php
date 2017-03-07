<?php

namespace OpenLoyalty\Domain\Transaction\Model;

use Broadway\Serializer\SerializableInterface;
use OpenLoyalty\Domain\Model\Label;
use OpenLoyalty\Domain\Model\SKU;

/**
 * Class Item.
 */
class Item implements SerializableInterface
{
    /**
     * @var SKU
     */
    protected $sku;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var int
     */
    protected $quantity;

    /**
     * @var float
     */
    protected $grossValue;

    /**
     * @var string
     */
    protected $category;

    /**
     * @var string
     */
    protected $maker;

    /**
     * @var Label[]
     */
    protected $labels;

    /**
     * Item constructor.
     *
     * @param SKU     $sku
     * @param string  $name
     * @param int     $quantity
     * @param float   $grossValue
     * @param string  $category
     * @param string  $maker
     * @param Label[] $labels
     */
    public function __construct(SKU $sku, $name, $quantity, $grossValue, $category, $maker = null, array $labels = [])
    {
        $this->sku = $sku;
        $this->name = $name;
        $this->quantity = $quantity;
        $this->grossValue = $grossValue;
        $this->category = $category;
        $this->maker = $maker;
        $this->labels = $labels;
    }

    /**
     * @param array $data
     *
     * @return Item
     */
    public static function deserialize(array $data)
    {
        $labels = [];
        if (isset($data['labels'])) {
            foreach ($data['labels'] as $label) {
                $labels[] = Label::deserialize($label);
            }
        }

        return new self(
            SKU::deserialize($data['sku']),
            $data['name'],
            $data['quantity'],
            $data['grossValue'],
            $data['category'],
            isset($data['maker']) ? $data['maker'] : null,
            $labels
        );
    }

    /**
     * @return array
     */
    public function serialize()
    {
        $labels = [];
        foreach ($this->labels as $label) {
            $labels[] = $label->serialize();
        }

        return [
            'sku' => $this->sku->serialize(),
            'name' => $this->name,
            'quantity' => $this->quantity,
            'grossValue' => $this->grossValue,
            'category' => $this->category,
            'labels' => $labels,
            'maker' => $this->maker,
        ];
    }

    /**
     * @return SKU
     */
    public function getSku()
    {
        return $this->sku;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @return float
     */
    public function getGrossValue()
    {
        return $this->grossValue;
    }

    /**
     * @return string
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @return string
     */
    public function getMaker()
    {
        return $this->maker;
    }

    /**
     * @return Label[]
     */
    public function getLabels()
    {
        return $this->labels;
    }
}
