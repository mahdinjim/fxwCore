<?php
namespace Acmtool\AppBundle\Entity;

abstract class ProjectStates
{
	const ALL="all";
	const ACTIVE="active";
	const TLASSIGN="tlassign";
	const TEAMASSIGN="teamassign";
	const PENDING="pending";
	const CLOSED="closed";
	const FINISH="finish";
	const ARCHIVED="archived";
}