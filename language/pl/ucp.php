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
	'UNREAD_POSTS_CONTROL'			=> 'W tym miejscu możesz zarządzać tym jakie posty będą widoczne na stronie „Nieprzeczytane posty”. Możesz wybrać wyświetlanie wiadomości jedynie z zaznaczonych forów lub wyświetlanie ze wszystkich oprócz zaznaczonych poniżej.',
	'UNREAD_POSTS_CONTROL_MODE'		=> 'Wyświetlaj nieprzeczytane posty',
	'UNREAD_POSTS_CONTROL_EXCLUDE_MODE'	=> 'Ze wszystkich forów za wyjątkiem wybranych',
	'UNREAD_POSTS_CONTROL_INCLUDE_MODE'	=> 'Wyłącznie z wybranych forów',
	'UNREAD_POSTS_CONTROL_FORUMS'		=> 'Wybrane fora',
	'UNREAD_POSTS_CONTROL_FORUMS_EXPLAIN'	=> 'Aby zaznaczyć wiele forów, użyj klawisza Control lub Command.',
));
