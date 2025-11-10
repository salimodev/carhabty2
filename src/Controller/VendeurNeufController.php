<?php

namespace App\Controller;

use App\Entity\Demande;
use App\Entity\Users;
use App\Service\UsersService;
use App\Entity\OffrePiece;
use App\Entity\Pieces;
use App\Entity\Offre;
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

class VendeurNeufController extends AbstractController
{
    #[Route(path: '/vendeur/neuf/LayoutSeller', name: 'layout_seller', methods: "GET")]
    public function layout_seller()
    {


        return $this->render('vendeurNeuf/layoutVN.html.twig');
    }

  #[Route(path: '/vendeur/neuf/dashboard', name: 'dashboard_vendeurNeuf', methods: "GET")]
public function dashboard_VN(DemandeRepository $demandeRepository, Request $request, EntityManagerInterface $em,OffreRepository $offreRepo)
{
    $session = $request->getSession();
    $session->set('PageMenu', 'dashboard_vendeurNeuf');

    $user = $this->getUser();
    $zoneVendeur = $user->getAdresse();

    $countDemandes = $demandeRepository->countDemandesDispoVendeur($zoneVendeur);
    $dernieresDemandes = $demandeRepository->findLatestForVendeurNeuf($zoneVendeur);

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


  // Calcul du taux d'acceptation
    $tauxAcceptation = $nombreOffres > 0 ? round(($nbOffresAcceptees / $nombreOffres) * 100, 2) : 0;

    return $this->render('vendeurNeuf/dashboardVN.html.twig', [
        'countDemandes' => $countDemandes,
        'demandes' => $dernieresDemandes,
        'nombreOffres' => $nombreOffres,
        'dernieresOffres' => $dernieresOffres,
        'nbOffresAcceptees' => $nbOffresAcceptees,
         'tauxAcceptation' => $tauxAcceptation
    ]);
}


    #[Route('/vendeur/demande/detail/{id}', name: 'detail_demande_vendeur')]
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

        return $this->render('vendeurNeuf/detailDemande.html.twig', [
            'demande' => $demande,
            'pieces' => $pieces,
            'client' => $client,
        ]);
    }

    #[Route('/vendeur/demandes', name: 'vendeur_demandes')]
public function demandesDisponibles(Request $request,DemandeRepository $demandeRepository, PaginatorInterface $paginator): Response
{
    $session = $request->getSession();
        $session->set('PageMenu', 'vendeur_demandes');
    $user = $this->getUser();
    $zoneVendeur = $user->getAdresse();

    $demandes = $demandeRepository->findAllForVendeurNeuf($zoneVendeur);
$dem = $paginator->paginate(
        $demandes,
        $request->query->getInt('page', 1),
        12
    );
    return $this->render('vendeurNeuf/demandes.html.twig', [
        'demandes' => $dem,
    ]);
}

#[Route('/recherche/demande/vendeur/neuf', name: 'recherche_demande_vendeur_neuf')]
public function rechercheDemandeVendeurNeuf(Request $request, EntityManagerInterface $em): JsonResponse
{
    $marque = $request->get('marque');
    $date   = $request->get('date');
    $vendeur = $this->getUser();
    $zoneVendeur = $vendeur->getAdresse(); 

    $demandes = $em->getRepository(Demande::class)
                   ->filterDemandesvendeurNeuf($marque, $zoneVendeur, $date, 'neuf', null);

    $result = [];
    foreach ($demandes as $d) {
        $pieces = [];
        foreach ($d->getPieces() as $p) {
            $pieces[] = [
                'designation' => $p->getDesignation(),
                'observation' => $p->getObservation(),
                'photo'       => $p->getPhoto() ?: '/assets/img/placeholder.png',
            ];
        }

        // Vérifie si l’utilisateur a déjà proposé une offre pour cette demande
        $dejaPropose = count(array_filter($d->getOffres()->toArray(), fn($o) => $o->getUser()->getId() === $vendeur->getId())) > 0;

        $result[] = [
            'id'         => $d->getId(),
            'marque'     => $d->getMarque(),
            'modele'     => $d->getModele(),
            'zone'       => $d->getZone(),
            'date'       => $d->getDatecreate()->format('Y-m-d H:i'),
            'pieces'     => $pieces,
            'dejaPropose'=> $dejaPropose, // ajout de la propriété pour le JS
        ];
    }

    return new JsonResponse($result);
}

