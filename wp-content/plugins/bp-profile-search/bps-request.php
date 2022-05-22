<?php

add_action ('wp', 'bps_set_request');
function bps_set_request ()
{
	bps_set_directory ();

	if (isset ($_REQUEST['bps_debug']))
	{
		$cookie = apply_filters ('bps_cookie_name', 'bps_debug');
		setcookie ($cookie, 1, 0, COOKIEPATH);
	}

	$showing_errors = isset ($_REQUEST['bps_errors']);
	$persistent = bps_get_option ('persistent', '1') || $showing_errors;
	$new_search = isset ($_REQUEST[bp_core_get_component_search_query_arg ('members')]);

	if ($new_search || !$persistent)
		if (!isset ($_REQUEST[BPS_FORM]))  $_REQUEST[BPS_FORM] = 'clear';

	$cookie = apply_filters ('bps_cookie_name', 'bps_request');
	if (isset ($_REQUEST[BPS_FORM]))
	{
		if ($_REQUEST[BPS_FORM] != 'clear')
		{
			$_REQUEST['bps_directory'] = bps_current_page ();
			setcookie ($cookie, http_build_query ($_REQUEST), 0, COOKIEPATH);

			list (, $errors) = bps_get_form_fields ($_REQUEST[BPS_FORM]);
			if ($errors)  _bps_redirect_on_errors ($errors);
		}
		else
		{
			setcookie ($cookie, '', 0, COOKIEPATH);
		}
	}
	else if ($showing_errors)
	{
		setcookie ($cookie, '', 0, COOKIEPATH);
	}
}

function bps_get_request2 ($type, $form=0)		// published interface, 20190324
{
	static $saved_request = array ();
	if (isset ($saved_request["$type-$form"]))  return $saved_request["$type-$form"];

	$request = _bps_clean_request ();

	if (!empty ($request))  switch ($type)
	{
	case 'form':
		if ($request[BPS_FORM] != $form)  $request = array ();
		break;

	case 'filters':
		$current = bps_current_page ();
		$showing_errors = isset ($_REQUEST['bps_errors']);
		if ($request['bps_directory'] != $current || $showing_errors)  $request = array ();
		break;

	case 'search':
		$current = bps_current_page ();
		if (empty ($request['bps_directory']) || $request['bps_directory'] != $current)  $request = array ();
		break;
	}

	$request = apply_filters ('bps_request', $request, $type, $form);
	if (bps_debug ())
	{
		echo "<!--\n";
		echo "type $type, $form\n";
		echo "request "; print_r ($request);
		echo "-->\n";
	}

	$saved_request["$type-$form"] = $request;
	return $request;
}

function _bps_clean_request ()
{
	$request = $_REQUEST;
	if (empty ($request[BPS_FORM]))
	{
		$cookie = apply_filters ('bps_cookie_name', 'bps_request');
		if (empty ($_COOKIE[$cookie]))
		{
			$clean = array ();				// no search
		}
		else
		{
			parse_str (stripslashes ($_COOKIE[$cookie]), $request);
			if (empty ($request[BPS_FORM]))
				$clean = array ();					// bad cookie
			else if ($request[BPS_FORM] == 'clear')
				$clean = array ();					// bad cookie
			else
				$clean = bps_clean ($request);		// saved search
		}
	}
	else if ($request[BPS_FORM] == 'clear')
	{
		$clean = array ();				// clear search
	}
	else
	{
		$clean = bps_clean ($request);	// new search
	}

	return $clean;
}

function bps_clean ($request)		// $request[BPS_FORM] is set and != 'clear'
{
	$clean = array ();

	$form = $request[BPS_FORM];
	$meta = bps_meta ($form);

	$hidden_filters = bps_get_hidden_filters ();
	foreach ($hidden_filters as $key => $value)  unset ($request[$key]);

	foreach ($meta['field_code'] as $k => $code)
	{
		$filter = $meta['field_mode'][$k];
		$key = bps_key ($code, $filter, '_');
		if (!isset ($request[$key]))  continue;

		$value = $request[$key];
		if (bps_Fields::is_empty_value ($value, $filter))  continue;

		$key = bps_key ($code, $filter);
		$clean[$key] = $value;

		$label = $meta['field_label'][$k];
		if ($label)
		{
			$key = bps_key ($code, 'label');
			$clean[$key] = $label;
		}
	}

	if (empty ($clean))  return $clean;

	$clean[BPS_FORM] = $form;
	$clean['bps_form_page'] = $request['bps_form_page'];
	$clean['bps_directory'] = $request['bps_directory'];
	return $clean;
}

function bps_get_form_fields ($form)
{
	static $form_fields = array ();
	static $errors = array ();

	if (isset ($form_fields[$form]))  return [$form_fields[$form], $errors[$form]];

	list (, $fields) = bps_get_fields ();
	$request = bps_get_request ('form', $form);

	$form_fields[$form] = array ();
	$errors[$form] = 0;
	$meta = bps_meta ($form);
	foreach ($meta['field_code'] as $k => $code)
	{
		if (empty ($fields[$code]))  continue;

		$f = clone $fields[$code];

		$filter = $meta['field_mode'][$k];
		if (empty ($f->display))
			$f->display = bps_Fields::get_display ($f, $filter);
		if ($f->display == false)  continue;

		switch ($f->display)
		{
		case 'selectbox':
			$f->options = array ('' => '') + $f->options;
			break;

		case 'multiselectbox':
			$f->multiselect_size = 4;
			break;
		}

		$f->label = $meta['field_label'][$k]?: $f->name;

		$description = $meta['field_desc'][$k];
		if ($description == '-')
			$f->description = '';
		else if ($description)
			$f->description = $description;

		$f->form_id = $form;
		$f->value = bps_Fields::get_empty_value ($filter);
		$f->html_name = bps_key ($code, $filter, '_');
		$f->mode = bps_Fields::get_filter_label ($filter);
		$f->required = (strpos ($f->label, '*') === 0);
		$f->error_message = '';

		do_action ('bps_field_before_search_form', $f);

		if (!empty ($request))
		{
			$key = bps_key ($code, $filter);
			if (isset ($request[$key]))
			{
				$f->filter = $filter;
				$f->value = $request[$key];
			}

			$f->error_message = _bps_validate_field ($f);
			if ($f->error_message)  $errors[$form] += 1;
		}

		$form_fields[$form][] = $f;
	}

	return [$form_fields[$form], $errors[$form]];
}

