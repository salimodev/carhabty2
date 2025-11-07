<?php

namespace App\Controller;

use App\Entity\Demande;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\DemandeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Knp\Component\Pager\PaginatorInterface;

class VendeurNeufController extends AbstractController
{
    #[Route(path: '/vendeur/neuf/LayoutSeller', name: 'layout_seller', methods: "GET")]
    public function layout_seller()
    {


        return $this->render('vendeurNeuf/layoutVN.html.twig');
    }

    #[Route(path: '/vendeur/neuf/dashboard', name: 'dashboard_vendeurNeuf', methods: "GET")]
    public function dashboard_VN(DemandeRepository $demandeRepository, Request $request,)
    {
        $session = $request->getSession();
        $session->set('PageMenu', 'dashboard_vendeurNeuf');
        $user = $this->getUser();
        $zoneVendeur = $user->getAdresse(); // suppose que l’utilisateur a une zone enregistrée
        $countDemandes = $demandeRepository->countDemandesDispoVendeur($zoneVendeur);
        $dernieresDemandes = $demandeRepository->findLatestForVendeurNeuf($zoneVendeur);

        return $this->render('vendeurNeuf/dashboardVN.html.twig', [
            'countDemandes' => $countDemandes,
            'demandes' => $dernieresDemandes,
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

        $result[] = [
            'id'       => $d->getId(),
            'marque'   => $d->getMarque(),
            'modele'   => $d->getModele(),
            'zone'     => $d->getZone(),
            'date'     => $d->getDatecreate()->format('Y-m-d H:i'),
            'pieces'   => $pieces,
        ];
    }

    return new JsonResponse($result);
}

}
