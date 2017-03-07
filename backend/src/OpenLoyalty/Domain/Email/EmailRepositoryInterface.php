<?php
/*
 * This file is part of the "OpenLoyalty" package.
 *
 * (c) Divante Sp. z o. o.
 *
 * Author: Cezary Olejarczyk
 * Date: 01.02.17 08:56
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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
