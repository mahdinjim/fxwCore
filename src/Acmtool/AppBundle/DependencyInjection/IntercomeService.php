// <?php
// namespace TigerVPN\Library;
// use Intercom\IntercomBasicAuthClient;
// use Intercom\Exception\ClientErrorResponseException;
// use Intercom\Exception\ServerErrorResponseException;
// class IntercomeService
// {
// 	const APP_ID="d4ofbole";
// 	const APPI_KEY="4a0ef93c2aedefe8dbb412972642f17675e53884";
// 	private $intercom;
// 	public function __construct() {
//         $this->intercom = IntercomBasicAuthClient::factory(array(
// 		    'app_id' => static::APP_ID,
// 		    'api_key' => static::APPI_KEY
// 		));
//     }
//     public function createNewUser($email,$userid)
//     {
//     	$user_data=array(
//     		"email"=>$email,
//     		"last_request_at"=>time(),
//     		"custom_attributes"=>array(
//     			"tigerID"=>$userid)
//     		);
//         try {
//             $user = $this->intercom->createUser($user_data);
//             } catch (ServerErrorResponseException $e) {
//                 // log intercom problem
//                 $request = $e->getRequest();
//                 $url = $request->getUrl();
//                 $params = serialize($request->getParams()->toArray());
//                 error_log("[API SERVER ERROR] Status Code: {$url} | Body: {$params}");

//                 $response = $e->getResponse();
//                 $code = $response->getStatusCode();
//                 $body = $response->getBody();
//                 error_log("[API SERVER ERROR] Status Code: {$code} | Body: {$body}");

//             } catch (ClientErrorResponseException $e) {
//                  // log client problem
//                 $request = $e->getRequest();
//                 $url = $request->getUrl();
//                 $params = serialize($request->getParams()->toArray());
//                 error_log("[Client ERROR] Status Code: {$url} | Body: {$params}");

//                 $response = $e->getResponse();
//                 $code = $response->getStatusCode();
//                 $body = $response->getBody();
//                 error_log("[Client ERROR] Status Code: {$code} | Body: {$body}");
//             }
//     }
//     public function addCustomAttribute($email,$attributes)
//     {
//         try{
//             $user = $this->intercom->getUser(array("email" => $email));
//             $this->intercom->updateUser(array(
//                 "id" => $user["id"],
//                 "custom_attributes"=>$attributes
//             ));
//         }
//         catch (ServerErrorResponseException $e) {
//                 // log intercom problem
//                 $request = $e->getRequest();
//                 $url = $request->getUrl();
//                 $params = serialize($request->getParams()->toArray());
//                 error_log("[API SERVER ERROR] Status Code: {$url} | Body: {$params}");

//                 $response = $e->getResponse();
//                 $code = $response->getStatusCode();
//                 $body = $response->getBody();
//                 error_log("[API SERVER ERROR] Status Code: {$code} | Body: {$body}");

//             } catch (ClientErrorResponseException $e) {
//                  // log intercom problem
//                 $request = $e->getRequest();
//                 $url = $request->getUrl();
//                 $params = serialize($request->getParams()->toArray());
//                 error_log("[API SERVER ERROR] Status Code: {$url} | Body: {$params}");

//                 $response = $e->getResponse();
//                 $code = $response->getStatusCode();
//                 $body = $response->getBody();
//                 error_log("[API SERVER ERROR] Status Code: {$code} | Body: {$body}");
//             }
//     }
//     public function updateUserEmail($email,$newemail)
//     {
//         try{
//             $user = $this->intercom->getUser(array("email" => $email));
//             $this->intercom->updateUser(array(
//                 "id" => $user["id"],
//                 "email"=>$newemail
//             ));
//         }
//         catch (ServerErrorResponseException $e) {
//                 // log intercom problem
//                 $request = $e->getRequest();
//                 $url = $request->getUrl();
//                 $params = serialize($request->getParams()->toArray());
//                 error_log("[API SERVER ERROR] Status Code: {$url} | Body: {$params}");

//                 $response = $e->getResponse();
//                 $code = $response->getStatusCode();
//                 $body = $response->getBody();
//                 error_log("[API SERVER ERROR] Status Code: {$code} | Body: {$body}");

//             } catch (ClientErrorResponseException $e) {
//                  // log intercom problem
//                 $request = $e->getRequest();
//                 $url = $request->getUrl();
//                 $params = serialize($request->getParams()->toArray());
//                 error_log("[API SERVER ERROR] Status Code: {$url} | Body: {$params}");

//                 $response = $e->getResponse();
//                 $code = $response->getStatusCode();
//                 $body = $response->getBody();
//                 error_log("[API SERVER ERROR] Status Code: {$code} | Body: {$body}");
//             }
//     }
//     public function deleteIntercomUser($email)
//     {
//        try {
//             $this->intercom->deleteUser(array("email" => $email));
//             } catch (ServerErrorResponseException $e) {
//                 // log intercom problem
//                 $request = $e->getRequest();
//                 $url = $request->getUrl();
//                 $params = serialize($request->getParams()->toArray());
//                 error_log("[API SERVER ERROR] Status Code: {$url} | Body: {$params}");

//                 $response = $e->getResponse();
//                 $code = $response->getStatusCode();
//                 $body = $response->getBody();
//                 error_log("[API SERVER ERROR] Status Code: {$code} | Body: {$body}");

//             } catch (ClientErrorResponseException $e) {
//                  // log client problem
//                 $request = $e->getRequest();
//                 $url = $request->getUrl();
//                 $params = serialize($request->getParams()->toArray());
//                 error_log("[Client ERROR] Status Code: {$url} | Body: {$params}");

//                 $response = $e->getResponse();
//                 $code = $response->getStatusCode();
//                 $body = $response->getBody();
//                 error_log("[Client ERROR] Status Code: {$code} | Body: {$body}");
//             } 
//     }
// }