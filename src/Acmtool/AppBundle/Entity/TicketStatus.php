<?php
namespace Acmtool\AppBundle\Entity;

abstract class TicketStatus
{
	const ALL="all";
	const DRAFT="draft";
	const ESTIMATION="estimation";
	const GOPRODUCTION="goproduction";
	const WAITING="waiting";
	const PRODUCTION="production";
	const TESTING="testing";
	const ACCEPT="accept";
	const REJECT="reject";
	const DONE="done";
}