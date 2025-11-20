<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    public function index(): Response
    {
        return $this->render('LayoutAdmin.html.twig');
    }

    #[Route(path: '/admin/dashboard', name: 'dashboard_admin')]
    public function dashboard(): Response
    {
       
        return $this->render('admin/dashboard.html.twig');
    }
    

}
