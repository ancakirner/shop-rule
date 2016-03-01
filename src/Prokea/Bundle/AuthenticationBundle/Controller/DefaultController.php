<?php

namespace Prokea\Bundle\AuthenticationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('ProkeaAuthenticationBundle:Default:index.html.twig');
    }
}
