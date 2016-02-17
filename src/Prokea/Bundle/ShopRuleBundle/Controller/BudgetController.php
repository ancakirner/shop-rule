<?php
namespace Prokea\Bundle\ShopRuleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Prokea\Bundle\ShopRuleBundle\Entities\BudgetEntry;

class BudgetController extends Controller
{
    public function setInitialBudgetAction(Request $request)
    {
        $thisMonth = new \DateTime('now');
        
        $repository = $this->getDoctrine()->getRepository('ProkeaShopRuleBundle:BudgetEntry');
        $cashBudget = $repository->findOneBy(
            array('sourceCategory' => 'cash',
                'isMonthlyIncome' => '1',
                'date' => $thisMonth
            )
        );
        
        $cardBudget = $repository->findOneBy(
            array('sourceCategory' => 'card',
                'isMonthlyIncome' => '1',
                'date' => $thisMonth
            )
        );
        
        if (!empty($cashBudget) && !empty($cardBudget)) {
            return $this->redirect($this->generateUrl('homepage'), 301);
        }
        
        $form = $this->createFormBuilder()
            ->add('init_cash', 'text', array('label' => 'INIT CASH: '))
            ->add('init_card', 'text', array('label' => 'INIT CARD: '))
            ->add('save', 'submit', array('label' => 'Initialize budget'))
            ->getForm();
        
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()){
            $this->submitBudget($form, $thisMonth);
            return $this->redirect($this->generateUrl('homepage'), 301);
        }        
            
        return $this->render('ProkeaShopRuleBundle:Budget:budget_view.html.twig', array(
            'form' => $form->createView()
        ));
    }
    
    private function submitBudget($form, $thisMonth)
    {
        $data = $form->getData();
                
        $budget = new BudgetEntry();
        $budget->setAmount($data['init_cash']);
        $budget->setDescription('Total cash budget');
        $budget->setSourceCategory('cash');
        $budget->setSign('+');
        $budget->setIsMonthlyIncome(true);
        $budget->setDate($thisMonth);

        $dm = $this->getDoctrine()->getManager();
        $dm->persist($budget);
        $dm->flush();

        $budget = new BudgetEntry();
        $budget->setAmount($data['init_card']);
        $budget->setDescription('Total card budget');
        $budget->setSourceCategory('card');
        $budget->setSign('+');
        $budget->setIsMonthlyIncome(true);
        $budget->setDate($thisMonth);

        $dm = $this->getDoctrine()->getManager();
        $dm->persist($budget);
        $dm->flush();
    }
}
