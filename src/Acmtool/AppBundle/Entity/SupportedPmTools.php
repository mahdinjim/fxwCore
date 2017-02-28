<?php
namespace Acmtool\AppBundle\Entity;
class SupportedPmTools
{
	public static $JIRA="Jira";
	public static $TEST="Test";
	public static function getAll()
	{
		return [SupportedPmTools::$JIRA,SupportedPmTools::$TEST];
	}
}