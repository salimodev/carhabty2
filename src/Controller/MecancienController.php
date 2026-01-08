<?php

namespace App\Controller;


use App\Entity\Demande;
use App\Entity\Users;
use App\Service\UsersService;
use App\Entity\Offre;
use App\Entity\Notification;
use App\Entity\InvitePageMecanicien;
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


final class MecancienController extends AbstractController
{
    #[Route('/mecanicien', name: 'app_mecancien')]
    public function dashboard_mecano(Request $request, EntityManagerInterface $em, OffreRepository $offreRepo): Response
    {
        $session = $request->getSession();
        $session->set('PageMenu', 'mecanicien');
        $user = $this->getUser();

        if (!$user) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour voir vos demandes.');
        }

        // ✅ Récupérer toutes les demandes liées à ce user
        $demandes = $em->getRepository(\App\Entity\Demande::class)
            ->findBy(
                ['offrecompte' => $user], // critère
                ['datecreate' => 'DESC'], // ordre
                5 // limite
            );
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

        return $this->render('mecancien/mecancien.html.twig', [
            'demandeCount' => $demandeCount,
            'demandes' => $demandes,
            'nbOffres' => $nbOffres,
            'nbOffresAcceptees' => $nbOffresAcceptees,
        ]);
    }

    #[Route(path: '/mecanicien/profile/Modifier/{id}', name: 'app_mecancien_profile', methods: ['GET'])]
    public function profile(
        int $id,
        Request $request,
        ManagerRegistry $doctrine,
        UsersService $UsersService
    ): Response {
        $session = $request->getSession();
        $session->set('PageMenu', 'app_mecancien_profile');

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

        return $this->render('mecancien/profile.html.twig', [
            'profileuser' => $profileuser,
        ]);
    }

    #[Route(path: '/mecanicien/profile/Modifier', name: 'Modifier_profile_mecancien')]
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


    #[Route(path: '/mecanicien/demandes', name: 'app_mecanicien_demandes')]
    public function demandedeprix_prop(Request $request, EntityManagerInterface $em): Response
    {
        $session = $request->getSession();
        $session->set('PageMenu', 'mecanicien_demande');
        $user = $this->getUser();

        if (!$user) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour voir vos demandes.');
        }
        $demandes = $em->getRepository(\App\Entity\Demande::class)
            ->findBy(['offrecompte' => $user], ['datecreate' => 'DESC']);
        return $this->render('mecancien/demandes.html.twig', [
            'demandes' => $demandes
        ]);
    }

    #[Route('/mecanicien/demande/detail/{code}', name: 'detail_demande_mecan')]
    public function detailDemande(
        string $code,
        Request $request,
        SessionInterface $session,
        DemandeRepository $demandeRepository
    ): Response {
        $session = $request->getSession();
        $session->set('PageMenu', 'detail_demande_mecan');

        // 🔹 Récupérer la demande
        $demande = $demandeRepository->findOneBy([
        'code' => $code
    ]);

        if (!$demande) {
            throw $this->createNotFoundException('Demande introuvable');
        }

        // 🔹 Récupérer les pièces liées
        $pieces = $demande->getPieces();

        // 🔹 Récupérer le client
        $client = $demande->getOffrecompte();

        return $this->render('mecancien/detailDemandeMec.html.twig', [
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

    #[Route('/mecanicien/mes-offres', name: 'mecan_offres')]
    public function mesOffres(Request $request, DemandeRepository $demandeRepo, OffreRepository $offreRepo): Response
    {

        $session = $request->getSession();
        $session->set('PageMenu', 'mecan_offres');
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

        return $this->render('mecancien/offres.html.twig', [
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
            // 🆕 On lie la notification à l’offre concernée
            $notif->setOffre($offre);

            $em->persist($notif);
            $em->flush();
        }

        return $this->json(['success' => true, 'message' => 'Le statut de l\'offre a été mis à jour et le vendeur notifié.']);
    }



    #[Route('/mecanicien/offre/{numeroOffre}', name: 'offre_show_mecano', methods: ['GET'])]
    public function showOffre(
        EntityManagerInterface $em,
        NotificationRepository $notificationRepository,
        Security $security,
        Offre $offre
    ): Response {
        $user = $security->getUser();

        $offrePieces = $offre->getOffrePieces();

        if ($user) {
            // Chercher la notification liée à cette offre et à cet utilisateur
            $notification = $notificationRepository->findOneBy([
                'User'  => $user,
                'offre' => $offre,
                'isRead' => false
            ]);

            // Si elle existe, on la marque comme lue
            if ($notification) {
                $notification->setIsRead(true);
                $em->flush();
            }
        }
        return $this->render('mecancien/offreDetail.html.twig', [
            'offre' => $offre,
            'offrePieces' => $offrePieces,
            'demande' => $offre->getDemande()
        ]);
    }


    #[Route('/inviter/mecanicien', name: 'inviter_mecanicien')]
    public function inviter(EntityManagerInterface $em): Response
    {
        $invitePage = $em->getRepository(InvitePageMecanicien::class)->find(1);
        return $this->render('mecancien/inviter.html.twig', [
        'invitePage' => $invitePage
    ]);
    }

       #[Route('/mecanicien/notifications', name: 'mecanicien_notifications')]
    public function notification(EntityManagerInterface $em, Request $request, PaginatorInterface $paginator): Response
    {

        $session = $request->getSession();
        $session->set('PageMenu', 'mecanicien_notifications');
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        // Récupérer toutes les notifications du propriétaire connecté
        $query = $em->getRepository(Notification::class)
            ->createQueryBuilder('n')
            ->where('n.User = :user')
            ->setParameter('user', $user)
            ->orderBy('n.createdAt', 'DESC')
            ->getQuery();

        // Pagination
        $notifications = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('mecancien/notification.html.twig', [
            'notifications' => $notifications,
        ]);
    }
}
