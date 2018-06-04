var tags;
var items;
var galleryDetails;
var uploading = false;

$(document).ready(function() {

	$('#newButton').click(function() {
		showNewItemForm();
	});

	var galleryId = getParam('id');
	// get gallery details (incl. photos)
	getJson('galleries=true&id=' + galleryId, galleryLoaded);
});

function galleryLoaded(data) {
	galleryDetails = data[0];
	// present gallery details
	var detailsHtml = '';
	/*if (galleryDetails.thumb_path != '') {
		detailsHtml = detailsHtml + '<div><img height="100" src="' + galleryDetails.thumb_path + '"></img>';
	}*/
	detailsHtml = detailsHtml + '<h2>' + galleryDetails.name + '</h2>';/* + 
					  '<h3>' + galleryDetails.description + '</h3>' +
					  '<div>' + galleryDetails.url + '</div><br/>';*/
	
	
	$('#galleryDetails').html(detailsHtml);
	// get gallery photos
	getJson('gallery_details=true&id=' + galleryDetails.id, photosLoaded);
}

function photosLoaded(data) {
	$('#newItems').removeClass('hide');
	
	items = data;
	var html = '';
	var prev = 0;
	for (var i=0; i<items.length; i++) {				
		html = html + prepareItemHtml(items[i], prev);	
		prev = items[i].order_id;		
	}
	
	$('#content').html(html);
}

// prepares item html string
function prepareItemHtml(item, prev) {
	var itemHtml = '<div class="item" id="item' + item.id + '">' +
	// image	
	'<div class="itemcontent"><img height="100" src="' + item.path + '"></img>' +
	// replace img button
	'<div id="replace' + item.id + '"><button onclick="showReplaceImageForm(' + item.id + ',\'replace' + item.id + '\');">Replace Image</button></div></div>' +
	// name
	'<div id="name'+ item.id + '" class="itemcontent itemcontenteditable" onclick="editItem(' + item.id + ', \'name\');">' + item.name + '</div>' +
	// description
	'<div id="description' + item.id + '" class="itemcontent itemcontenteditable" onclick="editItem(' + item.id + ', \'description\');">' + nl2br_js(item.description) + '</div>' +
		
	// no tags for now.		
	// edit gallery content
	'<div class="itempanel" id="buttonspanel' + item.id + '">' +
	
	// save
	'<button id="save' + item.id + '"disabled=true onclick="javascript:saveItem(' + item.id + ');">save</button>' +
	
	// delete item
	'<button onclick="javascript:deleteItem(' + item.id + ',\'images\')";>delete</button>';
	// 'up'	
	if (prev != 0) {
		itemHtml = itemHtml  + '<button onclick="javascript:changeOrder(' + item.order_id + ', ' + prev + ',\'images\')";>up</button>';
	}
	
	itemHtml = itemHtml + '</div></div>';
	return itemHtml;
}

function editItem(id, toedit) {
	var item = getItemById(id);
	// put text inputs in name and description divs
	$('#name' + id).html('<textarea id = "nameinput' + id +'" rows="1" wrap="physical" cols="50">' + item.name + '</textarea>');
	$('#description' + id).html('<textarea id = "descriptioninput' + id + '"  rows="8" wrap="physical" cols="50">' + item.description + '</textarea>'); 
	// focus on relevant control
	$('#' + toedit + 'input' + id).focus();
	
	// enable save button
	$('#save' + id).removeAttr('disabled');
	// add cancel button
	$('#buttonspanel' + id).append($('<button onclick="javascript:cancelEdit(' + id + ');" id="cancel' + id + '">cancel</button>'));
	// remove editable class
	$('#name' + id).removeClass('itemcontenteditable');
	$('#description' + id).removeClass('itemcontenteditable');
	
	// remove onclick handler
	$('#name' + id).prop('onclick', null);
	$('#description' + id).prop('onclick', null);
	
}

function cancelEdit(id) {
	var item = getItemById(id);	
	// add content
	$('#name' + id).html(item.name);
	$('#description' + id).html(item.description);
	
	// remove cancel button
	$('#cancel' + id).remove();
	// disable save
	$('#save' + id).attr('disabled', 'true');
	// bring back classes and handlers to name and content
	// remove editable class
	$('#name' + id).addClass('itemcontenteditable');
	$('#description' + id).addClass('itemcontenteditable');
	
	// remove onclick handler
	$('#name' + id).attr('onclick', 'editItem(' + item.id + ', \'name\');');
	$('#description' + id).attr('onclick', 'editItem(' + item.id + ', \'description\');');	
	
}

function saveItem(itemid) {
	// get contents
	var newName = $('#nameinput' + itemid).val();
	var newDescription = $('#descriptioninput' + itemid).val();
	
	$.ajax({
		url : 'api.php',
		type: 'POST',
		
		data: {
			edit : 'image',			
			name : newName,			
			description : newDescription,
			id : itemid			
		},
		success: function() {			
			location.reload(true);
		}, 
		error: function() {
			alert('err');
		}
		
	});
}

function showNewItemForm() {
	var formHtml = '<form action="api.php" method="POST" enctype="multipart/form-data" target="uploadTarget">' +
					'name:<br/><textarea rows="1" cols="50" name="name" id="name"/><br/>' +
					'description:<br/><textarea rows="5" cols="50" name="description" id="description"/><br/>' + 					
					'<label for="photo">Choose a file and press upload</label><br/>' +
					'<input type="file" name="photo" id="photo"/><br/>' +
					//generatePhotoInputHtml() +
					'<input type="hidden" name="gallery_id" id="gallery_id" value="' + galleryDetails.id + '" />' +
					'<input type="hidden" name="type" id="type" value="new"/>' +
					'<input type="submit" value="upload" />' +
					'</form>';
	uploading = true;	
	
	$('#newItemDiv').html(formHtml);
}

function showReplaceImageForm(id, divId) {
	var formHtml = '<form action="api.php" method="POST" enctype="multipart/form-data" target="uploadTarget">' +
					'<label for="photo">Choose a file and press upload</label><br/>' +
					'<input type="file" name="photo" id="photo"/><br/>' +
					//generatePhotoInputHtml() +
					'<input type="hidden" name="id" id="id" value="' + id + '" />' +
					'<input type="hidden" name="gallery_id" id="id" value="' + galleryDetails.id + '" />' +
					'<input type="hidden" name="type" id="type" value="replace"/>' +
					'<input type="submit" value="replace" />' +
					'</form>';
	uploading = true;	

	$('#' + divId).html(formHtml);
}

