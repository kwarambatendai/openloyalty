<?php

namespace OpenLoyalty\Bundle\LevelBundle\ParamConverter;

use OpenLoyalty\Domain\Level\Level;
use OpenLoyalty\Domain\Level\LevelId;
use OpenLoyalty\Domain\Level\LevelRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class LevelParamConverter.
 */
class LevelParamConverter implements ParamConverterInterface
{
    /**
     * @var LevelRepository
     */
    protected $repository;

    /**
     * LevelParamConverter constructor.
     *
     * @param LevelRepository $repository
     */
    public function __construct(LevelRepository $repository)
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
        $object = $this->repository->byId(new LevelId($value));

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
        return $configuration->getClass() === Level::class;
    }
}
