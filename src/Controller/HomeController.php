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
     public function footer(Request $request): Response
    {
    
        return $this->render('/footer.html.twig');
    }

      #[Route(path: '/header', name: 'app_header')]
    public function header(Request $request): Response
    {
            
        return $this->render('/header.html.twig');
    }

      #[Route(path: '/sideheader', name: 'sideheader')]
    public function sideheader(Request $request): Response
    {
           
        return $this->render('/sideHeader.html.twig');
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
$page   = max(1, (int)$request->get('page', 1)); // page par défaut = 1
    $limit  = 12; // 
        $demandes = $em->getRepository(Demande::class)
                        ->filterDemandes($marque, $zone, $date, $type, $trier);

        $result = [];

        foreach ($demandes as $d) {
            // Récupérer les pièces
            $pieces = [];
            foreach ($d->getPieces() as $p) {
                $pieces[] = [
                    'designation' => $p->getDesignation(),
                    'observation' => $p->getObservation(),
                    'photo'       => $p->getPhoto() ?: '/assets/img/placeholder.png',
                ];
            }

            $result[] = [
                'id'         => $d->getId(),
                'marque'     => $d->getMarque(),
                'modele'     => $d->getModele(),
                'zone'       => $d->getZone(),
                'date'       => $d->getDatecreate()->format('Y-m-d H:i'),
                'time_ago'   => $this->timeAgo($d->getDatecreate()),
                'type'       => $d->getVendeuroccasion() == 1 ? 'occasion' : 'neuf',
                'offrecompte'=> $d->getOffrecompte() ? $d->getOffrecompte()->getNom() : 'Anonyme',
                'pieces'     => $pieces,
            ];
        }

        return new JsonResponse($result);
    }

    // Fonction compatible DateTimeImmutable et DateTime
    private function timeAgo(\DateTimeInterface $datetime): string
    {
        $now  = new \DateTimeImmutable();
        $diff = $now->diff($datetime);

        if ($diff->y > 0) return $diff->y . ' an(s) ago';
        if ($diff->m > 0) return $diff->m . ' mois ago';
        if ($diff->d > 0) return $diff->d . ' jour(s) ago';
        if ($diff->h > 0) return $diff->h . ' heure(s) ago';
        if ($diff->i > 0) return $diff->i . ' minute(s) ago';
        return 'à l\'instant';
    }
}