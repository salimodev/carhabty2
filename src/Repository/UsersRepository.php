<?php

namespace App\Repository;

use App\Entity\Users;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<Users>
 */
class UsersRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Users::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof Users) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    //    /**
    //     * @return Users[] Returns an array of Users objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('u.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Users
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
   
public function countRole(string $role): int
{
    $conn = $this->getEntityManager()->getConnection();

    $sql = 'SELECT COUNT(*) AS total FROM users WHERE JSON_CONTAINS(roles, :role)';
    $stmt = $conn->prepare($sql);
    $result = $stmt->executeQuery(['role' => '"' . $role . '"']);
    $data = $result->fetchAssociative();

    return (int) $data['total'];
}
public function findByRole(string $role): array
{
    return $this->createQueryBuilder('u')
        ->where('u.roles LIKE :role')
        ->setParameter('role', '%"'.$role.'"%') // le rôle est stocké en JSON
        ->orderBy('u.id', 'DESC')
        ->getQuery()
        ->getResult();
}

public function countDemandesForMecanicien(int $mecanicienId): int
{
    return $this->getEntityManager()->createQuery("
        SELECT COUNT(DISTINCT d.id)
        FROM App\Entity\Demande d
        JOIN d.offres o
        WHERE o.user = :mec
    ")
    ->setParameter('mec', $mecanicienId)
    ->getSingleScalarResult();
}



}