#[Route(path: '/vendeur/profile/Modifier/{id}', name: 'app_vendeurneuf_profile', methods: ['GET'])]
public function profile(
    int $id,
    Request $request,
    ManagerRegistry $doctrine,
    UsersService $UsersService
): Response {
    $session = $request->getSession();
    $session->set('PageMenu', 'app_vendeurneuf_profile');

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

    return $this->render('vendeurNeuf/profile.html.twig', [
        'profileuser' => $profileuser,
    ]);
}

   #[Route(path: '/vendeur/profile/Modifier', name: 'Modifier_profile_vendeurNeuf')]
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
  #[Route('/demande/{id}/proposer-offre', name: 'propose_offre', methods: ['GET'])]
    public function showForm(Demande $demande): Response
    {
        // Vérifier que l'utilisateur est vendeur
        if (!$this->isGranted('ROLE_VENDEUR_NEUF')) {
            throw $this->createAccessDeniedException('Accès refusé');
        }

        return $this->render('vendeurNeuf/proposeoffre.html.twig', [
            'demande' => $demande,
        ]);
    }

#[Route('/demande/{id}/proposer-offre', name: 'offre_create', methods: ['POST'])]
public function createOffre(Request $request, Demande $demande, EntityManagerInterface $em): Response
{
    $user = $this->getUser();

    $offre = new Offre();
    $offre->setDemande($demande);
    $offre->setUser($user);
    $offre->setNumeroOffre('OFF-' . $demande->getId() . '-' . time());
    $offre->setObservation($request->request->get('observation'));

    // --- Gestion validité ---
    $validite = $request->request->get('validite');
    if ($validite) {
        $dates = explode(' - ', $validite);
        if (count($dates) === 2) {
            try {
                $debut = \DateTimeImmutable::createFromFormat('d/m/Y H:i:s', trim($dates[0]) . ' 00:00:01');
                $fin = \DateTimeImmutable::createFromFormat('d/m/Y H:i:s', trim($dates[1]) . ' 23:59:59');

                if (!$debut || !$fin) {
                    throw new \Exception('Erreur de format de date');
                }

                $offre->setValiditeDebut($debut);
                $offre->setValiditeFin($fin);
            } catch (\Exception $e) {
                $this->addFlash('error', 'Format de date invalide pour la validité.');
            }
        }
    }

    // --- Récupération des pièces ---
    $postData = $request->request->all();
    $piecesData = $postData['pieces'] ?? [];

    $offreValide = true; // contrôle global

    foreach ($demande->getPieces() as $piece) {
        if (!isset($piecesData[$piece->getId()])) {
            continue;
        }

        $data = $piecesData[$piece->getId()];
        $offrePiece = new OffrePiece();
        $offrePiece->setPiece($piece);
        $offrePiece->setOffre($offre);

        // --- Vérifie au moins une combinaison (prixX + marqueX) ---
        $combinaisonValide = false;

        for ($i = 1; $i <= 3; $i++) {
            $prix = trim($data["prix$i"] ?? '');
            $marque = trim($data["marque$i"] ?? '');

            // S’il y a une paire complète (prix + marque)
            if ($prix !== '' && $marque !== '') {
                $combinaisonValide = true;
            }

            // On enregistre les valeurs, même si vides
            $setPrix = 'setPrix' . $i;
            $setMarque = 'setMarque' . $i;
            $offrePiece->$setPrix($prix !== '' ? $prix : null);
            $offrePiece->$setMarque($marque !== '' ? $marque : null);
        }

        if (!$combinaisonValide) {
            $offreValide = false;
        }

        $em->persist($offrePiece);
        $offre->addOffrePiece($offrePiece);
    }

    // 🔴 Si une pièce n’a aucune combinaison valide
    if (!$offreValide) {
        $this->addFlash('error', 'Chaque pièce doit avoir au moins une combinaison (prix + marque) remplie.');
        return $this->redirectToRoute('offre_create', ['id' => $demande->getId()]);
    }

    $em->persist($offre);
    $em->flush();

    $this->addFlash('success', 'Offre proposée avec succès !');
    return $this->redirectToRoute('vendeur_offres');
}


    #[Route('/vendeur/neuf/offres', name: 'vendeur_offres')]
    public function toutesOffres(Request $request,EntityManagerInterface $em): Response
    {
          $session = $request->getSession();
        $session->set('PageMenu', 'vendeur_offres');
        $user = $this->getUser();

        // Vérifier que l'utilisateur est connecté
        if (!$user) {
            $this->addFlash('error', 'Vous devez être connecté pour voir vos offres.');
            return $this->redirectToRoute('app_login'); // adapter selon ton login route
        }
        

        // Récupérer toutes les offres du vendeur
        $offres = $em->getRepository(Offre::class)
            ->createQueryBuilder('o')
            ->where('o.user = :user')
            ->setParameter('user', $user)
            ->orderBy('o.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
$now = new \DateTimeImmutable('today'); // ignore l’heure
foreach ($offres as $offre) {
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
        return $this->render('vendeurNeuf/mesoffres.html.twig', [
            'offres' => $offres,
        ]);
    }

  #[Route('/offre/retirer', name: 'retirer_offre', methods: ['POST'])]
public function retirerOffre(Request $request, EntityManagerInterface $em): JsonResponse
{
    $id = $request->request->get('id');
    $offre = $em->getRepository(Offre::class)->find($id);

    if (!$offre) {
        return new JsonResponse(['success' => false, 'message' => 'Offre introuvable !']);
    }

    $em->remove($offre);
    $em->flush();

    return new JsonResponse(['success' => true]);
}


// src/Controller/VendeurNeufController.php

#[Route('/offre/{id}/modifier', name: 'offre_edit', methods: ['GET', 'POST'])]
public function editOffre(
    Offre $offre,
    Request $request,
    EntityManagerInterface $em
): Response {
    // Récupérer les pièces de la demande associée
    $pieces = $offre->getDemande()->getPieces();

    if ($request->isMethod('POST')) {
        $postData = $request->request->all();
        $piecesData = $postData['pieces'] ?? [];

        // Mettre à jour l'observation
        $offre->setObservation($postData['observation'] ?? '');

        // Gestion de la validité
        $validite = $postData['validite'] ?? null;
        if ($validite) {
            $dates = explode(' - ', $validite);
            if (count($dates) === 2) {
                $debut = \DateTimeImmutable::createFromFormat('d/m/Y H:i:s', trim($dates[0]) . ' 00:00:01');
                $fin   = \DateTimeImmutable::createFromFormat('d/m/Y H:i:s', trim($dates[1]) . ' 23:59:59');
                if ($debut && $fin) {
                    $offre->setValiditeDebut($debut);
                    $offre->setValiditeFin($fin);
                }
            }
        }

        // Mise à jour des OffrePiece existantes ou création si nécessaire
        foreach ($pieces as $piece) {
            $data = $piecesData[$piece->getId()] ?? null;
            if (!$data) {
                continue;
            }

            $offrePiece = $offre->getOffrePieceByPiece($piece->getId());
            if (!$offrePiece) {
                $offrePiece = new OffrePiece();
                $offrePiece->setPiece($piece);
                $offrePiece->setOffre($offre);
                $em->persist($offrePiece);
                $offre->addOffrePiece($offrePiece);
            }

            // Gestion des prix avec valeur par défaut 0
            foreach (['prix1','prix2','prix3'] as $prix) {
                $setter = 'set' . ucfirst($prix);
                $value = isset($data[$prix]) && $data[$prix] !== '' ? $data[$prix] : 0;
                $offrePiece->$setter($value);
            }

            // Gestion des marques
            foreach (['marque1','marque2','marque3'] as $marque) {
                $setter = 'set' . ucfirst($marque);
                $value = $data[$marque] ?? null;
                $offrePiece->$setter($value);
            }
        }

        $em->flush();
        $this->addFlash('success', 'Offre mise à jour avec succès !');

        return $this->redirectToRoute('vendeur_offres');
    }

    return $this->render('vendeurNeuf/modifoffre.html.twig', [
        'offre' => $offre,
        'demande' => $offre->getDemande(),
    ]);
}


#[Route('/vendeur/offre/{id}', name: 'offre_show')]
public function show(Offre $offre): Response
{
    return $this->render('vendeurNeuf/voiroffre.html.twig', [
        'offre' => $offre,
    ]);
}

}
