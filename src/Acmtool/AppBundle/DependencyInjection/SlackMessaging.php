<?php
namespace Acmtool\AppBundle\DependencyInjection;
class SlackMessaging implements IMessaging
{
	const clienttoken="xoxp-9690007030-13891980720-41230371793-40f72d292f";
	const admintoken="xoxp-9690007030-9689729651-41046604725-f3b338f063";
	const Baseurl="https://slack.com/api/";
	const channelInfo="channels.info?";
	const userInfo="users.info?";
	const channelthistory="channels.history?";
	const sendmessage="chat.postMessage?";
	const updatemessage="chat.update?";
	const markRead="channels.mark?";
	const deleteMessage="chat.delete?";
	const createchannel="channels.create?";
	const invitechannel="channels.invite?";
	const channellist="channels.list?";
	public function getChannelId($name)
	{
		$mess=json_decode(file_get_contents(self::Baseurl.self::channellist."token=".self::admintoken));
		if($mess->{"ok"})
		{
			foreach ($mess->{"channels"} as $key) {
				if($key->{"name"}==$name)
					return $key->{"id"};
			}
		}
	}
	public function sendMessage($text,$group,$client)
	{
		$text=urlencode($text);
		$mess=json_decode(file_get_contents(self::Baseurl.self::sendmessage."token=".self::clienttoken."&channel=".$group."&username=".$client."&text=".$text));
		if($mess->{"ok"})
		{
			return $mess;
		}
		else
		{
			return json_decode('{"ok":false,"error":"'.$mess->{"error"}.'"}');
		}
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
	public function getNewMessagesNumber($group)
	{
		$data=array();
		$history=json_decode(file_get_contents(self::Baseurl.self::channelthistory."token=".self::clienttoken."&channel=".$group."&count=1"."&unreads=1"));
		if($history->{"ok"})
		{
			$data["result"]=true;
			$data["undread_count"]=$history->{"unread_count_display"};
		}
		else
		{
			$data["result"]=false;
		}
		return $data;
	}
	public function createGroupForProject($name)
	{
		$data=array();
		$mess=json_decode(file_get_contents(self::Baseurl.self::createchannel."token=".self::admintoken."&name=".$name));
		if($mess->{"ok"})
		{
			$channel_id=$mess->{"channel"}->{"id"};
			$mess2=json_decode(file_get_contents(self::Baseurl.self::invitechannel."token=".self::admintoken."&channel=".$channel_id."&user=U0DS7UUM6"));
			if($mess2->{"ok"})
			{
				$data["result"]=true;
				$data["id"]=$channel_id;
			}
			else
			{
				$data["result"]=false;
				$data["reason"]=$mess->{"error"};
			}

		}
		else
		{
			$data["result"]=false;
			$data["reason"]=$mess->{"error"};
		}
		return $data;
	}
	
}