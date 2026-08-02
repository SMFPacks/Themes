<?php
// Version: 2.0.1; MessageIndex

function template_main()
{
	global $context, $settings, $options, $scripturl, $modSettings, $txt, $board_info;

	echo '
		<a id="top"></a>';
		
	echo'
		<h1 class="regular_text topic_title">
			', $board_info['name'], '
		</h1>
		<h2 class="topic_title_desc">
			', $context['description'], '
		</h2>
		<br class="clear" />';

	if (!empty($context['boards']) && (!empty($options['show_children']) || $context['start'] == 0))
	{
		echo '
		<fieldset class="messageindex_border">
			<div class="tborder" id="childboards">
				<div class="cat_bar">
					<h3 class="catbg">
						', $txt['parent_boards'], '
					</h3>
				</div>';
			
		$cont = 0;
		foreach ($context['boards'] as $board)
		{
			$cont++;
			echo'
				<div class="table_layout boardindex_board ', ($cont != count($context['boards']) ? ' boardindex_on_index_border_bottom' : ''),'">
						<div class="first_on_index">
							&nbsp;
						</div>
						<div class="image_on_index">
							<a href="', ($board['is_redirect'] || $context['user']['is_guest'] ? $board['href'] : $scripturl . '?action=unread;board=' . $board['id'] . '.0;children'), '" class="boardindex_icon boardindex_';
							
						if ($board['new'] || $board['children_new'])
							echo'new" title="' . $txt['new_posts'] . '">';
						elseif ($board['is_redirect'])
							echo'redirect" title="*">';
						else
							echo'nonew" title="' . $txt['old_posts'] . '">';						
				echo'
							</A>
						</div>
						<div class="title_on_index">
							<div class="floatright">
								<a href="', $scripturl, '?action=.xml;board=', $board['id'], ';type=rss" class="rss_on_boardindex">
								</a>
							</div>
							<h2 class="boardname_on_boardindex">
								<a href="', $board['href'], '" name="b', $board['id'], '">
								', $board['name'], '
								</a>
							</h2>';
								
				// Has it outstanding posts for approval?
				if ($board['can_approve_posts'] && ($board['unapproved_posts'] || $board['unapproved_topics']))
					echo '
						<a href="', $scripturl, '?action=moderate;area=postmod;sa=', ($board['unapproved_topics'] > 0 ? 'topics' : 'posts'), ';brd=', $board['id'], ';', $context['session_var'], '=', $context['session_id'], '" title="', sprintf($txt['unapproved_posts'], $board['unapproved_topics'], $board['unapproved_posts']), '" class="moderation_link">(!)</a>';
						
				echo'
							<span class="topic_title_desc">
								', $board['description'] , '
							</span>';
							
				// Show the "Moderators: ". Each has name, href, link, and id. (but we're gonna use link_moderators.)
				if (!empty($board['moderators']))
					echo '
						<br /><span class="moderators topic_title_desc">
							<strong>', count($board['moderators']) == 1 ? $txt['moderator'] : $txt['moderators'], '</strong>: ', implode(', ', $board['link_moderators']), '
						</span>';
						
				// Show the "Child Boards: ". (there's a link_children but we're going to bold the new ones...)
				if (!empty($board['children']))
				{
					// Sort the links into an array with new boards bold so it can be imploded.
					$children = array();
					/* Each child in each board's children has:
							id, name, description, new (is it new?), topics (#), posts (#), href, link, and last_post. */
					foreach ($board['children'] as $child)
					{
						if (!$child['is_redirect'])
							$child['link'] = '<a href="' . $child['href'] . '" ' . ($child['new'] ? 'class="new_posts" ' : '') . 'title="' . ($child['new'] ? $txt['new_posts'] : $txt['old_posts']) . ' (' . $txt['board_topics'] . ': ' . comma_format($child['topics']) . ', ' . $txt['posts'] . ': ' . comma_format($child['posts']) . ')">' . $child['name'] . ($child['new'] ? '</a> <a href="' . $scripturl . '?action=unread;board=' . $child['id'] . '" title="' . $txt['new_posts'] . ' (' . $txt['board_topics'] . ': ' . comma_format($child['topics']) . ', ' . $txt['posts'] . ': ' . comma_format($child['posts']) . ')"><img src="' . $settings['lang_images_url'] . '/new.gif" class="new_posts" alt="" />' : '') . '</a>';
						else
							$child['link'] = '<a href="' . $child['href'] . '" title="' . comma_format($child['posts']) . ' ' . $txt['redirects'] . '">' . $child['name'] . '</a>';

						// Has it posts awaiting approval?
						if ($child['can_approve_posts'] && ($child['unapproved_posts'] || $child['unapproved_topics']))
							$child['link'] .= ' <a href="' . $scripturl . '?action=moderate;area=postmod;sa=' . ($child['unapproved_topics'] > 0 ? 'topics' : 'posts') . ';brd=' . $child['id'] . ';' . $context['session_var'] . '=' . $context['session_id'] . '" title="' . sprintf($txt['unapproved_posts'], $child['unapproved_topics'], $child['unapproved_posts']) . '" class="moderation_link">(!)</a>';

						$children[] = $child['new'] ? '<strong>' . $child['link'] . '</strong>' : $child['link'];
					}
					
					echo '
					<br />
					<span class="topic_title_desc">
						<strong>', $txt['parent_boards'], '</strong>: ', implode(', ', $children), '
					</span>';
				}
						
			echo'
						</div>
						<div class="stats_on_boardindex" align="center">
							<strong>', comma_format($board['posts']), '</strong> ', $board['is_redirect'] ? $txt['redirects'] : $txt['posts'], ' <br />
							', $board['is_redirect'] ? '' : '<strong>' . comma_format($board['topics']) . '</strong> ' . $txt['board_topics'], '
						</div>';
						
					if (!empty($board['last_post']['id']))
						echo'
							<div class="last_post_on_boardindex">
								', $txt['last_post'], ' ', $txt['in'], ' ', $board['last_post']['link'], '<br />
								<span class="topic_time_on_index">
									', $txt['by'], ' <a href="', $board['last_post']['member']['href'], '">', $board['last_post']['member']['name'], '</a>
									', $txt['on'], ' ', date('j M Y', $board['last_post']['timestamp']), '
								</span>
							</div>';
				echo'
					</div>';
		}
		
		echo'
			</fieldset>';
	}

	// Create the button set...
	$normal_buttons = array(
		'new_topic' => array('test' => 'can_post_new', 'text' => 'new_topic', 'image' => 'new_topic.gif', 'lang' => true, 'url' => $scripturl . '?action=post;board=' . $context['current_board'] . '.0', 'active' => true),
		'post_poll' => array('test' => 'can_post_poll', 'text' => 'new_poll', 'image' => 'new_poll.gif', 'lang' => true, 'url' => $scripturl . '?action=post;board=' . $context['current_board'] . '.0;poll'),
		'notify' => array('test' => 'can_mark_notify', 'text' => $context['is_marked_notify'] ? 'unnotify' : 'notify', 'image' => ($context['is_marked_notify'] ? 'un' : ''). 'notify.gif', 'lang' => true, 'custom' => 'onclick="return confirm(\'' . ($context['is_marked_notify'] ? $txt['notification_disable_board'] : $txt['notification_enable_board']) . '\');"', 'url' => $scripturl . '?action=notifyboard;sa=' . ($context['is_marked_notify'] ? 'off' : 'on') . ';board=' . $context['current_board'] . '.' . $context['start'] . ';' . $context['session_var'] . '=' . $context['session_id']),
		'markread' => array('text' => 'mark_read_short', 'image' => 'markread.gif', 'lang' => true, 'url' => $scripturl . '?action=markasread;sa=board;board=' . $context['current_board'] . '.0;' . $context['session_var'] . '=' . $context['session_id']),
	);

	// They can only mark read if they are logged in and it's enabled!
	if (!$context['user']['is_logged'] || !$settings['show_mark_read'])
		unset($normal_buttons['markread']);
		
	// Allow adding new buttons easily.
	call_integration_hook('integrate_messageindex_buttons', array(&$normal_buttons));

	if (!$context['no_topic_listing'])
	{
		echo '
		<div class="pagesection">
			<div class="pagelinks align_left">', $txt['pages'], ': ', $context['page_index'], !empty($modSettings['topbottomEnable']) ? $context['menu_separator'] . '&nbsp;&nbsp;<a href="#bot"><strong>' . $txt['go_down'] . '</strong></a>' : '', '</div>
			', template_button_strip($normal_buttons, 'right'), '
		</div>';
		
		echo'
			<fieldset class="messageindex_border">';

		// If Quick Moderation is enabled start the form.
		if (!empty($context['can_quick_mod']) && $options['display_quick_mod'] == 1 && !empty($context['topics']))
			echo '
	<form action="', $scripturl, '?action=quickmod;board=', $context['current_board'], '.', $context['start'], '" method="post" accept-charset="', $context['character_set'], '" class="clear" name="quickModForm" id="quickModForm">';

		if (!empty($context['topics']))
		{
		echo '
			<div class="tborder topic_table cat_bar" id="messageindex">
				<div class="catbg full_width table_layout messageindex_header">
					<div class="title_on_messageindex">
						<a href="', $scripturl, '?board=', $context['current_board'], '.', $context['start'], ';sort=subject', $context['sort_by'] == 'subject' && $context['sort_direction'] == 'up' ? ';desc' : '', '" rel="nofollow">', $txt['subject'], $context['sort_by'] == 'subject' ? ' <img src="' . $settings['images_url'] . '/sort_' . $context['sort_direction'] . '.gif" alt="" />' : '', '</a> / <a href="', $scripturl, '?board=', $context['current_board'], '.', $context['start'], ';sort=starter', $context['sort_by'] == 'starter' && $context['sort_direction'] == 'up' ? ';desc' : '', '" rel="nofollow">', $txt['started_by'], $context['sort_by'] == 'starter' ? ' <img src="' . $settings['images_url'] . '/sort_' . $context['sort_direction'] . '.gif" alt="" />' : '', '</a>
					</div>
					<div class="stats_on_messageindex">
						<a href="', $scripturl, '?board=', $context['current_board'], '.', $context['start'], ';sort=replies', $context['sort_by'] == 'replies' && $context['sort_direction'] == 'up' ? ';desc' : '', '" rel="nofollow">', $txt['replies'], $context['sort_by'] == 'replies' ? ' <img src="' . $settings['images_url'] . '/sort_' . $context['sort_direction'] . '.gif" alt="" />' : '', '</a> / <a href="', $scripturl, '?board=', $context['current_board'], '.', $context['start'], ';sort=views', $context['sort_by'] == 'views' && $context['sort_direction'] == 'up' ? ';desc' : '', '" rel="nofollow">', $txt['views'], $context['sort_by'] == 'views' ? ' <img src="' . $settings['images_url'] . '/sort_' . $context['sort_direction'] . '.gif" alt="" />' : '', '</a>
					</div>';
					
	// Show a "select all" box for quick moderation?
	if (empty($context['can_quick_mod']))
		echo '
					<div class="last_post_on_index">
						<a href="', $scripturl, '?board=', $context['current_board'], '.', $context['start'], ';sort=last_post', $context['sort_by'] == 'last_post' && $context['sort_direction'] == 'up' ? ';desc' : '', '" rel="nofollow">', $txt['last_post'], $context['sort_by'] == 'last_post' ? ' <img src="' . $settings['images_url'] . '/sort_' . $context['sort_direction'] . '.gif" alt="" />' : '', '</a>
					</div>';
	else
		echo '
					<div class="last_post_on_index">
						<a href="', $scripturl, '?board=', $context['current_board'], '.', $context['start'], ';sort=last_post', $context['sort_by'] == 'last_post' && $context['sort_direction'] == 'up' ? ';desc' : '', '">', $txt['last_post'], $context['sort_by'] == 'last_post' ? ' <img src="' . $settings['images_url'] . '/sort_' . $context['sort_direction'] . '.gif" alt="" />' : '', '</a>
					</div>';
					
	 // Show a "select all" box for quick moderation?
	if (!empty($context['can_quick_mod']) && $options['display_quick_mod'] == 1)
		echo '
					<div class="floatleft" style="width: 24px;">
						<input type="checkbox" onclick="invertAll(this, this.form, \'topics[]\');" class="input_check" />
					</div>';

	// If it's on in "image" mode, don't show anything but the column.
	elseif (!empty($context['can_quick_mod']))
		echo '
					<div class="floatleft" style="width: 4px;">
						&nbsp;
					</div>';
					
	echo'
					</div>
				</div>';
	}
	else
		echo $txt['msg_alert_none'];

		if (!empty($settings['display_who_viewing']))
		{
			echo '
						<tr class="windowbg2 whos_viewing">
							<td colspan="', !empty($context['can_quick_mod']) ? '5' : '4', '" class="smalltext">';
			if ($settings['display_who_viewing'] == 1)
				echo count($context['view_members']), ' ', count($context['view_members']) == 1 ? $txt['who_member'] : $txt['members'];
			else
				echo empty($context['view_members_list']) ? '0 ' . $txt['members'] : implode(', ', $context['view_members_list']) . ((empty($context['view_num_hidden']) or $context['can_moderate_forum']) ? '' : ' (+ ' . $context['view_num_hidden'] . ' ' . $txt['hidden'] . ')');
			echo $txt['who_and'], $context['view_num_guests'], ' ', $context['view_num_guests'] == 1 ? $txt['guest'] : $txt['guests'], $txt['who_viewing_board'], '
							</td>
						</tr>';
		}

		// If this person can approve items and we have some awaiting approval tell them.
		if (!empty($context['unapproved_posts_message']))
		{
			echo '
						<tr class="windowbg2">
							<td colspan="', !empty($context['can_quick_mod']) ? '6' : '5', '">
								<span class="alert">!</span> ', $context['unapproved_posts_message'], '
							</td>
						</tr>';
		}
		
		$cont = 1;
		foreach ($context['topics'] as $topic)
		{
			echo'
				<div class="message_on_index table_layout', ($cont != count($context['topics']) ? ' message_on_index_border_bottom' : ''),'">
					<div class="image_on_index">
						', (!empty($topic['first_post']['member']['href']) ? '<a href="' . $topic['first_post']['member']['href'] . '">' : ''), '
							', $topic['first_post']['member']['avatar'], '
						', (!empty($topic['first_post']['member']['href']) ? '</a>' : ''), '
					</div>
					<div class="title_on_index" ', (!empty($topic['quick_mod']['modify']) ? 'id="topic_' . $topic['first_post']['id'] . '" onmouseout="mouse_on_div = 0;" onmouseover="mouse_on_div = 1;" ondblclick="modify_topic(\'' . $topic['id'] . '\', \'' . $topic['first_post']['id'] . '\');"' : ''), '>
						<div class="floatright">
							<img src="', $topic['first_post']['icon_url'], '" alt="" />
						</div>
						<h3 class="topic_title_messageindex">
							', $context['can_approve_posts'] && $topic['unapproved_posts'] ? '<span class="unapproved special_topic">' . $txt['unapproved'] . '</span>' : '', '
							', $topic['is_sticky'] ? '<span class="sticky special_topic">' . trim(str_replace($txt['topic'], '', $txt['sticky_topic'])) . '</span>' : '', ' ', $topic['is_locked'] ? '<span class="locked special_topic">' . trim(str_replace($txt['topic'], '', $txt['locked_topic'])) . '</span>' : '', ' <a href="', $topic['first_post']['href'], '">
								<span id="msg_' . $topic['first_post']['id'] . '">
									', $topic['first_post']['subject'], '
								</span>
							</a>
							', ( $topic['new'] && $context['user']['is_logged'] ? '	<a href="' . $topic['new_href'] . '" id="newicon' . $topic['first_post']['id'] . '"><img src="' . $settings['lang_images_url'] . '/new.gif" alt="' . $txt['new'] . '" /></a>' : ''),'
							', (!$context['can_approve_posts'] && !$topic['approved'] ? '&nbsp;<em>(' . $txt['awaiting_approval'] . ')</em>' : ''), '					<br />
							<span class="topic_title_desc">
								', $txt['started_by'], ' ', $topic['first_post']['member']['link'], '
								<small id="pages' . $topic['first_post']['id'] . '">', $topic['pages'], '</small>
							</span>
						</h3>
					</div>
					<div class="stats_on_index" align="center">
						', $topic['replies'], ' ', $txt['replies'], '
						<br />
						', $topic['views'], ' ', $txt['views'], '
					</div>';
					
			if (!empty($context['can_quick_mod']) && $options['display_quick_mod'] == 1)
				echo'	
					<div class="last_post_on_index last_post_on_index3">';
			elseif (!empty($context['can_quick_mod']))
				echo'
					<div class="last_post_on_index last_post_on_index4">';
			else
				echo'
					<div class="last_post_on_index">';
					
						echo $txt['by'], ' <span class="last_post_username">', $topic['last_post']['member']['link'], '</span> <a href="', $topic['last_post']['href'], '"><img src="', $settings['images_url'], '/icons/last_post.gif" alt="', $txt['last_post'], '" title="', $txt['last_post'], '" /></a><br />
						<a href="', $topic['last_post']['href'], '" title="', $txt['last_post'], '">
							<span class="topic_time_on_index">
								', date('j M Y', $topic['last_post']['timestamp']), '
							</span>
						</a>
					</div>';
					
			// Show the quick moderation options?
			if (!empty($context['can_quick_mod']))
			{
				echo '
					<div class="moderation', ($options['display_quick_mod'] != 1 ? '2' : ''),'">';
				if ($options['display_quick_mod'] == 1)
					echo '
						<input type="checkbox" name="topics[]" value="', $topic['id'], '" class="input_check" />';
				else
				{
					// Check permissions on each and show only the ones they are allowed to use.
					if ($topic['quick_mod']['remove'])
						echo '<a href="', $scripturl, '?action=quickmod;board=', $context['current_board'], '.', $context['start'], ';actions[', $topic['id'], ']=remove;', $context['session_var'], '=', $context['session_id'], '" onclick="return confirm(\'', $txt['quickmod_confirm'], '\');"><img src="', $settings['images_url'], '/icons/quick_remove.gif" width="16" alt="', $txt['remove_topic'], '" title="', $txt['remove_topic'], '" /></a>';

					if ($topic['quick_mod']['lock'])
						echo '<a href="', $scripturl, '?action=quickmod;board=', $context['current_board'], '.', $context['start'], ';actions[', $topic['id'], ']=lock;', $context['session_var'], '=', $context['session_id'], '" onclick="return confirm(\'', $txt['quickmod_confirm'], '\');"><img src="', $settings['images_url'], '/icons/quick_lock.gif" width="16" alt="', $txt['set_lock'], '" title="', $txt['set_lock'], '" /></a>';

					if ($topic['quick_mod']['lock'] || $topic['quick_mod']['remove'])
						echo '<br />';

					if ($topic['quick_mod']['sticky'])
						echo '<a href="', $scripturl, '?action=quickmod;board=', $context['current_board'], '.', $context['start'], ';actions[', $topic['id'], ']=sticky;', $context['session_var'], '=', $context['session_id'], '" onclick="return confirm(\'', $txt['quickmod_confirm'], '\');"><img src="', $settings['images_url'], '/icons/quick_sticky.gif" width="16" alt="', $txt['set_sticky'], '" title="', $txt['set_sticky'], '" /></a>';

					if ($topic['quick_mod']['move'])
						echo '<a href="', $scripturl, '?action=movetopic;board=', $context['current_board'], '.', $context['start'], ';topic=', $topic['id'], '.0"><img src="', $settings['images_url'], '/icons/quick_move.gif" width="16" alt="', $txt['move_topic'], '" title="', $txt['move_topic'], '" /></a>';
				}
				
				echo '
					</div>';
			}
					
			echo'
				</div>';
			$cont++;
		}
				
		echo '
			<div class="tborder" id="topic_icons">
				<div class="description">';
				
		if (!empty($context['can_quick_mod']) && $options['display_quick_mod'] == 1 && !empty($context['topics']))
		{	
			echo'
					<div class="floatleft">
							<select name="qaction"', $context['can_move'] ? ' onchange="' : '' ,' ', $context['can_move'] ? 'this.form.moveItTo.disabled = (this.options[this.selectedIndex].value != \'move\');' : '' ,' ', $context['can_move'] ? '"' : '' ,'>
									<option value="">--------</option>
									', $context['can_approve'] ? '<option value="approve">' . $txt['quick_mod_approve'] . '</option>' : '', '
									', $context['can_remove'] ? '<option value="remove">' . $txt['quick_mod_remove'] . '</option>' : '', '
									', $context['can_lock'] ? '<option value="lock">' . $txt['quick_mod_lock'] . '</option>' : '', '
									', $context['can_sticky'] ? '<option value="sticky">' . $txt['quick_mod_sticky'] . '</option>' : '', '
									', $context['can_move'] ? '<option value="move">' . $txt['quick_mod_move'] . ': </option>' : '', '
									', $context['can_merge'] ? '<option value="merge">' . $txt['quick_mod_merge'] . '</option>' : '', '
									', $context['can_restore'] ? '<option value="restore">' . $txt['quick_mod_restore'] . '</option>' : '', '
									', $context['user']['is_logged'] ? '<option value="markread">' . $txt['quick_mod_markread'] . '</option>' : '', '
								</select>';
								
					// Show a list of boards they can move the topic to.
					if ($context['can_move'])
					{
							echo '
										<select id="moveItTo" name="move_to" disabled="disabled">';
		
							foreach ($context['move_to_boards'] as $category)
							{
								echo '
											<optgroup label="', $category['name'], '">';
								foreach ($category['boards'] as $board)
										echo '
												<option value="', $board['id'], '"', $board['selected'] ? ' selected="selected"' : '', '>', $board['child_level'] > 0 ? str_repeat('==', $board['child_level'] - 1) . '=&gt;' : '', ' ', $board['name'], '</option>';
								echo '
											</optgroup>';
							}
							echo '
										</select>';
					}
			
			echo'
						<input type="submit" value="', $txt['quick_mod_go'], '" onclick="return document.forms.quickModForm.qaction.value != \'\' &amp;&amp; confirm(\'', $txt['quickmod_confirm'], '\');" class="button_submit" />
						<input type="hidden" name="' . $context['session_var'] . '" value="' . $context['session_id'] . '" />
					</form>
				</div>';
		}
					
		echo'
					<div class="align_right" id="message_index_jump_to">&nbsp;</div>
					<script type="text/javascript"><!-- // --><![CDATA[
						if (typeof(window.XMLHttpRequest) != "undefined")
							aJumpTo[aJumpTo.length] = new JumpTo({
								sContainerId: "message_index_jump_to",
								sJumpToTemplate: "<label class=\"smalltext\" for=\"%select_id%\">', $context['jump_to']['label'], ':<" + "/label> %dropdown_list%",
								iCurBoardId: ', $context['current_board'], ',
								iCurBoardChildLevel: ', $context['jump_to']['child_level'], ',
								sCurBoardName: "', $context['jump_to']['board_name'], '",
								sBoardChildLevelIndicator: "==",
								sBoardPrefix: "=> ",
								sCatSeparator: "-----------------------------",
								sCatPrefix: "",
								sGoButtonLabel: "', $txt['quick_mod_go'], '"
							});
					// ]]></script>
					<br class="clear" />
				</div>
			</div>
		</div>
		</fieldset>
		<a id="bot"></a>';
		
		echo '
	<div class="pagesection">
		', template_button_strip($normal_buttons, 'right'), '
		<div class="pagelinks">', $txt['pages'], ': ', $context['page_index'], !empty($modSettings['topbottomEnable']) ? $context['menu_separator'] . '&nbsp;&nbsp;<a href="#top"><strong>' . $txt['go_up'] . '</strong></a>' : '', '</div>
	</div>';
	}

	// Show breadcrumbs at the bottom too.
	theme_linktree();

	// Javascript for inline editing.
	echo '
<script type="text/javascript" src="' . $settings['default_theme_url'] . '/scripts/topic.js"></script>
<script type="text/javascript"><!-- // --><![CDATA[

	// Hide certain bits during topic edit.
	hide_prefixes.push("lockicon", "stickyicon", "pages", "newicon");

	// Use it to detect when we\'ve stopped editing.
	document.onclick = modify_topic_click;

	var mouse_on_div;
	function modify_topic_click()
	{
		if (in_edit_mode == 1 && mouse_on_div == 0)
			modify_topic_save("', $context['session_id'], '", "', $context['session_var'], '");
	}

	function modify_topic_keypress(oEvent)
	{
		if (typeof(oEvent.keyCode) != "undefined" && oEvent.keyCode == 13)
		{
			modify_topic_save("', $context['session_id'], '", "', $context['session_var'], '");
			if (typeof(oEvent.preventDefault) == "undefined")
				oEvent.returnValue = false;
			else
				oEvent.preventDefault();
		}
	}

	// For templating, shown when an inline edit is made.
	function modify_topic_show_edit(subject)
	{
		// Just template the subject.
		setInnerHTML(cur_subject_div, \'<input type="text" name="subject" value="\' + subject + \'" size="60" style="width: 95%;" maxlength="80" onkeypress="modify_topic_keypress(event)" class="input_text" /><input type="hidden" name="topic" value="\' + cur_topic_id + \'" /><input type="hidden" name="msg" value="\' + cur_msg_id.substr(4) + \'" />\');
	}

	// And the reverse for hiding it.
	function modify_topic_hide_edit(subject)
	{
		// Re-template the subject!
		setInnerHTML(cur_subject_div, \'<a href="', $scripturl, '?topic=\' + cur_topic_id + \'.0">\' + subject + \'<\' +\'/a>\');
	}

// ]]></script>';
}

?>