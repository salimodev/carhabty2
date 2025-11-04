<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\DemandeRepository;

class HomeController extends AbstractController
{
    #[Route('/', name: 'Accueil')]
    public function index(Request $request,DemandeRepository $demandeRepository): Response
    {
        $session =$request->getSession();
        $session->set('PageMenu', 'Accueil');
        $lastDemandes = $demandeRepository->findLastTen();

        return $this->render('home/index.html.twig', [
        'lastDemandes' => $lastDemandes,
    ]);
    }

     #[Route(path: '/footer', name: 'app_footer')]
     public function footer(Request $request): Response
    {
    
        return $this->render('/footer.html.twig');
    }

      #[Route(path: '/header', name: 'app_header')]
    public function header(Request $request): Response
    {
            
        return $this->render('/header.html.twig');
    }

      #[Route(path: '/sideheader', name: 'sideheader')]
    public function sideheader(Request $request): Response
    {
           
        return $this->render('/sideHeader.html.twig');
    }

    #[Route('/contact', name: 'app_contact')]
    public function contact(Request $request): Response
    {
               $session =$request->getSession();
        $session->set('PageMenu', 'app_contact');
        return $this->render('contact.html.twig');
    }
}
