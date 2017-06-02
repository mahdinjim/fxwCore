<?php
namespace Acmtool\AppBundle\Command;
use Acmtool\AppBundle\Entity\TicketStatus;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
class CreateCompanyDoaminCommand extends ContainerAwareCommand
{
 	protected function configure()
    {
        $this
            ->setName('client:domain')
            ->setDescription('Generating missing client domains in db');
    }
    protected function execute(InputInterface $input, OutputInterface $output)
    {
    	$em=$this->getContainer()->get('doctrine')->getEntityManager();
    	$clients=$em->getRepository("AcmtoolAppBundle:Customer")->findAll();
        foreach ($clients as $key) {
            $email=$key->getEmail();
            $domain = substr(strrchr($email, "@"), 1);
            $key->setCompnayDomain($domain);
            $em->flush();
        }
        
        
    }
}