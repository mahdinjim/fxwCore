<?php
namespace Acmtool\AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateDisplayIDCommand extends ContainerAwareCommand
{
 	protected function configure()
    {
        $this
            ->setName('dbsync:displayid')
            ->setDescription('Generating missing display ids in database');
    }
    protected function execute(InputInterface $input, OutputInterface $output)
    {
    	$em=$this->getContainer()->get('doctrine')->getEntityManager();
    	$customrers=$em->getRepository("AcmtoolAppBundle:Customer")->findAll();
    	foreach ($customrers as $customer) {
            $projects=$customer->getProjects();
            $i=1;
            foreach ($projects as $project) {
               
                    $owner_id =$project->getOwner()->getId();
                    if($owner_id<10){
                        $owner_id='00'.$owner_id;
                    }
                    elseif ($owner_id>=10 && $owner_id<100) {
                        $owner_id='0'.$owner_id;
                    }
                    $projectCount=$project->getId();
                    if($projectCount<10){
                        $projectCount="00".$projectCount;
                    }
                    elseif ($projectCount>=10 && $projectCount<100) {
                        $projectCount="0".$projectCount;
                    }
                    $displayid=$owner_id.$projectCount;
                    $project->setDisplayid($displayid);
                    $tickets=$project->getTickets();
                    foreach ($tickets as $ticket) {
                        $project_id=$project->getId();
                        if($project_id<10){
                            $project_id='00'.$project_id;
                        }
                        elseif ($project_id>=10 && $project_id<100) {
                            $project_id='0'.$project_id;
                        }
                        $ticketCount=$ticket->getId();
                        if($ticketCount<10){
                            $ticketCount="00".$ticketCount;
                        }
                        elseif ($ticketCount>=10 && $ticketCount<100) {
                            $ticketCount="0".$ticketCount;
                        }
                        $displayid=$project_id.$ticketCount;
                        $ticket->setDiplayId($displayid);
                    }
                    $output->writeln("Display set #$displayid");
                
                $i++;
            }
    		

    		$em->flush();
    	}

    }
    
}