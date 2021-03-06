<?php

namespace Acmtool\AppBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Acmtool\AppBundle\Entity\WorkedHours;

/**
 * TokenRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class WorkedHoursRepository extends EntityRepository
{
	public function getWorkedHoursByMonth($user,$month)
	{
		$em=$this->getEntityManager();
		$result=$em->createQuery('SELECT w FROM AcmtoolAppBundle:WorkedHours w 
			WHERE w.user= :u AND w.month= :m')
		->setParameter("u",$user)
		->setParameter("m",$month)
		->getResult();
		return $result;

	}
}