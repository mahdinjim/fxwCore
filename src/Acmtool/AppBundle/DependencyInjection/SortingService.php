<?php
namespace Acmtool\AppBundle\DependencyInjection;
use Doctrine\Common\Collections\ArrayCollection;
class SortingService
{
	public function sortTickets($tickets)
	{
		
        uasort($tickets,function($f1,$f2){
            if( $f1->getPrio()===$f2->getPrio())
                return 0;
            return ((int)$f1->getPrio()<(int)$f2->getPrio()) ? 1: -1;
        });
        return $tickets;
	}
}