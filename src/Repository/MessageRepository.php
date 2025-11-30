<?php

namespace App\Repository;

use App\Entity\Message;
use App\Entity\Users;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Message>
 */
class MessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Message::class);
    }

    //    /**
    //     * @return Message[] Returns an array of Message objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('m.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Message
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }


public function findConversation($user1, $user2)
{
    return $this->createQueryBuilder('m')
        ->where('(m.sender = :user1 AND m.receiver = :user2)')
        ->orWhere('(m.sender = :user2 AND m.receiver = :user1)')
        ->setParameter('user1', $user1)
        ->setParameter('user2', $user2)
        ->orderBy('m.createdAt', 'ASC')
        ->getQuery()
        ->getResult();
}


public function findUsersWhoSentMessagesTo($user)
{
    return $this->createQueryBuilder('m')
        ->join('m.sender', 'u')
        ->where('m.receiver = :user')
        ->setParameter('user', $user)
        ->select('DISTINCT u')  // funktioniert nur wenn Root auch im Select ist
        ->addSelect('m')        // Root hinzufügen → Fehler verschwindet
        ->getQuery()
        ->getResult();
}


}
