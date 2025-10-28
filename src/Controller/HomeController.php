<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('home/index.html.twig');
    }

     #[Route(path: '/footer', name: 'app_footer')]
     public function footer(): Response
    {
       
        return $this->render('/footer.html.twig');
    }

      #[Route(path: '/header', name: 'app_header')]
    public function header(): Response
    {
       
        return $this->render('/header.html.twig');
    }

      #[Route(path: '/sideheader', name: 'sideheader')]
    public function sideheader(Request $request): Response
    {
        $session =$request->getSession();
        return $this->render('/sideHeader.html.twig');
    }

    #[Route('/contact', name: 'app_contact')]
    public function contact(): Response
    {
        return $this->render('contact.html.twig');
    }
}
