<?php

namespace Acmtool\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Tester
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Acmtool\AppBundle\Entity\TesterRepository")
 */
class Tester
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }
}
