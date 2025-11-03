<?php

namespace App\Controller;


use App\Service\pieceService;
use App\Entity\Pieces;
use App\Entity\Demande;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;

final class DemandeController extends AbstractController

{
     
    private $mailer;
    
    public function __construct(\Doctrine\ORM\EntityManagerInterface $em,MailerInterface $mailer) {
       
        $this->mailer = $mailer;
    }

      #[Route('/demande', name: 'app_demande')]
    public function demande2(Request $request): Response
    {
       $session =$request->getSession();
       $session->set('PageMenu', 'demande');
        return $this->render('demande/demande.html.twig');
    }

   #[Route('/piece/ajouter', name: 'add_piece', methods: ['POST'])]
public function ajouter_piece(
    Request $request,
    pieceService $pieceService
): JsonResponse {
    $designation = $request->get('designation');
    $reference = $request->get('reference');
    $photo = $request->get('photo');
    $observation = $request->get('observation');

    $piece = $pieceService->ajouterPiece($designation, $reference, $photo, $observation);

    return new JsonResponse([
        'success' => true,
        'id' => $piece->getId(), // 🔹 très important pour ton JS
    ]);
}

 #[Route('/envoyer-demande', name: 'envoyer_demande', methods: ['POST'])]
public function envoyerDemande(Request $request, EntityManagerInterface $em): JsonResponse
{
    // Récupération des données envoyées par AJAX
    $marque = $request->request->get('marque');
    $modele = $request->request->get('modele');
    $numerochassis = $request->request->get('numerochassis');
    $carburant = $request->request->get('carburant');
    $etatmoteur = $request->request->get('etatmoteur');
    $photocartegrise = $request->request->get('NewPhotos2');
    $vendNeuf = $request->request->get('vendNeuf');
    $vendOcc = $request->request->get('vendOcc');
    $zone = $request->request->get('zone');
    $email = $request->request->get('email');
    $userId = $request->request->get('user_id');
    $pieces = $request->request->all('pieces');

    
    $choixCompte = !empty($userId);

    if ($choixCompte && $user = $em->getRepository(\App\Entity\Users::class)->find($userId)) {
       
        $email = $user->getEmail(); // ✅ on récupère l'email du compte
    }  

    if (!$marque || !$modele || empty($pieces)) {
        return new JsonResponse(['status' => 'error', 'message' => 'Données manquantes ou invalides !'], 400);
    }

    // --- Création de la demande ---
    $demande = new Demande();
    $demande->setMarque($marque);
    $demande->setModele($modele);
    $demande->setChassis($numerochassis);
    $demande->setEnergie($carburant);
    $demande->setEtatMoteur($etatmoteur);
    $demande->setPhotocartegrise($photocartegrise);
    $demande->setZone($zone);
    $demande->setVendeurNeuf($vendNeuf);
    $demande->setVendeurOccasion($vendOcc);
    $demande->setOffreemail($email);
    $demande->setCode($this->generateCode());
    $demande->setStatut('ouvert');
    $demande->setDatecreate(new \DateTimeImmutable());

    if ($userId && $user = $em->getRepository(\App\Entity\Users::class)->find($userId)) {
        $demande->setOffrecompte($user);
    }

    $em->persist($demande);
    $em->flush();

    foreach ($pieces as $pieceId) {
        $piece = $em->getRepository(Pieces::class)->find($pieceId);
        if ($piece) {
            $piece->setDemande($demande);
            $em->persist($piece);
        }
    }

   $em->flush();

$pieces = $em->getRepository(Pieces::class)->findBy(["demande" => $demande]);
$piecesData = [];

foreach ($pieces as $prod) {
    if ($prod !== null) {
        $arrayprod = [
            'id' => $prod->getId(),
            'designation' => $prod->getDesignation(),
            'reference' => $prod->getReferance(),
            'photo' => $prod->getPhoto(),
            'observation' => $prod->getObservation(),
        ];

        $piecesData[] = $arrayprod;
    }
}
$email = (new TemplatedEmail())
    ->from('salimabbessi.dev@gmail.com')
    ->to($email)
    ->subject('Votre demande a été bien creé')
    ->htmlTemplate('emails/demande.html.twig')
    ->context([
        'demande' => $demande,
    ]);


// Embed images
$email 
    ->embedFromPath('https://res.cloudinary.com/aladdineshoping/image/upload/v1762169290/logo1_hdbhq8.png', 'logo', 'image/png')
    ->embedFromPath('https://res.cloudinary.com/b-ja/image/upload/v1681422924/h2qdkkms0lmucs44rc6s.png', 'facebook', 'image/png')
    ->embedFromPath('https://res.cloudinary.com/b-ja/image/upload/v1681422987/jtxpgab3dhykivmpw6y5.png', 'instagram', 'image/png')
    ->embedFromPath('https://res.cloudinary.com/b-ja/image/upload/v1681423044/gchpepwboglj5oyudqr8.png', 'twitter', 'image/png')
    ->embedFromPath('https://res.cloudinary.com/b-ja/image/upload/v1681423097/afyzbcguyosyjfbhpn8v.png', 'Linkidin', 'image/png')
    ->embedFromPath('https://res.cloudinary.com/b-ja/image/upload/v1681423192/irpvbr5wdjsewm0i5jgu.png', 'Tick', 'image/png');
                 
// Send the email
$this->mailer->send($email);
return new JsonResponse([
    'status' => 'success',
    'message' => 'Demande enregistrée avec succès !',
    'demande_id' => $demande->getId(),
    'code' => $demande->getCode(),
    'marque' => $demande->getMarque(),
    'modele' => $demande->getModele(),
    'chassis' => $demande->getChassis(),
    'grise' => $demande->getPhotocartegrise(),
    'energie' => $demande->getEnergie(),
    'etatmoteur' => $demande->getEtatmoteur(),
    'zone' => $demande->getZone(),
    'reception' => $demande->getOffreemail(),
    'statut' => $demande->getStatut(),
    'pieces' => $piecesData,
]);




}

/** 🔹 Fonction utilitaire pour générer le code unique */
private function generateCode(): string
{
    $date = new \DateTimeImmutable();
    $timestamp = $date->format('His');
    $millis = $date->format('u');
    return substr($millis, 0, 3) . '-' . $timestamp;
}

  

}