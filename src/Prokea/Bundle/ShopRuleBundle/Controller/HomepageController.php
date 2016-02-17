<?php
namespace Prokea\Bundle\ShopRuleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class HomepageController extends Controller
{
   public function homepageAction()
   {
        $thisMonth = new \DateTime(date('Y-m-01'));        
        $repository = $this->getDoctrine()->getRepository('ProkeaShopRuleBundle:BudgetEntry');
        
        $cashBudget = $repository->findOneBy(
            array('sourceCategory' => 'cash',
                'isMonthlyIncome' => '1',
//                'date' => $thisMonth
            )
        );
        
        $cardBudget = $repository->findOneBy(
            array('sourceCategory' => 'card',
                'isMonthlyIncome' => '1',
//                'date' => $thisMonth
            )
        );
        
        if (empty($cashBudget) || empty($cardBudget)) {
            return $this->redirect($this->generateUrl('init_budget'), 301);
        }

        return $this->render('ProkeaShopRuleBundle:Homepage:homepage.html.twig', array(
            'budget' => array(
                'card' => $cardBudget->getAmountToString(),
                'cash' => $cashBudget->getAmountToString(),
                'total' => number_format($cardBudget->getAmount() + $cashBudget->getAmount(), 2)
            )
        ));
   }
}
