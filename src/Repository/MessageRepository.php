<?php

namespace App\Repository;

use App\Entity\Message;
use App\Enum\MessageStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Message>
 *
 * @method Message|null find($id, $lockMode = null, $lockVersion = null)
 * @method Message|null findOneBy(array $criteria, array $orderBy = null)
 * @method Message[]    findAll()
 * @method Message[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Message::class);
    }

    /**
     * I am changing this method and its name for clarity.
     * I will filter by status hence the name.
     * If the status is not correct, an empty array will be returned.
     *
     * Filters messages by status
     *
     * @return Message[]
     */
    public function filterByStatus(?string $status): array
    {
        /*
         * This is based on the openapi.yaml specification for parameter status.
         * I know that the valid status query parameter can be null
         * and any value defined in enum MessageStatus.
         *
         * But we allow any possible query parameter since it is a filter, and we don't want to
         * send a bad request exposing in a way all of our different statuses.
         *
         * Also if this is not true, I don't want to send the query to the database
         * because this operation is expensive both on computing resources and
         * AWS RDS, for example, charges for data transfer so avoid whenever possible!!!!
         * And I know that an empty array will be returned.
         */
        if (!MessageStatus::isValidForFilterByStatus($status)) {
            return [];
        }

        /*
         * If we want to use query builder for more complex queries
         * $messages = $this->createQueryBuilder('messages')
         * ->where('messages.status = :status')
         * ->setParameter('status', $status)
         * ->getQuery()
         * ->getResult();
         *
         * Both methods are better for SQL Injection since it automatically handles parameter escaping.
         * This is according to Symfony documentation. I chose the second one for simplicity
         * and because it is built in MessageRepository class
         */
        return $status
            ? $this->findBy(['status' => $status])
            : $this->findAll();
    }
}
