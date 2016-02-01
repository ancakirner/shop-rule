<?php
namespace Shop\ShopRuleBundle\Wrappers;

use JMS\SerializerBundle\Annotation\Type;

class AddCashDetailWrapper
{
    /**
     * @Type("double")
     * @var double
     */
    private $amount;
    
    /**
     * @Type(""string")
     * @var string
     */
    private $product;
    
    /**
     * @Type("string")
     * @var \DateTime
     */
    private $date;
    
    public function getAmount()
    {
        return $this->amount;
    }

    public function getProduct()
    {
        return $this->product;
    }

    public function getDate()
    {
        return $this->date;
    }

    public function setAmount($amount)
    {
        $this->amount = $amount;
    }

    public function setProduct($product)
    {
        $this->product = $product;
    }

    public function setDate($date)
    {
        $this->date = new \DateTime($date);
    }


}
