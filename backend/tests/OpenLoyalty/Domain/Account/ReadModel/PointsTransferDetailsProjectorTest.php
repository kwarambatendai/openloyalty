<?php

namespace OpenLoyalty\Domain\Customer\ReadModel;

use Broadway\ReadModel\InMemory\InMemoryRepository;
use Broadway\ReadModel\Testing\ProjectorScenarioTestCase;
use OpenLoyalty\Domain\Account\Account;
use OpenLoyalty\Domain\Account\AccountId;
use OpenLoyalty\Domain\Account\Event\PointsTransferHasBeenCanceled;
use OpenLoyalty\Domain\Account\Event\PointsTransferHasBeenExpired;
use OpenLoyalty\Domain\Account\Event\PointsWereAdded;
use OpenLoyalty\Domain\Account\Event\PointsWereSpent;
use OpenLoyalty\Domain\Account\Model\AddPointsTransfer;
use OpenLoyalty\Domain\Account\Model\SpendPointsTransfer;
use OpenLoyalty\Domain\Account\PointsTransferId;
use OpenLoyalty\Domain\Account\ReadModel\PointsTransferDetails;
use OpenLoyalty\Domain\Account\ReadModel\PointsTransferDetailsProjector;
use OpenLoyalty\Domain\Customer\Customer;
use OpenLoyalty\Domain\Account\CustomerId;
use OpenLoyalty\Domain\Pos\PosRepository;
use OpenLoyalty\Domain\Transaction\ReadModel\TransactionDetailsRepository;

/**
 * Class PointsTransferDetailsProjectorTest.
 */
class PointsTransferDetailsProjectorTest extends ProjectorScenarioTestCase
{
    /**
     * @var AccountId
     */
    protected $accountId;

    /**
     * @var CustomerId
     */
    protected $customerId;

    /**
     * {@inheritdoc}
     */
    protected function createProjector(InMemoryRepository $repository)
    {
        $this->accountId = new AccountId('00000000-0000-0000-0000-000000000000');
        $this->customerId = new CustomerId('00000000-1111-0000-0000-000000000000');

        $accountRepository = $this->getMockBuilder(InMemoryRepository::class)->getMock();
        $account = Account::createAccount($this->accountId, $this->customerId);

        $accountRepository->method('find')->willReturn($account);
        $customerRepository = $this->getMockBuilder(InMemoryRepository::class)->getMock();
        $customer = Customer::registerCustomer(
            new \OpenLoyalty\Domain\Customer\CustomerId($this->customerId->__toString()),
            $this->getCustomerData()
        );
        $customerRepository->method('find')->willReturn($customer);

        $transactionRepo = $this->getMockBuilder(TransactionDetailsRepository::class)->getMock();
        $transactionRepo->method('find')->willReturn(null);
        $posRepo = $this->getMockBuilder(PosRepository::class)->getMock();

        return new PointsTransferDetailsProjector($repository, $accountRepository, $customerRepository, $transactionRepo, $posRepo);
    }

    /**
     * @test
     */
    public function it_creates_a_read_model_on_add_points_transfer()
    {
        $pointsId = new PointsTransferId('00000000-0000-0000-0000-000000000000');
        $expectedReadModel = $this->createReadModel($pointsId);
        $expectedReadModel->setValue(100);
        $expectedReadModel->setState('active');
        $expectedReadModel->setType('adding');
        $date = new \DateTime();
        $expectedReadModel->setCreatedAt($date);
        $this->scenario->given(array())
            ->when(new PointsWereAdded($this->accountId, new AddPointsTransfer($pointsId, 100, $date)))
            ->then(array(
                $expectedReadModel,
            ));
    }

    /**
     * @test
     */
    public function it_creates_a_read_model_on_spending_points_transfer()
    {
        $pointsId = new PointsTransferId('00000000-0000-0000-0000-000000000000');
        $expectedReadModel = $this->createReadModel($pointsId);
        $expectedReadModel->setValue(100);
        $expectedReadModel->setState('active');
        $expectedReadModel->setType('spending');
        $date = new \DateTime();
        $expectedReadModel->setCreatedAt($date);
        $this->scenario->given(array())
            ->when(new PointsWereSpent($this->accountId, new SpendPointsTransfer($pointsId, 100, $date)))
            ->then(array(
                $expectedReadModel,
            ));
    }

    /**
     * @test
     */
    public function it_cancels_previously_added_transfer()
    {
        $pointsId = new PointsTransferId('00000000-0000-0000-0000-000000000000');
        $expectedReadModel = $this->createReadModel($pointsId);
        $expectedReadModel->setValue(100);
        $expectedReadModel->setState('canceled');
        $expectedReadModel->setType('adding');
        $date = new \DateTime();
        $expectedReadModel->setCreatedAt($date);
        $this->scenario
            ->given(array(
                new PointsWereAdded($this->accountId, new AddPointsTransfer($pointsId, 100, $date)),
            ))
            ->when(new PointsTransferHasBeenCanceled($this->accountId, $pointsId))
            ->then(array(
                $expectedReadModel,
            ));
    }

    /**
     * @test
     */
    public function it_expires_previously_added_transfer()
    {
        $pointsId = new PointsTransferId('00000000-0000-0000-0000-000000000000');
        $expectedReadModel = $this->createReadModel($pointsId);
        $expectedReadModel->setValue(100);
        $expectedReadModel->setState('expired');
        $expectedReadModel->setType('adding');
        $date = new \DateTime();
        $expectedReadModel->setCreatedAt($date);
        $this->scenario
            ->given(array(
                new PointsWereAdded($this->accountId, new AddPointsTransfer($pointsId, 100, $date)),
            ))
            ->when(new PointsTransferHasBeenExpired($this->accountId, $pointsId))
            ->then(array(
                $expectedReadModel,
            ));
    }

    /**
     * @test
     * @expectedException \OpenLoyalty\Domain\Account\Exception\CannotBeExpiredException
     */
    public function it_expires_only_adding_transfer()
    {
        $pointsId = new PointsTransferId('00000000-0000-0000-0000-000000000000');
        $this->scenario
            ->given(array(
                new PointsWereSpent($this->accountId, new SpendPointsTransfer($pointsId, 100)),
            ))
            ->when(new PointsTransferHasBeenExpired($this->accountId, $pointsId))
            ->then(array(
            ));
    }

    private function createReadModel(PointsTransferId $pointsTransferId)
    {
        $model = new PointsTransferDetails($pointsTransferId, $this->customerId, $this->accountId);
        $customerData = $this->getCustomerData();
        $model->setCustomerFirstName($customerData['firstName']);
        $model->setCustomerLastName($customerData['lastName']);
        $model->setCustomerPhone($customerData['phone']);
        $model->setCustomerEmail($customerData['email']);

        return $model;
    }

    private function getCustomerData()
    {
        return [
            'firstName' => 'John',
            'lastName' => 'Doe',
            'gender' => 'male',
            'email' => 'customer@open.com',
            'birthDate' => 653011200,
            'phone' => '123',
            'createdAt' => 1470646394,
            'loyaltyCardNumber' => '000000',
            'company' => [
                'name' => 'test',
                'nip' => 'nip',
            ],
            'address' => [
                'street' => 'Dmowskiego',
                'address1' => '21',
                'city' => 'Wrocław',
                'country' => 'PL',
                'postal' => '50-300',
                'province' => 'Dolnośląskie',
            ],
        ];
    }
}
