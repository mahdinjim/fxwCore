<?php

namespace Acmtool\AppBundle\DependencyInjection;

use Symfony\Component\Httpfoundation\Response;

class JsonValidatorService
{
	public function validate($message)
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