<?php

namespace App\Controller;

use App\Entity\Users;
use App\Entity\Demande;
use App\Entity\Pieces;
use App\Service\UsersService;
use App\Entity\Offre;
use App\Entity\Annonce;
use App\Entity\BannerMenu;
use App\Entity\Footer;
use App\Repository\VisitRepository;
use App\Repository\UsersRepository;
use App\Repository\DemandeRepository;
use App\Repository\AnnonceRepository;
use App\Repository\OffreRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;

final class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    public function index(AnnonceRepository $repo ,DemandeRepository $demandeRepository): Response
    {
        $totalEnAttenteA = $repo->count(['statut' => 'en_attente']);
        $demandesEnAttente = $demandeRepository->createQueryBuilder('d')
    ->select('COUNT(d.id)')
    ->where('d.publier = :publier')
    ->andWhere('d.statut = :statut')
    ->setParameter('publier', 0)
    ->setParameter('statut', 'en_attente')
    ->getQuery()
    ->getSingleScalarResult();
        return $this->render('LayoutAdmin.html.twig', [
    'demandesEnAttente' => $demandesEnAttente,'totalEnAttenteA'=>$totalEnAttenteA
   
]);
    }

   #[Route(path: '/admin/dashboard', name: 'dashboard_admin')]
public function dashboard(VisitRepository $visitRepository,UsersRepository $usersRepository,DemandeRepository $demandeRepository,AnnonceRepository $repo): Response
{
     $totalEnAttenteA = $repo->count(['statut' => 'en_attente']);
    // Nombre de visiteurs aujourd'hui
    $today = new \DateTime();
    $today->setTime(0, 0, 0);

    $visitorsToday = $visitRepository->createQueryBuilder('v')
        ->select('COUNT(v.id)')
        ->where('v.visitedAt >= :today')
        ->setParameter('today', $today)
        ->getQuery()
        ->getSingleScalarResult();

     $demandesEnAttente = $demandeRepository->createQueryBuilder('d')
    ->select('COUNT(d.id)')
    ->where('d.publier = :publier')
    ->andWhere('d.statut = :statut')
    ->setParameter('publier', 0)
    ->setParameter('statut', 'en_attente')
    ->getQuery()
    ->getSingleScalarResult();
       
    
    // Passer à Twig
    return $this->render('admin/dashboard.html.twig', [
        'visitorsToday' => $visitorsToday, 'demandesEnAttente' => $demandesEnAttente,'totalEnAttenteA'=>$totalEnAttenteA,
        
    ]);
}

#[Route('/admin/dashboard/annonces-data', name: 'dashboard_annonces_data')]
public function annoncesData(AnnonceRepository $repo): JsonResponse
{
   $totalAttente = (int) $repo->count(['publier' => 0]) ?? 0;
$totalPublie  = (int) $repo->count(['publier' => 1]) ?? 0;
$totalVendu   = (int) $repo->count(['statut' => 'vendu']) ?? 0;

    return $this->json([
        'attente' => (int) $totalAttente,
        'publie'  => (int) $totalPublie,
        'vendu'   => (int) $totalVendu
    ]);
}

    
#[Route(path: '/admin/visiteurs/total', name: 'visiteurs_total')]
public function visiteursTotal(VisitRepository $visitRepository): JsonResponse
{
    $today = new \DateTime();
    $today->setTime(0, 0, 0);

    $visitorsToday = $visitRepository->createQueryBuilder('v')
        ->select('COUNT(v.id)')
        ->where('v.visitedAt >= :today')
        ->setParameter('today', $today)
        ->getQuery()
        ->getSingleScalarResult();

    return new JsonResponse(['total' => $visitorsToday]);
}


#[Route('/admin/stats/users-role', name: 'stats_users_role')]
public function statsUsersRole(UsersRepository $usersRepository): JsonResponse
{
    return $this->json([
        'proprietaires' => $usersRepository->countRole('ROLE_PROPRIETAIRE'),
        'mecaniciens' => $usersRepository->countRole('ROLE_MECANICIEN'),
        'vendeursNeuf' => $usersRepository->countRole('ROLE_VENDEUR_NEUF'),
        'vendeursOccasion' => $usersRepository->countRole('ROLE_VENDEUR_OCCASION'),
        'particuliers' => $usersRepository->countRole('ROLE_PARTICULIER'),
    ]);
}

#[Route(path: '/admin/dashboard/demandes-stats', name: 'dashboard_demandes_stats')]
public function demandesStats(DemandeRepository $demandeRepository): JsonResponse
{
    $totalDemandes = $demandeRepository->countTotalDemandes();
    $demandesNeuf = $demandeRepository->countDemandesNeuf();
    $demandesOccasion = $demandeRepository->countDemandesOccasion();

    return $this->json([
        'totalDemandes' => $totalDemandes,
        'demandesNeuf' => $demandesNeuf,
        'demandesOccasion' => $demandesOccasion,
    ]);
}

#[Route(path: '/admin/dashboard/demandes-data', name: 'dashboard_demandes_data')]
public function demandesData(DemandeRepository $demandeRepository): JsonResponse
{
    $qb = $demandeRepository->createQueryBuilder('d')
        ->select("SUBSTRING(d.datecreate, 1, 10) as jour")
        ->addSelect("SUM(CASE WHEN d.vendeurneuf = 1 THEN 1 ELSE 0 END) as nbNeuf")
        ->addSelect("SUM(CASE WHEN d.vendeuroccasion = 1 THEN 1 ELSE 0 END) as nbOccasion")
        ->groupBy('jour')
        ->orderBy('jour', 'ASC');

    $results = $qb->getQuery()->getResult();

    // 👉 Sécurité : si aucune demande dans la BD
    if (empty($results)) {
        return $this->json([
            'labels' => [],
            'neuf' => [],
            'occasion' => []
        ]);
    }

    $labels = [];
    $neufData = [];
    $occasionData = [];

    foreach ($results as $r) {
        // 👉 Sécurité : vérifier que la date existe
        if (!empty($r['jour'])) {
            $date = new \DateTimeImmutable($r['jour']);
            $labels[] = $date->format('d/m');
        } else {
            $labels[] = "";
        }

        $neufData[] = (int) ($r['nbNeuf'] ?? 0);
        $occasionData[] = (int) ($r['nbOccasion'] ?? 0);
    }

    return $this->json([
        'labels' => $labels,
        'neuf' => $neufData,
        'occasion' => $occasionData
    ]);
}


