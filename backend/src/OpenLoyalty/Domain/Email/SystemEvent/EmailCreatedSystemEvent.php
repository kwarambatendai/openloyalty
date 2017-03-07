<?php
/*
 * This file is part of the "OpenLoyalty" package.
 *
 * (c) Divante Sp. z o. o.
 *
 * Author: Cezary Olejarczyk
 * Date: 02.02.17 14:33
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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
