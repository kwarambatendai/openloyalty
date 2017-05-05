<?php

namespace OpenLoyalty\Domain\Seller\Validator;

use Broadway\ReadModel\RepositoryInterface;
use OpenLoyalty\Domain\Seller\ReadModel\SellerDetails;
use OpenLoyalty\Domain\Seller\SellerId;

/**
 * Class SellerUniqueValidatorTest.
 */
class SellerUniqueValidatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RepositoryInterface
     */
    protected $sellerDetailsRepository;

    public function setUp()
    {
        $seller1 = new SellerDetails(new SellerId('00000000-0000-0000-0000-000000000011'));
        $seller1->setEmail('a@a.com');
        $seller2 = new SellerDetails(new SellerId('00000000-0000-0000-0000-000000000012'));
        $seller2->setEmail('b@b.com');
        $seller3 = new SellerDetails(new SellerId('00000000-0000-0000-0000-000000000012'));
        $seller3->setEmail('c@c.com');
        $sellers = [
            'a@a.com' => $seller1,
            'b@b.com' => $seller2,
            'c@c.com' => $seller3,
        ];

        $this->sellerDetailsRepository = $this->getMockBuilder('Broadway\ReadModel\RepositoryInterface')->getMock();
        $this->sellerDetailsRepository->method('findBy')->with(
            $this->arrayHasKey('email')
        )
            ->will($this->returnCallback(function($params) use ($sellers) {
                if (isset($params['email'])) {
                    $email = $params['email'];

                    return array_filter($sellers, function(SellerDetails $sellerDetails) use ($email) {
                        if ($sellerDetails->getEmail() == $email) {
                            return true;
                        }

                        return false;
                    });
                }

                return [];
            }));
    }

    /**
     * @test
     * @expectedException \OpenLoyalty\Domain\Seller\Exception\EmailAlreadyExistsException
     */
    public function it_throws_exception_when_email_is_not_unique()
    {
        $validator = new SellerUniqueValidator($this->sellerDetailsRepository);
        $validator->validateEmailUnique('a@a.com');
    }

    /**
     * @test
     */
    public function it_not_throwing_exception_when_email_belongs_to_user()
    {
        $validator = new SellerUniqueValidator($this->sellerDetailsRepository);
        $validator->validateEmailUnique('a@a.com', new SellerId('00000000-0000-0000-0000-000000000011'));
    }

    /**
     * @test
     */
    public function it_not_throwing_exception_when_email_is_unique()
    {
        $validator = new SellerUniqueValidator($this->sellerDetailsRepository);
        $validator->validateEmailUnique('a2@a.com');
    }
}
