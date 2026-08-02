<?php
// Version: 2.0.1; BoardIndex

function template_main()
{
	global $context, $settings, $options, $txt, $scripturl, $modSettings, $smcFunc, $mbname;
	
	// Show the news fader?  (assuming there are things to show...)
	if ($settings['show_newsfader'] && !empty($context['fader_news_lines']))
	{
		echo '
	<div id="newsfader">
		<div class="cat_bar">
			<h3 class="catbg">
				<img id="newsupshrink" src="', $settings['images_url'], '/collapse.gif" alt="*" title="', $txt['upshrink_description'], '" align="bottom" style="display: none;" />
				', $txt['news'], '
			</h3>
		</div>
		<ul class="reset" id="smfFadeScroller"', empty($options['collapse_news_fader']) ? '' : ' style="display: none;"', '>';

			foreach ($context['news_lines'] as $news)
				echo '
			<li>', $news, '</li>';

	echo '
		</ul>
	</div>
	<script type="text/javascript" src="', $settings['default_theme_url'], '/scripts/fader.js"></script>
	<script type="text/javascript"><!-- // --><![CDATA[

		// Create a news fader object.
		var oNewsFader = new smf_NewsFader({
			sSelf: \'oNewsFader\',
			sFaderControlId: \'smfFadeScroller\',
			sItemTemplate: ', JavaScriptEscape('<strong>%1$s</strong>'), ',
			iFadeDelay: ', empty($settings['newsfader_time']) ? 5000 : $settings['newsfader_time'], '
		});

		// Create the news fader toggle.
		var smfNewsFadeToggle = new smc_Toggle({
			bToggleEnabled: true,
			bCurrentlyCollapsed: ', empty($options['collapse_news_fader']) ? 'false' : 'true', ',
			aSwappableContainers: [
				\'smfFadeScroller\'
			],
			aSwapImages: [
				{
					sId: \'newsupshrink\',
					srcExpanded: smf_images_url + \'/collapse.gif\',
					altExpanded: ', JavaScriptEscape($txt['upshrink_description']), ',
					srcCollapsed: smf_images_url + \'/expand.gif\',
					altCollapsed: ', JavaScriptEscape($txt['upshrink_description']), '
				}
			],
			oThemeOptions: {
				bUseThemeSettings: ', $context['user']['is_guest'] ? 'false' : 'true', ',
				sOptionName: \'collapse_news_fader\',
				sSessionVar: ', JavaScriptEscape($context['session_var']), ',
				sSessionId: ', JavaScriptEscape($context['session_id']), '
			},
			oCookieOptions: {
				bUseCookie: ', $context['user']['is_guest'] ? 'true' : 'false', ',
				sCookieName: \'newsupshrink\'
			}
		});
	// ]]></script>';
	}
	
	echo'
		<div>
			<div class="floatleft">
				<h1 class="regular_text board_title">
					', $mbname, '
				</h1>
			</div>';

    if ($settings['show_mark_read'] && !empty($context['categories']) && $context['user']['is_logged'])
		echo '
		<div class="floatright">
			<a class="button_tw" href="'.$scripturl.'?action=markasread;sa=all;', $context['session_var'], '=', $context['session_id'], '">
				', $txt['mark_as_read'], '
			</a>
		</div>';
	
	echo'
		</div>
		<br class="clear" /><br />
		<fieldset id="boardindex_table" class="messageindex_border">
			<ol class="null_list">';

	/* Each category in categories is made up of:
	id, href, link, name, is_collapsed (is it collapsed?), can_collapse (is it okay if it is?),
	new (is it new?), collapse_href (href to collapse/expand), collapse_image (up/down image),
	and boards. (see below.) */
	foreach ($context['categories'] as $category)
	{
		// If theres no parent boards we can see, avoid showing an empty category (unless its collapsed)
		if (empty($category['boards']) && !$category['is_collapsed'])
			continue;
			
		// Category
		echo'
			<li>
				<div class="cat_bar">
				<div class="catbg full_width table_layout', $category['is_collapsed'] ? ' collapsed_board' : '','">
					<div class="left_bar subject_on_index">
						<h2>
							<a name="c',$category['id'],'"></a>
							<a href="', $scripturl, '?action=forum#c', $category['id'], '" class="helvet">
								', $category['name'], '
							</a>
						</h2>
					</div>
					<div class="right_bar">';
	
			if (!$context['user']['is_guest'] && !empty($category['show_unread']))
				echo '
							<a class="unreadlink" href="', $scripturl, '?action=unread;c=', $category['id'], '">', $txt['view_unread_category'], '</a>';
							
			// If this category even can collapse, show a link to collapse it.
			if ($category['can_collapse'])
				echo '
							<a class="collapse" href="', $category['collapse_href'], '">', $category['collapse_image'], '</a>';
	
		echo'
					</div>
				</div>
			</div>
			<ol class="null_list">';
			
		// Assuming the category hasn't been collapsed...
		if (!$category['is_collapsed'])
		{
			$cont = 0;
			foreach ($category['boards'] as $board)
			{
				$cont++;
				
				echo'
					<li>
					<div class="table_layout boardindex_board ', ($cont != count($category['boards']) ? ' boardindex_on_index_border_bottom' : ''),'">
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
					</div>
				</li>';
			}
			
			echo'
				</ol>';
		}
		
		echo'
				</li>';
	}
	echo '
		</ol>
	</fieldset>';

	template_info_center();
}

