<?php
namespace Acmtool\AppBundle\Entity;

abstract class LogAction
{
	const PROJECTCREATED="project created";
	const TICKETCREATED="added";
	const TICKETSTARTED="started";
	const TICKETESTIMATED="estimated";
	const TICKETACESTIMATION="est.accepted";
	const TICKETREJSTIMATION="re-edit";
	const TICKETINPROD="in-production";
	const TICKETINQA="in-qa";
	const TICKETDELIVRED="delivred";
	const TICKETACCEPTED="accepted";
	const STORYCREATED="s.created";
	const STORYESTIMATED="s.estimated";
	const STORYSTARTED="s.started";
	const STORYFINISHED="s.finished";
	const STORYREALTIME="s.realtime";
	const BUGCREATED="b.created";
	const BUGACCEPTED="b.accepted";
	const BUGREJECTED="b.moved";
	const BUGDELIVRED="b.delivred";
	const STORYASSIGNED="s.assigned";
}