#[Route('/admin/dashboard/offres-data', name: 'dashboard_offres_data')]
public function offresData(OffreRepository $offreRepository): JsonResponse
{
    $stats = $offreRepository->getOffresStatsForJson();

return $this->json([
    'refuse' => (int)($stats['refusee'] ?? 0),
    'accepte' => (int)($stats['acceptee'] ?? 0),
    'en_attente' => (int)($stats['en_attente'] ?? 0),
    'total' => (int)($stats['total'] ?? 0)
]);


}

 #[Route('/admin/dashboard/offres-par-jour', name: 'dashboard_offres_jour')]
public function offresParJour(OffreRepository $offreRepository): JsonResponse
{
   $qb = $offreRepository->createQueryBuilder('o')
    ->select("SUBSTRING(o.createdAt, 1, 10) AS date, COUNT(o.id) AS total")
    ->groupBy("date")
    ->orderBy("date", "ASC");

$result = $qb->getQuery()->getResult();

$labels = [];
$offres = [];
foreach ($result as $row) {
    $labels[] = $row['date'];
    $offres[] = (int)$row['total'];
}

return $this->json([
    'labels' => $labels,
    'offres' => $offres,
]);

}


 // Liste des mécaniciens
#[Route('/admin/users/mecanicien', name: 'users_liste_mecanicien')]
public function admin_users_mecanicien(UsersRepository $usersRepository, DemandeRepository $demandeRepo,AnnonceRepository $AnnonceRepository): Response
{
    $mecaniciens = $usersRepository->findByRole('ROLE_MECANICIEN'); // muss Entities zurückgeben

foreach ($mecaniciens as $m) {
    // wenn $m ein Array ist: $m['id']
    $id = is_array($m) ? $m['id'] : $m->getId();
    $m->nbDemandes = $demandeRepo->countByUserId($id);
}
   $demandesEnAttente = $demandeRepo->createQueryBuilder('d')
    ->select('COUNT(d.id)')
    ->where('d.publier = :publier')
    ->andWhere('d.statut = :statut')
    ->setParameter('publier', 0)
    ->setParameter('statut', 'en_attente')
    ->getQuery()
    ->getSingleScalarResult();
 $totalEnAttenteA = $AnnonceRepository->count(['statut' => 'en_attente']);
    return $this->render('admin/mecancien.html.twig', [
        'users' => $mecaniciens,'demandesEnAttente' => $demandesEnAttente,'totalEnAttenteA'=>$totalEnAttenteA
    ]);
}

// Liste des propriétaires
#[Route('/admin/users/proprietaire', name: 'users_liste_proprietaire')]
public function admin_users_proprietaire(UsersRepository $usersRepository, DemandeRepository $demandeRepo,AnnonceRepository $AnnonceRepository): Response
{
    
     // Récupérer seulement les propriétaires
    $proprietaires = $usersRepository->findByRole('ROLE_PROPRIETAIRE');

    // Ajouter nbDemandes dans chaque objet user
    foreach ($proprietaires as $p) {
        $p->nbDemandes = $demandeRepo->countByProprietaire($p);
    }

     $demandesEnAttente = $demandeRepo->createQueryBuilder('d')
    ->select('COUNT(d.id)')
    ->where('d.publier = :publier')
    ->andWhere('d.statut = :statut')
    ->setParameter('publier', 0)
    ->setParameter('statut', 'en_attente')
    ->getQuery()
    ->getSingleScalarResult();
$totalEnAttenteA = $AnnonceRepository->count(['statut' => 'en_attente']);
    return $this->render('admin/proprietaire.html.twig', [
        'users' => $proprietaires,'demandesEnAttente' => $demandesEnAttente,'totalEnAttenteA'=>$totalEnAttenteA
    ]);
}

#[Route('/admin/users/venneur-neuf', name: 'users_liste_vendeur_neuf')]
public function admin_users_venneur_neuf(
    UsersRepository $usersRepository, 
    OffreRepository $offreRepository, DemandeRepository $demandeRepo,AnnonceRepository $AnnonceRepository
): Response
{
    $vendeursNeufs = $usersRepository->findByRole('ROLE_VENDEUR_NEUF');
    
    $offresCount = [];
    foreach ($vendeursNeufs as $u) {
        $offresCount[$u->getId()] = $offreRepository->countByVendeurNeuf($u);
    }

     $demandesEnAttente = $demandeRepo->createQueryBuilder('d')
    ->select('COUNT(d.id)')
    ->where('d.publier = :publier')
    ->andWhere('d.statut = :statut')
    ->setParameter('publier', 0)
    ->setParameter('statut', 'en_attente')
    ->getQuery()
    ->getSingleScalarResult();
$totalEnAttenteA = $AnnonceRepository->count(['statut' => 'en_attente']);
    return $this->render('admin/venneur_neuf.html.twig', [
        'users' => $vendeursNeufs,
        'offresCount' => $offresCount,'demandesEnAttente' => $demandesEnAttente,'totalEnAttenteA'=>$totalEnAttenteA
    ]);
}


// Liste des vendeurs d’occasion
#[Route('/admin/users/venneur-occasion', name: 'users_liste_vendeur_occas')]
public function admin_users_venneur_occasion(
    UsersRepository $usersRepository,
    OffreRepository $offreRepository, DemandeRepository $demandeRepo,AnnonceRepository $AnnonceRepository
): Response
{
    $vendeursOccasion = $usersRepository->findByRole('ROLE_VENDEUR_OCCASION');

    $offresCount = [];
    foreach ($vendeursOccasion as $u) {
        $offresCount[$u->getId()] = $offreRepository->countByVendeurOccasion($u);
    }

      $demandesEnAttente = $demandeRepo->createQueryBuilder('d')
    ->select('COUNT(d.id)')
    ->where('d.publier = :publier')
    ->andWhere('d.statut = :statut')
    ->setParameter('publier', 0)
    ->setParameter('statut', 'en_attente')
    ->getQuery()
    ->getSingleScalarResult();
$totalEnAttenteA = $AnnonceRepository->count(['statut' => 'en_attente']);
    return $this->render('admin/venneur_occas.html.twig', [
        'users' => $vendeursOccasion,
        'offresCount' => $offresCount,'demandesEnAttente' => $demandesEnAttente,'totalEnAttenteA'=>$totalEnAttenteA
    ]);
}


