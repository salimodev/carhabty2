<?php

namespace App\Controller;


use App\Entity\Demande;
use App\Entity\Users;
use App\Service\UsersService;
use App\Entity\Offre;
use App\Entity\Notification;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\DemandeRepository;
use App\Repository\OffreRepository;
use Symfony\Component\HttpFoundation\JsonResponse;

final class ProprietaireController extends AbstractController
{
  #[Route('/proprietaire', name: 'app_proprietaire')]
    public function index(Request $request,EntityManagerInterface $em,OffreRepository $offreRepo): Response
    {
        $session =$request->getSession();
       $session->set('PageMenu', 'proprietaire');
     $user = $this->getUser();

    if (!$user) {
        throw $this->createAccessDeniedException('Vous devez être connecté pour voir vos demandes.');
    }

    // ✅ Récupérer toutes les demandes liées à ce user
    $demandes = $em->getRepository(\App\Entity\Demande::class)
        ->findBy(['offrecompte' => $user], ['datecreate' => 'DESC']);
     $demandeCount = $em->getRepository(Demande::class)->countByUser($this->getUser());
     $user = $this->getUser();
     $nbOffres = $offreRepo->createQueryBuilder('o')
        ->join('o.demande', 'd')
        ->where('d.offrecompte = :userId')
        ->setParameter('userId', $user->getId())
        ->select('COUNT(o.id)')
        ->getQuery()
        ->getSingleScalarResult();

        // Nombre de devis acceptés
        $nbOffresAcceptees = $offreRepo->createQueryBuilder('o')
            ->join('o.demande', 'd')
            ->where('d.offrecompte = :userId')
            ->andWhere('o.status = :status')
            ->setParameter('userId', $user->getId())
            ->setParameter('status', 'acceptee')
            ->select('COUNT(o.id)')
            ->getQuery()
            ->getSingleScalarResult();

    return $this->render('proprietaire/proprietaire.html.twig', [
        'demandeCount' => $demandeCount, 'demandes' => $demandes,'nbOffres' => $nbOffres,
        'nbOffresAcceptees' => $nbOffresAcceptees,
    ]);
     
      
    }

   


    
#[Route(path: '/proprietaire/profile/Modifier/{id}', name: 'app_proprietaire_profile', methods: ['GET'])]
public function profile(
    int $id,
    Request $request,
    ManagerRegistry $doctrine,
    UsersService $UsersService
): Response {
    $session = $request->getSession();
    $session->set('PageMenu', 'app_proprietaire_profile');

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

    return $this->render('proprietaire/profile.html.twig', [
        'profileuser' => $profileuser,
    ]);
}

    #[Route(path: '/proprietaire/profile/Modifier', name: 'Modifier_profile_proprietaire')]
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

    #[Route(path: '/proprietaire/demandes', name: 'app_prop_demandes')]
    public function demandedeprix_prop(Request $request,EntityManagerInterface $em): Response
    {
            $session =$request->getSession();
       $session->set('PageMenu', 'proprietaire_demande');
         $user = $this->getUser();

    if (!$user) {
        throw $this->createAccessDeniedException('Vous devez être connecté pour voir vos demandes.');
    }
        $demandes = $em->getRepository(\App\Entity\Demande::class)
        ->findBy(['offrecompte' => $user], ['datecreate' => 'DESC']);
        return $this->render('/proprietaire/demande.html.twig', [
         'demandes' => $demandes
    ]);
    }

    #[Route('/proprietaire/offres', name: 'app_prop_offres')]
    public function offres_prop(Request $request): Response
    {
               $session =$request->getSession();
        $session->set('PageMenu', 'app_prop_offres');
        return $this->render('contact.html.twig');
    }

    
    #[Route('/proprietaire/vendeursNeuf', name: 'app_prop_vendeurs')]
    public function vendeur_neuf(Request $request): Response
    {
               $session =$request->getSession();
        $session->set('PageMenu', 'app_prop_vendeurs');
        return $this->render('contact.html.twig');
    }

     #[Route('/proprietaire/vendeursOccasion', name: 'app_prop_vendeursOccasion')]
    public function vendeur_occasion(Request $request): Response
    {
               $session =$request->getSession();
        $session->set('PageMenu', 'app_prop_vendeursOccasion');
        return $this->render('contact.html.twig');
    }

      #[Route('/proprietaire/mecancien', name: 'app_prop_mecancien')]
    public function mecancien(Request $request): Response
    {
               $session =$request->getSession();
        $session->set('PageMenu', 'app_prop_mecancien');
        return $this->render('contact.html.twig');
    }

