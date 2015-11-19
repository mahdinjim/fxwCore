<?php
namespace Acmtool\AppBundle\DependencyInjection;
use Acmtool\AppBundle\DependencyInjection\SlackMessaging;
class MessagingService
{
	public function CreateChatProvider()
	{
		return new SlackMessaging();
	}
}