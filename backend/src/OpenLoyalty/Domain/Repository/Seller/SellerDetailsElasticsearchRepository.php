<?php

namespace OpenLoyalty\Domain\Repository\Seller;

use OpenLoyalty\Domain\Repository\OloyElasticsearchRepository;
use OpenLoyalty\Domain\Seller\ReadModel\SellerDetailsRepository;

/**
 * Class SellerDetailsElasticsearchRepository.
 */
class SellerDetailsElasticsearchRepository extends OloyElasticsearchRepository implements SellerDetailsRepository
{
    public function findByParametersPaginated(
        array $params,
        $exact = true,
        $page = 1,
        $perPage = 10,
        $sortField = null,
        $direction = 'DESC'
    ) {
        if ($page < 1) {
            $page = 1;
        }
        if ($perPage < 1) {
            $perPage = 10;
        }

        $filter = [];
        $filter[] = ['term' => ['deleted' => false]];

        foreach ($params as $key => $value) {
            if (!$exact) {
                $filter[] = ['wildcard' => [
                    $key => '*'.$value.'*',
                ]];
            } else {
                $filter[] = ['term' => [
                    $key => $value,
                ]];
            }
        }

        if ($sortField) {
            $sort = [
                $sortField => ['order' => strtolower($direction), 'ignore_unmapped' => true],
            ];
        } else {
            $sort = null;
        }

        if (count($filter) > 0) {
            $query = array(
                'bool' => array(
                    'must' => $filter,
                ),
            );
        } else {
            $query = array(
                'filtered' => array(
                    'query' => array(
                        'match_all' => array(),
                    ),
                ),
            );
        }

        return $this->paginatedQuery($query, ($page - 1) * $perPage, $perPage, $sort);
    }

    public function countTotal(array $params = [], $exact = true)
    {
        $filter = [];
        $filter[] = ['term' => ['deleted' => false]];

        foreach ($params as $key => $value) {
            if (!$exact) {
                $filter[] = ['wildcard' => [
                    $key => '*'.$value.'*',
                ]];
            } else {
                $filter[] = ['term' => [
                    $key => $value,
                ]];
            }
        }

        if (count($filter) > 0) {
            $query = array(
                'bool' => array(
                    'must' => $filter,
                ),
            );
        } else {
            $query = array(
                'filtered' => array(
                    'query' => array(
                        'match_all' => array(),
                    ),
                ),
            );
        }

        return $this->count($query);
    }
}
