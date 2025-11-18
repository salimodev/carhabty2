<?php

namespace App\Controller;

use App\Entity\Demande;
use App\Entity\Users;
use App\Service\UsersService;
use App\Entity\OffrePiece;
use App\Entity\Pieces;
use App\Entity\Offre;
use App\Entity\Notification;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\DemandeRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Knp\Component\Pager\PaginatorInterface;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\OffreRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\SecurityBundle\Security;

class VendeurOccasionController extends AbstractController
{

    #[Route(path: '/vendeur/occasion/LayoutSellerOcc', name: 'layout_seller_occ', methods: "GET")]
    public function layout_seller_occ(EntityManagerInterface $em, Security $security, PaginatorInterface $paginator, Request $request): Response
    {

        $user = $security->getUser();
        $query = $em->getRepository(Notification::class)
            ->createQueryBuilder('n')
            ->where('n.User = :user')
            ->setParameter('user', $user)
            ->orderBy('n.createdAt', 'DESC')
            ->getQuery();

        $notifications = $paginator->paginate($query, $request->query->getInt('page', 1), 10);

        return $this->render('vendeurOccasion/layoutVO.html.twig', [
            'notifications' => $notifications,
        ]);
    }

     #[Route(path: '/vendeur/occasion/dashboard', name: 'dashboard_vendeurOccasion', methods: "GET")]
public function dashboard_VN(DemandeRepository $demandeRepository, Request $request,Security $security,PaginatorInterface $paginator, EntityManagerInterface $em,OffreRepository $offreRepo)
{
    $session = $request->getSession();
    $session->set('PageMenu', 'dashboard_vendeurOccasion');

    $user = $this->getUser();
    $zoneVendeur = $user->getAdresse();

    $countDemandes = $demandeRepository->countDemandesDispoVendeurOcc($zoneVendeur);
    $dernieresDemandes = $demandeRepository->findLatestForVendeurOccasion($zoneVendeur);

    $nombreOffres = $em->getRepository(Offre::class)
        ->createQueryBuilder('o')
        ->select('COUNT(o.id)')
        ->where('o.user = :user')
        ->setParameter('user', $user)
        ->getQuery()
        ->getSingleScalarResult();

    $dernieresOffres = $em->getRepository(Offre::class)
        ->createQueryBuilder('o')
        ->where('o.user = :user')
        ->setParameter('user', $user)
        ->orderBy('o.createdAt', 'DESC')
        ->setMaxResults(5)
        ->getQuery()
        ->getResult();

        // Nombre d'offres acceptées pour ce vendeur
    $nbOffresAcceptees = $offreRepo->createQueryBuilder('o')
    ->where('o.user = :user')
    ->andWhere('o.status = :status')
    ->setParameter('user', $user)
    ->setParameter('status', 'acceptee')
    ->select('COUNT(o.id)')
    ->getQuery()
    ->getSingleScalarResult();


 $now = new \DateTimeImmutable('today'); // ignore l’heure
foreach ($dernieresOffres as $offre) {
    $validiteFin = $offre->getValiditeFin();
    $validiteDebut = $offre->getValiditeDebut();

    if ($validiteFin && $validiteDebut) {
        // On considère uniquement les dates (ignore les heures)
        $start = $validiteDebut->setTime(0,0,0);
        $end = $validiteFin->setTime(0,0,0);

        $diffJours = (int)$now->diff($end)->format('%r%a'); // nombre de jours relatifs

        $joursRestants = max(0, $diffJours); // +1 pour inclure le dernier jour
        $offre->joursRestants = $joursRestants;
    } else {
        $offre->joursRestants = null;
    }
}

  $user = $security->getUser();
    $query = $em->getRepository(Notification::class)
        ->createQueryBuilder('n')
        ->where('n.User = :user')
        ->setParameter('user', $user)
        ->orderBy('n.createdAt', 'DESC')
        ->getQuery();

    $notifications = $paginator->paginate($query, $request->query->getInt('page', 1), 10);
  // Calcul du taux d'acceptation
    $tauxAcceptation = $nombreOffres > 0 ? round(($nbOffresAcceptees / $nombreOffres) * 100, 2) : 0;

    return $this->render('vendeurOccasion/dashboardVO.html.twig', [
        'countDemandes' => $countDemandes,
        'demandes' => $dernieresDemandes,
        'nombreOffres' => $nombreOffres,
        'dernieresOffres' => $dernieresOffres,
        'nbOffresAcceptees' => $nbOffresAcceptees,
         'tauxAcceptation' => $tauxAcceptation,
          'notifications' => $notifications,
    ]);
}

