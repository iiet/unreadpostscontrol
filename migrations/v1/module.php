<?php

namespace iiet\unreadpostscontrol\migrations\v1;

class module extends \phpbb\db\migration\migration
{
	public function update_schema()
	{
		return array(
			'add_tables' => array(
				$this->table_prefix . 'unreadposts_users' => array(
					'COLUMNS' => array(
						'user_id' => array('UINT', 0),
						'unreadposts_include_forums' => array('BOOL', 0),
					),
					'PRIMARY_KEY' => 'user_id',
				),
				$this->table_prefix . 'unreadposts_forums_select' => array(
					'COLUMNS' => array(
						'user_id' => array('UINT', 0),
						'forum_id' => array('UINT', 0),
					),
					'PRIMARY_KEY' => array('user_id', 'forum_id'),
				),
			),
		);
	}

	public function revert_schema()
	{
		return array(
			'drop_tables' => array(
				$this->table_prefix . 'unreadposts_users',
				$this->table_prefix . 'unreadposts_forums_select',
			),
		);
	}
	
	public function update_data()
	{
		return array(
			array('module.add', array(
				'ucp', 'UCP_MAIN', array(
					'module_basename' => '\iiet\unreadpostscontrol\ucp\unread_posts_control_module',
					'modes' => array('unread_posts_control')
				)
			)),
			array('module.remove', array(
				'ucp', 0, 'UCP_MAIN_IGNORED'
			)),
		);
	}
}