// Liste des particuliers
#[Route('/admin/users/particulier', name: 'users_liste_particulier')]
public function admin_users_particulier(
    UsersRepository $usersRepository,
    AnnonceRepository $annonceRepository, DemandeRepository $demandeRepo
): Response {
    // Alle Particuliers holen
    $particuliers = $usersRepository->findByRole('ROLE_PARTICULIER');

    // Anzahl der Annonces pro Nutzer berechnen
    $annoncesCount = [];
    foreach ($particuliers as $u) {
        $annoncesCount[$u->getId()] = $annonceRepository->countByUser($u);
    }
      $demandesEnAttente = $demandeRepo->createQueryBuilder('d')
    ->select('COUNT(d.id)')
    ->where('d.publier = :publier')
    ->andWhere('d.statut = :statut')
    ->setParameter('publier', 0)
    ->setParameter('statut', 'en_attente')
    ->getQuery()
    ->getSingleScalarResult();
$totalEnAttenteA = $annonceRepository->count(['statut' => 'en_attente']);
    return $this->render('admin/particulier.html.twig', [
        'users' => $particuliers,
        'annoncesCount' => $annoncesCount,'demandesEnAttente' => $demandesEnAttente,'totalEnAttenteA'=>$totalEnAttenteA
    ]);
}


#[Route('/admin/users/mecanicien/delete/{id}', name: 'admin_users_delete_mecanicien')]
public function deleteMecanicien(Users $user, EntityManagerInterface $em): RedirectResponse
{
    if ($user->getAnnonces()->count() > 0 || $user->getDemandes()->count() > 0 || $user->getOffres()->count() > 0) {
        $this->addFlash('danger', "Impossible de supprimer cet utilisateur : il possède des données liées.");
    } else {
        $em->remove($user);
        $em->flush();
        $this->addFlash('success', "Utilisateur supprimé avec succès.");
    }

    return $this->redirectToRoute('users_liste_mecanicien');
}

#[Route('/admin/users/mecanicien/toggle-block/{id}', name: 'admin_users_toggle_block_mecanicien')]
public function toggleBlockMecanicien(Users $user, EntityManagerInterface $em): RedirectResponse
{
    $user->setIsBlocked(!$user->isBlocked());
    $em->flush();

    $message = $user->isBlocked() ? "L’utilisateur a été bloqué." : "L’utilisateur a été débloqué.";
    $this->addFlash('info', $message);

    return $this->redirectToRoute('users_liste_mecanicien');
}

#[Route('/admin/users/venneur-occasion/delete/{id}', name: 'admin_users_delete_venneur_occas')]
public function deleteVendeurOccas(Users $user, EntityManagerInterface $em): RedirectResponse
{
    if ($user->getAnnonces()->count() > 0 || $user->getDemandes()->count() > 0 || $user->getOffres()->count() > 0) {
        $this->addFlash('danger', "Impossible de supprimer cet utilisateur : il possède des données liées.");
    } else {
        $em->remove($user);
        $em->flush();
        $this->addFlash('success', "Utilisateur supprimé avec succès.");
    }

    return $this->redirectToRoute('users_liste_vendeur_occas');
}

#[Route('/admin/users/venneur-occasion/toggle-block/{id}', name: 'admin_users_toggle_block_venneur_occas')]
public function toggleBlockVendeurOccas(Users $user, EntityManagerInterface $em): RedirectResponse
{
    $user->setIsBlocked(!$user->isBlocked());
    $em->flush();
    $this->addFlash('info', $user->isBlocked() ? "L’utilisateur a été bloqué." : "L’utilisateur a été débloqué.");
    return $this->redirectToRoute('users_liste_vendeur_occas');
}

#[Route('/admin/users/proprietaire/delete/{id}', name: 'admin_users_delete_proprietaire')]
public function deleteProprietaire(Users $user, EntityManagerInterface $em): RedirectResponse
{
    if ($user->getAnnonces()->count() > 0 || $user->getDemandes()->count() > 0 || $user->getOffres()->count() > 0) {
        $this->addFlash('danger', "Impossible de supprimer cet utilisateur : il possède des données liées.");
    } else {
        $em->remove($user);
        $em->flush();
        $this->addFlash('success', "Utilisateur supprimé avec succès.");
    }

    return $this->redirectToRoute('users_liste_proprietaire');
}

#[Route('/admin/users/proprietaire/toggle-block/{id}', name: 'admin_users_toggle_block_proprietaire')]
public function toggleBlockProprietaire(Users $user, EntityManagerInterface $em): RedirectResponse
{
    $user->setIsBlocked(!$user->isBlocked());
    $em->flush();
    $this->addFlash('info', $user->isBlocked() ? "L’utilisateur a été bloqué." : "L’utilisateur a été débloqué.");
    return $this->redirectToRoute('users_liste_proprietaire');
}

#[Route('/admin/users/venneur-neuf/delete/{id}', name: 'admin_users_delete_venneur_neuf')]
public function deleteVendeurNeuf(Users $user, EntityManagerInterface $em): RedirectResponse
{
    if ($user->getAnnonces()->count() > 0 || $user->getDemandes()->count() > 0 || $user->getOffres()->count() > 0) {
        $this->addFlash('danger', "Impossible de supprimer cet utilisateur : il possède des données liées.");
    } else {
        $em->remove($user);
        $em->flush();
        $this->addFlash('success', "Utilisateur supprimé avec succès.");
    }

    return $this->redirectToRoute('users_liste_vendeur_neuf');
}

