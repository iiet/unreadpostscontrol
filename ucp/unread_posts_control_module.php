<?php

namespace iiet\unreadpostscontrol\ucp;

class unread_posts_control_module
{
	function main($id, $mode)
	{
		global $user, $template, $db, $auth, $table_prefix;

		$submit = (isset($_POST['submit'])) ? true : false;
		
		$user->add_lang_ext('iiet/unreadpostscontrol', 'ucp');

		$error = array();
		
		$sql = 'SELECT u.unreadposts_include_forums
			FROM ' . $table_prefix . 'unreadposts_users u
			WHERE u.user_id = ' . $user->data['user_id'];
		$result = $db->sql_query($sql);
		$include_forums_mode = $db->sql_fetchfield('unreadposts_include_forums');
		$db->sql_freeresult($result);
		$prefs_existing = ($include_forums_mode !== false);
		$include_forums_mode = request_var('includeforumsmode', (bool) $include_forums_mode);

		$sel_fid_ary = request_var('fid', array(0));

		if (!sizeof($sel_fid_ary))
		{
			$sel_fid_ary = array();
			$sql = 'SELECT f.forum_id
				FROM ' . $table_prefix . 'unreadposts_forums_select f
				WHERE f.user_id = ' . $user->data['user_id'];
			$result = $db->sql_query($sql);
			while ($fid = $db->sql_fetchfield('forum_id'))
			{
				$sel_fid_ary[] = $fid;
			}
			$db->sql_freeresult($result);
		}

		if ($submit)
		{
			$sql_ary = array(
				'unreadposts_include_forums' => $include_forums_mode
			);
			
			if ($prefs_existing)
			{
				$sql = 'UPDATE ' . $table_prefix . 'unreadposts_users
					SET ' . $db->sql_build_array('UPDATE', $sql_ary) . '
					WHERE user_id = ' . $user->data['user_id'];
				$db->sql_query($sql);
			}
			else
			{
				$sql_ary['user_id'] = $user->data['user_id'];
				$sql = 'INSERT INTO ' . $table_prefix . 'unreadposts_users' .
				$db->sql_build_array('INSERT', $sql_ary);
				$db->sql_query($sql);
			}
			
			$db->sql_transaction('begin');
			$sql = 'DELETE FROM ' . $table_prefix . 'unreadposts_forums_select
				WHERE user_id = ' . $user->data['user_id'];
			$db->sql_query($sql);
			
			$sql_fid_ary = array();
			foreach ($sel_fid_ary as $fid)
			{
				$sql_fid_ary[] = array('user_id' => $user->data['user_id'], 'forum_id' => $fid);
			}
			$db->sql_multi_insert($table_prefix . 'unreadposts_forums_select', $sql_fid_ary);
			$db->sql_transaction('commit');

			meta_refresh(3, $this->u_action);
			$message = $user->lang['PREFERENCES_UPDATED'] . '<br /><br />' . sprintf($user->lang['RETURN_UCP'], '<a href="' . $this->u_action . '">', '</a>');
			trigger_error($message);
		}
				
		/* ---------- Copied from search.php - BEGIN ---------- */
		
		// Search forum
		$s_forums = '';
		$sql = 'SELECT f.forum_id, f.forum_name, f.parent_id, f.forum_type, f.left_id, f.right_id, f.forum_password, f.enable_indexing, fa.user_id
			FROM ' . FORUMS_TABLE . ' f
			LEFT JOIN ' . FORUMS_ACCESS_TABLE . " fa ON (fa.forum_id = f.forum_id
				AND fa.session_id = '" . $db->sql_escape($user->session_id) . "')
			ORDER BY f.left_id ASC";
		$result = $db->sql_query($sql);

		$right = $cat_right = $padding_inc = 0;
		$padding = $forum_list = $holding = '';
		$pad_store = array('0' => '');

		while ($row = $db->sql_fetchrow($result))
		{
			if ($row['forum_type'] == FORUM_CAT && ($row['left_id'] + 1 == $row['right_id']))
			{
				// Non-postable forum with no subforums, don't display
				continue;
			}

			if ($row['forum_type'] == FORUM_POST && ($row['left_id'] + 1 == $row['right_id']) && !$row['enable_indexing'])
			{
				// Postable forum with no subforums and indexing disabled, don't display
				continue;
			}

			if ($row['forum_type'] == FORUM_LINK || ($row['forum_password'] && !$row['user_id']))
			{
				// if this forum is a link or password protected (user has not entered the password yet) then skip to the next branch
				continue;
			}

			if ($row['left_id'] < $right)
			{
				$padding .= '&nbsp; &nbsp;';
				$pad_store[$row['parent_id']] = $padding;
			}
			else if ($row['left_id'] > $right + 1)
			{
				if (isset($pad_store[$row['parent_id']]))
				{
					$padding = $pad_store[$row['parent_id']];
				}
				else
				{
					continue;
				}
			}

			$right = $row['right_id'];

			if ($auth->acl_gets('!f_search', '!f_list', $row['forum_id']))
			{
				// if the user does not have permissions to search or see this forum skip only this forum/category
				continue;
			}

			$selected = (in_array($row['forum_id'], $sel_fid_ary)) ? ' selected="selected"' : '';

			if ($row['left_id'] > $cat_right)
			{
				// make sure we don't forget anything
				$s_forums .= $holding;
				$holding = '';
			}

			if ($row['right_id'] - $row['left_id'] > 1)
			{
				$cat_right = max($cat_right, $row['right_id']);

				$holding .= '<option value="' . $row['forum_id'] . '"' . $selected . '>' . $padding . $row['forum_name'] . '</option>';
			}
			else
			{
				$s_forums .= $holding . '<option value="' . $row['forum_id'] . '"' . $selected . '>' . $padding . $row['forum_name'] . '</option>';
				$holding = '';
			}
		}

		if ($holding)
		{
			$s_forums .= $holding;
		}

		$db->sql_freeresult($result);
		unset($pad_store);

		if (!$s_forums)
		{
			trigger_error('NO_SEARCH');
		}

		/* ---------- Copied from search.php - END ---------- */

		// Replace "error" strings with their real, localised form
		$error = array_map(array($user, 'lang'), $error);
		
		$template->assign_vars(array(
			'L_TITLE' => $user->lang['UCP_MAIN_' . strtoupper($mode)],
			'S_FORUM_OPTIONS' => $s_forums,
			'S_INCLUDE_FORUMS_MODE' => $include_forums_mode,
			'ERROR' => (sizeof($error)) ? implode('<br />', $error) : '',
		));
		
		$this->tpl_name = 'ucp_main_' . $mode;
		$this->page_title = 'UCP_MAIN_' . strtoupper($mode);
	}
}
