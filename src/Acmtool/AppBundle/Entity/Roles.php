<?php
namespace Acmtool\AppBundle\Entity;

class Roles
{
	public static function Teamlead()
	{
		return array("Text"=>"Team Leader","role"=>"Teamlead");
	}
	public static function Developer()
	{
		return array("Text"=>"Developer","role"=>"Developer");
	}
	public static function Tester()
	{
		return array("Text"=>"Tester","role"=>"Tester");
	}
	public static function Designer()
	{
		return array("Text"=>"Designer","role"=>"Designer");
	}
	public static function SysAdmin()
	{
		return array("Text"=>"System Admin","role"=>"Sysadmin");
	}
	public static function KeyAccount()
	{
		return array("Text"=>"Key Account","role"=>"keyaccount");
	}
}