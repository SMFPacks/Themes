<?php
// Version: 2.0; index

/*	This template is, perhaps, the most important template in the theme. It
	contains the main template layer that displays the header and footer of
	the forum, namely with main_above and main_below. It also contains the
	menu sub template, which appropriately displays the menu; the init sub
	template, which is there to set the theme up; (init can be missing.) and
	the linktree sub template, which sorts out the link tree.

	The init sub template should load any data and set any hardcoded options.

	The main_above sub template is what is shown above the main content, and
	should contain anything that should be shown up there.

	The main_below sub template, conversely, is shown after the main content.
	It should probably contain the copyright statement and some other things.

	The linktree sub template should display the link tree, using the data
	in the $context['linktree'] variable.

	The menu sub template should display all the relevant buttons the user
	wants and or needs.

	For more information on the templating system, please see the site at:
	http://www.simplemachines.org/
*/

// Initialize the template... mainly little settings.
function template_init()
{
	global $context, $settings, $options, $txt, $scripturl;

	/* Use images from default theme when using templates from the default theme?
		if this is 'always', images from the default theme will be used.
		if this is 'defaults', images from the default theme will only be used with default templates.
		if this is 'never' or isn't set at all, images from the default theme will not be used. */
	$settings['use_default_images'] = 'never';

	/* What document type definition is being used? (for font size and other issues.)
		'xhtml' for an XHTML 1.0 document type definition.
		'html' for an HTML 4.01 document type definition. */
	$settings['doctype'] = 'xhtml';

	/* The version this template/theme is for.
		This should probably be the version of SMF it was created for. */
	$settings['theme_version'] = '2.0.1';

	/* Set a setting that tells the theme that it can render the tabs. */
	$settings['use_tabs'] = true;

	/* Use plain buttons - as opposed to text buttons? */
	$settings['use_buttons'] = true;

	/* Show sticky and lock status separate from topic icons? */
	$settings['separate_sticky_lock'] = true;

	/* Does this theme use the strict doctype? */
	$settings['strict_doctype'] = false;

	/* Does this theme use post previews on the message index? */
	$settings['message_index_preview'] = false;

	/* Set the following variable to true if this theme requires the optional theme strings file to be loaded. */
	$settings['require_theme_strings'] = false;
	
	/* Strings that are used for this theme */
	$txt['last_visit'] = 'Last Visit';
	$txt['unread'] = 'Unread';
	$txt['view_unread_replies'] = 'Unread Replies';
	$txt['view_unread_posts'] = 'Unread Posts';
	$txt['enable_sky_button'] = 'Enable Go Up Button';
	$txt['welcome_guest_message_txt'] = '<span class="middletext">Join '.$context['forum_name'].' to get access to all our features. Once registered you will be able to create topics, new replies, personal messages, polls and more. It\'s quick, fast and best of all free so feel free to <a href="'.$scripturl.'?action=register">register now</a></span>.';
	$txt['welcome_guest_message'] = 'Enable Welcome Guest Message';
	$txt['unapproved'] = 'Unapproved';
	$txt['full_edit'] = 'Full Edit';
	$txt['posted_on'] = 'Posted on';
	$txt['visit_website'] = 'Visit Website';
	$txt['send_personal_message'] = 'Send Personal Message';
	$txt['unread_post'] = 'Unread Post';
}

