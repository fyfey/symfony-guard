<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
 
class DefaultController extends AbstractController
{
    public function index()
    {
        return $this->render('home.html.twig');
    }

    public function docs()
    {
        return $this->render('docs.html.twig');
    }
}