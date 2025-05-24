<?php

namespace App\Repository;

use App\Entity\Message;
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
     * I will filter by request hence the name
     * @param string|null $status
     * @return Message[]
     */
    public function filterByStatus(?string $status): array
    {
        /**
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
