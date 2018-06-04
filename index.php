<?php
include 'user.php';

init_db();

if (validate_user_session()) {
	header('Location: galleries.html');	
} else {
	header('Location: login.html');	
}
?>