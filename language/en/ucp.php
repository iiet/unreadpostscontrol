<?php

if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

$lang = array_merge($lang, array(
	'UNREAD_POSTS_CONTROL'			=> 'Here you can decide which posts would be displayed in unread posts view. You can choose to show posts from all forums excluding selected or to show posts only from selected forums.',
	'UNREAD_POSTS_CONTROL_MODE'		=> 'Show unread posts',
	'UNREAD_POSTS_CONTROL_EXCLUDE_MODE'	=> 'From all forums excluding selected',
	'UNREAD_POSTS_CONTROL_INCLUDE_MODE'	=> 'Only from selected forums',
	'UNREAD_POSTS_CONTROL_FORUMS'		=> 'Selected forums',
	'UNREAD_POSTS_CONTROL_FORUMS_EXPLAIN'	=> 'To select multiple forums, use Control or Command key.',
));