#[Route('/admin/users/venneur-neuf/toggle-block/{id}', name: 'admin_users_toggle_block_venneur_neuf')]
public function toggleBlockVendeurNeuf(Users $user, EntityManagerInterface $em): RedirectResponse
{
    $user->setIsBlocked(!$user->isBlocked());
    $em->flush();
    $this->addFlash('info', $user->isBlocked() ? "L’utilisateur a été bloqué." : "L’utilisateur a été débloqué.");
    return $this->redirectToRoute('users_liste_vendeur_neuf');
}


#[Route('/admin/users/particulier/delete/{id}', name: 'admin_users_delete_particulier')]
public function deleteParticulier(Users $user, EntityManagerInterface $em): RedirectResponse
{
    if ($user->getAnnonces()->count() > 0 || $user->getDemandes()->count() > 0 || $user->getOffres()->count() > 0) {
        $this->addFlash('danger', "Impossible de supprimer cet utilisateur : il possède des données liées.");
    } else {
        $em->remove($user);
        $em->flush();
        $this->addFlash('success', "Utilisateur supprimé avec succès.");
    }

    return $this->redirectToRoute('users_liste_particulier');
}

#[Route('/admin/users/particulier/toggle-block/{id}', name: 'admin_users_toggle_block_particulier')]
public function toggleBlockParticulier(Users $user, EntityManagerInterface $em): RedirectResponse
{
    $user->setIsBlocked(!$user->isBlocked());
    $em->flush();
    $this->addFlash('info', $user->isBlocked() ? "L’utilisateur a été bloqué." : "L’utilisateur a été débloqué.");
    return $this->redirectToRoute('users_liste_particulier');
}


#[Route('/admin/user/{id}', name: 'admin_mecanicien_detail')]
public function detailMecanicien(
    int $id,
    UsersRepository $userRepo,
    DemandeRepository $demandeRepo,
    OffreRepository $offreRepo,AnnonceRepository $annonceRepository,
): Response {

    // 1️⃣ Récupération du mécanicien
    $mechanic = $userRepo->find($id);

    if (!$mechanic) {
        throw $this->createNotFoundException("Mécanicien introuvable");
    }

    // 2️⃣ Récupérer toutes ses demandes
    $demandes = $demandeRepo->findBy(
        ['offrecompte' => $mechanic], // ici 'user' correspond au champ qui relie la demande au mécanicien
        ['datecreate' => 'DESC']
    );

    // 3️⃣ Récupérer toutes les offres reçues pour ses demandes
    $offresRecues = $offreRepo->findByMecanicien($mechanic->getId());

    // 4️⃣ Calculer le total demandes et offres
    $totalDemandes = count($demandes);
    $totalOffresRecues = count($offresRecues);

      $demandesEnAttente = $demandeRepo->createQueryBuilder('d')
    ->select('COUNT(d.id)')
    ->where('d.publier = :publier')
    ->andWhere('d.statut = :statut')
    ->setParameter('publier', 0)
    ->setParameter('statut', 'en_attente')
    ->getQuery()
    ->getSingleScalarResult();
$totalEnAttenteA = $annonceRepository->count(['statut' => 'en_attente']);
    return $this->render('admin/detailMecProp.html.twig', [
        'mechanic'        => $mechanic,
        'demandes'        => $demandes,
        'offresRecues'    => $offresRecues,
        'totalDemandes'   => $totalDemandes,
        'totalOffresRecues' => $totalOffresRecues,'demandesEnAttente' => $demandesEnAttente,'totalEnAttenteA'=>$totalEnAttenteA
    ]);
}


 #[Route('/admin/demande/detail/{id}', name: 'detail_demande_admin')]
public function detailDemande(int $id, ManagerRegistry $doctrine, OffreRepository $offreRepository,AnnonceRepository $annonceRepository, DemandeRepository $demandeRepo,): Response
{
    $em = $doctrine->getManager();

    // Récupérer la demande
    $demande = $em->getRepository(Demande::class)->find($id);
    if (!$demande) {
        throw $this->createNotFoundException('Demande non trouvée');
    }



    // Récupérer les pièces liées à la demande
    $pieces = $demande->getPieces();

    // Récupérer les offres liées à la demande
    $offresRecues = $offreRepository->findBy(['demande' => $demande]);
      $demandesEnAttente = $demandeRepo->createQueryBuilder('d')
    ->select('COUNT(d.id)')
    ->where('d.publier = :publier')
    ->andWhere('d.statut = :statut')
    ->setParameter('publier', 0)
    ->setParameter('statut', 'en_attente')
    ->getQuery()
    ->getSingleScalarResult();
$totalEnAttenteA = $annonceRepository->count(['statut' => 'en_attente']);
    return $this->render('admin/detaildemandemecprop.html.twig', [
        'demande' => $demande,
        'pieces' => $pieces,
        'offresRecues' => $offresRecues,'demandesEnAttente' => $demandesEnAttente,'totalEnAttenteA'=>$totalEnAttenteA
    ]);
}

#[Route(path: '/admin/demande/supprimer', name: 'admin_supprimer_demande')]
public function supprimerDemande_admin(Request $request, EntityManagerInterface $em): JsonResponse
{
    $idDemande = (int) $request->get('id');
    $demande = $em->getRepository(Demande::class)->find($idDemande);

    if (!$demande) {
        return new JsonResponse('error');
    }

    // Supprimer toutes les pièces associées
    foreach ($demande->getPieces() as $piece) {
        $em->remove($piece);
    }

    $em->remove($demande);
    $em->flush();

    return new JsonResponse('done');
}

