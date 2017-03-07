<?php

namespace OpenLoyalty\Domain\Customer;

use Broadway\EventSourcing\EventSourcedAggregateRoot;
use OpenLoyalty\Domain\Customer\Event\CampaignUsageWasChanged;
use OpenLoyalty\Domain\Customer\Event\CampaignWasBoughtByCustomer;
use OpenLoyalty\Domain\Customer\Event\CustomerDetailsWereUpdated;
use OpenLoyalty\Domain\Customer\Event\CustomerWasActivated;
use OpenLoyalty\Domain\Customer\Event\CustomerWasDeactivated;
use OpenLoyalty\Domain\Customer\Event\PosWasAssignedToCustomer;
use OpenLoyalty\Domain\Customer\Event\CustomerWasMovedToLevel;
use OpenLoyalty\Domain\Customer\Model\Address;
use OpenLoyalty\Domain\Customer\Model\Coupon;
use OpenLoyalty\Domain\Customer\Model\Gender;
use OpenLoyalty\Domain\Customer\Event\CustomerAddressWasUpdated;
use OpenLoyalty\Domain\Customer\Event\CustomerCompanyDetailsWereUpdated;
use OpenLoyalty\Domain\Customer\Event\CustomerLoyaltyCardNumberWasUpdated;
use OpenLoyalty\Domain\Customer\Event\CustomerWasRegistered;
use OpenLoyalty\Domain\Customer\Model\Company;
use Assert\Assertion as Assert;

/**
 * Class Customer.
 */
class Customer extends EventSourcedAggregateRoot
{
    /**
     * @var CustomerId
     */
    protected $id;

    /**
     * @var string
     */
    protected $firstName;

    /**
     * @var string
     */
    protected $lastName;

    /**
     * @var Gender
     */
    protected $gender;

    /**
     * @var string
     */
    protected $email;

    /**
     * @var string
     */
    protected $phone;

    /**
     * @var \DateTime
     */
    protected $birthDate;

    /**
     * @var Address
     */
    protected $address;

    /**
     * @var string
     */
    protected $loyaltyCardNumber;

    /**
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * @var \DateTime
     */
    protected $firstPurchaseAt;

    protected $agreement1 = false;

    protected $agreement2 = false;

    protected $agreement3 = false;

    /**
     * @var Company
     */
    protected $company = null;

    /**
     * @return string
     */
    public function getAggregateRootId()
    {
        return $this->id;
    }

    public static function registerCustomer(CustomerId $customerId, array $customerData)
    {
        $customer = new self();
        $customer->register($customerId, $customerData);

        return $customer;
    }

    public function updateAddress(array $addressData)
    {
        $this->apply(
            new CustomerAddressWasUpdated($this->id, $addressData)
        );
    }

    public function updateCompanyDetails(array $companyData)
    {
        $this->apply(
            new CustomerCompanyDetailsWereUpdated($this->id, $companyData)
        );
    }

    public function updateLoyaltyCardNumber($cardNumber)
    {
        $this->apply(
            new CustomerLoyaltyCardNumberWasUpdated($this->id, $cardNumber)
        );
    }

    public function addToLevel(LevelId $levelId = null, $manually = false)
    {
        $this->apply(
            new CustomerWasMovedToLevel($this->getId(), $levelId, $manually)
        );
    }

    private function register(CustomerId $userId, array $customerData)
    {
        $this->apply(
            new CustomerWasRegistered($userId, $customerData)
        );
    }

    public function updateCustomerDetails(array $customerData)
    {
        $this->apply(
            new CustomerDetailsWereUpdated($this->getId(), $customerData)
        );
    }

    public function assignPosToCustomer(PosId $posId)
    {
        $this->apply(
            new PosWasAssignedToCustomer($this->getId(), $posId)
        );
    }

    public function buyCampaign(CampaignId $campaignId, $campaignName, $costInPoints, Coupon $coupon)
    {
        $this->apply(
            new CampaignWasBoughtByCustomer($this->getId(), $campaignId, $campaignName, $costInPoints, $coupon)
        );
    }

    public function changeCampaignUsage(CampaignId $campaignId, Coupon $coupon, $used)
    {
        $this->apply(
            new CampaignUsageWasChanged($this->getId(), $campaignId, $coupon, $used)
        );
    }

    public function deactivate()
    {
        $this->apply(
            new CustomerWasDeactivated($this->getId())
        );
    }

    public function activate()
    {
        $this->apply(
            new CustomerWasActivated($this->getId())
        );
    }

    protected function applyCustomerWasRegistered(CustomerWasRegistered $event)
    {
        $data = $event->getCustomerData();
        $data = $this->resolveOptions($data);

        $this->id = $event->getCustomerId();
        $this->setFirstName($data['firstName']);
        $this->setLastName($data['lastName']);
        if (isset($data['phone'])) {
            $this->setPhone($data['phone']);
        }
        if (isset($data['gender'])) {
            $this->setGender(new Gender($data['gender']));
        }
        $this->setEmail($data['email']);
        if (isset($data['birthDate'])) {
            $this->setBirthDate($data['birthDate']);
        }

        if (isset($data['agreement1'])) {
            $this->setAgreement1($data['agreement1']);
        }

        if (isset($data['agreement2'])) {
            $this->setAgreement2($data['agreement2']);
        }

        if (isset($data['agreement3'])) {
            $this->setAgreement3($data['agreement3']);
        }

        $this->setCreatedAt($data['createdAt']);
    }

