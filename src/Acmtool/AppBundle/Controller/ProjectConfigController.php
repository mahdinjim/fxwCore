<?php

namespace Acmtool\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Httpfoundation\Response;
use Acmtool\AppBundle\Entity\Project;
use Acmtool\AppBundle\Entity\ProjectConfig;
use Acmtool\AppBundle\Entity\ConstValues;

class ProjectConfigController extends Controller
{
    public function CreateAction()
    {
    	$request = $this->get('request');
        $message = $request->getContent();
        $em = $this->getDoctrine()->getManager();
        $result = $this->get('acmtool_app.validation.json')->validate($message);
        if(!$result["valid"])
            return $result['response'];
        else
        {
            $json=$result['json'];
            if(!(isset($json->{'project_id'}) && isset($json->{'config'})))
            {
                $response=new Response('{"err":"'.ConstValues::INVALIDREQUEST.'"}',400);
                $response->headers->set('Content-Type', 'application/json');
                return $response;
            }
            else
            {
            	$project=$em->getRepository("AcmtoolAppBundle:Project")->findOneById($json->{'project_id'});
            	$config=new ProjectConfig();
            	$config->setConfig($json->{'config'});
            	$config->addProject($project);
            	$project->setConfig($config);
            	$em->persist($config);
            	$em->flush();
            	$res=new Response();
                $res->setStatusCode(200);
                $res->setContent(ConstValues::CONFIGADDED);
                return $res;

            }
        }
    }
    public function UpdateAction()
    {
        $request = $this->get('request');
        $message = $request->getContent();
        $em = $this->getDoctrine()->getManager();
        $result = $this->get('acmtool_app.validation.json')->validate($message);
        if(!$result["valid"])
            return $result['response'];
        else
        {
            $json=$result['json'];
            if(!(isset($json->{'config_id'}) && isset($json->{'config'})))
            {
                $response=new Response('{"err":"'.ConstValues::INVALIDREQUEST.'"}',400);
                $response->headers->set('Content-Type', 'application/json');
                return $response;
            }
            else
            {
                $config=$em->getRepository("AcmtoolAppBundle:ProjectConfig")->findOneById($json->{'project_id'});
                $config->setConfig($json->{'config'});
                $em->flush();
                $res=new Response();
                $res->setStatusCode(200);
                $res->setContent(ConstValues::CONFIGUPDATED);
                return $res;

            }
        }
    }
    public function getProjectConfigAction($project_id)
    {
        $project=$em->getRepository("AcmtoolAppBundle:Project")->findOneById($json->{'project_id'});
        if($project)
        {
            $mess=$project->getConfig();
            $res=new Response();
            $res->setStatusCode(200);
            $res->setContent($mess);
            return $res;
        }
        else
        {
            $response=new Response('{"err":"'.ConstValues::INVALIDREQUEST.'"}',400);
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
    }
    public function deleteAction($project_id)
    {
        $project=$em->getRepository("AcmtoolAppBundle:Project")->findOneById($json->{'project_id'});
        if($project)
        {
            $config=$project->getConfig();
            $em->remove($config);
            $em->flush();
            $res=new Response();
            $res->setStatusCode(200);
            $res->setContent(ConstValues::CONFIGDELETED);
            return $res;
        }
        else
        {
            $response=new Response('{"err":"'.ConstValues::INVALIDREQUEST.'"}',400);
            $response->headers->set('Content-Type', 'application/json');
            return $response;   
        }
    }
}