// The main sub template above the content.
function template_html_above()
{
	global $context, $settings, $options, $scripturl, $txt, $modSettings, $smcFunc, $user_info, $sourcedir;

	// Show right to left and the character set for ease of translating.
	echo '<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" dir="', $context['right_to_left'] ? 'rtl' : 'ltr', '">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=', $context['character_set'], '" />
	<meta name="description" content="', $context['page_title_html_safe'], '" />', !empty($context['meta_keywords']) ? '
	<meta name="keywords" content="' . $context['meta_keywords'] . '" />' : '', '
	<title>', $context['page_title_html_safe'], '</title>';
	
	// Please don't index these Mr Robot.
	if (!empty($context['robot_no_index']))
		echo '
	<meta name="robots" content="noindex" />';

	// Present a canonical url for search engines to prevent duplicate content in their indices.
	if (!empty($context['canonical_url']))
		echo '
	<link rel="canonical" href="', $context['canonical_url'], '" />';

	// The ?rc3 part of this link is just here to make sure browsers don't cache it wrongly.
	echo '
	<link rel="stylesheet" type="text/css" href="', $settings['theme_url'], '/css/index', $context['theme_variant'], '.css?201" />
	<link rel="stylesheet" type="text/css" href="', $settings['default_theme_url'], '/css/print.css?201" media="print" />
	<link rel="SHORTCUT ICON" href="', $settings['images_url'],'/favicon.ico" />';

	// Show all the relative links, such as help, search, contents, and the like.
	echo '
	<link rel="help" href="', $scripturl, '?action=help" />
	<link rel="search" href="', $scripturl, '?action=search" />
	<link rel="contents" href="', $scripturl, '" />';

	// If RSS feeds are enabled, advertise the presence of one.
	if (!empty($modSettings['xmlnews_enable']) && (!empty($modSettings['allow_guestAccess']) || $context['user']['is_logged']))
		echo '
	<link rel="alternate" type="application/rss+xml" title="', $context['forum_name_html_safe'], ' - ', $txt['rss'], '" href="', $scripturl, '?type=rss;action=.xml" />';

	// If we're viewing a topic, these should be the previous and next topics, respectively.
	if (!empty($context['current_topic']))
		echo '
	<link rel="prev" href="', $scripturl, '?topic=', $context['current_topic'], '.0;prev_next=prev" />
	<link rel="next" href="', $scripturl, '?topic=', $context['current_topic'], '.0;prev_next=next" />';

	// If we're in a board, or a topic for that matter, the index will be the board's index.
	if (!empty($context['current_board']))
		echo '
	<link rel="index" href="', $scripturl, '?board=', $context['current_board'], '.0" />';

	// Some browsers need an extra stylesheet due to bugs/compatibility issues.
	foreach (array('ie7', 'ie6', 'webkit') as $cssfix)
		if ($context['browser']['is_' . $cssfix])
			echo '
	<link rel="stylesheet" type="text/css" href="', $settings['default_theme_url'], '/css/', $cssfix, '.css" />';

	// RTL languages require an additional stylesheet.
	if ($context['right_to_left'])
		echo '
	<link rel="stylesheet" type="text/css" href="', $settings['theme_url'], '/css/rtl.css" />';
	
	// Sky Button!
	if (!empty($settings['enable_sky_button']))
	    echo'<script language="JavaScript" type="text/javascript" src="' . $settings['theme_url'] . '/scripts/top.js"></script>';

	echo '
	<script type="text/javascript" src="', $settings['theme_url'], '/scripts/theme.js?201"></script>
	<script type="text/javascript" src="', $settings['default_theme_url'], '/scripts/script.js?201"></script>
	<script type="text/javascript"><!-- // --><![CDATA[
		var smf_theme_url = "', $settings['theme_url'], '";
		var smf_default_theme_url = "', $settings['default_theme_url'], '";
		var smf_images_url = "', $settings['images_url'], '";
		var smf_scripturl = "', $scripturl, '";
		var smf_iso_case_folding = ', $context['server']['iso_case_folding'] ? 'true' : 'false', ';
		var smf_charset = "', $context['character_set'], '";', $context['show_pm_popup'] ? '
		var fPmPopup = function ()
		{
			if (confirm("' . $txt['show_personal_messages'] . '"))
				window.open(smf_prepareScriptUrl(smf_scripturl) + "action=pm");
		}
		addLoadEvent(fPmPopup);' : '', '
		var ajax_notification_text = "', $txt['ajax_in_progress'], '";
		var ajax_notification_cancel_text = "', $txt['modify_cancel'], '";
	// ]]></script>';

	// Output any remaining HTML headers. (from mods, maybe?)
	echo $context['html_headers'];

	echo '
</head>
<body>';
}

function template_body_above()
{
	global $context, $user_info;

	echo'<a name="sky"></a>
	<div id="wrapper">
		<div id="content_section">
				<div id="main_content_section">';
				
	// Show the navigation tree.
	theme_linktree();
}

function template_body_below()
{
	global $context, $settings, $options, $scripturl, $txt, $modSettings, $mbname;

	echo '
		<br class="clear" />
	</div></div>';

	// Sky Button!
	if (!empty($settings['enable_sky_button']))
		echo '<a href="#sky" class="sky_button" title="'.$txt['go_up'].'"><img src="'.$settings['images_url'].'/sky_bottom.png" alt="'.$txt['go_up'].'" /></a>';
	
	echo'<div id="footer_section">
	</div>';
}

