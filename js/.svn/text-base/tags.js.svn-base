var items;

$(document).ready(function() {
	// new Item button
	$('#newButton').click(function() {
		showNewItemForm();
	});
	
	getJson('tags=true', itemsLoaded);
});


function itemsLoaded(data) {
	items = data;
	var html = '';
	var prev = 0;
	for (var i=0; i<items.length; i++) {				
		html = html + prepareItemHtml(items[i]);			
	}
	
	$('#content').html(html);
}

// prepares item html string
function prepareItemHtml(item) {
	var itemHtml = '<div class="item" id="item' + item.id + '">' +
	// name
	'<div id="name'+ item.id + '" class="itemcontent itemcontenteditable" onclick="editItem(' + item.id + ',\'name\');">' + item.name + '</div>' +
	
	'<div class="itempanel" id="buttonspanel' + item.id + '"><button id="save' + item.id + '"disabled=true onclick="javascript:saveItem(' + item.id + ');">save</button>' +
	
	// delete item
	'<button onclick="javascript:deleteItem(' + item.id + ',\'tags\')";>delete</button>';
		
	itemHtml = itemHtml + '</div></div>';
	return itemHtml;
}

function editItem(id, toedit) {
	var item = getItemById(id);
	// put text inputs in name and description divs
	$('#name' + id).html('<textarea id = "nameinput' + id +'" rows="1" wrap="physical" cols="50">' + item.name + '</textarea>');

	// focus on relevant control
	$('#' + toedit + 'input' + id).focus();
	// enable save button
	$('#save' + id).removeAttr('disabled');
	// add cancel button
	$('#buttonspanel' + id).append($('<button onclick="javascript:cancelEdit(' + id + ');" id="cancel' + id + '">cancel</button>'));
	// remove editable class
	$('#name' + id).removeClass('itemcontenteditable');
	// remove onclick handler
	$('#name' + id).prop('onclick', null);

}

function cancelEdit(id) {
	var item = getItemById(id);	
	// add content
	$('#name' + id).html(item.name);
	
	// remove cancel button
	$('#cancel' + id).remove();
	// disable save
	$('#save' + id).attr('disabled', 'true');
	// bring back classes and handlers to name and content
	// remove editable class
	$('#name' + id).addClass('itemcontenteditable');
	
	// remove onclick handler
	$('#name' + id).attr('onclick', 'editItem(' + item.id + ',\'name\');');
	
}

function saveItem(itemid) {
	// get contents
	var newName = $('#nameinput' + itemid).val();
	
	$.ajax({
		url : 'api.php',
		type: 'POST',
		
		data: {
			edit : 'tags',			
			name : newName,			
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
	$('#newItemDiv').html('<div>tag:<br/><textarea rows="1" cols="50" id="newName"></textarea><br/>' +  	
						  '<button onclick="javascript:newItem();">Save</button></div>');
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
		
	$.ajax({
		url : 'api.php',
		type: 'POST',
		
		data: {
			tags : 'true',			
			name : newName,
			
		},
		success: function() {			
			location.reload(true);
		}, 
		error: function() {
			alert('err');
		}
		
	});
}