   #[Route('/vendeur/occasion/demande/detail/{id}', name: 'detail_demande_vendeur_occa')]
    public function detailDemande_occ(
        int $id,
        Request $request,EntityManagerInterface $em,Security $security,PaginatorInterface $paginator,
        SessionInterface $session,
        DemandeRepository $demandeRepository
    ): Response {
        $session = $request->getSession();
        $session->set('PageMenu', 'detail_demande_occ');

        // 🔹 Récupérer la demande
        $demande = $demandeRepository->find($id);

        if (!$demande) {
            throw $this->createNotFoundException('Demande introuvable');
        }

        // 🔹 Récupérer les pièces liées
        $pieces = $demande->getPieces();

        // 🔹 Récupérer le client
        $client = $demande->getOffrecompte();
         $user = $security->getUser();
    $query = $em->getRepository(Notification::class)
        ->createQueryBuilder('n')
        ->where('n.User = :user')
        ->setParameter('user', $user)
        ->orderBy('n.createdAt', 'DESC')
        ->getQuery();

    $notifications = $paginator->paginate($query, $request->query->getInt('page', 1), 10);


        return $this->render('vendeurOccasion/detailDemandeOcca.html.twig', [
            'demande' => $demande,
            'pieces' => $pieces,
            'client' => $client,
            'notifications' => $notifications,
        ]);
    }


