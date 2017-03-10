<?php
namespace Acmtool\AppBundle\DependencyInjection;
use Intercom\IntercomClient;
class IntercomService
{
	const APP_ID="bf90791k";
	const APPI_KEY="b87559853a358e3d28788652894af1f62cc0f8e9";
	private $intercom;
	public function __construct() {
        $this->intercom = new IntercomClient(static::APP_ID,static::APPI_KEY);
    }
    public function createNewUser($user,$company_name,$county,$city)
    {
       
    	$user_data=array(
    		"email"=>$user->getEmail(),
            "name"=>$user->getName()." ".$user->getSurname(),
    		"custom_attributes"=>array(
    			"company_name"=>$company_name,
                'country'=>$county,
                'city'=>$city)
    		);
       
        	$user = $this->intercom->users->create($user_data);
           

    }
    public function addCustomAttribute($email,$attributes)
    {
       try{
            $user_data=array(
                "email"=>$email,
                "last_request_at"=>time(),
                "custom_attributes"=>$attributes
                );
            $user = $this->intercom->users->create($user_data);
       }
       catch(\GuzzleHttp\Exception\GuzzleException $exception)
        {

        }
            
    }
    public function updateUserEmail($email,$newemail)
    {
        try{
            $user = $this->intercom->users->getUsers(array("email" => $email));
            $this->intercom->users->create(array(
                "id" =>$user->id,
                "email"=>$newemail
            ));
        }
        catch(\GuzzleHttp\Exception\GuzzleException $exception)
        {

        }
        
    }
    public function deleteIntercomUser($email)
    {
        try{
             $user = $this->intercom->users->getUsers(array("email" => $email));
            $this->intercom->users->deleteUser($user['id']); 
        }
        catch(\GuzzleHttp\Exception\GuzzleException $exception)
        {

        }
      
    }
}