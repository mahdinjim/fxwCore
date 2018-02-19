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
        $closedTickets=0;
        $ticketTitles=array();
    	foreach ($tickets as $key) {
    		$project=$key->getProject();
    		if($today>$key->getClosingdate())
    		{
    			$key->setStatus(TicketStatus::DONE);
				$key->setFinisheddate(new \DateTime("UTC"));
				$em->flush();
				$emails=array();
                array_push($ticketTitles, $key->getTitle());
                $closedTickets++;
				$this->getContainer()->get("acmtool_app.notifier.handler")->ticketAccepted($key,null);
    		}
    		$notifDate=$key->getClosingdate()->sub(new \DateInterval("P1D"));
    		if(($today >= $notifDate) && !$key->getClosenotif())
    		{
                $key->setClosenotif(true);
                $em->flush();
    			$this->getContainer()->get("acmtool_app.notifier.handler")->ticketWillclose($key);
    		}
    	}
        if($closedTickets>0)
        {
            $body="Number of ticket closed: ".$closedTickets."\nTickets:\n".implode("\n", $ticketTitles);
            $message =\Swift_Message::newInstance()
                ->setSubject("closed tickets")
                ->setFrom("bb8@flexwork.io")
                ->setTo("mn@flexwork.io")
                ->setBody($body);
            $this->getContainer()->get('mailer')->send($message);
        }
        
    }
}