<?php

add_filter ('bp_get_template_stack', 'bps_template_stack', 20);
function bps_template_stack ($stack)
{
	$stack[] = dirname (__FILE__). '/templates';
	return $stack;
}

function bps_templates ()
{
	$templates = array ('members/bps-form-default');
	return apply_filters ('bps_templates', $templates);
}

function bps_default_template ()
{
	$templates = bps_templates ();
	return $templates[0];
}

function bps_is_template ($template)
{
	$templates = bps_templates ();
	return in_array ($template, $templates);
}

function bps_valid_template ($template)
{
	return bps_is_template ($template)? $template: bps_default_template ();
}

function bps_template_info ($template)
{
	$located = bp_locate_template ($template. '.php');
	if ($located === false)
	{
		echo '<strong style="color:red;">'. $template. '</strong><br>'. __('template not found', 'bp-profile-search');
		return false;
	}

	if (dirname ($located) == dirname (__FILE__). '/templates/members')
	{
		echo '<strong style="color:green;">'. $template. '</strong><br>'. __('built-in template', 'bp-profile-search');
		return $located;
	}

	ob_start ();
	$response = include $located;
	ob_get_clean ();

	$path = str_replace (WP_CONTENT_DIR. '/', '', $located);
	$path = str_replace ($template. '.php', '', $path);

	if ($response == 'end_of_options 4.9')
		echo '<strong style="color:blue;">'. $template. '</strong><br>'. sprintf (__('custom template located in: %1$s', 'bp-profile-search'), $path);
	else
	{
		echo '<strong style="color:red;">'. $template. '</strong><br>'. sprintf (__('unsupported template located in: %1$s', 'bp-profile-search'), $path);
		echo '<br><a href="https://dontdream.it/bp-profile-search-5-3/">'. __('more information...', 'bp-profile-search'). '</a>';
	}
	return $located;
}

function bps_call_template ($template, $args = array ())
{
	$located = bp_locate_template ($template. '.php');

	if ($located === false)
		return bps_error ('template_not_found', $template);

	$GLOBALS['bps_template_args'][] = $args;

	echo "\n<!-- BP Profile Search ". BPS_VERSION. " $template -->\n";
	if (bps_debug ())
	{
		$path = str_replace (WP_CONTENT_DIR, '', $located);
		echo "<!--\n";
		echo "path $path\n";
		echo "args "; print_r ($args);
		echo "-->\n";
	}

	include $located;

	echo "\n<!-- BP Profile Search end $template -->\n";
	array_pop ($GLOBALS['bps_template_args']);

	return true;
}

function bps_call_form_template ($form, $location)
{
	$meta = bps_meta ($form);

	if (empty ($meta['field_code']))
		return bps_error ('form_empty_or_nonexistent', $form);

	$args = array ($form, $location);
	$template = bps_valid_template ($meta['template']);
	$located = bp_locate_template ($template. '.php');

	if ($located === false)
		return bps_error ('template_not_found', $template);

	$GLOBALS['bps_template_args'][] = $args;
	$options = isset ($meta['template_options'][$template])? $meta['template_options'][$template]: array ();

	echo "\n<!-- BP Profile Search ". BPS_VERSION. " $template -->\n";
	if (bps_debug ())
	{
		$path = str_replace (WP_CONTENT_DIR, '', $located);
		echo "<!--\n";
		echo "path $path\n";
		echo "args "; print_r ($args);
		echo "options "; print_r ($options);
		echo "-->\n";
	}

	include $located;

	echo "\n<!-- BP Profile Search end $template -->\n";
	array_pop ($GLOBALS['bps_template_args']);

	return true;
}

function bps_template_args ()
{
	return end ($GLOBALS['bps_template_args']);
}

function bps_jquery_ui_themes ()
{
	$themes = array (
		'' => __('no jQuery UI', 'bp-profile-search'),
		'base' => 'Base',
		'black-tie' => 'Black Tie',
		'blitzer' => 'Blitzer',
		'cupertino' => 'Cupertino',
		'dark-hive' => 'Dark Hive',
		'dot-luv' => 'Dot Luv',
		'eggplant' => 'Eggplant',
		'excite-bike' => 'Excite Bike',
		'flick' => 'Flick',
		'hot-sneaks' => 'Hot Sneaks',
		'humanity' => 'Humanity',
		'le-frog' => 'Le Frog',
		'mint-choc' => 'Mint Choc',
		'overcast' => 'Overcast',
		'pepper-grinder' => 'Pepper Grinder',
		'redmond' => 'Redmond',
		'smoothness' => 'Smoothness',
		'south-street' => 'South Street',
		'start' => 'Start',
		'sunny' => 'Sunny',
		'swanky-purse' => 'Swanky Purse',
		'trontastic' => 'Trontastic',
		'ui-darkness' => 'UI darkness',
		'ui-lightness' => 'UI lightness',
		'vader' => 'Vader',
	);	

	return apply_filters ('bps_jquery_ui_themes', $themes);
}