#[Route('/admin/offre/{id}', name: 'detail_offre_admin')]
public function detailOffre(int $id, ManagerRegistry $doctrine,DemandeRepository $demandeRepo,AnnonceRepository $annonceRepository): Response
{
    $em = $doctrine->getManager();

    // Récupérer l'offre
    $offre = $em->getRepository(Offre::class)->find($id);
    if (!$offre) {
        throw $this->createNotFoundException('Offre non trouvée');
    }

    // Récupérer la demande associée
    $demande = $offre->getDemande();

   
    // Récupérer les pièces de la demande si nécessaire
    $pieces = $demande->getPieces();

    // Récupérer l'utilisateur qui a fait l'offre
    $offreur = $offre->getUser(); // ou le nom exact de la relation dans Offre

      $demandesEnAttente = $demandeRepo->createQueryBuilder('d')
    ->select('COUNT(d.id)')
    ->where('d.publier = :publier')
    ->andWhere('d.statut = :statut')
    ->setParameter('publier', 0)
    ->setParameter('statut', 'en_attente')
    ->getQuery()
    ->getSingleScalarResult();
$totalEnAttenteA = $annonceRepository->count(['statut' => 'en_attente']);
    return $this->render('admin/offredetail.html.twig', [
        'offre' => $offre,
        'demande' => $demande,
        'pieces' => $pieces,'demandesEnAttente' => $demandesEnAttente,'totalEnAttenteA'=>$totalEnAttenteA
    ]);
}

#[Route('/admin/particulier/{id}', name: 'detail_particulier_admin')]
public function detailParticulier(int $id, ManagerRegistry $doctrine,DemandeRepository $demandeRepo,AnnonceRepository $annonceRepository): Response
{
    $em = $doctrine->getManager();

    // Récupérer le particulier
    $particulier = $em->getRepository(Users::class)->find($id);
    if (!$particulier) {
        throw $this->createNotFoundException('Particulier non trouvé');
    }

    // Récupérer les annonces du particulier
    $annonces = $em->getRepository(Annonce::class)->findBy(['user' => $particulier]);

    // Compter le nombre total d'annonces
    $totalAnnonces = count($annonces);

       $demandesEnAttente = $demandeRepo->createQueryBuilder('d')
    ->select('COUNT(d.id)')
    ->where('d.publier = :publier')
    ->andWhere('d.statut = :statut')
    ->setParameter('publier', 0)
    ->setParameter('statut', 'en_attente')
    ->getQuery()
    ->getSingleScalarResult();
$totalEnAttenteA = $annonceRepository->count(['statut' => 'en_attente']);
    return $this->render('admin/detailparticulier.html.twig', [
        'particulier' => $particulier,
        'annonces' => $annonces,
        'totalAnnonces' => $totalAnnonces,'demandesEnAttente' => $demandesEnAttente,'totalEnAttenteA'=>$totalEnAttenteA
    ]);
}

#[Route(path: '/admin/annonce/supprimer', name: 'admin_supprimer_annonce')]
public function supprimerAnnonce_admin(Request $request, EntityManagerInterface $em): JsonResponse
{
    $idAnnonce = $request->get('id');
    $annonce = $em->getRepository(Annonce::class)->find($idAnnonce);

    if (!$annonce) {
        return new JsonResponse(['status' => 'error', 'message' => 'Annonce non trouvée']);
    }

    // Supprimer l'annonce
    $em->remove($annonce);
    $em->flush();

    return new JsonResponse(['status' => 'success', 'message' => 'Annonce supprimée avec succès']);
}

#[Route('/admin/annonce/{id}', name: 'detail_annonce_admin')]
public function detailAnnonceAdmin(int $id, ManagerRegistry $doctrine, AnnonceRepository $annonceRepository,DemandeRepository $demandeRepo,): Response
{
    $em = $doctrine->getManager();

    // Récupérer l'annonce
    $annonce = $em->getRepository(Annonce::class)->find($id);
    if (!$annonce) {
        throw $this->createNotFoundException('Annonce non trouvée');
    }
    // Produits de la même marque (exclut l'annonce actuelle)
    $produitsMemeMarque = $annonceRepository->findBy(
        ['marque' => $annonce->getMarque()],
        ['dateCreation' => 'DESC'],
        4
    );

       $demandesEnAttente = $demandeRepo->createQueryBuilder('d')
    ->select('COUNT(d.id)')
    ->where('d.publier = :publier')
    ->andWhere('d.statut = :statut')
    ->setParameter('publier', 0)
    ->setParameter('statut', 'en_attente')
    ->getQuery()
    ->getSingleScalarResult();
$totalEnAttenteA = $annonceRepository->count(['statut' => 'en_attente']);
    return $this->render('admin/detailannonce.html.twig', [
        'annonce' => $annonce,'annoncesMemeMarque' => $produitsMemeMarque,'demandesEnAttente' => $demandesEnAttente,'totalEnAttenteA'=>$totalEnAttenteA
    ]);
}


#[Route('/admin/update/publier', name: 'admin_update_publier_annonce', methods: ['POST'])]
public function updatePublier(Request $request, EntityManagerInterface $em): JsonResponse
{
    $id = $request->request->get('id');
    $publier = $request->request->get('publier');

    $annonce = $em->getRepository(Annonce::class)->find($id);

    if (!$annonce) {
        return new JsonResponse(['status' => 'error']);
    }

    // Publier setzen
    $annonce->setPublier($publier);

    // Statut automatisch setzen
    if ($publier == 1) {
        $annonce->setStatut('publié');
    } else {
        $annonce->setStatut('en_attente');
    }

    $em->flush();

    return new JsonResponse(['status' => 'ok']);
}

#[Route('/admin/annonce/vendu', name: 'admin_annonce_marquer_vendu', methods: ['POST'])]
public function marquerVendu(Request $request, EntityManagerInterface $em): Response
{
    $id = $request->request->get('id');

    if (!$id) {
        return new JsonResponse(['error' => 'ID manquant'], 400);
    }

    $annonce = $em->getRepository(Annonce::class)->find($id);

    if (!$annonce) {
        return new JsonResponse(['error' => 'Annonce introuvable'], 404);
    }

    // Mettre à jour le statut
    $annonce->setStatut('vendu');
    $em->flush();

    return new JsonResponse(['success' => 'Statut changé en vendu']);
}

