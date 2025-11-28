<?php

namespace App\Controller;

use App\Entity\Annonce;
use App\Entity\Demande;
use App\Entity\Users;
use App\Service\UsersService;
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
use App\Repository\NotificationRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\SecurityBundle\Security;


final class ParticulierController extends AbstractController
{
      #[Route('/particulier', name: 'app_particulier')]
    public function index(Request $request, EntityManagerInterface $em, OffreRepository $offreRepo): Response
    {
        $session = $request->getSession();
        $session->set('PageMenu', 'particulier');
        $user = $this->getUser();

        if (!$user) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour voir vos demandes.');
        }

     
        $user = $this->getUser();
      $annonces = $em->getRepository(Annonce::class)->findBy(
        ['user' => $user],
        ['dateCreation' => 'DESC'],
        4
    );
    $nbPieces =$em->getRepository(Annonce::class)->count(['user' => $user]);
       

        return $this->render('particulier/particulier.html.twig', [
        'annonces' => $annonces,'nbPieces' => $nbPieces,
    ]);
    }

    
     #[Route('/particulier/annonces', name: 'app_particulier_annonces')]
public function mesannonces(Request $request, EntityManagerInterface $em, PaginatorInterface $paginator): Response
{
    $session = $request->getSession();
    $session->set('PageMenu', 'app_particulier_annonces');

    $user = $this->getUser();
    if (!$user) {
        throw $this->createAccessDeniedException('Vous devez être connecté pour voir vos annonces.');
    }

    // Requête
    $query = $em->getRepository(Annonce::class)->createQueryBuilder('a')
        ->where('a.user = :user')
        ->setParameter('user', $user)
        ->orderBy('a.dateCreation', 'DESC')
        ->getQuery();

    // Pagination
    $annonces = $paginator->paginate(
        $query,
        $request->query->getInt('page', 1), // page actuelle
        10 // nombre par page
    );

    return $this->render('particulier/annonces.html.twig', [
        'annonces' => $annonces
    ]);
}




     #[Route(path: '/particulier/profile/Modifier/{id}', name: 'app_particulier_profile', methods: ['GET'])]
    public function profile(
        int $id,
        Request $request,
        ManagerRegistry $doctrine,
        UsersService $UsersService
    ): Response {
        $session = $request->getSession();
        $session->set('PageMenu', 'app_particulier_profile');

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

        return $this->render('particulier/profile.html.twig', [
            'profileuser' => $profileuser,
        ]);
    }

    #[Route(path: '/particulier/profile/Modifier', name: 'Modifier_profile_particulier')]
    public function Modifier_profile(Request $request, UserPasswordHasherInterface $userPasswordHasher, UsersService $UsersService)
    {

        $id = $request->get('id');
        $nom = $request->get('nom');
        $email = $request->get('email');
        $telephone = $request->get('numero');
        $password = $request->get('password');
        $user = $this->getUser();
        $logoImg = $request->get('logoImg');
        if ($password == "") {
            $profileuser = $UsersService->ModifierProfileSansMDW($id, $nom, $email, $telephone, $logoImg);
        } else {
            $password = $userPasswordHasher->hashPassword($user, $request->request->get('password'));
            $profileuser = $UsersService->ModifierProfileAvecMDW($id, $nom, $email, $telephone, $password, $logoImg);
        }

        return new response('success');
    }

  #[Route('/annonces', name: 'annonces')]
    public function annonces(Request $request): Response
    {
       $session =$request->getSession();
       $session->set('PageMenu', 'annonces');
        return $this->render('particulier/annonces.html.twig');
    }

    #[Route('/particulier/annonces/ajouter', name: 'add_annonces')]
    public function addannonces(Request $request): Response
    {
       $session =$request->getSession();
       $session->set('PageMenu', 'add_annonces');
        return $this->render('particulier/ajouterannonces.html.twig');
    }


   #[Route('/particulier/annonces/add', name: 'Ajouter_annonces_particulier', methods: ['POST'])]
public function ajout_ann_part(Request $request, EntityManagerInterface $em): Response
{
    // Vérifier si l'utilisateur est connecté
    $user = $this->getUser();
    if (!$user) {
        return new JsonResponse(['error' => 'Utilisateur non connecté'], 403);
    }

    // Récupération des données venant de AJAX
    $nom = $request->get('nom');
    $marque = $request->get('marque');
    $modele = $request->get('modele');
    $reference = $request->get('reference');
    $prix = $request->get('prix');
    $description = $request->get('description');
    $banner = $request->get('banner');

    // cloudImages arrive en tableau → JSON ENCODED ou tableau direct selon JS
    $cloudImages = $request->get('cloudImages');
    if (is_string($cloudImages)) {
        $cloudImages = json_decode($cloudImages, true);
    }

    // Vérification rapide des champs obligatoires
    if (!$nom || !$marque || !$modele || !$prix || !$banner) {
        return new JsonResponse(['error' => 'Champs manquants'], 400);
    }

    // Création de l'objet Annonce
    $annonce = new Annonce();
    $annonce->setNom($nom);
    $annonce->setMarque($marque);
    $annonce->setModele($modele);
    $annonce->setReference($reference);
    $annonce->setPrix($prix);
    $annonce->setDescription($description);
    $annonce->setImagePrincipale($banner);        // Image principale
    $annonce->setImages($cloudImages);
    $annonce->setStatut('en_attente');        // Galérie (tableau JSON)
    $annonce->setUser($user);                 // Associer au particulier connecté
    $annonce->setDateCreation(new \DateTime()); 
    $annonce->setDateModification(new \DateTime()); 

    // Enregistrement en base
    $em->persist($annonce);
    $em->flush();

    return new JsonResponse(['success' => true, 'id' => $annonce->getId()]);
}

}