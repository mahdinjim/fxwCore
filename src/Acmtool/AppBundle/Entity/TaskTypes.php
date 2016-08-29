<?php
namespace Acmtool\AppBundle\Entity;

class TaskTypes
{
	public static $ALL = array('text' =>"All" ,"type"=>"all" );
	public static $BUG = array('text' =>"Bug" ,"type"=>"bug" );
	public static $TEST = array('text' =>"Test" ,"type"=>"test" );
	public static $CONCEPT = array('text' =>"Concept" ,"type"=>"concept" );
	public static $DEPLOYMENT = array('text' =>"Deployment" ,"type"=>"deployment" );
	public static $UIDESIGN = array('text' =>"UI Design" ,"type"=>"uidesign" );
	public static $UICODING = array('text' =>"UI Coding" ,"type"=>"uicoding" );
	public static $FRONTEND = array('text' =>"Frontend dev" ,"type"=>"frontenddev" );
	public static $BACKEND = array('text' =>"Backend dev" ,"type"=>"backenddev" );
	public static function serialize()
	{
		$types=[TaskTypes::$All,TaskTypes::$BUG,TaskTypes::$TEST,TaskTypes::$CONCEPT,TaskTypes::$DEPLOYMENT,TaskTypes::$UIDESIGN,TaskTypes::$UICODING,TaskTypes::$FRONTEND,TaskTypes::$BACKEND];
		$mess=array("All"=>TaskTypes::$All,"Bug"=>TaskTypes::$BUG,"Test"=>TaskTypes::$TEST,"Concept"=>TaskTypes::$CONCEPT,"Deployment"=>TaskTypes::$DEPLOYMENT,"Uidesign"=>TaskTypes::$UIDESIGN,"Uicoding"=>TaskTypes::$UICODING,"Frontend"=>TaskTypes::$FRONTEND,"Backend"=>TaskTypes::$BACKEND );
		$mess["types"]=$types;
		return $mess;

	}

}