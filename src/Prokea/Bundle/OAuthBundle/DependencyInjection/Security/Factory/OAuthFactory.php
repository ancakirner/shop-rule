<?php
namespace Prokea\Bundle\OAuthBundle\DependencyInjection\Security\Factory;

use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\SecurityFactoryInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;

class OAuthFactory implements SecurityFactoryInterface
{
    public function create(ContainerBuilder $container, $id, $config, $userProvider, $defaultEntryPoint)
    {
        $providerId = 'security.authentication.provider.oauth.' . $id;
        $container->setDefinition($providerId, new DefinitionDecorator('ilius_oauth.security.authentication.provider'))
            ->replaceArgument(0, new Reference($userProvider));

        $listenerId = 'security.authentication.listener.oauth.' . $id;
        $listener = $container->setDefinition(
            $listenerId,
            new DefinitionDecorator('ilius_oauth.security.authentication.listener')
        );

        return array($providerId, $listenerId, $defaultEntryPoint);
    }

    public function getPosition()
    {
        return 'pre_auth';
    }

    public function getKey()
    {
        return 'oauth';
    }

    public function addConfiguration(NodeDefinition $node)
    {
    }
}
