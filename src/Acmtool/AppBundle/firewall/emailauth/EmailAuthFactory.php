<?php
namespace Acmtool\AppBundle\firewall\emailauth;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\SecurityFactoryInterface;

class EmailAuthFactory implements SecurityFactoryInterface
{
	public function create(ContainerBuilder $container, $id, $config, $userProvider, $defaultEntryPoint)
    {
        $providerId = 'security.authentication.provider.Email_Auth.'.$id;
        $container
            ->setDefinition($providerId, new DefinitionDecorator('emailauth.security.authentication.provider'));

        $listenerId = 'security.authentication.listener.Email_Auth.'.$id;
        $listener = $container->setDefinition($listenerId, new DefinitionDecorator('emailauth.security.authentication.listener'));

        return array($providerId, $listenerId, $defaultEntryPoint);
    }

    public function getPosition()
    {
        return 'pre_auth';
    }

    public function getKey()
    {
        return 'Email_Auth';
    }

    public function addConfiguration(NodeDefinition $node)
    {
    }
}