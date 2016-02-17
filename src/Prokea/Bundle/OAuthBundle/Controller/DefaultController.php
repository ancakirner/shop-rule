<?php

namespace Prokea\Bundle\OAuthBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('ProkeaOAuthBundle:Default:index.html.twig');
    }
}
