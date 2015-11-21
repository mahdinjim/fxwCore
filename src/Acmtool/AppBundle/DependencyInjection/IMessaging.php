<?php
namespace Acmtool\AppBundle\DependencyInjection;
interface IMessaging
{
	
	public function sendMessage($text,$group,$client);
	public function deleteMessage($mess,$group);
	public function getAllmess($number,$group,$start);
	public function editMessage($mess,$text,$group);
	public function getGroupInfo($group);
	public function createGroupForProject();
	public function markAsRead($mess,$group);
	public function getNewMessages($group,$last);
}