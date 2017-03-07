<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace OpenLoyalty\Bundle\PosBundle\ParamConverter;

use OpenLoyalty\Domain\Pos\Pos;
use OpenLoyalty\Domain\Pos\PosId;
use OpenLoyalty\Domain\Pos\PosRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class PosParamConverter.
 */
class PosParamConverter implements ParamConverterInterface
{
    /**
     * @var PosRepository
     */
    protected $repository;

    /**
     * PosParamConverter constructor.
     *
     * @param PosRepository $repository
     */
    public function __construct(PosRepository $repository)
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
        $options = $configuration->getOptions();

        if (isset($options['identifier']) && $options['identifier']) {
            $identifier = true;
        } else {
            $identifier = false;
        }

        if (null === $request->attributes->get($name, false)) {
            $configuration->setIsOptional(true);
        }
        $value = $request->attributes->get($name);
        if ($identifier) {
            $object = $this->repository->oneByIdentifier($value);
        } else {
            $object = $this->repository->byId(new PosId($value));
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
        return $configuration->getClass() === Pos::class;
    }
}
