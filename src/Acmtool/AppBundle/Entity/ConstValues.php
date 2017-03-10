<?php
namespace Acmtool\AppBundle\Entity;

abstract class ConstValues
{
	const INVALIDREQUEST="invalid_request";
	const ADMINCREATED="Admin created successfully";
	const ADMINUPDATED="Admin updated successfully";
	const REASONWRONG="Wrong password/Username";
	const REASONMISSING="Missing password/Username";
	const TLCREATED="TeamLeader created successfully";
	const TLUPDATED="TeamLeader updated successfully";
	const TLDELETED="TeamLeader deleted successfully";
	const COUNT=50;
	const DEVCREATED="Developer created successfully";
	const DEVUPDATED="Developer updated successfully";
	const DEVDELETED="Developer deleted successfully";
	const TSTCREATED="Tester created successfully";
	const TSTUPDATED="Tester updated successfully";
	const TSTDELETED="Tester deleted successfully";
	const PERIOD=3600;
	const DESCREATED="Designer created successfully";
	const DESUPDATED="Designer updated successfully";
	const DESDELETED="Designer deleted successfully";
	const TIMEZONE="Europe/Berlin";
	const SYSACREATED="SystemAdmin created successfully";
	const SYSAUPDATED="SystemAdmin updated successfully";
	const SYSADELETED="SystemAdmin deleted successfully";
	const KEYACREATED="KeyAccount created successfully";
	const KEYAUPDATED="KeyAccount updated successfully";
	const KEYADELETED="KeyAccount deleted successfully";
	const CUSCREATED="Customer created successfully";
	const CUSUPDATED="Customer updated successfully";
	const CUSDELETED="Customer deleted successfully";
	const INVALIDDATE="The Date format is invalid dateformat must be yyyy-mm-dd";
	const PROJECTCREATED="The project was created successfully";
	const PROJECTUPDATED="The project was updated successfully";
	const PROJECTDELETED="The project was deleted successfully";
	const TEAMLEADERASSIGNED="TeamLeader assigned successfully";
	const MEMBERADDED="Members added successfully";
	const MEMBERDELETED="Members deleted successfully";
	const CONFIGADDED="Project config addedsuccessfully";
	const CONFIGUPDATED="Project config updated successfully";
	const CONFIGDELETED="Project config deleted successfully";
	const INVOICELIMITDAYS = 10;
	const DEFAULTCURRENCY = "EUR";
	const DEFAULTTAX = 19;
	const DEFAULTBILLEDFROM = "De";
	const GERMANYCODE ="De";
	const TURKEYCODE = "Tr";

}
