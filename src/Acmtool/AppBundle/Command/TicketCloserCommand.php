<?php
namespace Acmtool\AppBundle\Command;
use Acmtool\AppBundle\Entity\TicketStatus;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
Const TIMEZONE="Europe/Berlin";
class TicketCloserCommand extends ContainerAwareCommand
{
 	protected function configure()
    {
        $this
            ->setName('ticket:close')
            ->setDescription('Generating missing display ids in database');
    }
    protected function execute(InputInterface $input, OutputInterface $output)
    {
    	$em=$this->getContainer()->get('doctrine')->getEntityManager();
    	$tickets=$em->createQuery("SELECT t FROM AcmtoolAppBundle:Ticket t 
    								WHERE t.status = :ticketstatus AND t.closingdate IS NOT null")
    							->setParameter("ticketstatus",TicketStatus::ACCEPT)
    							->getResult();
    	$today =new \DateTime("NOW",new \DateTimeZone(TIMEZONE));
    	foreach ($tickets as $key) {
    		$project=$key->getProject();
    		if($today>$key->getClosingdate())
    		{
    			$key->setStatus(TicketStatus::DONE);
				$key->setFinisheddate(new \DateTime("UTC"));
				$em->flush();
				$emails=array();
				array_push($emails, $project->getKeyaccount()->getEmail());
		        if($project->getTeamleader())
		            array_push($emails, $project->getTeamleader()->getLogin());
		        $this->getContainer()->get("acmtool_app.email.notifier")->notifyTicketAccepted($emails,$project->getName(),$key->getTitle());
				$this->getContainer()->get("acmtool_app.email.notifier")->notifyClientTicketDone($project->getOwner(),$key);
    		}
    		$notifDate=$key->getClosingdate()->sub(new \DateInterval("P1D"));
    		if(($today >= $notifDate) && !$key->getClosenotif())
    		{
    			$this->getContainer()->get("acmtool_app.email.notifier")->notifyClientReminder($project->getOwner(),$key);
    			$key->setClosenotif(true);
    			$em->flush();
    		}
    	}
    }
}