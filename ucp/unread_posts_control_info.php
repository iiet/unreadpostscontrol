<?php

namespace iiet\unreadpostscontrol\ucp;

class unread_posts_control_info
{
	function module()
	{
		return array(
			'filename'	=> '\iiet\unreadpostscontrol\ucp\unread_posts_control_module',
			'title'		=> 'UCP_MAIN',
			'version'	=> '0.0.1',
			'modes'		=> array(
				'unread_posts_control' => array('title' => 'UCP_MAIN_UNREAD_POSTS_CONTROL', 'auth' => 'ext_iiet/unreadpostscontrol', 'cat' => array('UCP_MAIN'))
			)
		);
	}
}
