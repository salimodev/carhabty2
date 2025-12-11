<?php

namespace App\Repository;

use App\Entity\Offre;
use App\Entity\Users;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Offre>
 */
class OffreRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Offre::class);
    }

    //    /**
    //     * @return Offre[] Returns an array of Offre objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('o')
    //            ->andWhere('o.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('o.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Offre
    //    {
    //        return $this->createQueryBuilder('o')
    //            ->andWhere('o.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    // src/Repository/OffreRepository.php
public function countOffresByProprietaire($proprietaireId): int
{
    return $this->createQueryBuilder('o')
        ->join('o.demande', 'd')
        ->where('d.offrecompte = :userId')
        ->setParameter('userId', $proprietaireId)
        ->select('COUNT(o.id)')
        ->getQuery()
        ->getSingleScalarResult();
}


public function getOffresStatsForJson(): array
{
    $qb = $this->createQueryBuilder('o')
        ->addSelect("SUM(CASE WHEN o.status = 'en_attente' THEN 1 ELSE 0 END) as en_attente")
        ->addSelect("SUM(CASE WHEN o.status = 'acceptee' THEN 1 ELSE 0 END) as acceptee")
        ->addSelect("SUM(CASE WHEN o.status = 'refusee' THEN 1 ELSE 0 END) as refusee")
        ->addSelect("COUNT(o.id) as total");

    return $qb->getQuery()->getSingleResult() ?? [
        'en_attente' => 0,
        'acceptee' => 0,
        'refusee' => 0,
        'total' => 0
    ];
}


public function countOffresParJour(): array
{
    $qb = $this->createQueryBuilder('o')
        ->select("DATE(o.createdAt) as jour, COUNT(o.id) as total")
        ->groupBy('jour')
        ->orderBy('jour', 'ASC');

    return $qb->getQuery()->getResult();
}

public function countByVendeurNeuf(Users $user): int
{
    return (int) $this->createQueryBuilder('o')
        ->select('COUNT(o.id)')
        ->andWhere('o.user = :user')
        ->setParameter('user', $user)
        ->getQuery()
        ->getSingleScalarResult();
}


public function countByVendeurOccasion(Users $user): int
{
    return (int) $this->createQueryBuilder('o')
        ->select('COUNT(o.id)')
        ->andWhere('o.user = :user')
        ->setParameter('user', $user)
        ->getQuery()
        ->getSingleScalarResult();
}

 public function findByMecanicien(int $mecanicienId): array
    {
        return $this->createQueryBuilder('o')
            ->join('o.demande', 'd')            // on rejoint la demande liée à l’offre
            ->join('d.offrecompte', 'm')       // on rejoint le mécanicien (offrecompte)
            ->andWhere('m.id = :mecanicienId') // on filtre par le mécanicien
            ->setParameter('mecanicienId', $mecanicienId)
            ->orderBy('o.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

}
