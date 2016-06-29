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
                    $projectCount=$i;
                    if($projectCount<10){
                        $projectCount="00".$projectCount;
                    }
                    elseif ($projectCount>=10 && $projectCount<100) {
                        $projectCount="0".$projectCount;
                    }
                    $displayid=$owner_id.$projectCount;
                    $project->setDisplayid($displayid);

                    $output->writeln("Display set #$displayid");
                
                $i++;
            }
    		

    		$em->flush();
    	}

    }
    
}