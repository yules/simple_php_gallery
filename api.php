<?php 
/**
 * complete CMS API
 * created by: Yuval Leshem
 * yuval.leshem@gmail.com
 * http://simplegalleryma.sourceforge.net/
**/

include 'user.php';
include 'utils.php';

init_db();

cacheFix();

// REST redirection
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
	case 'GET':
		do_get();  
		break;
	case 'POST':
		if (!validate_user_session()) {
			header('HTTP/1.1 403 Forbidden');
		} else {
			do_post();
		}		
		break;	
}

function do_get() {
	// try and get some parameters:
	$id = $_GET['id']; // item id
	$tag_id = $_GET['tag_id']; // item tag id
	$count = $_GET['count']; // max answers
	
	// see which query to perform
	// tags (all)
	if (isset($_GET['tags'])) {		
		$ret = get_tags();
	}
	// galleries (all/ by id/ by tag)
	if (isset($_GET['galleries'])) {
		$ret = get_gallery($id, $tag_id, $count);
	}
	// gallery photos (by gallery id)
	if (isset($_GET['gallery_details'])) {
		$ret = get_gallery_details($id);
	}
	// news (all/ by tag)
	if (isset($_GET['news'])) {
		$ret = get_news($id, $tag_id, $count);
	}
	echo json_encode($ret);
}

function do_post() {	
	// new/ replace image (gallery thumbnail or photo)
	if (isset($_FILES['photo'])) {
		upload_photo();
	}
	// news file attachment
	if (isset($_FILES['attachment'])) {
		upload_news_attachment();
	}
	// new category
	else if (isset($_POST['tags'])) {
		new_tag($_POST['name']);
	}
	// new item (gallery/ news)
	else if (isset($_POST['type'])) {
		new_item($_POST['type']);
	}
	// edit gallery/ photo/ news item details
	else if (isset($_POST['edit'])) {
		edit_item($_POST['edit']);
	}
	// move gallery/ image/ news
	else if (isset($_POST['replace'])) {
		move_item($_POST['replace']);
	}
	
	else if (isset($_POST['todelete'])) {
		do_delete($_POST['todelete']);
	}	
}

function do_delete($type) {
	$id=intval($_POST['id']);
	
	// check item type
	switch($type) {
		case 'galleries':
			// delete gallery related photos
			dosql("DELETE FROM images WHERE gallery_id={$id}");
			// delete item and related categories
			dosql("DELETE FROM galleries WHERE id={$id}");
			dosql("DELETE FROM tags_items WHERE item_type=1 AND item_id={$id}");
			// delete gallery photo directory			
			rrmdir($id);
			delete_tags_from_item($id, $type);		
		break;
		case 'news':
			
		// delete item and related tags		
			dosql("DELETE FROM news WHERE id={$id}");
			dosql("DELETE FROM tags_items WHERE item_type=2 AND item_id={$id}");	
			delete_tags_from_item($id, $type);					
		break;
		case 'images':
			// find photo file path
			$result = mysql_query("SELECT path FROM images where id={$id}");
			list($path) =  mysql_fetch_row($result);
			// delete it		
			unlink($path);
			// delete item
			dosql("DELETE FROM images WHERE id=$id");
		break;
		case 'tags':
			// delete related tags_items entries
			dosql("DELETE FROM tags_items WHERE tag_id=$id");
			// delete item
			dosql("DELETE FROM tags WHERE id=$id");		
		break;		
	}
}

// get functionality
function get_tags() {
	$query = 'select * from tags';
	return getsql($query);
}

function get_item($table, $id, $tag_id, $count) {
	$query = 'SELECT * from ' . $table;
	
	if (isset($id)) {
		$query .= ' WHERE id=' . $id;
	}
	if (isset($tag_id)) { // more complicated query
		// use join ???
		//$query = 'SELECT * FROM ' . $table . ' WHERE ';
		$query = 'SELECT * FROM $table WHERE id IN (select item_id from tags_items where tag_id=';
		$query .= '$tag_id)';
	}
	
	$query .= ' ORDER BY order_id';
	
	if (isset($count)) {
		$query .= ' LIMIT 0, $count';
	}
	
	return getsql($query);
}

function get_gallery($id, $tag_id, $count) {
	return get_item('galleries', $id, $tag_id, $count);
}

function get_news($id, $tag_id, $count) {
	return get_item('news', $id, $tag_id, $count);
}

function get_gallery_details($id) {
	$query = 'SELECT * from images WHERE gallery_id = ' . $id;
	$query .= ' ORDER by order_id';
	return getsql($query);	
}

// POST functionality
function new_tag($name) {
	$query = "INSERT INTO tags SET name='$name'";
	dosql($query);	
}

