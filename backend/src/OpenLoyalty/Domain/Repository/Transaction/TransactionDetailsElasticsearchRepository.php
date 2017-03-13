<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Domain\Repository\Transaction;

use Elasticsearch\Common\Exceptions\Missing404Exception;
use OpenLoyalty\Domain\Repository\OloyElasticsearchRepository;
use OpenLoyalty\Domain\Transaction\ReadModel\TransactionDetailsRepository;

/**
 * Class TransactionDetailsElasticsearchRepository.
 */
class TransactionDetailsElasticsearchRepository extends OloyElasticsearchRepository implements TransactionDetailsRepository
{
    protected $dynamicFields = [
        [
            'grossValue' => [
                'match' => 'grossValue',
                'mapping' => [
                    'type' => 'double',
                ],
            ],
        ],
        [
            'maker' => [
                'match' => 'maker',
                'match_mapping_type' => 'string',
                'mapping' => [
                    'type' => 'string',
                    'analyzer' => 'small_letters',
                ],
            ],
        ],
        [
            'category' => [
                'match' => 'category',
                'match_mapping_type' => 'string',
                'mapping' => [
                    'type' => 'string',
                    'analyzer' => 'small_letters',
                ],
            ],
        ],
        [
            'label_value' => [
                'path_match' => 'items.labels.*',
                'mapping' => [
                    'type' => 'string',
                    'analyzer' => 'small_letters',
                ],
            ],
        ],
        [
            'document_number_raw' => [
                'match' => 'documentNumberRaw',
                'match_mapping_type' => 'string',
                'mapping' => [
                    'type' => 'string',
                    'index' => 'not_analyzed',
                ],
            ],
        ],
    ];

    public function findInPeriod(\DateTime $from, \DateTime $to, $onlyWithCustomers = true)
    {
        $filter = [];
        $filter[] = ['range' => [
            'purchaseDate' => [
                'gte' => $from->getTimestamp(),
                'lte' => $to->getTimestamp(),
            ],
        ]];
        $query = array(
            'bool' => array(
                'must' => [[
                    'bool' => [
                        'should' => $filter,
                    ],
                ]],
            ),
        );

        if ($onlyWithCustomers) {
            $query['bool']['must'][]['exists'] = ['field' => 'customerId'];
        }

        return $this->query($query);
    }

    public function findAllWithCustomer()
    {
        $query = array(
            'bool' => array(
                'must' => array(
                    'exists' => ['field' => 'customerId'],
                ),
            ),
        );

        return $this->query($query);
    }

    public function findBySKUs(array $skuIds, $withCustomer = true)
    {
        if (count($skuIds) == 0) {
            return [];
        }
        $filter = [];
        foreach ($skuIds as $id) {
            $filter[] = ['term' => [
                'items.sku.code' => strtolower($id),
            ]];
        }

        $query = array(
            'bool' => array(
                'must' => [[
                    'bool' => [
                        'should' => $filter,
                    ],
                ]],
            ),
        );

        if ($withCustomer) {
            $query['bool']['must'][]['exists'] = ['field' => 'customerId'];
        }

        return $this->query($query);
    }

    public function findByMakers(array $makers, $withCustomer = true)
    {
        if (count($makers) == 0) {
            return [];
        }
        $filter = [];
        foreach ($makers as $maker) {
            $filter[] = ['term' => [
                'items.maker' => strtolower($maker),
            ]];
        }

        $query = array(
            'bool' => array(
                'must' => [[
                    'bool' => [
                        'should' => $filter,
                    ],
                ]],
            ),
        );

        if ($withCustomer) {
            $query['bool']['must'][]['exists'] = ['field' => 'customerId'];
        }

        return $this->query($query);
    }

    public function findByLabels(array $labels, $withCustomer = true)
    {
        if (count($labels) == 0) {
            return [];
        }
        $filter = [];
        foreach ($labels as $label) {
            $filter[] = ['bool' => ['must' => [
                    ['term' => [
                            'items.labels.key' => strtolower($label['key']),
                        ],
                    ],
                    ['term' => [
                            'items.labels.value' => strtolower($label['value']),
                        ],
                    ],
                ],
            ]];
        }

        $query = array(
            'bool' => array(
                'must' => [[
                    'bool' => [
                        'should' => $filter,
                    ],
                ]],
            ),
        );

        if ($withCustomer) {
            $query['bool']['must'][]['exists'] = ['field' => 'customerId'];
        }

        return $this->query($query);
    }

    public function getAvailableLabels()
    {
        $query = array(
            'index' => $this->index,
            'body' => array(
                'aggregations' => [
                    'labels_key' => [
                        'terms' => ['field' => 'items.labels.key'],
                        'aggregations' => [
                            'label_values' => [
                                'terms' => [
                                    'field' => 'items.labels.value',
                                ],
                            ],
                        ],
                    ],
                ],
            ),
            'size' => 0,
        );

        try {
            $result = $this->client->search($query);
        } catch (Missing404Exception $e) {
            return [];
        }

        if (!array_key_exists('aggregations', $result)) {
            return [];
        }

        if (!array_key_exists('labels_key', $result['aggregations'])) {
            return [];
        }
        $labels = [];
        $labelKeys = $result['aggregations']['labels_key'];

        foreach ($labelKeys['buckets'] as $bucket) {
            $labels[$bucket['key']] = $this->getLabelValuesForBucket($bucket['label_values']);
        }

        return $labels;
    }

    protected function getLabelValuesForBucket(array $values)
    {
        $val = [];
        foreach ($values['buckets'] as $bucket) {
            $val[] = $bucket['key'];
        }

        return $val;
    }
}
