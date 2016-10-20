<?php
namespace Acmtool\AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class getTicketInfoCommand extends ContainerAwareCommand
{
	protected function configure()
	{
		$this
            ->setName('info:ticketInfo')
            ->setDescription('Getting ticket info and send it by email');
	}
	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$em=$this->getContainer()->get('doctrine')->getEntityManager();
		$tickets=$em->getRepository("AcmtoolAppBundle:Ticket")->findAll();
		$mess="";
		foreach ($tickets as $ticket) {
			$line="----------------------------------\n";
			$line.=$ticket->getId()."\t";
			$line.=$ticket->getTitle()."\n";
			$descriptionlengt=strlen($ticket->getDescription());
			$time=$descriptionlengt/90;
			$i=1;
			$description=$ticket->getDescription();
			while ($i<$time) {
				$pos=$i*90;
				$description=substr_replace($description, "\n", $pos, 0);
				$i++;
			}
			$line.=$description."\n";
			$line.="Realtime=".$ticket->getRealtime()."\t";
			$line.="Estimation=".$ticket->getEstimation()."\n\n";
			$mess.=$line;
		}
		$mailer=$this->getContainer()->get('mailer');
		$message =\Swift_Message::newInstance()
		->setSubject("Ticket Info")
		->setFrom("bb8@flexwork.io")
		->setTo("mn@flexwork.io")
		->setBody($mess);
		$mailer->send($message);

	}
}