function new_item($item) {		
	$query = "INSERT INTO ";
	switch($item) {
		case 'galleries':
			$url = sql_safe($_POST['url']);			
			$query .= "galleries SET url='$url', ";
		break;
		case 'news':			
			$query .= 'news SET ';			
		break;
	}
	
	$name = sql_safe($_POST['name']);
	$description = sql_safe($_POST['description']);
	
	$query .= " name='$name', description='$description'";
	
	dosql($query);
	
	$id = mysql_insert_id();
	// insert order_id
	$query = "UPDATE {$item} SET order_id = {$id} where id = {$id}";
	
	dosql($query);
	// now get insert id and add the tags
	if (isset($_POST['tags'])) {		
		// add tags
		insert_tags_for_item(split(',',  $_POST['tags']), $id, $item);
	}
}

function insert_tags_for_item($tags, $id, $type) {
	foreach ($tags as $tagid) {		
		$typeid = get_type_id($type);
		if (is_int($tagid)) {
			$query = "INSERT INTO tags_items SET item_id=$id, tag_id=$tagid, item_type=$typeid";
			dosql($query);
		}
	}
}

function get_type_id($type) {
	switch($type) {
		case 'galleries':
			$typeid = 1;
		break;
		default: // news
			$typeid = 2;
		break;
	}
	return $typeid;
}

function edit_item($item) {	
	$query = "UPDATE ";
			
	switch($item) {
		case 'galleries':
			$query .= 'galleries ';
			
			$url = sql_safe($_POST['url']);
			$query .= "SET url = '$url', ";			
		break;
		case 'news':
			$query .= 'news SET ';
			
		break;
		case 'image': // images do not have tags...
			$query .= 'images SET';			
		break;
	}
		
	$name = sql_safe($_POST['name']);
	$description = sql_safe($_POST['description']);
	$id = $_POST['id'];
	
	$query .= " name = '$name', description = '$description' WHERE id = $id";
	dosql($query);
	
	// now update tags, delete and re-set.
	if ($_POST['tags']) {
		delete_tags_from_item($id, $item);		
		insert_tags_for_item(split(',',  $_POST['tags']), $id, $item);
	}
}


function delete_tags_from_item($id, $type) {
	$typeid = get_type_id($type);
	$query = 'DELETE FROM tags_items WHERE item_id=$id AND item_type=$typeid';
	dosql($query);
}

function move_item($type) {
	switch ($type) {
		case 'galleries':
			$table = 'galleries';
		break;
		case 'news':
			$table = 'news';
		break;
		case 'images':
			$table = 'images';
		break;
	}
	// all we need to do is update order_ids
	$first = intval($_POST['first']);
	$second = intval($_POST['second']);
	// swap = 3 queries. sucks...
	
	dosql("UPDATE {$table} set order_id = -1 where order_id= {$first}");
	dosql("UPDATE {$table} set order_id = {$first} where order_id= {$second}");
	dosql("UPDATE {$table} set order_id = {$second} where order_id= -1");
	
}

function delete_news_attachment($id) {
	// see if item has attachment
	$query = "select attachment_path from news where id={$id}";
	$result = getsql($query);
	
	$path = $result[0]['attachment_path'];
	echo $path;
	
	if ($path != null) {
		// delete file
		unlink($path);
		// set link to null
		dosql("UPDATE news SET attachment_path=null WHERE id={$id}");
	}
}

function upload_news_attachment() {
	$id = $_POST['id'];
	delete_news_attachment($id);

	// get file name
	$origfile = $_FILES['attachment']['tmp_name'];
	$origname = $_FILES['attachment']['name'];
	
	if (!file_exists('news')) {
		mkdir('news');
	}
	// dest name is [time].[extension]
	$destpath = 'news/' . time() . '.' . getExtension($origname);
	
	copy($origfile, $destpath);
	// update path
	dosql("UPDATE news SET attachment_path='{$destpath}' WHERE id={$id}");	
}

// upload/ replace photo
function upload_photo() {	
	// should have galleryId as well!
	$gallery_id = $_POST['gallery_id'];	    	
	// set destination path
	$origfile = $_FILES['photo']['tmp_name'];
	$origname = $_FILES['photo']['name'];
						
	$destname = time() . '.' . getExtension($origname);
	$destpath = $gallery_id . '/' . $destname;
	// check existence or create a directory
	if (!file_exists($gallery_id)) {
		mkdir($gallery_id);
	}		
	
	// copy image to path, resize to height global (in config.php)
	resize_image($origfile, $origname, 0, $destpath);

	// see which mode we are and update db according
	switch ($_POST['type']) { 
	// new upload
		case 'new':
			$name = sql_safe($_POST['name']);
			$description = sql_safe($_POST['description']);
			dosql("INSERT INTO images SET name='$name', gallery_id=$gallery_id,	path='$destpath', description='$description'");
			// get insert id, update order_id
			$order_id = mysql_insert_id();
			$query = "UPDATE images SET order_id={$order_id} WHERE id={$order_id}";
			dosql($query);
		break;
	// replace photo
		case 'replace':
			$id = $_POST['id'];
			dosql("UPDATE images SET path='$destpath' WHERE id=$id");
		break;
	// gallery thumbnail
		case 'thumbnail':
			dosql("UPDATE galleries SET thumb_path='$destpath' WHERE id=$gallery_id");
		break;
	}
}
