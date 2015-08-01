<?php

namespace Acmtool\AppBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Acmtool\AppBundle\firewall\apiauth\ApiAuthFactory;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class AcmtoolAppBundle extends Bundle
{
	 public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $extension = $container->getExtension('security');
        $extension->addSecurityListenerFactory(new ApiAuthFactory());
    }
}
