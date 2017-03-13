<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Domain\Repository;

use Broadway\ReadModel\ElasticSearch\ElasticSearchRepository;
use Broadway\ReadModel\RepositoryInterface;
use Broadway\Serializer\SerializerInterface;
use Elasticsearch\Client;
use Elasticsearch\Common\Exceptions\Missing404Exception;

/**
 * Class OloyElasticsearchRepository.
 */
class OloyElasticsearchRepository extends ElasticSearchRepository implements RepositoryInterface
{
    protected $client;
    protected $serializer;
    protected $index;
    protected $class;
    protected $notAnalyzedFields;
    protected $dynamicFields = [];
    private $maxResultWindowSize = 10000;

    /**
     * @param Client              $client
     * @param SerializerInterface $serializer
     * @param string              $index
     * @param string              $class
     * @param array               $notAnalyzedFields = array
     */
    public function __construct(
        Client $client,
        SerializerInterface $serializer,
        $index,
        $class,
        array $notAnalyzedFields = array()
    ) {
        parent::__construct($client, $serializer, $index, $class, $notAnalyzedFields);
        $this->client = $client;
        $this->serializer = $serializer;
        $this->index = $index;
        $this->class = $class;
        $this->notAnalyzedFields = $notAnalyzedFields;
    }

    public function createIndex()
    {
        $class = $this->class;

        $indexParams = array(
            'index' => $this->index,
        );
        if (count($this->notAnalyzedFields)) {
            $indexParams['body']['mappings']['properties'] = $this->createNotAnalyzedFieldsMapping($this->notAnalyzedFields);
        }
        $defaultDynamicFields = [[
            'email' => [
                'match' => 'email',
                'match_mapping_type' => 'string',
                'mapping' => [
                    'type' => 'string',
                    'analyzer' => 'email',
                ],
            ],
        ],
            [
                'someemail' => [
                    'match' => '*Email',
                    'match_mapping_type' => 'string',
                    'mapping' => [
                        'type' => 'string',
                        'analyzer' => 'email',
                    ],
                ],
            ],
            [
                'notanalyzed' => [
                    'match' => '*Id',
                    'match_mapping_type' => 'string',
                    'mapping' => [
                        'type' => 'string',
                        'index' => 'not_analyzed',
                    ],
                ],
            ],
            [
                'loyaltyCard' => [
                    'match' => 'loyaltyCardNumber',
                    'match_mapping_type' => 'string',
                    'mapping' => [
                        'type' => 'string',
                        'index' => 'not_analyzed',
                    ],
                ],
            ],
            [
                'phone' => [
                    'match' => 'phone',
                    'match_mapping_type' => 'string',
                    'mapping' => [
                        'type' => 'string',
                        'index' => 'not_analyzed',
                    ],
                ],
            ],
        ];
        $indexParams['body'] = array(
            'settings' => [
                'analysis' => [
                    'analyzer' => [
                        'email' => [
                            'tokenizer' => 'uax_url_email',
                            'filter' => ['lowercase'],
                        ],
                        'small_letters' => [
                            'tokenizer' => 'keyword',
                            'filter' => ['lowercase'],
                        ],
                    ],
                    'filter' => [
                        'translation' => [
                            'type' => 'nGram',
                            'min_gram' => 2,
                            'max_gram' => 100,
                        ],
                    ],
                ],
            ],
            'mappings' => array(
                $class => array(
                    '_source' => array(
                        'enabled' => true,
                    ),
                    'dynamic_templates' => array_merge($this->dynamicFields, $defaultDynamicFields),
                ),
            ),
        );

        $this->client->indices()->create($indexParams);
        $response = $this->client->cluster()->health(array(
            'index' => $this->index,
            'wait_for_status' => 'yellow',
            'timeout' => '5s',
        ));

        return isset($response['status']) && $response['status'] !== 'red';
    }

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

        $filter = [];