function template_html_below()
{
	global $context, $settings, $options, $scripturl, $txt, $modSettings, $mbname;
	
	loadLanguage('Profile');

	echo'
</div>
<br />
	<footer>
		<div align="center"><div>', theme_copyright(), ' ',base64_decode('PHNwYW4gc3R5bGU9ImRpc3BsYXk6IGlubGluZTsgdmlzaWJpbGl0eTogdmlzaWJsZTsgZm9udC1mYW1pbHk6IFZlcmRhbmEsQXJpYWwsc2Fucy1zZXJpZjsiIGNsYXNzPSJzbWFsbHRleHQiPnwgPGEgaHJlZj0iaHR0cDovL3d3dy5zbWZwYWNrcy5jb20iIHRpdGxlPSJMaXR0bGUgUGFja2FnZXMgRG9pbmcgQXdlc29tZSBUaGluZ3MiPkJsdXggVGhlbWUgYnkgU01GUGFja3MuY29tICZjb3B5OyAyMDExPC9hPjwvc3Bhbj4='),'</div>
		<span class="smalltext"><a id="button_xhtml" href="http://validator.w3.org/check/referer" target="_blank" class="new_win" title="', $txt['valid_xhtml'], '"><span>', $txt['xhtml'], '</span></a> ', !empty($modSettings['xmlnews_enable']) && (!empty($modSettings['allow_guestAccess']) || $context['user']['is_logged']) ? ' | <a id="button_rss" href="' . $scripturl . '?action=.xml;type=rss" class="new_win"><span>' . $txt['rss'] . '</span></a>' : '', ' | <a id="button_wap2" href="', $scripturl , '?wap2" class="new_win"><span>', $txt['wap2'], '</span></a>
		</span>';
		
	// Show the load time?
	if ($context['show_load_time'])
		echo '<br /><span class="smalltext">', $txt['page_created'], $context['load_time'], $txt['seconds_with'], $context['load_queries'], $txt['queries'].'</span>';
		
	echo'
	</footer>
	<header>
		<div id="header">
				<div id="top_section">
					<div class="forumlogo">
						<a href="', $scripturl, '">
							', $mbname, '
						</a>
					</div>
					<div id="login_area" align="center"', ($context['user']['is_logged'] ? ' class="login_area_logged"' : ''),'>';
					
					if ($context['user']['is_logged'])
			        {
			        	if (!empty($context['user']['avatar']))
							echo $context['user']['avatar']['image'];
						else
							echo '<img src="', $settings['images_url'], '/header_no_avatar.png" alt="" />';
					
				        echo'
				        	<span class="member_box">', $txt['hello_member_ndt'], ' <a href="'.$scripturl.'?action=profile;u=', $context['user']['id'], '">', $context['user']['name'], '</a>', (allowedTo('pm_read') && $context['user']['unread_messages'] > 0 ? ' [' . $context['user']['unread_messages'] . ']' : ''),'!
				        	<div class="super_box member_box_header" id="member_info">';
				        	
				        	if (allowedTo('pm_read'))
				        	{
				        		echo'
				        			<div class="member_info_item">
				        				<a href="', $scripturl, '?action=pm">
				        					', $txt['pm_short'], ' ', ($context['user']['unread_messages'] > 0 ? ' [' . $context['user']['unread_messages'] . ']' : ''),'
				        				</a>';
				        				
				        			if (allowedTo('pm_send'))
				        				echo'
				        					<div class="floatright">
				        						<a href="'.$scripturl.'?action=pm;sa=send">
				        							', $txt['pm_menu_send'], '
				        						</a>
				        					</div>';
				        					
				        		echo'
				        			</div>';
				        	}
				        			
				        	echo'
				        		<div class="member_info_item member_info_item_last">
				        			<a href="', $scripturl, '?action=unreadreplies">
				        				', $txt['view_unread_replies'], '
				        			</a>
				        			<div class="floatright">
				        				<a href="'.$scripturl.'?action=unread">
				        					', $txt['view_unread_posts'], '
				        				</a>
				        			</div>
				        		</div>
				        		<div class="member_info_item member_info_item_last">
				        			<a href="', $scripturl, '?action=profile;area=pmprefs">
				        				', $txt['pmprefs'], '
				        			</a>
				        			<div class="floatright">
				        				<a href="'.$scripturl.'?action=profile;area=account">
				        					', $txt['account'], '
				        				</a>
				        			</div>
				        		</div>
				        		<div class="member_info_item member_info_item_last">
				        			<a href="', $scripturl, '?action=profile;area=forumprofile">
				        				', $txt['forumprofile'], '
				        			</a>
				        			<div class="floatright">
				        				<a href="'.$scripturl.'?action=profile;area=showposts">
				        					', $txt['showPosts'], '
				        				</a>
				        			</div>
				        		</div>';
				        		
				        if (!empty($modSettings['enable_buddylist']))
				        	echo'
				        		<div class="member_info_item member_info_item_last">
				        			<a href="', $scripturl, '?action=profile;area=lists;sa=buddies">
				        				', $txt['editBuddies'], '
				        			</a>
				        			<div class="floatright">
				        				<a href="'.$scripturl.'?action=profile;area=lists;sa=ignore">
				        					', $txt['editIgnoreList'], '
				        				</a>
				        			</div>
				        		</div>';
				        		
				        echo'
				        		<div class="member_info_item member_info_item_last">
				        			<a href="', $scripturl, '?action=profile;area=theme">
				        				', $txt['theme'], '
				        			</a>
				        			<div class="floatright">
				        				<a href="'.$scripturl.'?action=profile;area=notification">
				        					', $txt['notification'], '
				        				</a>
				        			</div>
				        		</div>
				        		<div class="member_info_item_last">
				        			<a href="', $scripturl, '?action=profile">
				        				', $txt['profile'], '
				        			</a>
				        			<div class="floatright">
				        				<a href="', $scripturl, '?action=logout;', $context['session_var'], '=', $context['session_id'], '">
				        					', $txt['logout'], '
				        				</a>
				        			</div>
				        		</div>
				        	</div>
				        </span>';
			
						// Is the forum in maintenance mode?
						if ($context['in_maintenance'] && $context['user']['is_admin'])
						echo ' [<strong>', $txt['maintenance'], '</strong>]';
			
						// Are there any members waiting for approval?
						if (!empty($context['unapproved_members']))
						echo ' [<a href="', $scripturl, '?action=viewmembers;sa=browse;type=approve">', $context['unapproved_members'] , ' ', $txt['approve'], '</a> ]';
			        }
			        // If the user is logged in, display stuff like their name, new messages, etc.
					elseif (!empty($context['show_login_bar']))
					{
						echo '
							<script type="text/javascript" src="', $settings['default_theme_url'], '/scripts/sha1.js"></script>
							<form id="guest_form" action="', $scripturl, '?action=login2" method="post" accept-charset="', $context['character_set'], '" ', empty($context['disable_login_hashing']) ? ' onsubmit="hashLoginPassword(this, \'' . $context['session_id'] . '\');"' : '', ' style="margin-bottom: 10px;">
								<input type="text" name="user" size="10" class="input_text" placeholder="', $txt['username'], '" />
								<input type="password" name="passwrd" size="10" class="input_password" placeholder="', $txt['password'], '" />
								<select name="cookielength">
									<option value="60">', $txt['one_hour'], '</option>
									<option value="1440">', $txt['one_day'], '</option>
									<option value="10080">', $txt['one_week'], '</option>
									<option value="43200">', $txt['one_month'], '</option>
									<option value="-1" selected="selected">', $txt['forever'], '</option>
								</select>
								<input type="submit" value="', $txt['login'], '" class="button_submit" />
								<a href="', $scripturl, '?action=register"><button class="button_submit">', $txt['register'], '</button></a><br />';
			
						if (!empty($modSettings['enableOpenID']))
							echo '
								<br />
								<input type="text" name="openid_identifier" id="openid_url" size="25" class="input_text openid_login" />';
			
						echo '
								<input type="hidden" name="hash_passwrd" value="" />
							</form>';
					}	
					
				echo'
					</div>
					<div id="search_wrap">';
					
					if (allowedTo('search_posts'))
					{
						echo'
							<form action="', $scripturl, '?action=search2" method="post" accept-charset="', $context['character_set'], '">
								<input type="text" id="main_search" name="search" size="32" placeholder="', $txt['search'], '..." />
								<input type="submit" class="submit_input clickable" value=""/>
								<input type="hidden" name="advanced" value="0" />';
			
						// Search within current topic?
						if (!empty($context['current_topic']))
							echo '
									<input type="hidden" name="topic" value="', $context['current_topic'], '" />';
							// If we're on a certain board, limit it to this board ;).
						elseif (!empty($context['current_board']))
							echo '
									<input type="hidden" name="brd[', $context['current_board'], ']" value="', $context['current_board'], '" />';
			
					echo '
							</form>
							<a href="', $scripturl, '?action=search;advanced" rel="search" class="advanced_search" title="', $txt['search_advanced'], '"></a>';
					}
						
				echo'
						<a href="', $scripturl, '?action=help" rel="help" class="advanced_search_help" title="', $txt['help'], '"></a>
					</div>
		        </div>';
        
	        	// Menu
	        	template_menu();
			
	echo '
		</header>
	</div>
</body></html>';
}

