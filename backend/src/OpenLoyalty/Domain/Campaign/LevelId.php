<?php

namespace OpenLoyalty\Domain\Campaign;

use OpenLoyalty\Domain\Identifier;
use Assert\Assertion as Assert;

/**
 * Class LevelId.
 */
class LevelId implements Identifier
{
    /**
     * @var string
     */
    protected $levelId;

    /**
     * LevelId constructor.
     *
     * @param string $levelId
     */
    public function __construct($levelId)
    {
        Assert::string($levelId);
        Assert::uuid($levelId);
        $this->levelId = $levelId;
    }

    public function __toString()
    {
        return $this->levelId;
    }
}
