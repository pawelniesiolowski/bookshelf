<?php

namespace App\Shared\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class WebsiteController extends AbstractController
{
    public function index()
    {
        return $this->render('index.html.twig');
    }

    public function receivers()
    {
        return $this->render('receivers.html.twig');
    }
}
