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
            if(!(isset($json->{'project_id'}) && isset($json->{'config'})&& isset($json->{'title'})))
            {
                $response=new Response('{"error":"'.ConstValues::INVALIDREQUEST.'"}',400);
                $response->headers->set('Content-Type', 'application/json');
                return $response;
            }
            else
            {
            	$loggeduser=$this->get("security.context")->getToken()->getUser();
                $project=$em->getRepository("AcmtoolAppBundle:Project")->getProjectByLoggedUser($loggeduser,$json->{'project_id'});
                if($project)
                {
                   $config=new ProjectConfig();
                    $config->setTitle($json->{'title'});
                    $config->setConfig($json->{'config'});
                    $config->setProject($project);
                    $project->addConfig($config);
                    $em->persist($config);
                    $em->flush();
                    $res=new Response();
                    $res->setStatusCode(200);
                    $res->setContent(ConstValues::CONFIGADDED);
                    return $res; 
                }
            	else
                {
                    $response=new Response('{"error":"'.ConstValues::INVALIDREQUEST.'"}',400);
                    $response->headers->set('Content-Type', 'application/json');
                    return $response;
                }

            }
        }
    }
    public function getAllProjectConfigsAction($project_id)
    {
        $em = $this->getDoctrine()->getManager();
        $loggeduser=$this->get("security.context")->getToken()->getUser();
        $project=$em->getRepository("AcmtoolAppBundle:Project")->getProjectByLoggedUser($loggeduser,$project_id);
        if($project)
        {
            $qb = $em->createQueryBuilder();
            $qb->select('k')
            ->from("AcmtoolAppBundle:ProjectConfig","k")
            ->where("k.project=:project")
            ->setParameter("project",$project);
            $result = $qb->getQuery()->getResult();
            //var_dump($result);die();
            $configs=array();
            $i=0;
            foreach ($result as $key) {
                $configs[$i]=array("id"=>$key->getId(),"title"=>$key->getTitle(),"config"=>$key->getConfig());
                $i++;
            }
            $res=new Response();
            $res->setStatusCode(200);
            $res->setContent(json_encode($configs));
            return $res;

        }
        else
        {
            $response=new Response('{"error":"'.ConstValues::INVALIDREQUEST.'"}',400);
            $response->headers->set('Content-Type', 'application/json');
            return $response;
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
            if(!(isset($json->{'config_id'}) && isset($json->{'config'}) && isset($json->{'title'})))
            {
                $response=new Response('{"error":"'.ConstValues::INVALIDREQUEST.'"}',400);
                $response->headers->set('Content-Type', 'application/json');
                return $response;
            }
            else
            {
                $config=$em->getRepository("AcmtoolAppBundle:ProjectConfig")->findOneById($json->{'config_id'});
                $loggeduser=$this->get("security.context")->getToken()->getUser();
                $project=$em->getRepository("AcmtoolAppBundle:Project")->getProjectByLoggedUser($loggeduser,$config->getProject()->getDisplayId());
                if($project)
                {
                    $config->setConfig($json->{'config'});
                    $config->setTitle($json->{'title'});
                    $em->flush();
                    $res=new Response();
                    $res->setStatusCode(200);
                    $res->setContent(ConstValues::CONFIGUPDATED);
                    return $res; 
                }
                else
                {
                    $response=new Response('{"error":"'.ConstValues::INVALIDREQUEST.'"}',400);
                    $response->headers->set('Content-Type', 'application/json');
                    return $response;
                }
                

            }
        }
    }
    public function deleteAction($config_id)
    {
        $em = $this->getDoctrine()->getManager();
        $config=$em->getRepository("AcmtoolAppBundle:ProjectConfig")->findOneById($config_id);
        $loggeduser=$this->get("security.context")->getToken()->getUser();
        $project=$em->getRepository("AcmtoolAppBundle:Project")->getProjectByLoggedUser($loggeduser,$config->getProject()->getDisplayId());
        if($config && $project)
        {
            $em->remove($config);
            $em->flush();
            $res=new Response();
            $res->setStatusCode(200);
            $res->setContent(ConstValues::CONFIGDELETED);
            return $res;
        }
        else
        {
            $response=new Response('{"error":"'.ConstValues::INVALIDREQUEST.'"}',400);
            $response->headers->set('Content-Type', 'application/json');
            return $response;   
        }
    }
}