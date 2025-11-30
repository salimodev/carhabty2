<?php

namespace App\Controller;

use App\Entity\Annonce;
use App\Entity\Message;
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
use App\Repository\AnnonceRepository;
use App\Repository\MessageRepository;
use App\Repository\UsersRepository;
use App\Repository\NotificationRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\SecurityBundle\Security;


final class ParticulierController extends AbstractController
{
      #[Route('/particulier', name: 'app_particulier')]
    public function index(Request $request, EntityManagerInterface $em, MessageRepository $messageRepo,AnnonceRepository $repo): Response
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
  
    // Annonces vendues
    $piecesVendues = $repo->count([
        'user' => $user,
        'statut' => 'vendu'
    ]);

    // Récupérer le nombre de messages non lus pour l'utilisateur connecté
$user = $this->getUser();
$unreadCount = $messageRepo->createQueryBuilder('m')
    ->select('COUNT(m.id)')
    ->where('m.receiver = :user')
    ->andWhere('m.isRead = false')
    ->setParameter('user', $user)
    ->getQuery()
    ->getSingleScalarResult();


        return $this->render('particulier/particulier.html.twig', [
        'annonces' => $annonces,'nbPieces' => $nbPieces, 'piecesVendues' => $piecesVendues, 'unreadCount' => $unreadCount,
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



 #[Route('/particulier/annonces/modifier/{id}', name: 'modifier_annonce', methods: ['GET'])]
public function afficherFormModifier(Annonce $annonce): Response
{
    // Benutzer darf nur seine eigene Anzeige bearbeiten
    if (!$this->isGranted('ROLE_PARTICULIER') || $annonce->getUser() !== $this->getUser()) {
        throw $this->createAccessDeniedException("Sie können diese Anzeige nicht bearbeiten.");
    }

    return $this->render('particulier/modifierAnnonce.html.twig', [
        'annonce' => $annonce
    ]);
}


#[Route('/particulier/annonces/modifier/{id}', name: 'modifier_annonce_ajax', methods: ['POST'])]
public function modifierAnnonceAjax(Request $request, AnnonceRepository $repo, EntityManagerInterface $em, int $id)
{
    $annonce = $repo->find($id);

    if (!$annonce || !$this->isGranted('ROLE_PARTICULIER') || $annonce->getUser() !== $this->getUser()) {
        return $this->json(['error' => 'Non autorisé'], 403);
    }

    $annonce->setNom($request->get('nom'));
    $annonce->setMarque($request->get('marque'));
    $annonce->setModele($request->get('modele'));
    $annonce->setReference($request->get('reference'));
    $annonce->setPrix((float)$request->get('prix'));
    $annonce->setDescription($request->get('description'));
    $annonce->setImagePrincipale($request->get('banner'));

    // Galerie-Bilder hinzufügen, ohne alte zu löschen
    $cloudImages = $request->get('cloudImages');
    if ($cloudImages) {
        if (is_string($cloudImages)) {
            $cloudImages = json_decode($cloudImages, true);
        }
        $existingImages = $annonce->getImages() ?? [];
        $annonce->setImages(array_merge($existingImages, $cloudImages));
    }

    $annonce->setDateModification(new \DateTime());

    $em->persist($annonce);
    $em->flush();

    return $this->json(['success' => true]);
}



 #[Route('/particulier/annonces/supprimer', name: 'supprimer_annonce_ajax', methods: ['POST'])]
public function supprimerAnnonceAjax(Request $request, EntityManagerInterface $em): JsonResponse
{
    $id = $request->request->get('id');
    $annonce = $em->getRepository(Annonce::class)->find($id);

   if (!$annonce) {
    return $this->json(['error' => 'Annonce introuvable'], 404);
}

    $em->remove($annonce);
    $em->flush();

    return new JsonResponse(['success' => true]);
}

#[Route('/particulier/annonce/marquer-vendu', name: 'marquer_vendu_ajax', methods: ['POST'])]
public function marquerVenduAjax(Request $request, AnnonceRepository $repo, EntityManagerInterface $em)
{
    $id = $request->request->get('id');
    $annonce = $repo->find($id);

    // Vérifier que l'annonce existe et que l'utilisateur connecté est le propriétaire
    if (!$annonce || $annonce->getUser() !== $this->getUser()) {
        return $this->json(['success' => false], 403);
    }

    // Modifier le statut
    $annonce->setStatut('vendu');
    $em->flush();

    return $this->json(['success' => true]);
}

#[Route('/particulier/message/{receiverId}', name: 'part_message')]
public function message(
    int $receiverId,
    Request $request,
    EntityManagerInterface $em,
    UsersRepository $usersRepo,
    MessageRepository $messageRepo
) {
    $sender = $this->getUser();
    $receiver = $usersRepo->find($receiverId);

    if (!$receiver) {
        throw $this->createNotFoundException('Utilisateur non trouvé');
    }

    // ---------------------------
    // Liste des utilisateurs avec lesquels on a échangé des messages
    // ---------------------------
    $allMessages = $messageRepo->createQueryBuilder('m')
        ->where('m.receiver = :me OR m.sender = :me')
        ->setParameter('me', $sender)
        ->orderBy('m.createdAt', 'DESC')
        ->getQuery()
        ->getResult();

    $usersWithMessages = [];
    foreach ($allMessages as $msg) {
        // Identifier l'autre utilisateur dans la conversation
        $otherUser = $msg->getSender()->getId() === $sender->getId() ? $msg->getReceiver() : $msg->getSender();

        // On ne garde que le dernier message par utilisateur
        if (!isset($usersWithMessages[$otherUser->getId()])) {
            $usersWithMessages[$otherUser->getId()] = [
                'user' => $otherUser,
                'lastMessage' => $msg
            ];
        }
    }

    $usersWithMessages = array_values($usersWithMessages);

    // ---------------------------
    // Envoi d’un message
    // ---------------------------
    if ($request->isMethod('POST')) {
        $content = $request->request->get('content');

        $message = new Message();
        $message->setSender($sender);
        $message->setReceiver($receiver);
        $message->setContent($content);
        $message->setCreatedAt(new \DateTimeImmutable());
        // Définir isRead à false pour un message nouvellement envoyé
    $message->setIsRead(false);

        $em->persist($message);
        $em->flush();

        return $this->json(['success' => true]);
    }

    // ---------------------------
    // Charger la conversation entre sender et receiver
    // ---------------------------
    $messages = $messageRepo->findConversation($sender, $receiver);

    return $this->render('particulier/message.html.twig', [
        'messages' => $messages,
        'receiver' => $receiver,
        'usersWithMessages' => $usersWithMessages
    ]);
}

#[Route('/particulier/get-messages/{receiverId}', name: 'get_messages')]
public function getMessages(
    int $receiverId,
    UsersRepository $usersRepo,
    MessageRepository $messageRepo
) {
    $sender = $this->getUser();
    $receiver = $usersRepo->find($receiverId);

    if (!$receiver) {
        return $this->json(['error' => 'Utilisateur non trouvé'], 404);
    }

    $messages = $messageRepo->findConversation($sender, $receiver);

    // Transformer les messages en format JSON
    $data = [];
    foreach ($messages as $msg) {
        $data[] = [
            'id' => $msg->getId(),
            'content' => $msg->getContent(),
            'senderId' => $msg->getSender()->getId(),
            'receiverId' => $msg->getReceiver()->getId(),
            'createdAt' => $msg->getCreatedAt()->format('Y-m-d H:i'),
        ];
    }

    return $this->json(['messages' => $data]);
}


#[Route('/particulier/mark-as-read/{senderId}', name: 'mark_as_read', methods: ['POST'])]
public function markAsRead(
    int $senderId,
    EntityManagerInterface $em,
    UsersRepository $usersRepo,
    MessageRepository $messageRepo
) {
    $user = $this->getUser();
    $sender = $usersRepo->find($senderId);
    if (!$sender) return $this->json(['error' => 'Utilisateur non trouvé'], 404);

    $messages = $messageRepo->createQueryBuilder('m')
        ->where('m.sender = :sender AND m.receiver = :receiver AND m.isRead = false')
        ->setParameter('sender', $sender)
        ->setParameter('receiver', $user)
        ->getQuery()
        ->getResult();

    foreach ($messages as $msg) {
        $msg->setIsRead(true);
    }

    $em->flush();

    return $this->json(['success' => true]);
}

}