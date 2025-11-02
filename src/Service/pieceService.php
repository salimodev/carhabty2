<?php


namespace App\Service;
use Doctrine\ORM\EntityManagerInterface;
use DateTime;
use DateTimeImmutable;
use App\Entity\Pieces;

class pieceService {
 private $em;
    public function __construct(\Doctrine\ORM\EntityManagerInterface $em )
    {
        $this->em = $em;

    }

    public function ajouterPiece($designation, $reference, $photo, $observation): Pieces
    {
        $piece = new Pieces();
        $piece->setDesignation($designation);
        $piece->setReferance($reference);
        $piece->setPhoto($photo);
        $piece->setObservation($observation);
        $piece->setDatecreateAt(new DateTimeImmutable());

        $this->em->persist($piece);
        $this->em->flush();

        return $piece;
    }
}
