<?php
/*
 * This file is part of the "OpenLoyalty" package.
 *
 * (c) Divante Sp. z o. o.
 *
 * Author: Cezary Olejarczyk
 * Date: 03.02.17 10:08
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace OpenLoyalty\Infrastructure\Email\ReadModel\Doctrine\Repository;

use Doctrine\DBAL\Connection;
use OpenLoyalty\Domain\Email\EmailId;
use OpenLoyalty\Domain\Email\ReadModel\DoctrineEmailRepositoryInterface;
use OpenLoyalty\Domain\Email\ReadModel\Email;

/**
 * Class DoctrineEmailRepository.
 */
class DoctrineEmailRepository implements DoctrineEmailRepositoryInterface
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * DoctrineEmailRepository constructor.
     *
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * {@inheritdoc}
     */
    public function getById(EmailId $emailId)
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        $queryBuilder->select('email.*')
                     ->from('ol__email', 'email')
                     ->where('email.email_id = :emailId')
                     ->setParameter('emailId', $emailId->__toString());

        $emailData = $this->connection->fetchAssoc(
            $queryBuilder->getSQL(),
            $queryBuilder->getParameters()
        );

        return $this->hydrate($emailData);
    }

    /**
     * {@inheritdoc}
     */
    public function getByKey($key)
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        $queryBuilder->select('email.*')
                     ->from('ol__email', 'email')
                     ->where('email.key = :key')
                     ->setParameter('key', $key);

        $emailData = $this->connection->fetchAssoc(
            $queryBuilder->getSQL(),
            $queryBuilder->getParameters()
        );

        if (empty($emailData)) {
            return;
        }

        return $this->hydrate($emailData);
    }

    /**
     * {@inheritdoc}
     */
    public function getAll()
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        $queryBuilder->select('email.*')
                     ->from('ol__email', 'email');

        $emailsData = $this->connection->fetchAll(
            $queryBuilder->getSQL(),
            $queryBuilder->getParameters()
        );

        if (empty($emailsData)) {
            return;
        }

        return array_map([$this, 'hydrate'], $emailsData);
    }

    /**
     * Hydrate array to object.
     *
     * @param array $emailData
     *
     * @return Email
     */
    protected function hydrate(array $emailData)
    {
        return new Email(
            new EmailId($emailData['email_id']),
            $emailData['key'],
            $emailData['subject'],
            $emailData['content'],
            $emailData['sender_name'],
            $emailData['sender_email'],
            new \DateTime($emailData['updated_at'])
        );
    }
}
