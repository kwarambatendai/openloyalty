<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace OpenLoyalty\Domain\Repository\Customer;

use Elasticsearch\Common\Exceptions\Missing404Exception;
use OpenLoyalty\Domain\Customer\LevelId;
use OpenLoyalty\Domain\Customer\ReadModel\CustomersBelongingToOneLevelRepository;
use OpenLoyalty\Domain\Repository\OloyElasticsearchRepository;

/**
 * Class CustomersBelongingToOneLevelElasticsearchRepository.
 */
class CustomersBelongingToOneLevelElasticsearchRepository extends OloyElasticsearchRepository implements CustomersBelongingToOneLevelRepository
{
    protected $dynamicFields = [[
        'nestedCustomers' => [
            'match' => 'customers',
            'mapping' => [
                'type' => 'nested',
            ],
        ],
    ]];

    public function findByLevelIdPaginated(
        LevelId $levelId,
        $page = 1,
        $perPage = 10,
        $sortField = null,
        $direction = 'DESC'
    ) {
        if ($sortField) {
            $sort = [
                'customers.'.$sortField => [
                    'order' => strtolower($direction),
                    'ignore_unmapped' => true,
                ],
            ];
        } else {
            $sort = null;
        }

        $innerHits = [
            'size' => $perPage,
            'from' => ($page - 1) * $perPage,
            'query' => ['match_all' => []],
        ];

        if ($sort) {
            $innerHits['sort'] = $sort;
        }

        $query = array(
            'bool' => [
                'must' => [
                    'term' => ['levelId' => $levelId->__toString()],
                ],
            ],
        );

        $query = array(
            'index' => $this->index,
            'body' => array(
                'query' => $query,
                'inner_hits' => [
                    'nested_customers' => [
                        'path' => ['customers' => $innerHits],
                    ],
                ],
            ),
        );
        try {
            $result = $this->client->search($query);
        } catch (Missing404Exception $e) {
            return;
        }

        if (!array_key_exists('hits', $result)) {
            return;
        }
        if (!array_key_exists('hits', $result['hits'])) {
            return;
        }
        if (count($result['hits']['hits']) == 0) {
            return;
        }

        if (!array_key_exists('inner_hits', $result['hits']['hits'][0])) {
            return;
        }

        if (!array_key_exists('nested_customers', $result['hits']['hits'][0]['inner_hits'])) {
            return;
        }

        if (!array_key_exists('hits', $result['hits']['hits'][0]['inner_hits']['nested_customers'])) {
            return;
        }
        $customers = $result['hits']['hits'][0]['inner_hits']['nested_customers']['hits']['hits'];
        $data = [
            'customers' => array_map(function ($customer) {
                return $customer['_source'];
            }, $customers),
        ];
        $data['levelId'] = $levelId->__toString();

        $result = $this->serializer->deserialize(
            array(
                'class' => $result['hits']['hits'][0]['_type'],
                'payload' => $data,
            )
        );
        if (!$result || $result == null || count($result) == 0) {
            return;
        }

        return $result;
    }

    public function countByLevelId(LevelId $levelId)
    {
        $query = array(
            'bool' => [
                'must' => [
                    'term' => ['levelId' => $levelId->__toString()],
                ],
            ],
        );

        $query = array(
            'index' => $this->index,
            'body' => array(
                'query' => $query,
                'aggregations' => [
                    'level_customers' => [
                        'nested' => ['path' => 'customers'],
                        'aggregations' => [
                            'customers_count' => [
                                'value_count' => ['field' => 'customers.customerId'],
                            ],
                        ],
                    ],
                ],
                'size' => 0,
            ),
        );

        try {
            $result = $this->client->search($query);
        } catch (Missing404Exception $e) {
            return 0;
        }

        if (!array_key_exists('aggregations', $result)) {
            return 0;
        }

        if (!array_key_exists('level_customers', $result['aggregations'])) {
            return 0;
        }

        if (!array_key_exists('customers_count', $result['aggregations']['level_customers'])) {
            return 0;
        }

        if (!array_key_exists('value', $result['aggregations']['level_customers']['customers_count'])) {
            return 0;
        }

        return $result['aggregations']['level_customers']['customers_count']['value'];
    }
}
