<?php

namespace OpenLoyalty\Domain\Repository\Account;

use Elasticsearch\Common\Exceptions\Missing404Exception;
use OpenLoyalty\Domain\Account\ReadModel\PointsTransferDetails;
use OpenLoyalty\Domain\Account\ReadModel\PointsTransferDetailsRepository;
use OpenLoyalty\Domain\Repository\OloyElasticsearchRepository;

/**
 * Class PointsTransferDetailsRepository.
 */
class PointsTransferDetailsElasticsearchRepository extends OloyElasticsearchRepository implements PointsTransferDetailsRepository
{
    public function findAllActiveAddingTransfersCreatedAfter($timestamp)
    {
        $filter = [];
        $filter[] = ['term' => [
            'state' => PointsTransferDetails::STATE_ACTIVE,
        ]];
        $filter[] = ['term' => [
            'type' => PointsTransferDetails::TYPE_ADDING,
        ]];

        $filter[] = ['range' => [
            'createdAt' => [
                'lt' => $timestamp,
            ],
        ]];

        $query = array(
            'bool' => array(
                'must' => $filter,
            ),
        );

        return $this->query($query);
    }

    public function findAllPaginated($page = 1, $perPage = 10, $sortField = 'pointsTransferId', $direction = 'DESC')
    {
        $query = array(
            'filtered' => array(
                'query' => array(
                    'match_all' => array(),
                ),
            ),
        );

        return $this->query($query);
    }

    public function countTotalSpendingTransfers()
    {
        return $this->countTotal(['type' => 'spending']);
    }

    public function getTotalValueOfSpendingTransfers()
    {
        $query = array(
            'index' => $this->index,
            'body' => array(
                'query' => [
                    'bool' => [
                        'must' => [
                            'term' => ['type' => PointsTransferDetails::TYPE_SPENDING],
                        ],
                        'filter' => [
                            'not' => [
                                'term' => ['state' => PointsTransferDetails::STATE_CANCELED],
                            ],
                        ],
                    ],
                ],
                'aggregations' => [
                    'summary' => [
                        'sum' => ['field' => 'value'],
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
