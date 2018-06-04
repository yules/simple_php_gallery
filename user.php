<?php	
/**
 * created by: Yuval Leshem
 * yuval.leshem@gmail.com
 * http://simplegalleryma.sourceforge.net/
**/
include 'db.php';

function validate_user_session()
{	
	$ret = false;
	if (isset($_COOKIE['username']) && isset($_COOKIE['password'])) {
		// get user by name from db
		$ret = validate_user($_COOKIE['username'], $_COOKIE['password']);
	} 
	return $ret;
}

function validate_user($username, $pass)
{
	$ret = false;
	
	$result = mysql_query("select password from users where username='{$username}'");
	
	if (mysql_num_rows($result) != 0) {	
		list($password) = mysql_fetch_row($result);
		// check that passwords match
		if ($pass == $password) {    				
			$ret = true;
		}
	}
	return $ret;
}

function change_password($username, $newpass)
{
	$newval = md5($newpass);
	if (! mysql_query("UPDATE users SET password = '{$newval}' where username='{$username}'")) {
		die("problem updating admin password, " . $mysql_error());
	}
}

function logout_user()
{
	setcookie('username', $username, time()-3600, '', $GLOBALS[domain]);
	setcookie('password', $password, time()-3600, '', $GLOBALS[domain]);
}
?>