// Show a linktree. This is that thing that shows "My Community | General Category | General Discussion"..
function theme_linktree($force_show = false)
{
	global $context, $settings, $options, $shown_linktree, $scripturl;

	// If linktree is empty, just return - also allow an override.
	if (empty($context['linktree']) || (!empty($context['dont_default_linktree']) && !$force_show))
		return;
		
	echo '
	<nav>
	<div class="navigate_section">
		<ul>
			<li itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb">
				<a href="', $scripturl, '" itemprop="url">
					<span itemprop="title">Home</span>
				</a>';
				
	if (count($context['linktree']) != 1)
				echo'
					&rarr;';
				
		echo'	
			</li>';

	// Each tree item has a URL and name. Some may have extra_before and extra_after.
	foreach ($context['linktree'] as $link_num => $tree)
	{
		if ($link_num == 0)
			continue;
			
		echo '
			<li', ($link_num == count($context['linktree']) - 1) ? ' class="last"' : '', ' itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb">';

		// Show something before the link?
		if (isset($tree['extra_before']))
			echo $tree['extra_before'];

		// Show the link, including a URL if it should have one.
		echo $settings['linktree_link'] && isset($tree['url']) ? '
				<a href="' . $tree['url'] . '" itemprop="url"><span itemprop="title">' . $tree['name'] . '</span></a>' : '<span itemprop="title">' . $tree['name'] . '</span>';

		// Show something after the link...?
		if (isset($tree['extra_after']))
			echo $tree['extra_after'];

		// Don't show a separator for the last one.
		if ($link_num != count($context['linktree']) - 1)
			echo ' &rarr;';

		echo '
			</li>';
	}
	echo '
		</ul>
	</div>
	</nav>';

	$shown_linktree = true;
}

