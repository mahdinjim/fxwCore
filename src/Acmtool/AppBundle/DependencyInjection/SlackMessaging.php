<?php
namespace Acmtool\AppBundle\DependencyInjection;
class SlackMessaging implements IMessaging
{
	const clienttoken="xoxp-9690007030-13891980720-13935717936-ad972deac5";
	const admintoken="";
	const Baseurl="https://slack.com/api/";
	const channelInfo="channels.info?";
	const userInfo="users.info?";
	const channelthistory="channels.history?";
	const sendmessage="chat.postMessage?";
	const updatemessage="chat.update?";
	const markRead="channels.mark?";
	const deleteMessage="chat.delete?";
	public function sendMessage($text,$group,$client)
	{
		$text=urlencode($text);
		$mess=json_decode(file_get_contents(self::Baseurl.self::sendmessage."token=".self::clienttoken."&channel=".$group."&username=".$client."&text=".$text));
		return $mess->{"ok"};
	}
	public function deleteMessage($mess,$group)
	{
		$mess=json_decode(file_get_contents(self::Baseurl.self::deleteMessage."token=".self::clienttoken."&channel=".$group."&ts=".$mess));
		return $mess->{"ok"};
	}
	public function getNewMessages($group,$last)
	{
		$data=array();
		$history=json_decode(file_get_contents(self::Baseurl.self::channelthistory."token=".self::clienttoken."&channel=".$group."&oldest=".$last."&unreads=1"));
		if($history->{"ok"})
		{
			$data["messages"]=$history->{"messages"};
			$data["hasmore"]=$history->{"has_more"};
			$data["result"]=true;
			$data["undread_count"]=$history->{"unread_count_display"};
		}
		else
		{
			$data["result"]=false;
		}
		return $data;
	}
	public function getAllmess($number,$group,$start)
	{
		$data=array();
		if($start==0)
			$history=json_decode(file_get_contents(self::Baseurl.self::channelthistory."token=".self::clienttoken."&channel=".$group."&count=".$number."&unreads=1"));
		else 
			$history=json_decode(file_get_contents(self::Baseurl.self::channelthistory."token=".self::clienttoken."&channel=".$group."&count=".$number."&unreads=1&latest=".$start));
		if($history->{"ok"})
		{
			$data["messages"]=$history->{"messages"};
			$data["hasmore"]=$history->{"has_more"};
			$data["result"]=true;
			$data["undread_count"]=$history->{"unread_count_display"};
		}
		else
		{
			$data["result"]=false;
		}
		return $data;
	}
	public function editMessage($mess,$text,$group)
	{
		$text=urlencode($text);
		echo self::Baseurl.self::updatemessage."token=".self::clienttoken."&channel=".$group."&ts=".$mess."&text=".$text;
		die();
		$mess=json_decode(file_get_contents(self::Baseurl.self::updatemessage."token=".self::clienttoken."&channel=".$group."&ts=".$mess."&text=".$text));
		return $mess->{"ok"};
	}
	public function markAsRead($mess,$group)
	{
		$mess=json_decode(file_get_contents(self::Baseurl.self::markRead."token=".self::clienttoken."&channel=".$group."&ts=".$mess));
		return $mess->{"ok"};
	}
	public function getGroupInfo($group)
	{
		$data=array();
		$channeldata=json_decode(file_get_contents(self::Baseurl.self::channelInfo."token=".self::clienttoken."&channel=".$group));
		if($channeldata->{"ok"})
		{
			$data["id"]=$channeldata->{"channel"}->{"id"};
			$data["name"]=$channeldata->{"channel"}->{"name"};
			$users=array();
			$i=0;
			foreach ($channeldata->{"channel"}->{"members"} as $key) {
				$userdata=json_decode(file_get_contents(self::Baseurl.self::userInfo."token=".self::clienttoken."&user=".$key));
				if($userdata->{"ok"})
				{
					$users[$i]=$userdata->{"user"};
				}
				$i++;	
			}
			$data["members"]=$users;
			$data["result"]=true;
		}
		else
		{
			$data["result"]=false;
		}
		return $data;
	}
	public function createGroupForProject()
	{

	}
}