   #[Route('/proprietaire/demande/detail/{id}', name: 'detail_demande')]
public function detailDemande(
    int $id,
    Request $request,
    SessionInterface $session,
    DemandeRepository $demandeRepository
): Response {
    $session = $request->getSession();
    $session->set('PageMenu', 'detail_demande');

    // 🔹 Récupérer la demande
    $demande = $demandeRepository->find($id);

    if (!$demande) {
        throw $this->createNotFoundException('Demande introuvable');
    }

    // 🔹 Récupérer les pièces liées
    $pieces = $demande->getPieces();

    // 🔹 Récupérer le client
    $client = $demande->getOffrecompte();

    return $this->render('proprietaire/detailDemande.html.twig', [
        'demande' => $demande,
        'pieces' => $pieces,
        'client' => $client,
    ]);
}

#[Route(path: '/demande/supprimer', name: 'supprimer_demande')]
public function supprimerDemande(Request $request, EntityManagerInterface $em): JsonResponse
{
    $idDemande = $request->get('id');
    $demande = $em->getRepository(Demande::class)->find($idDemande);

    if (!$demande) {
        return new JsonResponse('error');
    }

    // Supprimer toutes les pièces associées
    foreach ($demande->getPieces() as $piece) {
        $em->remove($piece);
    }

    // Supprimer la demande
    $em->remove($demande);
    $em->flush();

    return new JsonResponse('done');
}

#[Route('/proprietaire/mes-offres', name: 'proprietaire_offres')]
public function mesOffres(DemandeRepository $demandeRepo, OffreRepository $offreRepo): Response
{
    $user = $this->getUser();

    // Récupère toutes les demandes de ce propriétaire
   $demandes = $demandeRepo->findBy(['offrecompte' => $user]);


    // Récupère toutes les offres liées à ces demandes
    $offres = [];
    foreach ($demandes as $demande) {
        foreach ($offreRepo->findBy(['demande' => $demande]) as $offre) {
            $offres[] = $offre;
        }
    }

    return $this->render('proprietaire/offrerecu.html.twig', [
        'offres' => $offres,
    ]);
}

#[Route('/offre/changer-status', name: 'changer_status_offre', methods: ['POST'])]
public function changerStatus(Request $request, EntityManagerInterface $em, OffreRepository $offreRepo): JsonResponse
{
    $id = $request->request->get('id');
    $status = $request->request->get('status'); // 'acceptee' ou 'refusee'

    if (!$id || !$status) {
        return $this->json(['success' => false, 'message' => 'Paramètres manquants.']);
    }

    $offre = $offreRepo->find($id);

    if (!$offre) {
        return $this->json(['success' => false, 'message' => 'Offre non trouvée.']);
    }

    // Vérifier que l'utilisateur connecté est bien le propriétaire
    $user = $this->getUser();
    if ($offre->getDemande()->getOffrecompte()->getId() !== $user->getId()) {
        return $this->json(['success' => false, 'message' => 'Action non autorisée.']);
    }

    // Modifier le statut de l'offre
    if (!in_array($status, ['acceptee', 'refusee'])) {
        return $this->json(['success' => false, 'message' => 'Statut invalide.']);
    }

    $offre->setStatus($status);

    // Si l'offre est acceptée, fermer la demande
    if ($status === 'acceptee') {
        $demande = $offre->getDemande();
        $demande->setStatut('fermer'); // Assure-toi que le champ statut existe dans Demande
        $em->persist($demande);
    }

    $em->persist($offre);
    $em->flush();

    // --- Création de la notification pour le vendeur ---
    $vendeur = $offre->getUser(); // le vendeur qui a proposé l'offre
    if ($vendeur) {
        $notif = new Notification();
        $notif->setUser($vendeur);
        $notif->setMessage("Votre offre N° {$offre->getNumeroOffre()} a été " . ($status === 'acceptee' ? 'acceptée' : 'refusée') . " par le demandeur.");
        $notif->setCreatedAt(new \DateTimeImmutable());
        $em->persist($notif);
        $em->flush();
    }

    return $this->json(['success' => true, 'message' => 'Le statut de l\'offre a été mis à jour et le vendeur notifié.']);
}


#[Route('/proprietaire/offre/{id}', name: 'offre_show_prop', methods: ['GET'])]
public function showOffre(
    Offre $offre
): Response {
    // Récupérer les pièces associées à l'offre
    $offrePieces = $offre->getOffrePieces();

    return $this->render('proprietaire/offre_detail.html.twig', [
        'offre' => $offre,
        'offrePieces' => $offrePieces,
        'demande' => $offre->getDemande()
    ]);
}

#[Route('/offre/{id}/accepter', name: 'offre_accepter')]
public function accepter(Offre $offre, EntityManagerInterface $em): Response
{
    // Changer le statut de l'offre
    $offre->setStatus('acceptee');

    // Changer le statut de la demande associée
    $demande = $offre->getDemande();
    if ($demande) {
        $demande->setStatut('fermer'); // Assure-toi que la propriété status existe dans Demande
    }

    $em->flush();

    return new Response('Offre acceptée et demande fermée avec succès !');
}


#[Route('/offre/{id}/refuser', name: 'offre_refuser')]
public function refuser(Offre $offre, EntityManagerInterface $em): Response
{
    $offre->setStatus('refusee');
    $em->flush();

    return new Response('Offre refusée avec succès !');
}


}