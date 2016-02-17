<?php

namespace Prokea\Bundle\ShopRuleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('ProkeaShopRuleBundle:Default:index.html.twig');
    }
    
    public function homepageAction()
   {
       return $this->render('ProkeaShopRuleBundle:Default:index.html.twig');
   }
}
