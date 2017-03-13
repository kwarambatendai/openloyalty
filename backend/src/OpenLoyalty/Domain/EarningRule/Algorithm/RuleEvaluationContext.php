<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Domain\EarningRule\Algorithm;

use OpenLoyalty\Domain\Transaction\ReadModel\TransactionDetails;

/**
 * Class RuleEvaluationContext.
 */
class RuleEvaluationContext implements RuleEvaluationContextInterface
{
    /** @var array */
    private $products;

    /**
     * @var TransactionDetails
     */
    private $transaction;

    /**
     * RuleEvaluationContext constructor.
     *
     * @param TransactionDetails $transaction
     */
    public function __construct(TransactionDetails $transaction)
    {
        $this->products = [];
        $this->transaction = $transaction;
    }

    /**
     * {@inheritdoc}
     */
    public function getTransaction()
    {
        return $this->transaction;
    }

    /**
     * {@inheritdoc}
     */
    public function getProductPoints($sku)
    {
        if (!array_key_exists($sku, $this->products)) {
            return 0;
        }

        return $this->products[$sku];
    }

    /**
     * {@inheritdoc}
     */
    public function addProductPoints($sku, $points)
    {
        $current = $this->getProductPoints($sku);
        $this->setProductPoints($sku, $current + $points);
    }

    /**
     * {@inheritdoc}
     */
    public function setProductPoints($sku, $points)
    {
        if (!array_key_exists($sku, $this->products)) {
            $this->products[$sku] = 0;
        }

        $this->products[$sku] = $points;
    }

    /**
     * {@inheritdoc}
     */
    public function getProducts()
    {
        return $this->products;
    }
}
