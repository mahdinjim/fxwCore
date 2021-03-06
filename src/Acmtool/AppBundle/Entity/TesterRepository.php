<?php

namespace Acmtool\AppBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * TesterRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class TesterRepository extends EntityRepository
{
	public function findOneByCreds($creds)
	{
		$result=null;
		$em=$this->getEntityManager();
		

		try{
			$result=$em->createQuery('select u from AcmtoolAppBundle:Tester u
								WHERE u.credentials = :cred')
						->setParameter("cred",$creds)
                       
                        ->getSingleResult();
            return $result;
           }
        catch(\Doctrine\ORM\NoResultException $e)
        {
        	return null;
        }
    }
}
