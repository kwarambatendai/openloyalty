<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Bundle\UserBundle\ParamConverter;

use Assert\InvalidArgumentException;
use OpenLoyalty\Domain\Customer\ReadModel\CustomerDetails;
use OpenLoyalty\Domain\Customer\ReadModel\CustomerDetailsRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Assert\Assertion as Assert;

/**
 * Class CustomerDetailsParamConverter.
 */
class CustomerDetailsParamConverter implements ParamConverterInterface
{
    /**
     * @var CustomerDetailsRepository
     */
    protected $repository;

    /**
     * CustomerDetailsParamConverter constructor.
     *
     * @param CustomerDetailsRepository $repository
     */
    public function __construct(CustomerDetailsRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Stores the object in the request.
     *
     * @param Request        $request       The request
     * @param ParamConverter $configuration Contains the name, class and options of the object
     *
     * @return bool True if the object has been successfully set, else false
     */
    public function apply(Request $request, ParamConverter $configuration)
    {
        $name = $configuration->getName();

        if (null === $request->attributes->get($name, false)) {
            $configuration->setIsOptional(true);
        }
        $value = $request->attributes->get($name);
        try {
            Assert::uuid($value);
            $object = $this->repository->find($value);
        } catch (InvalidArgumentException $e) {
            $obj = $this->repository->findBy(['loyaltyCardNumber' => $value]);
            if (count($obj) == 0) {
                $object = null;
            } else {
                $object = reset($obj);
            }
        }

        if (null === $object && false === $configuration->isOptional()) {
            throw new NotFoundHttpException(sprintf('%s object not found.', $configuration->getClass()));
        }
        $request->attributes->set($name, $object);

        return true;
    }

    /**
     * Checks if the object is supported.
     *
     * @param ParamConverter $configuration Should be an instance of ParamConverter
     *
     * @return bool True if the object is supported, else false
     */
    public function supports(ParamConverter $configuration)
    {
        return $configuration->getClass() === CustomerDetails::class;
    }
}
