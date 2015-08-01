<?php

namespace Acmtool\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Httpfoundation\Response;

class DefaultController extends Controller
{
    public function indexAction()
    {
        echo "hello to controller";
        return new Response("hello");
    }
}
