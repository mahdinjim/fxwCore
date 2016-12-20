<?php
namespace Acmtool\AppBundle\Command;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
class UploadIntercomUsersCommand extends ContainerAwareCommand
{
	protected function configure()
	{
		$this
            ->setName('intercom:upload')
            ->setDescription('upload fxw clients to intercom');
	}
	protected function execute(InputInterface $input, OutputInterface $Output)
	{
		$em=$this->getContainer()->get('doctrine')->getEntityManager();
		$customrers=$em->getRepository("AcmtoolAppBundle:Customer")->findAll();
		$customrerUsers=$em->getRepository("AcmtoolAppBundle:CustomerUser")->findAll();
		foreach ($customrers as $key) {
			$this->getContainer()->get("acmtool_app.intercom")->createNewUser($key,
				$key->getCompanyname(),
				$key->getAddress()->getCountry(),
				$key->getAddress()->getCity());
		}
		foreach ($customrerUsers as $key) {
			$this->getContainer()->get("acmtool_app.intercom")->createNewUser($key,
				$key->getCompany()->getCompanyname(),
				$key->getCompany()->getAddress()->getCountry(),
				$key->getCompany()->getAddress()->getCity());
		}
	}
}