function template_info_center()
{
	global $context, $settings, $options, $txt, $scripturl, $modSettings;
	
	echo'<div style="margin-bottom: 35px;"><span></span></div>';
	
	// This is the "Recent Posts" bar.
	if (!empty($settings['number_recent_posts']))
	{
		echo'<span class="info_center_text"><img src="'.$settings['images_url'].'/page_add.png" alt="" /> <a href="'.$scripturl.'?action=recent">', $txt['recent_posts'], '</a></span><br /><hr />';
		
		// Only show one post.
		if ($settings['number_recent_posts'] == 1)
		{
			// latest_post has link, href, time, subject, short_subject (shortened with...), and topic. (its id.)
			echo '
				<strong><a href="', $scripturl, '?action=recent">', $txt['recent_posts'], '</a></strong>
				<p id="infocenter_onepost" class="middletext">
					', $txt['recent_view'], ' &quot;', $context['latest_post']['link'], '&quot; ', $txt['recent_updated'], ' (', $context['latest_post']['time'], ')<br />
				</p>';
		}
		// Show lots of posts.
		elseif (!empty($context['latest_posts']))
		{
			echo '
				<dl id="ic_recentposts" class="middletext">';
	
			/* Each post in latest_posts has:
					board (with an id, name, and link.), topic (the topic's id.), poster (with id, name, and link.),
					subject, short_subject (shortened with...), time, link, and href. */
			foreach ($context['latest_posts'] as $post)
				echo '
					<dt><strong>', $post['link'], '</strong> ', $txt['by'], ' ', $post['poster']['link'], ' (', $post['board']['link'], ')</dt>
					<dd>', $post['time'], '</dd>';
			echo '
				</dl>';
		}
	}
	
	// Show statistical style information...
	if ($settings['show_stats_index'])
	{
		echo'<span class="info_center_text"><img src="'.$settings['images_url'].'/chart_bar.png" alt="" /> <a href="', $scripturl, '?action=stats">', $txt['forum_stats'], '</a></span><br /><hr />
			<p>
				', $context['common_stats']['total_posts'], ' ', $txt['posts_made'], ' ', $txt['in'], ' ', $context['common_stats']['total_topics'], ' ', $txt['topics'], ' ', $txt['by'], ' ', $context['common_stats']['total_members'], ' ', $txt['members'], '. ', !empty($settings['show_latest_member']) ? $txt['latest_member'] . ': <strong> ' . $context['common_stats']['latest_member']['link'] . '</strong>' : '', '
			</p>';
	}
	
	// "Users online" - in order of activity.
		echo'<span class="info_center_text"><img src="'.$settings['images_url'].'/group_link.png"" alt="" /> <a href="'.$scripturl.'?action=who">', $txt['online_users'], '</a></span><br /><hr />
			<p class="inline stats">
				', $context['show_who'] ? '<a href="' . $scripturl . '?action=who">' : '', comma_format($context['num_guests']), ' ', $context['num_guests'] == 1 ? $txt['guest'] : $txt['guests'], ', ' . comma_format($context['num_users_online']), ' ', $context['num_users_online'] == 1 ? $txt['user'] : $txt['users'];

	// Handle hidden users and buddies.
	$bracketList = array();
	if ($context['show_buddies'])
		$bracketList[] = comma_format($context['num_buddies']) . ' ' . ($context['num_buddies'] == 1 ? $txt['buddy'] : $txt['buddies']);
	if (!empty($context['num_spiders']))
		$bracketList[] = comma_format($context['num_spiders']) . ' ' . ($context['num_spiders'] == 1 ? $txt['spider'] : $txt['spiders']);
	if (!empty($context['num_users_hidden']))
		$bracketList[] = comma_format($context['num_users_hidden']) . ' ' . $txt['hidden'];

	if (!empty($bracketList))
		echo ' (' . implode(', ', $bracketList) . ')';

	echo $context['show_who'] ? '</a>' : '', '
			</p>
			<p class="inline smalltext">';

	// Assuming there ARE users online... each user in users_online has an id, username, name, group, href, and link.
	if (!empty($context['users_online']))
	{
		echo '
				', sprintf($txt['users_active'], $modSettings['lastActive']), ':<br />', implode(', ', $context['list_users_online']);

		// Showing membergroups?
		if (!empty($settings['show_group_key']) && !empty($context['membergroups']))
			echo '
				<br />[' . implode(']&nbsp;&nbsp;[', $context['membergroups']) . ']';
	}

	echo '
			</p>
			<p class="last smalltext">
				', $txt['most_online_today'], ': <strong>', comma_format($modSettings['mostOnlineToday']), '</strong>.
				', $txt['most_online_ever'], ': ', comma_format($modSettings['mostOnline']), ' (', timeformat($modSettings['mostDate']), ')
			</p>';
			
	// Show information about events, birthdays, and holidays on the calendar.
	if ($context['show_calendar'])
	{
		echo '<span class="info_center_text"><img src="'.$settings['images_url'].'/menu_calendar.png" alt="" /> <a href="', $scripturl, '?action=calendar' . '"><img class="icon" src="', $settings['images_url'], '/icons/calendar.gif', '" alt="', $context['calendar_only_today'] ? $txt['calendar_today'] : $txt['calendar_upcoming'], '" /></a>
						', $context['calendar_only_today'] ? $txt['calendar_today'] : $txt['calendar_upcoming'], '</span><br /><hr />
			<p class="smalltext">';

		// Holidays like "Christmas", "Chanukah", and "We Love [Unknown] Day" :P.
		if (!empty($context['calendar_holidays']))
				echo '
				<span class="holiday">', $txt['calendar_prompt'], ' ', implode(', ', $context['calendar_holidays']), '</span><br />';

		// People's birthdays. Like mine. And yours, I guess. Kidding.
		if (!empty($context['calendar_birthdays']))
		{
				echo '
				<span class="birthday">', $context['calendar_only_today'] ? $txt['birthdays'] : $txt['birthdays_upcoming'], '</span> ';
		/* Each member in calendar_birthdays has:
				id, name (person), age (if they have one set?), is_last. (last in list?), and is_today (birthday is today?) */
		foreach ($context['calendar_birthdays'] as $member)
				echo '
				<a href="', $scripturl, '?action=profile;u=', $member['id'], '">', $member['is_today'] ? '<strong>' : '', $member['name'], $member['is_today'] ? '</strong>' : '', isset($member['age']) ? ' (' . $member['age'] . ')' : '', '</a>', $member['is_last'] ? '<br />' : ', ';
		}
		// Events like community get-togethers.
		if (!empty($context['calendar_events']))
		{
			echo '
				<span class="event">', $context['calendar_only_today'] ? $txt['events'] : $txt['events_upcoming'], '</span> ';
			/* Each event in calendar_events should have:
					title, href, is_last, can_edit (are they allowed?), modify_href, and is_today. */
			foreach ($context['calendar_events'] as $event)
				echo '
					', $event['can_edit'] ? '<a href="' . $event['modify_href'] . '" title="' . $txt['calendar_edit'] . '"><img src="' . $settings['images_url'] . '/icons/modify_small.gif" alt="*" /></a> ' : '', $event['href'] == '' ? '' : '<a href="' . $event['href'] . '">', $event['is_today'] ? '<strong>' . $event['title'] . '</strong>' : $event['title'], $event['href'] == '' ? '' : '</a>', $event['is_last'] ? '<br />' : ', ';
		}
		echo '
			</p>';
	}		

	// Here's where the "Info Center" starts...
	echo '
	<br class="clear" />';
}
?>