<?php

namespace OpenLoyalty\Domain\Repository;

use Broadway\ReadModel\RepositoryFactoryInterface;
use Broadway\ReadModel\RepositoryInterface;
use Broadway\Serializer\SerializerInterface;
use Elasticsearch\Client;

/**
 * Class OloyElasticsearchRepositoryFactory.
 */
class OloyElasticsearchRepositoryFactory implements RepositoryFactoryInterface
{
    private $client;
    private $serializer;
    private $maxResultWindowSize;

    public function __construct(Client $client, SerializerInterface $serializer, $maxResultWindowSize = null)
    {
        $this->client = $client;
        $this->serializer = $serializer;
        $this->maxResultWindowSize = $maxResultWindowSize;
    }

    /**
     * {@inheritdoc}
     */
    public function create($name, $class, $repositoryClass = null, array $notAnalyzedFields = array())
    {
        if ($repositoryClass != null) {
            $rClass = new \ReflectionClass($repositoryClass);

            if ($rClass->implementsInterface(RepositoryInterface::class)) {
                $repo = new $repositoryClass($this->client, $this->serializer, $name, $class, $notAnalyzedFields);
                if ($repo instanceof OloyElasticsearchRepository && $this->maxResultWindowSize) {
                    $repo->setMaxResultWindowSize($this->maxResultWindowSize);
                }

                return $repo;
            }
        }

        $repo = new OloyElasticsearchRepository($this->client, $this->serializer, $name, $class, $notAnalyzedFields);
        if ($this->maxResultWindowSize) {
            $repo->setMaxResultWindowSize($this->maxResultWindowSize);
        }

        return $repo;
    }
}