function template_menu()
{
    global $context, $settings, $options, $scripturl, $txt;
    
    echo'
    	<div id="menu_nav">
    		<ul class="menu">
    			<li class="first_tab"><span>&nbsp;</span></li>';
    
    foreach ($context['menu_buttons'] as $act => $button)
    {
    	$icon = $act;
		
		if ( $act == 'page' )
       		$icon = strtolower($button['title']);
       		
       	if ( in_array($act, array('help', 'search', 'profile', 'register', 'logout', 'pm')) )
       		continue;
		
		echo'
			<li>
				<a href="', $button['href'], '"', isset($button['target']) ? ' target="' . $button['target'] . '"' : '', ' ', isset($button['nofollow']) ? ' rel="nofollow"' : '', '>
					', $button['title'], '&nbsp;
				</a>';
			
		if (!empty($button['sub_buttons']))
		{
			echo '
					<ul>';

			foreach ($button['sub_buttons'] as $childbutton)
			{
				echo '
						<li>
							<a href="', $childbutton['href'], '"', isset($childbutton['target']) ? ' target="' . $childbutton['target'] . '"' : '', '>
								<span', isset($childbutton['is_last']) ? ' class="last"' : '', '>', $childbutton['title'], !empty($childbutton['sub_buttons']) ? '...' : '', '</span>
							</a>';

				echo '
						</li>';
			}
				echo '
					</ul>';
		}
		
		echo'
			</li>';
    }
	
	echo'
			</ul>
		</div>';
}

// Generate a strip of buttons.
function template_button_strip($button_strip, $direction = 'top', $strip_options = array())
{
	global $settings, $context, $txt, $scripturl;

	if (!is_array($strip_options))
		$strip_options = array();

	// Create the buttons...
	$buttons = array();
	foreach ($button_strip as $key => $value)
	{
		if (!isset($value['test']) || !empty($context[$value['test']]))
			$buttons[] = '
				<a' . (isset($value['id']) ? ' id="button_strip_' . $value['id'] . '"' : '') . ' class="button_tw button_strip_' . $key . '' . (isset($value['active']) ? ' button_tw_active' : '') . '" href="' . $value['url'] . '"' . (isset($value['custom']) ? ' ' . $value['custom'] : '') . '><span>' . $txt[$value['text']] . '</span></a>';
	}

	// No buttons? No button strip either.
	if (empty($buttons))
		return;

	// Make the last one, as easy as possible.
	$buttons[count($buttons) - 1] = str_replace('<span>', '<span class="last">', $buttons[count($buttons) - 1]);

	echo '
		<div class="m_buttons ', !empty($direction) ? 'align_' . $direction : '', '"', (empty($buttons) ? ' style="display: none;"' : ''), (!empty($strip_options['id']) ? ' id="' . $strip_options['id'] . '"': ''), '>
			', implode('', $buttons), '
		</div>';
}

?>