function bps_escaped_form_data ($version = '')
{
	if ($version == '4.9')	return bps_escaped_form_data49 ();

	return false;
}

function bps_escaped_filters_data ($version = '')
{
	if ($version == '5.4')	return bps_escaped_filters_data54 ();

	return false;
}

function bps_set_hidden_field ($name, $value)
{
	$new = new stdClass;
	$new->display = 'hidden';
	$new->html_name = $name;
	$new->value = $value;

	return $new;
}

function bps_unique_id ($id)
{
	static $k = array ();

	$k[$id] = isset ($k[$id])? $k[$id] + 1: 0;
	$unique = $k[$id]? $id. '_'. $k[$id]: $id;
	
	return apply_filters ('bps_unique_id', $unique, $id);
}

function bps_escaped_form_data49 ()
{
	list ($form, $location) = bps_template_args ();

	$meta = bps_meta ($form);
	list ($fields, $errors) = bps_get_form_fields ($form);

	$F = new stdClass;
	$F->id = $form;
	$F->title = bps_wpml ($form, '-', 'title', get_the_title ($form));
	$F->location = $location;
	$F->unique_id = bps_unique_id ('form_'. $form);
	$F->errors = $errors;

	$dirs = bps_directories ();
	$F->action = $dirs[bps_wpml_id ($meta['action'])]->path;
	$F->method = $meta['method'];

	$platform = bps_platform ();
	$F->strings['clear'] = esc_html (($platform == 'buddypress')? __('Clear', 'buddypress'): __('Clear', 'buddyboss'));
	$F->strings['search'] = esc_html (($platform == 'buddypress')? __('Search', 'buddypress'): __('Search', 'buddyboss'));

	$F->fields = $fields;
	$F->fields[] = bps_set_hidden_field (BPS_FORM, $form);
	$F->fields[] = bps_set_hidden_field ('bps_form_page', bps_current_page ());

	do_action ('bps_before_search_form', $F);

	foreach ($F->fields as $f)
	{
		$f->unique_id = bps_unique_id ($f->html_name);

		if (!is_array ($f->value))
			$f->value = esc_attr (stripslashes ($f->value));
		else foreach ($f->value as $k => $value)
			$f->value[$k] = esc_attr (stripslashes ($value));
		if ($f->display == 'hidden')  continue;

		$f->label = esc_html ($f->label);
		$f->description = esc_html ($f->description);
		$f->error_message = esc_html ($f->error_message);

		$options = array ();
		foreach ($f->options as $key => $label)
			$options[esc_attr ($key)] = esc_html ($label);
		$f->options = $options;
	}

	return $F;
}

function bps_escaped_filters_data54 ()
{
	$F = new stdClass;

	$action = parse_url ($_SERVER['REQUEST_URI'], PHP_URL_PATH);
	$action = add_query_arg (BPS_FORM, 'clear', $action);
	$F->links[esc_url ($action)] = esc_html ((bps_platform () == 'buddypress')? __('Clear', 'buddypress'): __('Clear', 'buddyboss'));
	
	$form_page = bps_get_request ('filters')['bps_form_page'];
	if ($form_page != bps_current_page ())
		$F->links[esc_url ($form_page)] = esc_html (__('New Search', 'bp-profile-search'));

	$F->fields = bps_get_filters_fields ();

	do_action ('bps_before_filters', $F);

	foreach ($F->fields as $f)
	{
		$f->label = esc_html ($f->label);
		$f->mode = esc_html ($f->mode);
		if (!is_array ($f->value))
			$f->value = esc_html (stripslashes ($f->value));
		else foreach ($f->value as $k => $value)
			$f->value[$k] = esc_html (stripslashes ($value));
	}

	return $F;
}

function bps_escaped_details_data ()
{
	$F = new stdClass;
	$F->fields = array ();

	$details = bps_get_details ();
	foreach ($details as $code)
	{
		$f = bps_parsed_field ($code);
		if (!isset ($f->get_value) || !is_callable ($f->get_value))  continue;

		$f->d_label = (isset ($f->filter) && isset ($f->label))? $f->label: $f->name;
		call_user_func ($f->get_value, $f);

		$f->d_label = esc_html ($f->d_label);
		if (!is_array ($f->d_value))
			$f->d_value = esc_html (stripslashes ($f->d_value));
		else foreach ($f->d_value as $k => $value)
			$f->d_value[$k] = esc_html (stripslashes ($value));

		do_action ('bps_field_before_details', $f);
		$F->fields[] = $f;
	}

	do_action ('bps_before_details', $F);
	return $F;
}
