<?php
	// These first four lines should be executed as soon as possible.
	require_once "sso/config.php";
	require_once SSO_CLIENT_ROOT_PATH . "/index.php";

	$sso_client = new SSO_Client;
	$sso_client->Init(array("sso_impersonate", "sso_remote_id"));

	// The rest of this code can be executed whenever.
	$extra = array();
	if (isset($_REQUEST["sso_impersonate"]) && is_string($_REQUEST["sso_impersonate"]))  $extra["sso_impersonate"] = $_REQUEST["sso_impersonate"];
	else if (isset($_REQUEST["sso_remote_id"]) && is_string($_REQUEST["sso_remote_id"]))
	{
		$extra["sso_provider"] = "sso_remote";
		$extra["sso_remote_id"] = $_REQUEST["sso_remote_id"];
	}
	if (!$sso_client->LoggedIn())  $sso_client->Login("", "You must login to use this system.", $extra);

	// Fields names from the SSO server API key mapping.
	$fields = array(
		"username",
		"email",
		"firstname",
		"lastname",
		"emp_id"
	);

	// Reads user information from the browser cookie, session,
	// and/or the SSO server into a more convenient user object.
	$user = $sso_client->GetMappedUserInfo($fields);

	// Test permissions for the user.
	/* if (!$sso_client->IsSiteAdmin())
	{
		$sso_client->Login("", ($sso_client->FromSSOServer() ? "insufficient_permissions" : "You must login with an account with sufficient permissions to use this system."), $extra);
	} */

	// Get the internal token for use with XSRF defenses.
	// Not used in this example.
	$bb_usertoken = $sso_client->GetSecretToken();

	// A simple example.
	if (isset($_REQUEST["action"]) && $_REQUEST["action"] == "logout")
	{
		$sso_client->Logout();

		$url = $sso_client->GetFullRequestURLBase();

		header("Location: " . $url);
		exit();
	}
	else
	{
		echo "<pre>"; print_r($user);
		echo 'cookies';
		echo "<pre>"; print_r($_COOKIE);
		echo 'end of cookies';
		echo 'sessions';
		echo "<pre>"; print_r($_SESSION['__sso_client_/_sso_rpmes_staging']);
		echo 'end of sessions';
		echo "<br />";
		echo "<a href=\"test_oo.php\">Test local access</a><br />";
		echo "<a href=\"test_oo.php?action=logout\">Logout</a>";
	}
?>