#[Route('/admin/update/statut', name: 'admin_update_statut_annonce', methods: ['POST'])]
public function updateStatutAnnonce(Request $request, EntityManagerInterface $em, AnnonceRepository $repo): JsonResponse
{
    $id = $request->request->get('id');

    if (!$id) {
        return new JsonResponse(['message' => 'ID manquant'], 400);
    }

    $annonce = $repo->find($id);

    if (!$annonce) {
        return new JsonResponse(['message' => 'Annonce introuvable'], 404);
    }

    // 🔄 Basculer le statut
    if ($annonce->getStatut() === 'vendu') {
        $annonce->setStatut('publié');
        $message = "L’annonce est maintenant marquée comme non vendue.";
        $newStatut = "publié";
    } else {
        $annonce->setStatut('vendu');
        $message = "L’annonce est maintenant marquée comme vendue.";
        $newStatut = "vendu";
    }

    $em->persist($annonce);
    $em->flush();

    return new JsonResponse([
        'message' => $message,
        'statut' => $newStatut
    ]);
}


#[Route('/admin/vendeur/neuve/{id}/detail', name: 'admin_detail_vendeur_neuf')]
    public function detailVendeurNeuf(int $id, EntityManagerInterface $em,DemandeRepository $demandeRepo,AnnonceRepository $annonceRepository): Response
    {
        // Récupérer le vendeur
        $vendeur = $em->getRepository(Users::class)->find($id);
        if (!$vendeur) {
            throw $this->createNotFoundException('Vendeur introuvable');
        }

        // Récupérer les demandes de pièces neuves dans la zone du vendeur
        $demandesneuf = $em->getRepository(Demande::class)->createQueryBuilder('d')
            ->where('(d.zone = :zone OR d.zone = :toute)')
            ->andWhere('d.vendeurneuf = 1')
            ->setParameter('zone',  $vendeur->getAdresse())
            ->setParameter('toute', 'Toute la Tunisie') 
            ->orderBy('d.datecreate', 'DESC')
            ->getQuery()
            ->getResult();

        // Récupérer les offres envoyées par ce vendeur
        $offres = $em->getRepository(Offre::class)->findBy(
            ['user' => $vendeur],
            ['createdAt' => 'DESC']
        );

           $demandesEnAttente = $demandeRepo->createQueryBuilder('d')
    ->select('COUNT(d.id)')
    ->where('d.publier = :publier')
    ->andWhere('d.statut = :statut')
    ->setParameter('publier', 0)
    ->setParameter('statut', 'en_attente')
    ->getQuery()
    ->getSingleScalarResult();
$totalEnAttenteA = $annonceRepository->count(['statut' => 'en_attente']);
 $acceptees = count(array_filter($offres, fn($o) => $o->getStatus() === 'acceptee'));
 $refusees = count(array_filter($offres, fn($o) => $o->getStatus() === 'refusee'));
        return $this->render('admin/detailvendeurneuve.html.twig', [
            'vendeur' => $vendeur,
            'totalDemandes' => count($demandesneuf),
            'totalOffres' => count($offres),
            'demandesneuf' => $demandesneuf,
            'offres' => $offres,'demandesEnAttente' => $demandesEnAttente,'totalEnAttenteA'=>$totalEnAttenteA,
            'offresAcceptees'=>$acceptees,'offresRefusees'=>$refusees,
        ]);
    }


    #[Route('/admin/vendeur/occasion/{id}/detail', name: 'admin_detail_vendeur_occasion')]
    public function detailVendeurOccasion(int $id, EntityManagerInterface $em,AnnonceRepository $annonceRepository,DemandeRepository $demandeRepo,): Response
    {
        // Récupérer le vendeur
        $vendeur = $em->getRepository(Users::class)->find($id);
        if (!$vendeur) {
            throw $this->createNotFoundException('Vendeur introuvable');
        }

       
    // Récupérer les demandes de pièces OCCASION dans la zone du vendeur
$demandesOccasion = $em->getRepository(Demande::class)
    ->createQueryBuilder('d')
    ->where('(d.zone = :zone OR d.zone = :toute)')
    ->andWhere('d.vendeuroccasion = :valeur')
    ->setParameter('zone', $vendeur->getAdresse())
    ->setParameter('toute', 'Toute la Tunisie')
    ->setParameter('valeur', 1)
    ->orderBy('d.datecreate', 'DESC')
    ->getQuery()
    ->getResult();



        // Récupérer les offres envoyées par ce vendeur
        $offres = $em->getRepository(Offre::class)->findBy(
            ['user' => $vendeur],
            ['createdAt' => 'DESC']
        );

           $demandesEnAttente = $demandeRepo->createQueryBuilder('d')
    ->select('COUNT(d.id)')
    ->where('d.publier = :publier')
    ->andWhere('d.statut = :statut')
    ->setParameter('publier', 0)
    ->setParameter('statut', 'en_attente')
    ->getQuery()
    ->getSingleScalarResult();
$totalEnAttenteA = $annonceRepository->count(['statut' => 'en_attente']);
        return $this->render('admin/detailvendeuroccasion.html.twig', [
            'vendeur' => $vendeur,
            'totalDemandes' => count($demandesOccasion),
            'totalOffres' => count($offres),
            'demandes' => $demandesOccasion,
            'offres' => $offres,'demandesEnAttente' => $demandesEnAttente,'totalEnAttenteA'=>$totalEnAttenteA
        ]);
    }

    #[Route('/admin/demandes', name: 'admin_liste_demandes')]
    public function listeDemandes(AnnonceRepository $annonceRepository,DemandeRepository $demandeRepository): Response
    {
        // Récupérer toutes les demandes, triées par date de création décroissante
        $demandes = $demandeRepository->findBy([], ['datecreate' => 'DESC']);
 $demandesEnAttente = $demandeRepository->createQueryBuilder('d')
    ->select('COUNT(d.id)')
    ->where('d.publier = :publier')
    ->andWhere('d.statut = :statut')
    ->setParameter('publier', 0)
    ->setParameter('statut', 'en_attente')
    ->getQuery()
    ->getSingleScalarResult();
$totalEnAttenteA = $annonceRepository->count(['statut' => 'en_attente']);
        return $this->render('admin/demandes/liste.html.twig', [
            'demandes' => $demandes,'demandesEnAttente' => $demandesEnAttente,'totalEnAttenteA'=>$totalEnAttenteA,
             'totalDemandes' => $demandeRepository->countAll(),
        'totalEnAttente' => $demandeRepository->countEnAttente(),
        'totalPublie' => $demandeRepository->countPublie(),
        'totalServue' => $demandeRepository->countServue(),
        ]);
    }


  #[Route('/admin/demande/publier', name: 'admin_update_publier_demande', methods: ['POST'])]