function _bps_validate_field ($f)
{
	$error_message = '';
	$value = $f->value;
	$required = $f->required;
	$display = $f->display;
	if ($display == 'textbox' && $f->format == 'decimal')  $display = 'decimal';

	switch ($display)
	{
	case 'textbox':
		$exp = bps_is_expression ($value);
		if ($required && !isset ($f->filter))
			$error_message = __('this field is required, please enter a value or a search expression', 'bp-profile-search');
		else if (($exp == 'and' || $exp == 'mixed') && $f->filter == '')
			$error_message = __('AND expression not allowed here, use only OR', 'bp-profile-search');
		else if ($exp == 'mixed')
			$error_message = __('mixed expression not allowed, use only AND or only OR', 'bp-profile-search');
		break;

	case 'integer':
	case 'decimal':
		if ($required && !isset ($f->filter))
			$error_message = __('this field is required, please enter a value', 'bp-profile-search');
		break;

	case 'integer-range':
	case 'range':
		if ($required && !isset ($f->filter))
			$error_message = __('this field is required, please enter at least a value', 'bp-profile-search');
		break;

	case 'date':
		if ($required && !isset ($f->filter))
			$error_message = __('this field is required, please enter a date', 'bp-profile-search');
		break;

	case 'date-range':
		if ($required && !isset ($f->filter))
			$error_message = __('this field is required, please enter at least a date', 'bp-profile-search');
		break;

	case 'distance':
		if ($required && !isset ($f->filter))
			$error_message = __('this field is required, please enter a distance and select a location', 'bp-profile-search');
		else if ($value['distance'] === '' && $value['location'] !== '')
			$error_message = __('please enter a distance', 'bp-profile-search');
		break;

	case 'radio':
	case 'selectbox':
		if ($required && !isset ($f->filter))
			$error_message = __('this field is required, please select an option', 'bp-profile-search');
		break;

	case 'checkbox':
	case 'multiselectbox':
	case 'range-select':
		if ($required && !isset ($f->filter))
			$error_message = __('this field is required, please select at least an option', 'bp-profile-search');
		break;
	}

	$error_message = apply_filters ('bps_validate_field', $error_message, $f);
	return $error_message;
}

function bps_get_filters_fields ()
{
	static $filter_fields;
	if (isset ($filter_fields))  return $filter_fields;

	$filter_fields = array ();
	$request = bps_get_request ('filters');
	if (empty ($request))  return $filter_fields;

	list (, $fields) = bps_get_fields ();

	foreach ($request as $key => $value)
	{
		if (in_array ($key, [BPS_FORM, 'bps_form_page', 'bps_directory']))  continue;

		list ($code, $filter) = bps_reverse_key ($key);
		if (empty ($fields[$code]) || $filter == 'label')  continue;

		$f = clone $fields[$code];

		$key = bps_key ($code, 'label');
		$f->label = isset ($request[$key])? $request[$key]: $f->name;
		$f->filter = $filter;
		$f->mode = bps_Fields::get_filter_label ($filter);
		$f->value = $value;

		if (!empty ($f->options))
		{
			if (is_array ($f->value))
			{
				$values = array ();
				foreach ($f->value as $k => $key)
					$values[$k] = ($key === '')? '': $f->options[stripslashes ($key)];	// provisional
				$f->value = $values;
			}
			else
			{
				$key = $f->value;
				$f->value = $f->options[stripslashes ($key)];
			}
		}

		do_action ('bps_field_before_filters', $f);
		$filter_fields[] = $f;
	}

	return $filter_fields;
}

function bps_key ($code, $filter, $join='_')
{
	$key = ($filter == '')? $code: $code. $join. $filter;
	return $key;
}

function bps_reverse_key ($key)
{
	list (, $fields) = bps_get_fields ();
	foreach ($fields as $code => $f)
	{
		if ($key == $code)
			return array ($code, '');
		if (strpos ($key, $code. '_') === 0)
			return array ($code, substr ($key, strlen ($code) + 1));
	}
	return array (false, false);
}

function bps_reverse_key0 ($key)
{
	$reverse = explode ('.', $key);
	if (empty ($reverse[1]))  $reverse[1] = '';
	return $reverse;
}

function bps_debug ()
{
	$cookie = apply_filters ('bps_cookie_name', 'bps_debug');
	return isset ($_REQUEST['bps_debug'])? true: isset ($_COOKIE[$cookie]);
}

function _bps_redirect_on_errors ($errors)
{
	$redirect = parse_url ($_SERVER['HTTP_REFERER'], PHP_URL_PATH);
	$redirect = add_query_arg ('bps_errors', $errors, $redirect);
	wp_safe_redirect ($redirect);
	exit;
}
