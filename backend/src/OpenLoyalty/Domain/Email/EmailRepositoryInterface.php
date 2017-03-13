<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Domain\Email;

/**
 * Interface EmailRepositoryInterface.
 */
interface EmailRepositoryInterface
{
    /**
     * Save email.
     *
     * @param Email $email
     */
    public function save(Email $email);

    /**
     * Get email by id.
     *
     * @param EmailId $emailId
     *
     * @return null|Email
     */
    public function getById(EmailId $emailId);

    /**
     * Get email by key.
     *
     * @param string $key
     *
     * @return null|Email
     */
    public function getByKey($key);
}