     #[Route('/vendeur/occasion/demandes', name: 'vendeur_demandes_occa')]
public function demandesDisponibles_occa(EntityManagerInterface $em,Request $request,DemandeRepository $demandeRepository,Security $security, PaginatorInterface $paginator): Response
{
    $session = $request->getSession();
        $session->set('PageMenu', 'vendeur_demandes_occa');
    $user = $this->getUser();
    $zoneVendeur = $user->getAdresse();

    $demandes = $demandeRepository->findAllForVendeurOcca($zoneVendeur);
$dem = $paginator->paginate(
        $demandes,
        $request->query->getInt('page', 1),
        12
    );

         $user = $security->getUser();
    $query = $em->getRepository(Notification::class)
        ->createQueryBuilder('n')
        ->where('n.User = :user')
        ->setParameter('user', $user)
        ->orderBy('n.createdAt', 'DESC')
        ->getQuery();

    $notifications = $paginator->paginate($query, $request->query->getInt('page', 1), 10);

    return $this->render('vendeurOccasion/demandes.html.twig', [
        'demandes' => $dem,
        'notifications' => $notifications,
    ]);
}

#[Route('/recherche/demande/vendeur/occasion', name: 'recherche_demande_vendeur_occasion')]
public function rechercheDemandeVendeurOcca(Request $request, EntityManagerInterface $em): JsonResponse
{
    $marque = $request->get('marque');
    $date   = $request->get('date');
    $vendeur = $this->getUser();
    $zoneVendeur = $vendeur->getAdresse(); 

    $demandes = $em->getRepository(Demande::class)
                   ->filterDemandesvendeurOcca($marque, $zoneVendeur, $date, 'occasion', null);

    $result = [];
    foreach ($demandes as $d) {
          if ($d->getStatut() === 'fermer') {
        continue; // ← ignore cette demande
          }
        $pieces = [];
        foreach ($d->getPieces() as $p) {
            $pieces[] = [
                'designation' => $p->getDesignation(),
                'observation' => $p->getObservation(),
                'photo'       => $p->getPhoto() ?: '/image/placeholder.png',
            ];
        }

       // Vérifier si un vendeur est connecté
if ($vendeur) {
    // Vérifier si ce vendeur a déjà proposé une offre pour cette demande
    $dejaPropose = count(array_filter(
        $d->getOffres()->toArray(),
        fn($o) => $o->getUser() && $o->getUser()->getId() === $vendeur->getId()
    )) > 0;
} else {
    // Aucun utilisateur connecté → ne pas bloquer
    $dejaPropose = false;
}

// Ajouter les données au tableau résultat
$result[] = [
    'id'           => $d->getId(),
    'marque'       => $d->getMarque(),
    'modele'       => $d->getModele(),
    'zone'         => $d->getZone(),
    'date'         => $d->getDatecreate()->format('Y-m-d H:i'),
    'offrecompte'  => $d->getOffrecompte() ? $d->getOffrecompte()->getNom() : 'Anonyme',
    'pieces'       => $pieces,
    'dejaPropose'  => $dejaPropose, // ajout pour le JS
];
    }

    return new JsonResponse($result);
}


#[Route(path: '/vendeur/occasion/profile/Modifier/{id}', name: 'app_vendeurocca_profile', methods: ['GET'])]
public function profile(
    int $id,
    Request $request,EntityManagerInterface $em, Security $security, PaginatorInterface $paginator,
    ManagerRegistry $doctrine,
    UsersService $UsersService
): Response {
    $session = $request->getSession();
    $session->set('PageMenu', 'app_vendeurocca_profile');

    $user = $this->getUser();

    // Vérifie si un utilisateur est connecté
    if (!$user) {
        throw $this->createAccessDeniedException('Utilisateur non connecté.');
    }

    // Récupère le profil à modifier
    $profile = $doctrine->getRepository(Users::class)->find($id);

    // Vérifie si le profil existe
    if (!$profile) {
        throw $this->createNotFoundException('Profil non trouvé.');
    }

    // Vérifie que l'utilisateur connecté correspond bien au profil
    if ($profile->getId() !== $user->getId()) {
        throw $this->createAccessDeniedException('Vous n’êtes pas autorisé à modifier ce profil.');
    }

    // Récupération des infos via le service (si besoin d’infos supplémentaires)
    $profileuser = $UsersService->getProfile($id);
  $user = $security->getUser();
    $query = $em->getRepository(Notification::class)
        ->createQueryBuilder('n')
        ->where('n.User = :user')
        ->setParameter('user', $user)
        ->orderBy('n.createdAt', 'DESC')
        ->getQuery();

    $notifications = $paginator->paginate($query, $request->query->getInt('page', 1), 10);
    return $this->render('vendeurOccasion/profile.html.twig', [
        'profileuser' => $profileuser, 'notifications' => $notifications,
    ]);
}

   #[Route(path: '/vendeur/occasion/profile/Modifier', name: 'Modifier_profile_vendeurOcca')]
    public function Modifier_profile(Request $request,UserPasswordHasherInterface $userPasswordHasher,UsersService $UsersService){

        $id = $request->get('id');
        $nom = $request->get('nom');
        $email = $request->get('email');
        $telephone = $request->get('numero');
        $password = $request->get('password');
        $user = $this->getUser();
        $logoImg = $request->get('logoImg');
        if($password ==""){
            $profileuser = $UsersService->ModifierProfileSansMDW($id,$nom,$email,$telephone,$logoImg);
        }else{
            $password = $userPasswordHasher->hashPassword($user,$request->request->get('password'));
            $profileuser = $UsersService->ModifierProfileAvecMDW($id,$nom,$email,$telephone,$password,$logoImg);
        }
        
        return new response('success');
    }

}
