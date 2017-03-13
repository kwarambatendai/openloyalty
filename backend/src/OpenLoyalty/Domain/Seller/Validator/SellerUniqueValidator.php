<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Domain\Seller\Validator;

use Broadway\ReadModel\RepositoryInterface;
use OpenLoyalty\Domain\Seller\Exception\EmailAlreadyExistsException;
use OpenLoyalty\Domain\Seller\ReadModel\SellerDetails;
use OpenLoyalty\Domain\Seller\SellerId;

/**
 * Class SellerUniqueValidator.
 */
class SellerUniqueValidator
{
    /**
     * @var RepositoryInterface
     */
    protected $sellerDetailsRepository;

    /**
     * CustomerUniqueValidator constructor.
     *
     * @param RepositoryInterface $customerDetailsRepository
     */
    public function __construct(RepositoryInterface $customerDetailsRepository)
    {
        $this->sellerDetailsRepository = $customerDetailsRepository;
    }

    public function validateEmailUnique($email, SellerId $sellerId = null)
    {
        $sellers = $this->sellerDetailsRepository->findBy(['email' => $email]);
        if ($sellerId) {
            /** @var SellerDetails $seller */
            foreach ($sellers as $key => $seller) {
                if ($seller->getId() == $sellerId->__toString()) {
                    unset($sellers[$key]);
                }
            }
        }

        if (count($sellers) > 0) {
            throw new EmailAlreadyExistsException();
        }
    }
}
