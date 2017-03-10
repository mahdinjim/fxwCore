<?php

namespace Acmtool\JBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Httpfoundation\Response;
use Acmtool\JBundle\Entity\Account;
use Acmtool\JBundle\Entity\JiraProject;
Const PASSWORD = "SxON47G8IM";
Const JIRA_API_LINK = "https://jira.atlassian.com/rest/api/";
Const TEST_LINK = "/rest/api/2/project";
Const FXWBASELINK = "http://localhost/fxwCore/web/app_dev.php/api";
Const FXWLINKTOOL = "/private/client/linktool";
Const FXWUNLINKTOOL = "/private/client/unlinktool";
Const PMTOOLNAME = "Jira";
Const FXWLINKPROJECT = "/private/project/linktool/";
Const FXWUNLINKPROJECT = "/private/project/unlinktool/";
class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('AcmtoolJBundle:Default:index.html.twig', array('name' => $name));
    }
    public function linkAccountAction($linker,$token)
    {
    	$request = $this->get('request');
        $message = $request->getContent();
        $em = $this->getDoctrine()->getManager();
        $result = $this->validateJson($message);
        if(!$result["valid"])
        {
           	return $result['response'];

        }
        else
        {
            $json = $result['json'];
        	if(isset($json->{"creds"}))
            {
                $accounts = $em->getRepository("AcmtoolJBundle:Account")->findByLinker($linker);
                if($accounts>0)
                {
                    foreach ($accounts as $key) {
                        $em->remove($key);
                    }
                }
                $em->flush();
                $account = new Account();
                $aes256Key = hash("SHA256", PASSWORD, true);
                srand((double) microtime() * 1000000);
                $iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC), MCRYPT_RAND);
                $crypted = $this->fnEncrypt($iv,$json->{"creds"}, $aes256Key);
                if($json->{"link"} != "")
                {
                    $link = $json->{"link"};
                }
                else
                    $link = JIRA_API_LINK;
                $valid = $this->testCreds($link,$json->{"creds"});
                if($valid)
                {
                    $account->setT1($crypted);
                    $account->setT2(base64_encode($iv));
                    $account->setLink($json->{"link"});
                    $account->setLinker(intval($linker));
                    $url = FXWBASELINK.FXWLINKTOOL."/".$linker."/".PMTOOLNAME;
                    var_dump($url);
                    $header = ["x-crm-access-token: ".$token];
                    $ch = curl_init($url);
                    curl_setopt($ch, CURLOPT_HTTPHEADER,$header);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_HEADER, true);    // we want headers
                    curl_setopt($ch, CURLOPT_NOBODY, true);    // we don't need body
                    $response = curl_exec($ch);
                    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                    curl_close($ch);
                    if($httpcode == 200)
                    {
                        $em->persist($account);
                        $em->flush();
                        $res = new Response("Account added",200);
                        return $res;
                    }
                    else
                        return new Response("Flexwork API error",503);
                    
                }
                else
                    return new Response("Account not valid",401);
            }
            else
                return new Response("Invalid request",400);
        }
    }
    public function unLinkAccountAction($account,$token)
    {
        $em = $this->getDoctrine()->getManager();
        $accounts = $em->getRepository("AcmtoolJBundle:Account")->findByLinker($account);
        if($accounts>0)
        {
            foreach ($accounts as $key) {
                $em->remove($key);
            }
            $url = FXWBASELINK.FXWUNLINKTOOL."/".$account."/".PMTOOLNAME;
            $header = ["x-crm-access-token: ".$token];
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_HTTPHEADER,$header);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HEADER, true);    // we want headers
            curl_setopt($ch, CURLOPT_NOBODY, true);    // we don't need body
            $response = curl_exec($ch);
            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            if($httpcode == 200)
            {
                $em->flush();
                $res = new Response("Account unlinked",200);
                return $res;
            }
            else 
                return new Response("Flexwork API error",503);
        }
        else
        {
            return new Response("Account not valid",401); 
        }
    }
    public function getUIAction($account,$token)
    {
        $link = $this->generateUrl('j_add_account', array("linker"=>$account), true);
        return $this->render('AcmtoolJBundle:Default:addaccount.html.twig', array('token' => $token,"link"=>$link));
    } 
    public function getJiraProjectsAction($account)
    {
        $em = $this->getDoctrine()->getManager();
        $accounts = $em->getRepository("AcmtoolJBundle:Account")->findByLinker($account);
        if(count($accounts)>0)
        {
            $accountInfo = $accounts[count($accounts)-1];
            $url = $accountInfo->getLink().TEST_LINK;
            $aes256Key = hash("SHA256", PASSWORD, true);
            $creds = base64_encode($this->fnDecrypt(base64_decode($accountInfo->getT2()),$accountInfo->getT1(),$aes256Key));
            $header = array("Authorization: Basic ".$creds,"Content-Type: application/json");
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_HTTPHEADER,$header);
            curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
            curl_setopt($ch, CURLOPT_HEADER, false);
            $response = curl_exec($ch);
            curl_close($ch);
            if($response)
            {
                $result = $this->validateJson($response);
                if(!$result["valid"])
                {
                    return $result['response'];
                }
                else
                {
                    $json = $result['json'];
                    $mess = array();
                    $i=0;
                    foreach ($json as $key) {
                        $data = array("project_id"=>$key->{"id"},"name"=>$key->{"name"});
                        $mess[$i]=$data;
                        $i++;
                    }
                    return new Response(json_encode($mess),200);

                }
            }
            else
                 return new Response("Can't connect",401);

        }
        else
        {
            return new Response("No account found",400);
        }
    }
    public function linkProjectsAction($token)
    {
        $request = $this->get('request');
        $message = $request->getContent();
        $em = $this->getDoctrine()->getManager();
        $result = $this->validateJson($message);
        if(!$result["valid"])
        {
            return $result['response'];

        }
        else
        {
            $json = $result['json'];
            if(isset($json->{"fxw_project"}) && isset($json->{"j_project"}) && isset($json->{"project_name"}))
            {
                $project = new JiraProject();
                $project->setProjectName($json->{"project_name"});
                $project->setProjectId($json->{"j_project"});
                $project->setLinkerId($json->{"fxw_project"});
                $validator = $this->get('validator');
                $errorList = $validator->validate($project);
                if(count($errorList)>0)
                {
                    $errosmsg=array();
                    foreach ($errorList as $error) {
                        array_push($errosmsg, $error->getMessage());
                    }
                    return new Response(json_encode(array("errors"=>$errosmsg)),400);
                }
                $url = FXWBASELINK.FXWLINKPROJECT.$json->{"fxw_project"}."/".urlencode($json->{"project_name"})."/".PMTOOLNAME;
                $header = ["x-crm-access-token: ".$token];
                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_HTTPHEADER,$header);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HEADER, true);    // we want headers
                curl_setopt($ch, CURLOPT_NOBODY, true);    // we don't need body
                $response = curl_exec($ch);
                $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);
                if($httpcode == 200)
                {
                    $em->persist($project);
                    $em->flush();
                    return new Response("Project linked",200);
                }
                else
                    return new Response("Flexwork API error",503);
            }
            else
                return new Response("Invalid request",400);
        }
    }
    public function unlinkProjectsAction($project_id,$token)
    {
        $em = $this->getDoctrine()->getManager();
        $projects = $em->getRepository("AcmtoolJBundle:JiraProject")->findByLinkerId($project_id);
        if(count($projects) > 0)
        {
            foreach ($projects as $key) {
                $em->remove($key);
            }
            $url = FXWBASELINK.FXWUNLINKPROJECT.$project_id."/".PMTOOLNAME;
            $header = ["x-crm-access-token: ".$token];
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_HTTPHEADER,$header);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HEADER, true);    // we want headers
            curl_setopt($ch, CURLOPT_NOBODY, true);    // we don't need body
            $response = curl_exec($ch);
            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            if($httpcode == 200)
            {
                $em->flush();
                return new Response("Project unlinked",200);
            }
            else
                return new Response("Flexwork API error",503);
        }
        else
            return new Response("Project not linked",200);
    }
    public function getfxwProjectIdAction($jira_project_id)
    {
        $em = $this->getDoctrine()->getManager();
        $projects = $em->getRepository("AcmtoolJBundle:JiraProject")->findByProjectId($jira_project_id);
        if($projects > 0)
        {
            $project = $projects[0];
            $theid = $project->getLinkerId();
            return new Response(json_encode(array('fxw_project' =>$theid)),200);
        }
        else
            return new Response("No flexwork project linked to this project");
    }

    private function testCreds($link,$creds)
    {
        $url = $link.TEST_LINK;
        $ch = curl_init($url);
        $header = array('Authorization' =>"Basic ".base64_encode($creds));
        curl_setopt($ch, CURLOPT_HTTPHEADER,$header);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, true);    // we want headers
        curl_setopt($ch, CURLOPT_NOBODY, true);    // we don't need body
        $response = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return ($httpcode === 200);
    }
    private function fnEncrypt($iv,$sValue, $sSecretKey) {
        return rtrim(base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $sSecretKey, $sValue, MCRYPT_MODE_CBC, $iv)), "\0\3");
    }
    function fnDecrypt($iv,$sValue, $sSecretKey) {
        return rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $sSecretKey, base64_decode($sValue), MCRYPT_MODE_CBC, $iv), "\0\3");
    }
    private function validateJson($message)
    {
    	$json = json_decode($message);
        if(json_last_error()){
            $response = new Response();
            $response->setStatusCode(400);
            switch (json_last_error()) {
                case JSON_ERROR_NONE:
                    $erro_message =  ' - No errors';
                break;
                case JSON_ERROR_DEPTH:
                    $erro_message = ' - Maximum stack depth exceeded';
                break;
                case JSON_ERROR_STATE_MISMATCH:
                    $erro_message = ' - Underflow or the modes mismatch';
                break;
                case JSON_ERROR_CTRL_CHAR:
                    $erro_message = ' - Unexpected control character found';
                break;
                case JSON_ERROR_SYNTAX:
                    $erro_message = ' - Syntax error, malformed JSON';
                break;
                case JSON_ERROR_UTF8:
                    $erro_message = ' - Malformed UTF-8 characters, possibly incorrectly encoded';
                break;
                default:
                    $erro_message = ' - Unknown error';
                break;

            }
            $response->setContent(json_encode(array('errors' => $erro_message)));
            return array('valid' => false,'response'=>$response );
        }
        else
            return array('valid' => true,'json'=>$json );
    }
}
