<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Domain\Email\Command;

use OpenLoyalty\Domain\Email\EmailId;

/**
 * Class UpdateEmail.
 */
final class UpdateEmail extends EmailCommand
{
    /**
     * Email data.
     * 
     * @var array
     */
    private $data;

    /**
     * {@inheritdoc}
     *
     * @param array $data
     */
    public function __construct(EmailId $emailId, array $data)
    {
        parent::__construct($emailId);

        $this->validateCommand($data);

        $this->data = $data;
    }

    /**
     * Get email data.
     *
     * @return array
     */
    public function getEmailData()
    {
        return $this->data;
    }
}
