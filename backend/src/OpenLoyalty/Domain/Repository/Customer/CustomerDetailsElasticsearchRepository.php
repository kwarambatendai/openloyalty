<?php

namespace OpenLoyalty\Domain\Repository\Customer;

use Elasticsearch\Common\Exceptions\BadRequest400Exception;
use Elasticsearch\Common\Exceptions\Missing404Exception;
use OpenLoyalty\Domain\Customer\CustomerId;
use OpenLoyalty\Domain\Customer\Exception\ToManyResultsException;
use OpenLoyalty\Domain\Customer\ReadModel\CustomerDetails;
use OpenLoyalty\Domain\Customer\ReadModel\CustomerDetailsRepository;
use OpenLoyalty\Domain\Repository\OloyElasticsearchRepository;

/**
 * Class CustomerDetailsElasticsearchRepository.
 */
class CustomerDetailsElasticsearchRepository extends OloyElasticsearchRepository implements CustomerDetailsRepository
{
    protected $dynamicFields = [
        [
            'nestedCampaignPurchases' => [
                'match' => 'campaignPurchases',
                'mapping' => [
                    'type' => 'nested',
                ],
            ],
        ],
        [
            'transactionsAmount' => [
                'match' => 'transactionsAmount',
                'mapping' => [
                    'type' => 'double',
                ],
            ],
        ],
        [
            'averageTransactionAmount' => [
                'match' => 'averageTransactionAmount',
                'mapping' => [
                    'type' => 'double',
                ],
            ],
        ],
        [
            'transactionsAmountWithoutDeliveryCosts' => [
                'match' => 'transactionsAmountWithoutDeliveryCosts',
                'mapping' => [
                    'type' => 'double',
                ],
            ],
        ],
    ];

    public function findByBirthdayAnniversary(\DateTime $from, \DateTime $to, $onlyActive = true)
    {
        $filter = [];
        foreach ($this->getTimestamps($from, $to) as $period) {
            $filter[] = ['range' => [
                'birthDate' => [
                    'gte' => $period['from'],
                    'lte' => $period['to'],
                ],
            ]];
        }

        $query = array(
            'bool' => array(
                'must' => [
                    ['bool' => [
                        'should' => $filter,
                    ]],
                ],
            ),
        );

        if ($onlyActive) {
            $query['bool']['must'][] = ['term' => ['active' => true]];
        }

        return $this->query($query);
    }

    public function findByCreationAnniversary(\DateTime $from, \DateTime $to, $onlyActive = true)
    {
        $filter = [];
        foreach ($this->getTimestamps($from, $to) as $period) {
            $filter[] = ['range' => [
                'createdAt' => [
                    'gte' => $period['from'],
                    'lte' => $period['to'],
                ],
            ]];
        }

        $query = array(
            'bool' => array(
                'must' => [
                    ['bool' => [
                        'should' => $filter,
                    ]],
                ],
            ),
        );

        if ($onlyActive) {
            $query['bool']['must'][] = ['term' => ['active' => true]];
        }

        return $this->query($query);
    }

