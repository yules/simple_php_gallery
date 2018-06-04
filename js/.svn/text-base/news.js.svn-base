var tags;
var items;

$(document).ready(function() {
	// new Item button
	$('#newButton').click(function() {
		showNewItemForm();
	});
	getJson('news=true', itemsLoaded);
	// GET all tags - deprecated for now
//	getJson('tags=true', tagsLoaded);
});

function tagsLoaded(data) {
	tags = data;	
	// GET all news items
	getJson('news=true', itemsLoaded);
}

function itemsLoaded(data) {
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
	// name
	'<div id="name'+ item.id + '" class="itemcontent itemcontenteditable" onclick="editItem(' + item.id + ',\'name\');">' + item.name + '</div>' +
	// description
	'<div id="description' + item.id + '" class="itemcontent itemcontenteditable" onclick="editItem(' + item.id + ',\'description\');">' + nl2br_js(item.description) + '</div>';
	// no tags for now.		
	// attachment
	if (item.attachment_path != '') {
		itemHtml = itemHtml + '<div class="itemcontent"><a href="' + item.attachment_path + '">' + item.attachment_path + '</a></div>';
	}	
	itemHtml = itemHtml + '<div id="attachment' + item.id + '"><button onclick="javascript:showUploadAttachmentForm(' + item.id + ',\'attachment' + item.id + '\');">Upload Attachment</button></div></div>' +
	// save
	'<div class="itempanel" id="buttonspanel' + item.id + '"><button id="save' + item.id + '"disabled=true onclick="javascript:saveItem(' + item.id + ');">save</button>' +
	
	// delete item
	'<button onclick="javascript:deleteItem(' + item.id + ',\'news\')";>delete</button>';
	// 'up'	
	if (prev != 0) {
		itemHtml = itemHtml  + '<button onclick="javascript:changeOrder(' + item.order_id + ', ' + prev + ',\'news\')";>up</button>';
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
	$('#name' + id).attr('onclick', 'editItem(' + item.id + ',\'name\');');
	$('#description' + id).attr('onclick', 'editItem(' + item.id + ',\'description\');');	
}

function saveItem(itemid) {
	// get contents
	var newName = $('#nameinput' + itemid).val();
	var newDescription = $('#descriptioninput' + itemid).val();
	
		$.ajax({
		url : 'api.php',
		type: 'POST',
		
		data: {
			edit : 'news',			
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

function addTag(itemId, tagId) {

}

function removeTag(itemId, tagId) {

}

function showNewItemForm() {
	$('#newItemDiv').html('<div>name:<br/><textarea rows="1" cols="50" id="newName"></textarea><br/>' +  
						  'description:<br/><textarea rows="10" cols="50" id="newDescription"></textarea><br/>' + 
						  // tags code is commented for now.
						  //'<div id="newtags"></div>' + getTagsComboHtml('newtagscombo') + '<button onclick="javascript:addTagToItem(\'newtags\', \'newtagscombo\')">Add Tag</button><br/>' +
						  '<button onclick="javascript:newItem();">Save</button></div>' + 
						  '');
}

// add tag + delete buttons to tag item
function addTagToItem(itemTagsDivId, comboId) {
	// get tag name
	var tagId = $('#' + comboId).val();
	var tagName = getTagById(tagId);
	var tagHtml = '<div>' + tagName + '<button onclick="javascript:removeTagFromItem(' + tagId + ');">X</button>'+ '</div>';
	$('#' + itemTagsDivId).append($(tagHtml));
}

function newItem() {
	var newName = $('#newName').val();
	var newDescription = $('#newDescription').val();
	
	$.ajax({
		url : 'api.php',
		type: 'POST',
		
		data: {
			type : 'news',			
			name : newName,
			description : newDescription
		},
		success: function() {			
			location.reload(true);
		}, 
		error: function() {
			alert('err');
		}
		
	});
}

function showUploadAttachmentForm(itemId, divId) {
	var formHtml = '<form action="api.php" method="POST" enctype="multipart/form-data" target="uploadTarget">' +
					'<label for="attachment">Choose a file attachment and press upload</label><br/>' +
					'<input type="file" name="attachment" id="attachment"/><br/>' +
					'<input type="hidden" name="id" id="id" value="' + itemId + '" />' +					
					'<input type="submit" value="upload" />' +
					'</form>';
	uploading = true;	

	$('#' + divId).html(formHtml);
}