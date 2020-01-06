<?php

namespace App\Catalog\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class WebsiteController extends AbstractController
{
    public function form()
    {
        return $this->render('catalog/book_form.twig');
    }
}
