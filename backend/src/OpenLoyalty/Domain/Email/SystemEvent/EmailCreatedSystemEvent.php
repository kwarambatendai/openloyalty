<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Domain\Email\SystemEvent;

use OpenLoyalty\Domain\Email\EmailId;

/**
 * Class EmailCreatedSystemEvent.
 */
class EmailCreatedSystemEvent extends EmailSystemEvent
{
    /**
     * @var array
     */
    protected $data;

    /**
     * {@inheritdoc}
     *
     * @param array|null $data
     */
    public function __construct(EmailId $emailId, array $data = null)
    {
        parent::__construct($emailId);

        $this->data = $data;
    }

    /**
     * @return array
     */
    public function getEmailData()
    {
        return $this->data;
    }
}