        foreach ($params as $key => $value) {
            if (is_array($value) && isset($value['type'])) {
                if ($value['type'] == 'number') {
                    $filter[] = ['term' => [
                        $key => floatval($value['value']),
                    ]];
                } elseif ($value['type'] == 'range') {
                    $filter[] = ['range' => [
                        $key => $value['value'],
                    ]];
                }
            } elseif (!$exact) {
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

        return $this->paginatedQuery($query, $perPage === null ? null : ($page - 1) * $perPage, $perPage, $sort);
    }

    private function createNotAnalyzedFieldsMapping(array $notAnalyzedFields)
    {
        $fields = array();

        foreach ($notAnalyzedFields as $field) {
            $fields[$field] = array(
                'type' => 'string',
                'index' => 'not_analyzed',
            );
        }

        return $fields;
    }

    /**
     * Deletes the index for this repository's ReadModel.
     *
     * @return True, if the index was successfully deleted
     */
    public function deleteIndex()
    {
        $indexParams = array(
            'index' => $this->index,
            'timeout' => '5s',
        );

        $this->client->indices()->delete($indexParams);

        return true;
    }

    public function findByParameters(array $params, $exact = true)
    {
        $filter = [];
        foreach ($params as $key => $value) {
            if (is_array($value) && isset($value['type'])) {
                if ($value['type'] == 'number') {
                    $filter[] = ['term' => [
                        $key => floatval($value['value']),
                    ]];
                } elseif ($value['type'] == 'range') {
                    $filter[] = ['range' => [
                        $key => $value['value'],
                    ]];
                }
            } elseif (!$exact) {
                $filter[] = ['wildcard' => [
                    $key => '*'.$value.'*',
                ]];
            } else {
                $filter[] = ['term' => [
                    $key => $value,
                ]];
            }
        }

        $query = array(
            'bool' => array(
                'must' => $filter,
            ),
        );

        return $this->query($query);
    }

    public function countTotal(array $params = [], $exact = true)
    {
        $filter = [];
        foreach ($params as $key => $value) {
            if (is_array($value) && isset($value['type'])) {
                if ($value['type'] == 'number') {
                    $filter[] = ['term' => [
                        $key => floatval($value['value']),
                    ]];
                } elseif ($value['type'] == 'range') {
                    $filter[] = ['range' => [
                        $key => $value['value'],
                    ]];
                }
            } elseif (!$exact) {
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

    protected function paginatedQuery(array $query, $from = 0, $size = 500, $sort = null)
    {
        $query = array(
            'index' => $this->index,
            'body' => array(
                'query' => $query,
                'size' => $size === null ? $this->getMaxResultWindowSize() : $size,
                'from' => $from,
            ),
        );
        if ($sort) {
            $query['body']['sort'] = $sort;
        }

        return $this->searchAndDeserializeHits(
            $query
        );
    }

    protected function searchAndDeserializeHits(array $query)
    {
        try {
            $result = $this->client->search($query);
        } catch (Missing404Exception $e) {
            return array();
        }

        if (!array_key_exists('hits', $result)) {
            return array();
        }

        return $this->deserializeHits($result['hits']['hits']);
    }

    protected function deserializeHits(array $hits)
    {
        return array_map(array($this, 'deserializeHit'), $hits);
    }

    private function deserializeHit(array $hit)
    {
        return $this->serializer->deserialize(
            array(
                'class' => $hit['_type'],
                'payload' => $hit['_source'],
            )
        );
    }

    protected function count(array $query)
    {
        $query = array(
            'index' => $this->index,
            'body' => array(
                'query' => $query,
            ),
        );

        try {
            $result = $this->client->count($query);
        } catch (Missing404Exception $e) {
            return 0;
        }

        if (!array_key_exists('count', $result)) {
            return 0;
        }

        return $result['count'];
    }

    /**
     * @param array $query
     * @param array $facets
     * @param int   $size
     *
     * @return array
     */
    protected function search(array $query, array $facets = array(), $size = null)
    {
        if (null === $size) {
            $size = $this->getMaxResultWindowSize();
        }

        try {
            return $this->client->search(array(
                'index' => $this->index,
                'body' => array(
                    'query' => $query,
                    'facets' => $facets,
                ),
                'size' => $size,
            ));
        } catch (Missing404Exception $e) {
            return array();
        }
    }

    protected function query(array $query)
    {
        return $this->searchAndDeserializeHits(
            array(
                'index' => $this->index,
                'body' => array(
                    'query' => $query,
                ),
                'size' => $this->getMaxResultWindowSize(),
            )
        );
    }

    /**
     * @return mixed
     */
    public function getMaxResultWindowSize()
    {
        return $this->maxResultWindowSize;
    }

    /**
     * @param mixed $maxResultWindowSize
     */
    public function setMaxResultWindowSize($maxResultWindowSize)
    {
        $this->maxResultWindowSize = $maxResultWindowSize;
    }
}