public function publierDemande(Request $request, EntityManagerInterface $em, DemandeRepository $repo): JsonResponse
{
    $id = (int) $request->request->get('id');
    $demande = $repo->find($id);

    if (!$demande) {
        return new JsonResponse(['message' => 'Demande non trouvée'], 404);
    }

    // Toggle publier
    $publier = !$demande->isPublier();
    $demande->setPublier($publier);

    // Mettre à jour le statut automatiquement
    if ($publier) {
        $demande->setStatut('publié');
    } else {
        $demande->setStatut('en_attente');
    }

    $em->flush();

    return new JsonResponse([
        'message' => $publier ? 'Demande publiée' : 'Demande en attente',
        'publier' => $publier
    ]);
}

#[Route('/admin/demande/toggle/servue', name: 'admin_toggle_servue', methods: ['POST'])]
public function toggleServue(Request $request, EntityManagerInterface $em, DemandeRepository $repo): JsonResponse
{
    $id = $request->request->get('id');
    $demande = $repo->find($id);

    if (!$demande) {
        return new JsonResponse(['message' => 'Demande non trouvée'], 404);
    }

    // Toggle statut servue / non servue
    if ($demande->getStatut() === 'fermer') {
        $demande->setStatut('publié'); // Non servue
        $message = 'Demande marquée comme non servue';
    } else {
        $demande->setStatut('fermer'); // Servue
        $message = 'Demande marquée comme servue';
    }

    $em->flush();

    return new JsonResponse([
        'message' => $message,
        'statut' => $demande->getStatut()
    ]);
}


#[Route('/admin/annonces', name: 'admin_annonces')]
public function annonces(AnnonceRepository $annonceRepository , DemandeRepository $demandeRepository): Response
{

    $totalAnnonces = $annonceRepository->count([]);
    $totalEnAttenteA = $annonceRepository->count(['statut' => 'en_attente']);
    $totalPublieA = $annonceRepository->count(['statut' => 'publié']);
    $totalArchiveA = $annonceRepository->count(['statut' => 'vendu']);


    $demandesEnAttente = $demandeRepository->createQueryBuilder('d')
    ->select('COUNT(d.id)')
    ->where('d.publier = :publier')
    ->andWhere('d.statut = :statut')
    ->setParameter('publier', 0)
    ->setParameter('statut', 'en_attente')
    ->getQuery()
    ->getSingleScalarResult();
$totalEnAttenteA = $annonceRepository->count(['statut' => 'en_attente']);
    return $this->render('admin/annonces/listes.html.twig', [
        'annonces' => $annonceRepository->findAll(),'demandesEnAttente' => $demandesEnAttente,
        'totalAnnonces' => $totalAnnonces,
    'totalEnAttenteA' => $totalEnAttenteA,
    'totalPublieA' => $totalPublieA,
    'totalArchiveA' => $totalArchiveA,'totalEnAttenteA'=>$totalEnAttenteA
    ]);
}



#[Route('/admin/offres', name: 'admin_liste_offres')]
public function listeOffres(OffreRepository $offreRepository,AnnonceRepository $annonceRepository , DemandeRepository $demandeRepository): Response
{

     $demandesEnAttente = $demandeRepository->createQueryBuilder('d')
    ->select('COUNT(d.id)')
    ->where('d.publier = :publier')
    ->andWhere('d.statut = :statut')
    ->setParameter('publier', 0)
    ->setParameter('statut', 'en_attente')
    ->getQuery()
    ->getSingleScalarResult();
    $totalEnAttenteA = $annonceRepository->count(['statut' => 'en_attente']);
    // Alle Offres aus der Datenbank holen
    $offres = $offreRepository->findBy([], ['id' => 'DESC']);

    $totalOffres = $offreRepository->count([]);

    $totalOffresEnAttente = $offreRepository->count([
        'status' => 'en_attente'
    ]);

    $totalOffresAcceptees = $offreRepository->count([
        'status' => 'acceptee'
    ]);

    $totalOffresRefusees = $offreRepository->count([
        'status' => 'refusee'
    ]);

    return $this->render('admin/offres/liste.html.twig', [
        'offres' => $offres,'demandesEnAttente' => $demandesEnAttente,'totalEnAttenteA'=>$totalEnAttenteA,
        'totalOffres' => $totalOffres,
    'totalOffresEnAttente' => $totalOffresEnAttente,
    'totalOffresAcceptees' => $totalOffresAcceptees,
    'totalOffresRefusees' => $totalOffresRefusees,
    ]);
}

#[Route('/admin/offres/toggles', name: 'admin_toggle_offres', methods: ['POST'])]
public function toggleOffre(Request $request, EntityManagerInterface $em, OffreRepository $repo): JsonResponse
{
    $id = $request->request->get('id');
    $action = $request->request->get('action'); // 'accepter', 'refuser' ou 'en_attente'

    $offre = $repo->find($id);

    if (!$offre) {
        return new JsonResponse(['message' => 'Offre non trouvée'], 404);
    }

    switch ($action) {
        case 'accepter':
            $offre->setStatus('acceptee');
            $message = "Offre marquée comme acceptée";
            break;
        case 'refuser':
            $offre->setStatus('refusee');
            $message = "Offre marquée comme refusée";
            break;
        case 'en_attente':
            $offre->setStatus('en_attente');
            $message = "Offre mise en attente";
            break;
        default:
            return new JsonResponse(['message' => 'Action invalide'], 400);
    }

    $em->flush();

    return new JsonResponse([
        'message' => $message,
        'statut' => $offre->getStatus()
    ]);
}