    protected function applyCustomerDetailsWereUpdated(CustomerDetailsWereUpdated $event)
    {
        $data = $event->getCustomerData();

        if (!empty($data['firstName'])) {
            $this->setFirstName($data['firstName']);
        }
        if (!empty($data['lastName'])) {
            $this->setLastName($data['lastName']);
        }
        if (isset($data['phone'])) {
            $this->setPhone($data['phone']);
        }
        if (!empty($data['gender'])) {
            $this->setGender(new Gender($data['gender']));
        }
        if (!empty($data['email'])) {
            $this->setEmail($data['email']);
        }
        if (!empty($data['birthDate'])) {
            $this->setBirthDate($data['birthDate']);
        }

        if (isset($data['agreement1'])) {
            $this->setAgreement1($data['agreement1']);
        }

        if (isset($data['agreement2'])) {
            $this->setAgreement2($data['agreement2']);
        }

        if (isset($data['agreement3'])) {
            $this->setAgreement3($data['agreement3']);
        }
    }

    protected function applyCustomerAddressWasUpdated(CustomerAddressWasUpdated $event)
    {
        $this->setAddress(Address::fromData($event->getAddressData()));
    }

    protected function applyCustomerCompanyDetailsWereUpdated(CustomerCompanyDetailsWereUpdated $event)
    {
        $companyData = $event->getCompanyData();
        if (!$companyData || count($companyData) == 0) {
            $this->setCompany(null);
        } else {
            $this->setCompany(new Company($companyData['name'], $event->getCompanyData()['nip']));
        }
    }

    protected function applyCustomerLoyaltyCardNumberWasUpdated(CustomerLoyaltyCardNumberWasUpdated $event)
    {
        $this->setLoyaltyCardNumber($event->getCardNumber());
    }

    /**
     * @return CustomerId
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     */
    public function setFirstName($firstName)
    {
        Assert::notEmpty($firstName);
        $this->firstName = $firstName;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     */
    public function setLastName($lastName)
    {
        Assert::notEmpty($lastName);
        $this->lastName = $lastName;
    }

    /**
     * @return string
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * @param Gender $gender
     */
    public function setGender(Gender $gender)
    {
        Assert::notEmpty($gender);
        $this->gender = $gender;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        Assert::notEmpty($email);
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param string $phone
     */
    public function setPhone($phone)
    {
        Assert::notEmpty($phone);
        $this->phone = $phone;
    }

    /**
     * @return \DateTime
     */
    public function getBirthDate()
    {
        return $this->birthDate;
    }

    /**
     * @param \DateTime $birthDate
     */
    public function setBirthDate(\DateTime $birthDate)
    {
        Assert::notEmpty($birthDate);
        $this->birthDate = $birthDate;
    }

    /**
     * @return Address
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param Address $address
     */
    public function setAddress($address)
    {
        Assert::notEmpty($address);
        $this->address = $address;
    }

    /**
     * @return string
     */
    public function getLoyaltyCardNumber()
    {
        return $this->loyaltyCardNumber;
    }

    /**
     * @param string $loyaltyCardNumber
     */
    public function setLoyaltyCardNumber($loyaltyCardNumber)
    {
        Assert::notEmpty($loyaltyCardNumber);
        $this->loyaltyCardNumber = $loyaltyCardNumber;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        Assert::notEmpty($createdAt);
        $this->createdAt = $createdAt;
    }

    /**
     * @return \DateTime
     */
    public function getFirstPurchaseAt()
    {
        return $this->firstPurchaseAt;
    }

    /**
     * @param \DateTime $firstPurchaseAt
     */
    public function setFirstPurchaseAt($firstPurchaseAt)
    {
        $this->firstPurchaseAt = $firstPurchaseAt;
    }

    /**
     * @return bool
     */
    public function isCompany()
    {
        return $this->company != null ? true : false;
    }

    /**
     * @param Company $company
     */
    public function setCompany(Company $company = null)
    {
        $this->company = $company;
    }

    /**
     * @return bool
     */
    public function isAgreement1()
    {
        return $this->agreement1;
    }

    /**
     * @param bool $agreement1
     */
    public function setAgreement1($agreement1)
    {
        $this->agreement1 = $agreement1;
    }

    /**
     * @return bool
     */
    public function isAgreement2()
    {
        return $this->agreement2;
    }

    /**
     * @param bool $agreement2
     */
    public function setAgreement2($agreement2)
    {
        $this->agreement2 = $agreement2;
    }

    /**
     * @return bool
     */
    public function isAgreement3()
    {
        return $this->agreement3;
    }

    /**
     * @param bool $agreement3
     */
    public function setAgreement3($agreement3)
    {
        $this->agreement3 = $agreement3;
    }

    public static function resolveOptions($data)
    {
        $defaults = [
            'firstName' => null,
            'lastName' => null,
            'address' => null,
            'gender' => null,
            'birthDate' => null,
            'company' => null,
            'loyaltyCardNumber' => null,
        ];

        return array_merge($defaults, $data);
    }
}
