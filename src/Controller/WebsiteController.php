<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class WebsiteController extends AbstractController
{
    public function index()
    {
        return $this->render('index.html.twig');
    }
}

