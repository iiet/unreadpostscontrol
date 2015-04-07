<?php

namespace iiet\unreadpostscontrol\event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class listener implements EventSubscriberInterface
{
	protected $db;
	protected $table_prefix;
	protected $user;
	
	public function __construct(\phpbb\db\driver\driver_interface $db, $table_prefix, \phpbb\user $user)
	{
		$this->db = $db;
		$this->table_prefix = $table_prefix;
		$this->user = $user;
	}
	
	static public function getSubscribedEvents()
	{
		return array(
			'core.search_modify_param_before' => 'inject_ex_fid_ary_to_unreadposts_search',
			'core.user_setup' => 'language',
		);
	}

	public function inject_ex_fid_ary_to_unreadposts_search($event)
	{
		if ($event['search_id'] === 'unreadposts')
		{
			$sql = 'SELECT u.unreadposts_include_forums
				FROM ' . $this->table_prefix . 'unreadposts_users u
				WHERE u.user_id = ' . $this->user->data['user_id'];
			$result = $this->db->sql_query($sql);
			$include_forums_mode = (bool) $this->db->sql_fetchfield('unreadposts_include_forums');

			$sel_fid_ary = array();

			$sql = 'SELECT f.forum_id
				FROM ' . $this->table_prefix . 'unreadposts_forums_select f
				WHERE f.user_id = ' . $this->user->data['user_id'];
			$result = $this->db->sql_query($sql);
			while ($fid = $this->db->sql_fetchfield('forum_id'))
			{
				$sel_fid_ary[] = $fid;
			}
			
			if ($include_forums_mode === false)
			{
				$ex_fid_ary = $sel_fid_ary;
			}
			else
			{
				$sql = 'SELECT f.forum_id
					FROM ' . FORUMS_TABLE . ' f';
				$result = $this->db->sql_query($sql);
				
				$fid_ary = array();
				
				while ($fid = $this->db->sql_fetchfield('forum_id'))
				{
					$fid_ary[] = $fid;
				}
				
				$ex_fid_ary = array_diff($fid_ary, $sel_fid_ary);
			}
			
			$event['ex_fid_ary'] = array_merge($event['ex_fid_ary'], $ex_fid_ary);
		}
	}

	public function language($event)
	{
		$event['lang_set_ext'] = array_merge($event['lang_set_ext'], array(
			array(
				'ext_name' => 'iiet/unreadpostscontrol',
				'lang_set' => 'common',
			)
		));
	}
}
