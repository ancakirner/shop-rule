<?php
namespace Prokea\Bundle\ShopRuleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Prokea\Bundle\ShopRuleBundle\Entities\BudgetEntry;

class SourceCategoryController extends Controller
{
    private $thisMonth;
    
    public function __construct()
    {
        $this->thisMonth = new \DateTime(date('Y-m-01'));
    }
    /**
     * Adds a new datail for incomming or spent money
     */
    public function saveNewItemAction(Request $request, $source, $type)
    {        
        $budget = new BudgetEntry();
        $budget->setSourceCategory($source);
        $budget->setSign($type == 'income' ? '+' : '-');
        $budget->setIsMonthlyIncome(false);

        $form = $this->createFormBuilder($budget)
            ->add('amount', 'text')
            ->add('description', 'text')
            ->add('date', 'date', array('data' => new \DateTime()))
            ->add('save', 'submit', array('label' => 'Add Entry'))
            ->getForm();
        
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()){
            $this->submitAddNewItems($budget, $source, $type);
            return $this->redirect(
                $this->generateUrl(
                    'view_source_category', 
                    array('source' => $source)),
                301);
        }
        
        return $this->render('ProkeaShopRuleBundle:SourceCategory:source_category.html.twig', array(
            'form' => $form->createView()
        ));
    }
    
    public function viewProductsAction($source)
    {        
        $plusItems = $this->getAllMonthItems('+', $source);
        $minusItems = $this->getAllMonthItems('-', $source);
        
        return $this->render('ProkeaShopRuleBundle:SourceCategory:source_category_view.html.twig', array(
            'plus_items' => $plusItems,
            'minus_items' => $minusItems,
            'source' => $source,
            'budget' =>array(
                'source' => strtoupper($source)
            )
        ));
    }
    
    private function submitAddNewItems($budget, $source, $type)
    {
        $dm = $this->getDoctrine()->getManager();
        $dm->persist($budget);
        $dm->flush();

        $initBudget = $this->getBudgetRepository()->findOneBy(
            array('sourceCategory' => $source,
                'isMonthlyIncome' => '1',
//                'date' => $this->thisMonth
            )
        );

        if ($type == 'income'){
            $initBudget->addAmount($budget->getAmount());
        } else {
            $initBudget->substractAmount($budget->getAmount());
        }

        $totalBudget = $this->getBudgetRepository()->find($initBudget->getId());
        $totalBudget->setAmount($initBudget->getAmount());
        $dm->flush();
    }
    
    private function getAllMonthItems($sign, $sourceBudget)
    {
        $nextMonth = new \DateTime(date('Y-m-d', strtotime('first day of next month')));
        
        $qb = $this->getBudgetRepository()->createQueryBuilder('be')
            ->where('be.sourceCategory = :source')
            ->andWhere('be.sign = :sign')
            ->andwhere('be.date BETWEEN :thisMonth AND :nextMonth')
            ->setParameter('source', $sourceBudget)
            ->setParameter('sign', $sign)
            ->setParameter('thisMonth', $this->thisMonth)
            ->setParameter('nextMonth', $nextMonth)
            ->orderBy('be.date', 'DESC');
        
        return $qb->getQuery()->getResult();
    }
    
    private function getBudgetRepository()
    {
        return $this->getDoctrine()->getRepository('ProkeaShopRuleBundle:BudgetEntry');
    }
}
