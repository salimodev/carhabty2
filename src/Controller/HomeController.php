<?php
namespace App\Controller;

use App\Entity\Demande;
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

    // 🔹 Si vendeur neuf → filtrer par zone
    if ($user && in_array('ROLE_VENDEUR_NEUF', $user->getRoles())) {
        $zoneVendeur = $user->getAdresse();

        $lastDemandes = $demandeRepository->createQueryBuilder('d')
            ->where('d.zone = :zone OR d.zone = :toute')
            ->andWhere('d.vendeurneuf = 1')
            ->setParameter('zone', $zoneVendeur)
            ->setParameter('toute', 'Toute la Tunisie')
            ->orderBy('d.datecreate', 'DESC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    } else {
        // 🔹 Si pas vendeur → afficher toutes les demandes
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

    return $this->render('/sideHeader.html.twig', [
        'demandeCount' => $demandeCount,
        'nbOffres' => $nbOffres,
    ]);
}


    #[Route('/contact', name: 'app_contact')]
    public function contact(Request $request): Response
    {
               $session =$request->getSession();
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
    $qb = $demandeRepository->findAllDemandesQB();

    // 🔹 Filtrage selon le rôle de l'utilisateur
    if ($user && in_array('ROLE_VENDEUR_NEUF', $user->getRoles())) {
        $zoneVendeur = $user->getAdresse();

        $qb->andWhere('d.zone = :zone OR d.zone = :toute')
           ->andWhere('d.vendeurneuf = 1')
           ->setParameter('zone', $zoneVendeur)
           ->setParameter('toute', 'Toute la Tunisie');
    }
    // 🔹 Si le rôle est propriétaire ou visiteur → pas de filtre (voit tout)

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
    $page   = max(1, (int)$request->get('page', 1));
    $limit  = 12;

  $user = $this->getUser();
  $userZone = $user ? $user->getAdresse() : null;
$estConnecte = $user ? true : false;
$estVendeur  = $user ? in_array('ROLE_VENDEUR_NEUF', $user->getRoles()) : false;

    $demandes = $em->getRepository(Demande::class)
                   ->filterDemandes($marque, $zone, $date, $type, $trier);

    $result = [];

   
    foreach ($demandes as $d) {
         if ($d->getStatut() === 'fermer') {
        continue; // ← ignore cette demande
          } // Récupérer les pièces
        $pieces = [];
        foreach ($d->getPieces() as $p) {
            $pieces[] = [
                'designation' => $p->getDesignation(),
                'observation' => $p->getObservation(),
                'photo'       => $p->getPhoto() ?: '/assets/img/placeholder.png',
            ];
        }

        // Vérifier si le vendeur a déjà proposé une offre
        $dejaPropose = $d->getOffres()->filter(fn($o) => $o->getUser()->getId() === $user->getId())->count() > 0;

        $result[] = [
            'id'          => $d->getId(),
            'marque'      => $d->getMarque(),
            'modele'      => $d->getModele(),
            'zone'        => $d->getZone(),
            'date'        => $d->getDatecreate()->format('Y-m-d H:i'),
            'time_ago'    => $this->timeAgo($d->getDatecreate()),
            'vendeurType' => $user ? (in_array('ROLE_VENDEUR_NEUF', $user->getRoles()) ? 'neuf' : 'occasion') : null,
            'type'        => $d->getVendeuroccasion() == 1 ? 'occasion' : 'neuf',
            'offrecompte' => $d->getOffrecompte() ? $d->getOffrecompte()->getNom() : 'Anonyme',
            'pieces'      => $pieces,
             'dejaPropose' => $dejaPropose,
        'estConnecte' => $estConnecte,
        'estVendeur'  => $estVendeur,
        'userZone'    => $userZone,
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

}