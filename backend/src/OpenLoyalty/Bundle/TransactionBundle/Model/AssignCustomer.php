<?php

namespace OpenLoyalty\Bundle\TransactionBundle\Model;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * Class AssignCustomer.
 */
class AssignCustomer
{
    /**
     * @var string
     * @Assert\NotBlank()
     */
    protected $transactionDocumentNumber;

    /**
     * @var string
     */
    protected $customerId;

    /**
     * @var string
     */
    protected $customerLoyaltyCardNumber;

    /**
     * @var string
     */
    protected $customerPhoneNumber;

    /**
     * @param ExecutionContextInterface $context
     * @Assert\Callback()
     */
    public function validateCustomer(ExecutionContextInterface $context)
    {
        $set = 0;
        if ($this->customerLoyaltyCardNumber) {
            ++$set;
        }
        if ($this->customerId) {
            ++$set;
        }
        if ($this->customerPhoneNumber) {
            ++$set;
        }

        if ($set == 0) {
            $context->buildViolation('Fill at least one customer field')->atPath('customerId')->addViolation();
            $context->buildViolation('Fill at least one customer field')->atPath('customerLoyaltyCardNumber')->addViolation();
            $context->buildViolation('Fill at least one customer field')->atPath('customerPhoneNumber')->addViolation();
        }
    }

    /**
     * @return string
     */
    public function getTransactionDocumentNumber()
    {
        return $this->transactionDocumentNumber;
    }

    /**
     * @param string $transactionDocumentNumber
     */
    public function setTransactionDocumentNumber($transactionDocumentNumber)
    {
        $this->transactionDocumentNumber = $transactionDocumentNumber;
    }

    /**
     * @return string
     */
    public function getCustomerId()
    {
        return $this->customerId;
    }

    /**
     * @param string $customerId
     */
    public function setCustomerId($customerId)
    {
        $this->customerId = $customerId;
    }

    /**
     * @return string
     */
    public function getCustomerLoyaltyCardNumber()
    {
        return $this->customerLoyaltyCardNumber;
    }

    /**
     * @param string $customerLoyaltyCardNumber
     */
    public function setCustomerLoyaltyCardNumber($customerLoyaltyCardNumber)
    {
        $this->customerLoyaltyCardNumber = $customerLoyaltyCardNumber;
    }

    /**
     * @return string
     */
    public function getCustomerPhoneNumber()
    {
        return $this->customerPhoneNumber;
    }

    /**
     * @param string $customerPhoneNumber
     */
    public function setCustomerPhoneNumber($customerPhoneNumber)
    {
        $this->customerPhoneNumber = $customerPhoneNumber;
    }
}
