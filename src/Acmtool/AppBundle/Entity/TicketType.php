<?php
namespace Acmtool\AppBundle\Entity;

class TicketType
{
	public static function All()
	{
		return array("text"=>"All","type"=>"All");
	}
	public static function Feature()
	{
		return array("text"=>"Feature","type"=>"Feature");
	}
	public static function Bug()
	{
		return array("text"=>"Bug","type"=>"Bug");
	}
	public static function Concept()
	{
		return array("text"=>"Concept","type"=>"Concept");
	}
	public static function Design()
	{
		return array("text"=>"Design","type"=>"Design");
	}
}