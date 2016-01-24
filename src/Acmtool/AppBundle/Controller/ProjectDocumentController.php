<?php
namespace Acmtool\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Httpfoundation\Response;
use Acmtool\AppBundle\Entity\ProjectDocument;
use Acmtool\AppBundle\Entity\Creds;
use Acmtool\AppBundle\Entity\Titles;
use Acmtool\AppBundle\Entity\ConstValues;
use Acmtool\AppBundle\Entity\Roles;

class ProjectDocumentController extends Controller
{
	public function uploadFileAction($project_id)
	{
		$request = $this->get('request');
		$em = $this->getDoctrine()->getManager();
    	$project=$em->getRepository("AcmtoolAppBundle:Project")->findOneById($project_id);
    	if($project)
    	{
	    	$fileBag = $request->files;
			$files=$fileBag->all();
			$filename=str_replace(' ', '', $files['file']->getClientOriginalName());
			$path=__DIR__.'/../../../../web'.'/uploads/pdocs/'.$project->getId();
			if(!file_exists($path))
			{
				mkdir($path);
			}
			$filepath=$path."/".$filename;
			if(!file_exists($filepath))
			{
				$files["file"]->move($path, $filename);
				$doc=new ProjectDocument();
				$doc->setName($filename);
				$doc->setPath('/uploads/pdocs/'.$project->getId()."/".$filename);
				$doc->setProject($project);
				$project->addDocument($doc);
				$em->persist($doc);
				$em->flush();
				$response=new Response("Document added",200);
                return $response;

			}
			else
			{
				$response=new Response("Document already added",201);
                return $response;
			}
    	}
    	else
		{
			$response=new Response('{"error":"'.ConstValues::INVALIDREQUEST.'"}',400);
        	$response->headers->set('Content-Type', 'application/json');
        	return $response;
		}
    	
	}
	public function listDocumentsAction($project_id)
	{
		$request = $this->get('request');
		$em = $this->getDoctrine()->getManager();
    	$project=$em->getRepository("AcmtoolAppBundle:Project")->findOneById($project_id);
    	if($project)
    	{
    		$baseurl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath();
    		$h=0;
    		$dosc=array();
    		foreach ($project->getDocuments() as $key) {
    			$data=array("id"=>$key->getId(),"name"=>$key->getName(),"link"=>$baseurl.$key->getPath());
    			$dosc[$h]=$data;
    			$h++;
    		}
    		$response=new Response(json_encode($dosc),200);
        	$response->headers->set('Content-Type', 'application/json');
        	return $response;
    	}
    	else
	{
		$response=new Response('{"error":"'.ConstValues::INVALIDREQUEST.'"}',400);
        	$response->headers->set('Content-Type', 'application/json');
        	return $response;
	}
	}
	
}