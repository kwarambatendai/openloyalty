<?php

namespace OpenLoyalty\Domain\Segment;

use OpenLoyalty\Domain\Identifier;
use Assert\Assertion as Assert;

/**
 * Class CriterionId.
 */
class CriterionId implements Identifier
{
    /**
     * @var string
     */
    protected $criterionId;

    /**
     * CriterionId constructor.
     *
     * @param string $criterionId
     */
    public function __construct($criterionId)
    {
        Assert::string($criterionId);
        Assert::uuid($criterionId);

        $this->criterionId = $criterionId;
    }

    public function __toString()
    {
        return $this->criterionId;
    }
}
