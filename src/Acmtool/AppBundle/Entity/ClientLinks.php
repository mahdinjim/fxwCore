<?php
namespace Acmtool\AppBundle\Entity;
class ClientLinks
{
	static  $BASELINK="http://app.fxw.io/#/";
	static $PROJECT="pdetails/";
	static $CLIENTPROJECTS="clientprojects/";
	static $TICKETDETAILS="stories/";
	public static function getProjectDetailLink()
	{
		return ClientLinks::$BASELINK.ClientLinks::$PROJECT;
	}
	public static function getClientProjectLink()
	{
		return ClientLinks::$BASELINK.ClientLinks::$CLIENTPROJECTS;
	}
	public static function getTicketDetailLink($project_id,$ticket_id)
	{
		return ClientLinks::$BASELINK.ClientLinks::$TICKETDETAILS.$project_id."/".$ticket_id;
	}
}