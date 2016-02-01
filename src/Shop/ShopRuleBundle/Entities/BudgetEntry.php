<?php
namespace Shop\ShopRuleBundle\Entities;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping\PrePersist;

/**
 * @ORM\Entity
 * @ORM\Table(name="budget_entries")
 */
class BudgetEntry
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @ORM\Column(type="decimal", precision=1)
     * @Assert\NotBlank
     * @Assert\GreaterThan(value=0)
     * @Assert\Type(
     *      type="float",
     *      message="The value {{ value }} is not a valid {{ type }}."
     * )
     */
    private $amount;
    
    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     */
    private $description;
    
    /**
     * @ORM\Column(type="string", name="source_category", length=10)
     * @Assert\NotBlank
     * @Assert\Choice(
     *      choices = {"card", "cash"},
     *      message = "Choose a valid method of paying."
     * )
     */
    private $sourceCategory;
    
    /**
     * @ORM\Column(type="datetimeutc")
     */
    private $date;
    
    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     * @Assert\Choice(
     *      choices = {"+", "-"},
     *      message = "Choose a valid sign for add or substract."
     * )
     */
    private $sign;
    
    /**
     * @ORM\Column(type="boolean", name="is_monthly_income", options={"default": false})
     */
    private $isMonthlyIncome;
    
    /**
     * @PrePersist
     */
    public function onPrePersistSetRegistrationDate()
    {
        $this->date = new \DateTime();
    }
    
    public function getId()
    {
        return $this->id;
    }

    public function getAmount()
    {
        return $this->amount;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getSourceCategory()
    {
        return $this->sourceCategory;
    }

    public function getDate()
    {
        return $this->date;
    }

    public function getSign()
    {
        return $this->sign;
    }

    public function getIsMonthlyIncome()
    {
        return $this->isMonthlyIncome;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setAmount($amount)
    {
        $this->amount = is_numeric($amount) ? floatval($amount): $amount;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function setSourceCategory($sourceCategory)
    {
        $this->sourceCategory = $sourceCategory;
    }

    public function setDate($date)
    {
        $this->date = $date;
    }

    public function setSign($sign)
    {
        $this->sign = $sign;
    }

    public function setIsMonthlyIncome($isMonthlyIncome)
    {
        $this->isMonthlyIncome = $isMonthlyIncome;
    }
    
    public function addAmount($amount)
    {
        $this->amount += $amount;
        return floatval($this->amount);
    }
    
    public function substractAmount($amount)
    {
        $this->amount -= $amount;
        return floatval($this->amount);
    }
    
    public function getAmountToString()
    {
        return number_format($this->amount, 2);
    }
}