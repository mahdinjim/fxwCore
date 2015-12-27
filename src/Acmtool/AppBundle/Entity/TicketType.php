<?php
namespace Acmtool\AppBundle\Entity;

abstract class TicketType
{
	const ALL="all";
	const FEATURE="feature";
	const BUG="bug";
	const CONCEPT="concept";
	const DESIGN="design";
}