<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace OpenLoyalty\Domain\EarningRule\Model;

use Assert\Assertion as Assert;

/**
 * Class UsageSubject.
 */
class UsageSubject
{
    protected $subjectId;

    public function __construct($subjectId)
    {
        Assert::string($subjectId);
        Assert::uuid($subjectId);

        $this->subjectId = $subjectId;
    }

    /**
     * @return mixed
     */
    public function getSubjectId()
    {
        return $this->subjectId;
    }

    public function __toString()
    {
        return $this->subjectId;
    }
}
