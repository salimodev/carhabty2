<?php

namespace App\Controller;


use App\Entity\Demande;
use App\Entity\Users;
use App\Service\UsersService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\DemandeRepository;
use Symfony\Component\HttpFoundation\JsonResponse;

final class ProprietaireController extends AbstractController
{
  #[Route('/proprietaire', name: 'app_proprietaire')]
    public function index(Request $request,EntityManagerInterface $em): Response
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

    return $this->render('proprietaire/proprietaire.html.twig', [
        'demandeCount' => $demandeCount, 'demandes' => $demandes,
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





}