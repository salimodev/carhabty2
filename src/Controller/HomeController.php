<?php

namespace App\Controller;

use App\Entity\Demande;
use App\Entity\Annonce;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\DemandeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Knp\Component\Pager\PaginatorInterface;
use App\Repository\OffreRepository;

class HomeController extends AbstractController
{
    #[Route('/', name: 'Accueil')]
    public function index(Request $request, DemandeRepository $demandeRepository): Response
    {
        $session = $request->getSession();
        $session->set('PageMenu', 'Accueil');

        $user = $this->getUser();

       // ---------------------------------------------------------------
    // 🔹 VENDEUR OCCASION : filtrer par zone + demandes vendeur occasion
    // ---------------------------------------------------------------
    if ($user && in_array('ROLE_VENDEUR_OCCASION', $user->getRoles())) {

        $zoneVendeur = $user->getAdresse();

        $lastDemandes = $demandeRepository->createQueryBuilder('d')
            ->where('(d.zone = :zone OR d.zone = :toute)')
            ->andWhere('d.vendeuroccasion = 1')
            ->setParameter('zone', $zoneVendeur)
            ->setParameter('toute', 'Toute la Tunisie')
            ->orderBy('d.datecreate', 'DESC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }

    // ---------------------------------------------------------------
    // 🔹 VENDEUR NEUF
    // ---------------------------------------------------------------
    elseif ($user && in_array('ROLE_VENDEUR_NEUF', $user->getRoles())) {

        $zoneVendeur = $user->getAdresse();

        $lastDemandes = $demandeRepository->createQueryBuilder('d')
            ->where('(d.zone = :zone OR d.zone = :toute)')
            ->andWhere('d.vendeurneuf = 1')
            ->setParameter('zone', $zoneVendeur)
            ->setParameter('toute', 'Toute la Tunisie')
            ->orderBy('d.datecreate', 'DESC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }

    // ---------------------------------------------------------------
    // 🔹 AUTRES RÔLES : afficher tout
    // ---------------------------------------------------------------
    else {
        $lastDemandes = $demandeRepository->createQueryBuilder('d')
            ->orderBy('d.datecreate', 'DESC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }

    return $this->render('home/index.html.twig', [
        'lastDemandes' => $lastDemandes,
    ]);
    }


    #[Route(path: '/footer', name: 'app_footer')]
    public function footer(Request $request, EntityManagerInterface $em, OffreRepository $offreRepo): Response
    {
        $user = $this->getUser();

        // Si utilisateur non connecté, on initialise les compteurs à 0
        if ($user) {
            $demandeCount = $em->getRepository(Demande::class)->countByUser($user);

            $nbOffres = $offreRepo->createQueryBuilder('o')
                ->join('o.demande', 'd')
                ->where('d.offrecompte = :userId')
                ->setParameter('userId', $user->getId())
                ->select('COUNT(o.id)')
                ->getQuery()
                ->getSingleScalarResult();
        } else {
            $demandeCount = 0;
            $nbOffres = 0;
        }

        return $this->render('/footer.html.twig', [
            'demandeCount' => $demandeCount,
            'nbOffres' => $nbOffres,
        ]);
    }


    #[Route(path: '/header', name: 'app_header')]
    public function header(Request $request): Response
    {

        return $this->render('/header.html.twig');
    }

    #[Route(path: '/sideheader', name: 'sideheader')]
    public function sideheader(Request $request, EntityManagerInterface $em, OffreRepository $offreRepo): Response
    {
        $user = $this->getUser();

        if ($user) {
            $demandeCount = $em->getRepository(Demande::class)->countByUser($user);
           
            $nbOffres = $offreRepo->createQueryBuilder('o')
                ->join('o.demande', 'd')
                ->where('d.offrecompte = :userId')
                ->setParameter('userId', $user->getId())
                ->select('COUNT(o.id)')
                ->getQuery()
                ->getSingleScalarResult();
        } else {
            $demandeCount = 0;
            $nbOffres = 0;
        }

         $nbPieces =$em->getRepository(Annonce::class)->count(['user' => $user]);

        return $this->render('/sideHeader.html.twig', [
            'demandeCount' => $demandeCount,
            'nbOffres' => $nbOffres,
            'nbPieces' => $nbPieces
        ]);
    }


    #[Route('/contact', name: 'app_contact')]
    public function contact(Request $request): Response
    {
        $session = $request->getSession();
        $session->set('PageMenu', 'app_contact');
        return $this->render('contact.html.twig');
    }

   #[Route('/demande/tous', name: 'app_demande_all')]
public function all_demande(
    Request $request,
    DemandeRepository $demandeRepository,
    PaginatorInterface $paginator
): Response {
    $session = $request->getSession();
    $session->set('PageMenu', 'demande_all');

    $user = $this->getUser();
    $qb = $demandeRepository->findAllDemandesQB(); // d déjà alias dans le repo

    // ---------------------------------------------------------------
    // 🔹 VENDEUR OCCASION → filtrer par zone + demandes occasion
    // ---------------------------------------------------------------
    if ($user && in_array('ROLE_VENDEUR_OCCASION', $user->getRoles())) {

        $zoneVendeur = $user->getAdresse();

        $qb->andWhere('(d.zone = :zone OR d.zone = :toute)')
            ->andWhere('d.vendeuroccasion = 1')
            ->setParameter('zone', $zoneVendeur)
            ->setParameter('toute', 'Toute la Tunisie');
    }

    // ---------------------------------------------------------------
    // 🔹 VENDEUR NEUF → filtrer par zone + demandes neuf
    // ---------------------------------------------------------------
    elseif ($user && in_array('ROLE_VENDEUR_NEUF', $user->getRoles())) {

        $zoneVendeur = $user->getAdresse();

        $qb->andWhere('(d.zone = :zone OR d.zone = :toute)')
            ->andWhere('d.vendeurneuf = 1')
            ->setParameter('zone', $zoneVendeur)
            ->setParameter('toute', 'Toute la Tunisie');
    }

    // ---------------------------------------------------------------
    // 🔹 AUTRES RÔLES (propriétaire, mécanicien, visiteur)
    // → ils voient tout, aucun filtre
    // ---------------------------------------------------------------

    $dem = $paginator->paginate(
        $qb,
        $request->query->getInt('page', 1),
        12
    );

    return $this->render('home/alldemande.html.twig', [
        'lastDemandes' => $dem,
    ]);
}

#[Route('/recherche/demande', name: 'recherche_demande')]
public function rechercheDemande(Request $request, EntityManagerInterface $em): JsonResponse
{
    $marque = $request->get('marque');
    $zone   = $request->get('zone');
    $date   = $request->get('date');
    $type   = $request->get('type');
    $trier  = $request->get('trier');

    $user = $this->getUser();
    $estConnecte = $user !== null;

    // 🔹 Rollenprüfung
    $isVendeurNeuf = $user && in_array('ROLE_VENDEUR_NEUF', $user->getRoles());
    $isVendeurOccasion = $user && in_array('ROLE_VENDEUR_OCCASION', $user->getRoles());

    $userZone = $user ? $user->getAdresse() : null;

    // 🔹 Holen der gefilterten Anfragen
    $demandes = $em->getRepository(Demande::class)
        ->filterDemandes($marque, $zone, $date, $type, $trier);

    $result = [];

    foreach ($demandes as $d) {

        // ❌ geschlossene Anfragen ignorieren
        if ($d->getStatut() === 'fermer') {
            continue;
        }

        // 🔹 Rollenspezifische Filterung
        if ($isVendeurNeuf) {
            if ($d->getVendeurneuf() != 1) continue;
            if ($d->getZone() !== $userZone && $d->getZone() !== "Toute la Tunisie") continue;
        }

        if ($isVendeurOccasion) {
            if ($d->getVendeuroccasion() != 1) continue;
            if ($d->getZone() !== $userZone && $d->getZone() !== "Toute la Tunisie") continue;
        }

        // 🔹 Stücke vorbereiten
        $pieces = [];
        foreach ($d->getPieces() as $p) {
            $pieces[] = [
                'designation' => $p->getDesignation(),
                'observation' => $p->getObservation(),
                'photo'       => $p->getPhoto() ?: '/image/placeholder.png',
            ];
        }

        // 🔹 Prüfen, ob der eingeloggte Verkäufer schon ein Angebot gemacht hat
        $dejaPropose = false;

        if ($user && ($isVendeurNeuf || $isVendeurOccasion)) {
            $dejaPropose = count(array_filter(
                $d->getOffres()->toArray(),
                fn($o) => $o->getUser() && $o->getUser()->getId() === $user->getId()
            )) > 0;
        }

        // 🔹 JSON-Antwort aufbauen
        $result[] = [
            'id'           => $d->getId(),
            'marque'       => $d->getMarque(),
            'modele'       => $d->getModele(),
            'zone'         => $d->getZone(),
            'date'         => $d->getDatecreate()->format('Y-m-d H:i'),
            'time_ago'     => $this->timeAgo($d->getDatecreate()),
            'type'         => $d->getVendeuroccasion() == 1 ? 'occasion' : 'neuf',
            'vendeurType'  => $isVendeurNeuf ? 'neuf' : ($isVendeurOccasion ? 'occasion' : null),
            'offrecompte'  => $d->getOffrecompte() ? $d->getOffrecompte()->getPrenom() : 'Anonyme',
            'pieces'       => $pieces,
            'dejaPropose'  => $dejaPropose,
            'estConnecte'  => $estConnecte,
            'estVendeur'   => ($isVendeurNeuf || $isVendeurOccasion),
            'userZone'     => $userZone,
        ];
    }

    return new JsonResponse($result);
}


    private function timeAgo(\DateTimeInterface $datetime): string
    {
        $now  = new \DateTimeImmutable();
        $diff = $now->diff($datetime);

        if ($diff->y > 0) {
            return 'il y a ' . $diff->y . ' an' . ($diff->y > 1 ? 's' : '');
        }

        if ($diff->m > 0) {
            return 'il y a ' . $diff->m . ' mois';
        }

        if ($diff->d > 0) {
            return 'il y a ' . $diff->d . ' jour' . ($diff->d > 1 ? 's' : '');
        }

        if ($diff->h > 0 && $diff->d === 0) {
            return 'il y a ' . $diff->h . ' heure' . ($diff->h > 1 ? 's' : '');
        }

        if ($diff->i > 0 && $diff->h === 0 && $diff->d === 0) {
            return 'il y a ' . $diff->i . ' minute' . ($diff->i > 1 ? 's' : '');
        }

        return 'à l’instant';
    }


    #[Route('/annonces', name: 'app_home_annonces')]
public function home_annonces(Request $request, EntityManagerInterface $em, PaginatorInterface $paginator): Response
{
     $session = $request->getSession();
    $session->set('PageMenu', 'app_home_annonces');

     $query = $em->getRepository(Annonce::class)->createQueryBuilder('a')
        ->join('a.user', 'u')
        ->where('u.roles LIKE :role')
        ->setParameter('role', '%ROLE_PARTICULIER%')
        ->orderBy('a.dateCreation', 'DESC')
        ->getQuery();

    $annonces = $paginator->paginate(
        $query,
        $request->query->getInt('page', 1),
        12
    );

    return $this->render('home/piecesoccasion.html.twig', [
        'annonces' => $annonces
    ]);
}

}