#[Route('/admin/offres/supprimer', name: 'admin_supprimer_offres', methods: ['POST'])]
public function supprimerOffre(Request $request, EntityManagerInterface $em, OffreRepository $repo): JsonResponse
{
    $id = $request->request->get('id');
    $offre = $repo->find($id);

    if (!$offre) {
        return new JsonResponse(['message' => 'Offre non trouvée'], 404);
    }

    $em->remove($offre);
    $em->flush();

    return new JsonResponse(['message' => 'Offre supprimée avec succès']);
}


#[Route(path: '/admin/config/profile/Modifier/{id}', name: 'app_admin_profile', methods: ['GET'])]
    public function adminprofile(AnnonceRepository $annonceRepository , DemandeRepository $demandeRepository,
        int $id,
        Request $request,
        ManagerRegistry $doctrine,
        UsersService $UsersService
    ): Response {
        $session = $request->getSession();
        $session->set('PageMenu', 'app_admin_profile');

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
 $demandesEnAttente = $demandeRepository->createQueryBuilder('d')
    ->select('COUNT(d.id)')
    ->where('d.publier = :publier')
    ->andWhere('d.statut = :statut')
    ->setParameter('publier', 0)
    ->setParameter('statut', 'en_attente')
    ->getQuery()
    ->getSingleScalarResult();
    $totalEnAttenteA = $annonceRepository->count(['statut' => 'en_attente']);
        return $this->render('admin/configuration/profile.html.twig', [
            'profileuser' => $profileuser,'demandesEnAttente' => $demandesEnAttente,'totalEnAttenteA'=>$totalEnAttenteA,
        ]);
    }


   #[Route('/admin/config/banner-menu', name: 'Banner_menu')]
public function bannerMenu(
    EntityManagerInterface $em,
    AnnonceRepository $annonceRepository,
    DemandeRepository $demandeRepository
): Response {

    // Liste des banners
    $bannerMenu = $em->getRepository(bannerMenu::class)->findBy([], ['id' => 'DESC']);

    // Compteurs
    $demandesEnAttente = $demandeRepository->createQueryBuilder('d')
        ->select('COUNT(d.id)')
        ->where('d.publier = 0')
        ->andWhere('d.statut = :statut')
        ->setParameter('statut', 'en_attente')
        ->getQuery()
        ->getSingleScalarResult();

    $totalEnAttenteA = $annonceRepository->count(['statut' => 'en_attente']);

    return $this->render('admin/configuration/bannerMenu.html.twig', [
        'BannerMenu' => $bannerMenu,
        'demandesEnAttente' => $demandesEnAttente,
        'totalEnAttenteA' => $totalEnAttenteA
    ]);
}

#[Route('/admin/banner-menu/ajouter', name: 'AjouterBannerMenu', methods: ['POST'])]
public function ajouterBannerMenu(Request $request, EntityManagerInterface $em): JsonResponse
{
    $banner = $request->request->get('banner');
    $publier = $request->request->get('publier', 0);

    if (!$banner) {
        return new JsonResponse(['status' => 'error', 'message' => 'Image manquante']);
    }

    $bannerMenu = new BannerMenu();
    $bannerMenu->setLogo($banner);
    $bannerMenu->setPublier((int)$publier);

    $em->persist($bannerMenu);
    $em->flush();

    return new JsonResponse(['status' => 'success']);
}

#[Route('/admin/banner-menu/publier', name: 'PublierBannermenu', methods: ['POST'])]
public function publierBannerMenu(Request $request, EntityManagerInterface $em): JsonResponse
{
    $id = $request->request->get('id');
    $publier = $request->request->get('Publier');

    $banner = $em->getRepository(BannerMenu::class)->find($id);

    if (!$banner) {
        return new JsonResponse(['status' => 'error']);
    }

    $banner->setPublier((int)$publier);
    $em->flush();

    return new JsonResponse(['status' => 'success']);
}

#[Route('/admin/banner-menu/supprimer', name: 'Supprimer_BannerMenu', methods: ['POST'])]
public function supprimerBannerMenu(Request $request, EntityManagerInterface $em): JsonResponse
{
    $id = $request->request->get('id');

    $banner = $em->getRepository(BannerMenu::class)->find($id);

    if (!$banner) {
        return new JsonResponse(['status' => 'error']);
    }

    $em->remove($banner);
    $em->flush();

    return new JsonResponse(['status' => 'success']);
}

// src/Controller/Admin/FooterController.php

#[Route('/admin/footer', name: 'admin_footer')]
public function footer(EntityManagerInterface $em,    AnnonceRepository $annonceRepository,
    DemandeRepository $demandeRepository): Response
{
    $footer = $em->getRepository(Footer::class)->find(1);

    if (!$footer) {
        $footer = new Footer();
    }
    $demandesEnAttente = $demandeRepository->createQueryBuilder('d')
        ->select('COUNT(d.id)')
        ->where('d.publier = 0')
        ->andWhere('d.statut = :statut')
        ->setParameter('statut', 'en_attente')
        ->getQuery()
        ->getSingleScalarResult();

    $totalEnAttenteA = $annonceRepository->count(['statut' => 'en_attente']);
    return $this->render('admin/configuration/footer.html.twig', [
        'configFooter' => $footer, 'demandesEnAttente' => $demandesEnAttente,
        'totalEnAttenteA' => $totalEnAttenteA
    ]);
}

#[Route('/admin/footer/save', name: 'admin_footer_save', methods: ['POST'])]
public function saveFooter(Request $request, EntityManagerInterface $em): JsonResponse
{
    $footer = $em->getRepository(Footer::class)->find(1);

    if (!$footer) {
        $footer = new Footer();
    }
    $footer->setLogoheader($request->get('logoHeader'));
    $footer->setAdresse($request->request->get('Adresse'));
    $footer->setTelephone($request->request->get('mobile'));
    $footer->setEmail($request->request->get('mail'));
    $footer->setFacebook($request->request->get('facebook'));
    $footer->setInstagram($request->request->get('instagram'));
    $footer->setLogo($request->request->get('logo'));

    $em->persist($footer);
    $em->flush();

    return new JsonResponse(['status' => 'success']);
}


}
