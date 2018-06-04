<?php
/**
 * created by: Yuval Leshem
 * yuval.leshem@gmail.com
 * http://simplegalleryma.sourceforge.net/
**/
include 'user.php';

$redirect = 'index.php';

if (isset($_POST['username']) && isset($_POST['password'])) {
	init_db();
	
	$username = $_POST['username'];
	$password = md5($_POST['password']);
	
	if (validate_user($username, $password)) {
		if (isset($_POST['newpass'])) { // change password
			$newpass = $_POST['newpass'];
			change_password($username, $newpass);
			
			logout_user();	
		} else { // login
			$time = false;
			if (isset($_POST['rememberme'])) {
				// Set cookie to last 2 weeks 
				$time = time()+60*60*24*14;			
			}			
			setcookie('username', $username, $time, '', $GLOBALS[domain]);
			setcookie('password', $password, $time, '', $GLOBALS[domain]);
		}
	} else {
		$redirect = 'login.html?invalid=true';
		
	}
} else { // logout action assumed
	logout_user();		
}

header('Location: ' . $redirect);		

?>


