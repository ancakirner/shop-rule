<?php
namespace Prokea\Bundle\AuthenticationBundle\Entity;

use AuthBucket\OAuth2\Exception\ServerErrorException;
use AuthBucket\OAuth2\Model\ModelManagerFactoryInterface;
use AuthBucket\OAuth2\Model\ModelManagerInterface;
use Doctrine\ORM\EntityManager;

/**
 * OAuth2 model manager factory implemention.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class ModelManagerFactory implements ModelManagerFactoryInterface
{
    protected $managers;

    public function __construct(EntityManager $em, array $models = [])
    {
        $managers = [];
        foreach ($models as $type => $model) {
            $manager = $em->getRepository($model);
            if (!$manager instanceof ModelManagerInterface) {
                throw new ServerErrorException();
            }
            $managers[$type] = $manager;
        }

        $this->managers = $managers;
    }

    public function getModelManager($type)
    {
        if (!isset($this->managers[$type])) {
            throw new ServerErrorException();
        }

        return $this->managers[$type];
    }
}
