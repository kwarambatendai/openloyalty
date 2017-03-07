<?php

namespace OpenLoyaltyPlugin\SalesManagoBundle\Service;

use Doctrine\ORM\EntityRepository;
use GuzzleHttp\Exception\RequestException;
use OpenLoyaltyPlugin\SalesManagoBundle\DataProvider\DataProviderInterface;
use OpenLoyaltyPlugin\SalesManagoBundle\Entity\Config;
use OpenLoyaltyPlugin\SalesManagoBundle\Entity\Deadletter;
use OpenLoyaltyPlugin\SalesManagoBundle\Entity\DeadletterRepository;
use Pixers\SalesManagoAPI\Client;
use Pixers\SalesManagoAPI\Exception\InvalidRequestException;
use Pixers\SalesManagoAPI\SalesManago;
use Psr\Log\LoggerInterface;

/**
 * Class SalesManagoContactSender.
 */
abstract class SalesManagoContactSender
{
    /**
     * @var SalesManago
     */
    protected $connector;
    /**
     * @var string
     */
    protected $ownerEmail;
    /**
     * @var EntityRepository
     */
    protected $repository;
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var DataProviderInterface
     */
    protected $dataProvider;

    /**
     * SalesManagoContactSender constructor.
     *
     * @param EntityRepository      $repository
     * @param LoggerInterface       $logger
     * @param DataProviderInterface $dataProvider
     * @param DeadletterRepository  $deadletterRepository
     */
    public function __construct(
        EntityRepository $repository,
        LoggerInterface $logger,
        DataProviderInterface $dataProvider,
        DeadletterRepository $deadletterRepository
    ) {
        /* @var Config repository */
        $this->repository = $repository;
        $this->logger = $logger;
        $this->dataProvider = $dataProvider;
        $this->deadletterRepository = $deadletterRepository;
        $this->createClient();
    }

    /**
     */
    protected function createClient()
    {
        if ($this->repository->findAll()) {

            /** @var Config $config */
            $config = $this->repository->findAll()[0];

            if ($config->getSalesManagoIsActive() === true) {
                $endpoint = $config->getSalesManagoApiEndpoint();
                $apiSecret = $config->getSalesManagoApiSecret();
                $apiKey = $config->getSalesManagoApiKey();
                $customerId = $config->getSalesManagoCustomerId();
                try {
                    $this->connector = $this->createConnector(new Client($customerId, $endpoint, $apiSecret, $apiKey));
                } catch (\Exception $exception) {
                    $this->logger->debug(json_encode($exception->getMessage()));
                }

                $this->ownerEmail = $config->getSalesManagoOwnerEmail();
            }
        }
    }

    /**
     * @param $client
     *
     * @return SalesManago
     */
    protected function createConnector($client)
    {
        return new SalesManago($client);
    }

    /**
     * For those with more time and resources - move it to RabbitMQ, and add proper workers  - this slows down a lot.
     *
     * @param string $customerEmail
     * @param array  $tag
     */
    public function send($customerEmail, $tag)
    {
        if ($customerEmail !== null) {
            try {
                $response = $this->connector->getTagService()->modify(
                    $this->ownerEmail,
                    $customerEmail,
                    $tag
                );
                $this->logger->debug(json_encode($response));
            } catch (RequestException $e) {
                $deadletter = new Deadletter($this->ownerEmail, $customerEmail, json_encode($tag));
                $this->deadletterRepository->save($deadletter);
                $this->logger->error(json_encode($e->getMessage()));
            } catch (InvalidRequestException $e) {
                $deadletter = new Deadletter($this->ownerEmail, $customerEmail, json_encode($tag));
                $this->deadletterRepository->save($deadletter);
                $this->logger->error(json_encode($e->getMessage()));
            }
        }
    }

    /**
     * @param string $tag
     *
     * @return array
     */
    public function buildTag($tag)
    {
        $tagsArray =
            [
                'tags' => [
                    $tag,
                ],
            ];

        return $tagsArray;
    }

    /**
     * @return SalesManago
     */
    protected function getConnector()
    {
        return $this->connector;
    }
}
