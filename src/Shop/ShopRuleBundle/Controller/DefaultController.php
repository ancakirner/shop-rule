<?php

namespace Shop\ShopRuleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('ShopShopRuleBundle:Default:index.html.twig', array('name' => $name));
    }
}