    public function findPurchasesByCustomerIdPaginated(
        CustomerId $customerId,
        $page = 1,
        $perPage = 10,
        $sortField = null,
        $direction = 'DESC'
    ) {
        if ($sortField) {
            $sort = [
                'campaignPurchases.'.$sortField => [
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
            'ids' => [
                'values' => [
                    $customerId->__toString(),
                ],
            ],
        );

        $query = array(
            'index' => $this->index,
            'body' => array(
                'query' => $query,
                'inner_hits' => [
                    'nested_campaign_purchases' => [
                        'path' => ['campaignPurchases' => $innerHits],
                    ],
                ],
            ),
        );
        try {
            $result = $this->client->search($query);
        } catch (Missing404Exception $e) {
            return [];
        } catch (BadRequest400Exception $e) {
            if (strpos($e->getMessage(), 'campaignPurchases') !== false) {
                return [];
            }

            throw $e;
        }

        if (!array_key_exists('hits', $result)) {
            return [];
        }
        if (!array_key_exists('hits', $result['hits'])) {
            return [];
        }
        if (count($result['hits']['hits']) == 0) {
            return [];
        }

        if (!array_key_exists('inner_hits', $result['hits']['hits'][0])) {
            return [];
        }

        if (!array_key_exists('nested_campaign_purchases', $result['hits']['hits'][0]['inner_hits'])) {
            return [];
        }
        if (!array_key_exists('_source', $result['hits']['hits'][0])) {
            return [];
        }

        if (!array_key_exists('hits', $result['hits']['hits'][0]['inner_hits']['nested_campaign_purchases'])) {
            return [];
        }
        $purchases = $result['hits']['hits'][0]['inner_hits']['nested_campaign_purchases']['hits']['hits'];
        $data = $this->serializer->deserialize(
            [
                'class' => $result['hits']['hits'][0]['_type'],
                'payload' => $result['hits']['hits'][0]['_source'],
            ]
        );
        $data = $data->serialize();
        $data['campaignPurchases'] = array_map(function ($purchase) {
            return $purchase['_source'];
        }, $purchases);

        /** @var CustomerDetails $result */
        $result = $this->serializer->deserialize(
            array(
                'class' => $result['hits']['hits'][0]['_type'],
                'payload' => $data,
            )
        );
        if (!$result || $result == null) {
            return [];
        }

        return $result->getCampaignPurchases();
    }

    public function countPurchasesByCustomerId(CustomerId $customerId)
    {
        $query = array(
            'ids' => [
                'values' => [
                    $customerId->__toString(),
                ],
            ],
        );

        $query = array(
            'index' => $this->index,
            'body' => array(
                'query' => $query,
                'aggregations' => [
                    'campaign_purchases' => [
                        'nested' => ['path' => 'campaignPurchases'],
                        'aggregations' => [
                            'campaign_purchases_count' => [
                                'value_count' => ['field' => 'campaignPurchases.campaignId'],
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
        } catch (BadRequest400Exception $e) {
            if (strpos($e->getMessage(), 'campaignPurchases') !== false) {
                return 0;
            }

            throw $e;
        }

        if (!array_key_exists('aggregations', $result)) {
            return 0;
        }

        if (!array_key_exists('campaign_purchases', $result['aggregations'])) {
            return 0;
        }

        if (!array_key_exists('campaign_purchases_count', $result['aggregations']['campaign_purchases'])) {
            return 0;
        }

        if (!array_key_exists('value', $result['aggregations']['campaign_purchases']['campaign_purchases_count'])) {
            return 0;
        }

        return $result['aggregations']['campaign_purchases']['campaign_purchases_count']['value'];
    }

    protected function getTimestamps(\DateTime $from, \DateTime $to)
    {
        $date = clone $from;
        $now = clone $to;
        $date->setTime(0, 0, 0);
        $now->setTime(23, 59, 59);
        $timestamps = [];
        $timestamps[] = ['from' => $date->getTimestamp(), 'to' => $now->getTimestamp()];
        for ($i = 0; $i < 100; ++$i) {
            $date->modify('-1 year');
            $now->modify('-1 year');
            $timestamps[] = [
                'from' => $date->getTimestamp(),
                'to' => $now->getTimestamp(),
            ];
        }

        return $timestamps;
    }

    public function findOneByCriteria($criteria, $limit)
    {
        $filter = [];
        foreach ($criteria as $key => $value) {
            if ($key == 'id') {
                continue;
            }
            $filter[] = ['term' => [
                $key => $value,
            ]];
        }

        if (count($filter) > 0) {
            $query = array(
                'bool' => array(
                    'must' => $filter,
                ),
            );

            if (isset($criteria['id'])) {
                $query['bool']['must'][]['ids'] = ['values' => [$criteria['id']]];
            }
        } else {
            $query = [
                'ids' => ['values' => [$criteria['id']]],
            ];
        }

        $result = $this->query($query);

        if (count($result) > $limit) {
            throw new ToManyResultsException();
        }

        return $result;
    }

    public function findAllWithAverageTransactionAmountBetween($from, $to, $onlyActive = true)
    {
        $filter = [['range' => [
            'averageTransactionAmount' => [
                'gte' => floatval($from),
                'lte' => floatval($to),
            ],
        ]]];
        if ($onlyActive) {
            $filter[] = ['term' => [
                'active' => true,
            ]];
        }

        $query = array(
            'filtered' => array(
                'query' => array(
                    'match_all' => array(),
                ),
                'filter' => ['and' => $filter],
            ),
        );

        return $this->query($query);
    }

    public function findAllWithTransactionAmountBetween($from, $to, $onlyActive = true)
    {
        $filter = [['range' => [
            'transactionsAmount' => [
                'gte' => floatval($from),
                'lte' => floatval($to),
            ],
        ]]];
        if ($onlyActive) {
            $filter[] = ['term' => [
                'active' => true,
            ]];
        }

        $query = array(
            'filtered' => array(
                'query' => array(
                    'match_all' => array(),
                ),
                'filter' => ['and' => $filter],
            ),
        );

        return $this->query($query);
    }

    public function findAllWithTransactionCountBetween($from, $to, $onlyActive = true)
    {
        $filter = [['range' => [
            'transactionsCount' => [
                'gte' => floatval($from),
                'lte' => floatval($to),
            ],
        ]]];
        if ($onlyActive) {
            $filter[] = ['term' => [
                'active' => true,
            ]];
        }

        $query = array(
            'filtered' => array(
                'query' => array(
                    'match_all' => array(),
                ),
                'filter' => ['and' => $filter],
            ),
        );

        return $this->query($query);
    }

    public function sumAllByField($fieldName)
    {
        $allowedFields = [
            'transactionsCount',
            'transactionsAmount',
            'transactionsAmountWithoutDeliveryCosts',
        ];
        if (!in_array($fieldName, $allowedFields)) {
            throw new \InvalidArgumentException($fieldName.' is not allowed');
        }

        $query = array(
            'index' => $this->index,
            'body' => array(
                'aggregations' => [
                    'summary' => [
                        'sum' => ['field' => $fieldName],
                    ],
                ],
            ),
            'size' => 0,
        );

        try {
            $result = $this->client->search($query);
        } catch (Missing404Exception $e) {
            return 0;
        }

        if (!array_key_exists('aggregations', $result)) {
            return 0;
        }

        if (!array_key_exists('summary', $result['aggregations'])) {
            return 0;
        }

        if (!array_key_exists('value', $result['aggregations']['summary'])) {
            return 0;
        }

        return $result['aggregations']['summary']['value'];
    }
}
