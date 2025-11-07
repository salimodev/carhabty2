<?php

namespace App\Repository;

use App\Entity\Demande;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Demande>
 */
class DemandeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Demande::class);
    }

//    /**
//     * @return Demande[] Returns an array of Demande objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('d.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Demande
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
public function countByUser($user): int
{
    return $this->createQueryBuilder('d')
        ->select('COUNT(d.id)')
        ->where('d.offrecompte = :user')
        ->setParameter('user', $user)
        ->getQuery()
        ->getSingleScalarResult();
}

// src/Repository/DemandeRepository.php
public function findLastTen(): array
{
    return $this->createQueryBuilder('d')
        ->leftJoin('d.Pieces', 'p')->addSelect('p')
        ->leftJoin('d.offrecompte', 'u')->addSelect('u')
        ->orderBy('d.datecreate', 'DESC')
        ->setMaxResults(10)
        ->getQuery()
        ->getResult();
}

public function findAllDemandes(): array
{
    return $this->createQueryBuilder('d')
        ->orderBy('d.datecreate', 'DESC')
        ->getQuery()
        ->getResult();
}


public function filterDemandes($marque = null, $zone = null, $date = null, $type = null): array
{
    $qb = $this->createQueryBuilder('d');

    if (!empty($marque)) {
        $qb->andWhere('d.marque = :marque')
           ->setParameter('marque', $marque);
    }

    if (!empty($zone)) {
        $qb->andWhere('d.zone = :zone')
           ->setParameter('zone', $zone);
    }

    if (!empty($date) && !in_array($date, ['recent', 'ancien'])) {
        try {
            $qb->andWhere('d.dateCreation BETWEEN :start AND :end')
               ->setParameter('start', new \DateTime($date . ' 00:00:00'))
               ->setParameter('end', new \DateTime($date . ' 23:59:59'));
        } catch (\Exception $e) {}
    }

    // 🔹 Filtrage selon type (neuf / occasion)
    if (!empty($type)) {
        if ($type === 'neuf') {
            $qb->andWhere('d.vendeurneuf = 1');
        } elseif ($type === 'occasion') {
            $qb->andWhere('d.vendeuroccasion = 1');
        }
    }

    // 🔹 Tri par date
    if ($date === 'recent') {
        $qb->orderBy('d.datecreate', 'DESC');
    } elseif ($date === 'ancien') {
        $qb->orderBy('d.datecreate', 'ASC');
    }

    return $qb->getQuery()->getResult();
}

public function findAllDemandesQB(): \Doctrine\ORM\QueryBuilder
{
    return $this->createQueryBuilder('d')
                ->orderBy('d.datecreate', 'DESC');
}

public function findLatestForVendeurNeuf(string $zoneVendeur): array
{
    return $this->createQueryBuilder('d')
        ->where('d.vendeurneuf = 1')
        ->andWhere('d.zone = :zoneVendeur OR d.zone = :zoneTunisie')
        ->setParameter('zoneVendeur', $zoneVendeur)
        ->setParameter('zoneTunisie', 'Toute la Tunisie')
        ->orderBy('d.datecreate', 'DESC')
        ->setMaxResults(5)
        ->getQuery()
        ->getResult();
}

public function findAllForVendeurNeuf(string $zoneVendeur): array
{
    return $this->createQueryBuilder('d')
        ->where('d.vendeurneuf = 1')
        ->andWhere('d.zone = :zoneVendeur OR d.zone = :zoneTunisie')
        ->setParameter('zoneVendeur', $zoneVendeur)
        ->setParameter('zoneTunisie', 'Toute la Tunisie')
        ->orderBy('d.datecreate', 'DESC')
        ->getQuery()
        ->getResult();
}

public function filterDemandesvendeurNeuf($marque = null, $zone = null, $date = null, $type = null): array
{
    $qb = $this->createQueryBuilder('d');

    if (!empty($marque)) {
        $qb->andWhere('d.marque = :marque')
           ->setParameter('marque', $marque);
    }

    if (!empty($zone)) {
        $qb->andWhere('(d.zone = :zone OR d.zone = :tunis)')
           ->setParameter('zone', $zone)
           ->setParameter('tunis', 'Toute la Tunisie'); // ou 'Toutes les zones', selon ta base
    }

    if (!empty($date) && !in_array($date, ['recent', 'ancien'])) {
        try {
            $qb->andWhere('d.dateCreation BETWEEN :start AND :end')
               ->setParameter('start', new \DateTime($date . ' 00:00:00'))
               ->setParameter('end', new \DateTime($date . ' 23:59:59'));
        } catch (\Exception $e) {}
    }

    // 🔹 Type (neuf / occasion)
    if (!empty($type)) {
        if ($type === 'neuf') {
            $qb->andWhere('d.vendeurneuf = 1');
        } elseif ($type === 'occasion') {
            $qb->andWhere('d.vendeuroccasion = 1');
        }
    }

    // 🔹 Tri
    if ($date === 'recent') {
        $qb->orderBy('d.datecreate', 'DESC');
    } elseif ($date === 'ancien') {
        $qb->orderBy('d.datecreate', 'ASC');
    }

    return $qb->getQuery()->getResult();
}

// src/Repository/DemandeRepository.php
public function countDemandesDispoVendeur(string $zoneVendeur): int
{
    return $this->createQueryBuilder('d')
        ->select('COUNT(d.id)')
        ->where('(d.zone = :zone OR d.zone = :toute)')
        ->andWhere('d.vendeurNeuf = 1')
        ->setParameter('zone', $zoneVendeur)
        ->setParameter('toute', 'Toute la Tunisie')
        ->getQuery()
        ->getSingleScalarResult();
}

 
}
