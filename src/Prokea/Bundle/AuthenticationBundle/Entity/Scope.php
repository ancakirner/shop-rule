<?php
namespace Prokea\Bundle\AuthenticationBundle\Entity;

use AuthBucket\OAuth2\Model\ScopeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * Scope.
 *
 * @ORM\Table(name="oauth_scope")
 * @ORM\Entity(repositoryClass="Prokea\Bundle\AuthenticationBundle\Entity\ScopeRepository")
 */
class Scope implements ScopeInterface
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="scope", type="string", length=255)
     */
    protected $scope;

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set scope.
     *
     * @param string $scope
     *
     * @return Scope
     */
    public function setScope($scope)
    {
        $this->scope = $scope;

        return $this;
    }

    /**
     * Get scope.
     *
     * @return string
     */
    public function getScope()
    {
        return $this->scope;
    }
}
