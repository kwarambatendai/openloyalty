<?php

namespace OpenLoyaltyPlugin\SalesManagoBundle\Service;

use GuzzleHttp\Exception\RequestException;
use OpenLoyalty\Bundle\UserBundle\Status\CustomerStatusProvider;
use OpenLoyalty\Domain\Customer\CustomerId;
use OpenLoyalty\Domain\Customer\ReadModel\CustomerDetailsRepository;
use OpenLoyalty\Domain\Level\LevelRepository;
use OpenLoyaltyPlugin\SalesManagoBundle\Entity\Deadletter;
use Pixers\SalesManagoAPI\Exception\InvalidRequestException;

/**
 * Class SalesManagoContactUpdateSender.
 */
class SalesManagoContactUpdateSender extends SalesManagoContactSender
{
    /**
     * @var CustomerDetailsRepository
     */
    protected $customerDetailsRepository;
    /**
     * @var LevelRepository
     */
    protected $levelRepository;
    /**
     * @var
     */
    protected $logger;
    /**
     * @var SalesManagoCustomerTranslator
     */
    protected $salesManagoTranslator;

    /**
     * @var CustomerStatusProvider
     */
    protected $customerStatusProvider;

    /**
     * @param CustomerId $customerId
     */
    public function customerUpdated($customerId)
    {
        if (empty($this->getConnector())) {
            return;
        }
        $data = $this->dataProvider->provideData($customerId);
        if ($data !== null) {
            $this->send($data['contact']['email'], $data);
        }
    }

    /**
     * @param string $customerId
     */
    public function customerCreated($customerId)
    {
        if (empty($this->getConnector())) {
            return;
        }
        $data = $this->dataProvider->provideInitalData($customerId);
        if ($data !== null) {
            $this->send($data['contact']['email'], $data);
        }
    }

    /**
     * @param string $email
     * @param array  $data
     */
    public function send($email, $data)
    {
        try {
            $response = $this->connector->getContactService()->upsert(
                $this->ownerEmail,
                $email,
                $data
            );
            $this->logger->debug(json_encode($response));
        } catch (RequestException $e) {
            $deadletter = new Deadletter($this->ownerEmail, $email, json_encode($data));
            $this->deadletterRepository->save($deadletter);
            $this->logger->error(json_encode($e->getMessage()));
        } catch (InvalidRequestException $e) {
            $deadletter = new Deadletter($this->ownerEmail, $email, json_encode($data));
            $this->deadletterRepository->save($deadletter);
            $this->logger->error(json_encode($e->getMessage()));
        }
    }
}
