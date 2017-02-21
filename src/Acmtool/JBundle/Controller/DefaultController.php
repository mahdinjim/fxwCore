<?php

namespace Acmtool\JBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Httpfoundation\Response;
use Acmtool\JBundle\Entity\Account;
Const PASSWORD = "SxON47G8IM";
Const JIRA_API_LINK = "https://jira.atlassian.com/rest/api/";
class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('AcmtoolJBundle:Default:index.html.twig', array('name' => $name));
    }
    public function linkAccountAction($linker)
    {
    	$request = $this->get('request');
        $message = $request->getContent();
        $em = $this->getDoctrine()->getManager();
        $result = $this->validateJson($message);
        if(!$result["valid"])
        {
        	$response= new Response($res);
           	return $result['response'];

        }
        else
        {
            $json = $result['json'];
        	if(isset($json->{"creds"}))
            {
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
                    $em->persist($account);
                    $em->flush();
                    $res = new Response("Account added",200);
                    return $res;
                }
                else
                    return new Response("Account not valid",400);
            }
            else
                return new Response("Invalid request",400);
        }
    }
    public function getUIAction($account,$token)
    {
        $link = $this->generateUrl('j_add_account', array("linker"=>$account), true);
        return $this->render('AcmtoolJBundle:Default:addaccount.html.twig', array('token' => $token,"link"=>$link));
    }
    private function testCreds($link,$creds)
    {
        return true;
    }
    private function fnEncrypt($iv,$sValue, $sSecretKey) {
        return rtrim(base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $sSecretKey, $sValue, MCRYPT_MODE_CBC, $iv)), "\0\3");
    }
    function fnDecrypt($iv,$sValue, $sSecretKey) {
        global $iv;
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
