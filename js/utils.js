// convert \n to <br/> tag
function nl2br_js(s){
	return s.replace( /\n/g, '<br />\n' );
}

// get request param
function getParam (name){
    name = name.replace(/[\[]/,"\\\[").replace(/[\]]/,"\\\]");
    var regexS = "[\\?&]"+name+"=([^&#]*)";
    var regex = new RegExp( regexS );
    var results = regex.exec( window.location.href );
    if( results == null ) {
        return null;
    }
    else {
        return results[1];
    }
}	

function refresh() {
	if (uploading) {
		location.reload(true);
	}
}

// Management API reqests and posts

// get json from server, according to parameters
function getJson(params, callback) {
	$.ajax({
			url: 'api.php?' + params,
			dataType: 'json',
			success: function(data) {callback(data);}
		});
}

// delete item (id/ item type)
function deleteItem(itemId, type) {
		$.ajax({
		url : 'api.php',
		type: 'POST',
		
		data: {
			todelete : type,				
			id : itemId
		},
		success: function() {			
			location.reload(true);
		}, 
		error: function() {
			alert('err');
		}
		
	});
}

// change items order
function changeOrder(firstOrderId, secondOrderId, type) {
	
	$.ajax({
		url : 'api.php',
		type: 'POST',
		
		data: {
			replace : type,			
			first : firstOrderId,
			second : secondOrderId
		},
		success: function() {			
			location.reload(true);
		}, 
		error: function() {
			alert('err');
		}
		
	});
}

// tags operations
function getTagById(id) {
	var ret = '';
	for (var i=0; i<tags.length; i++) {
		if (tags[i].id = id) {
			ret = tags[i].name;
			break;
		}
	}
	return ret;
}

function getTagsComboHtml(name) {
	var html = '';
	
	html = '<select name="' + name +'" id="' + name + '" >';
	for (var i=0; i<tags.length; i++) {
		html = html + '<option value="' + tags[i].id + '">' + tags[i].name + '</option>';
	}
	html = html + '</select>';
		
	return html;
}

// find item by item id
function getItemById(id) {
	for (var i=0; i<items.length; i++) {
		if (items[i].id == id) return items[i];
	}
}



function doLogout() {
	window.location = 'userApi.php';
}

// customized file input box!
function generatePhotoInputHtml() {
	var html = '<div class="file_input_div">' +
				'<input type="button" id="fbutton" value="Browse" class="file_input_button"/>' +
				'<input type="file" class="file_input_hidden" name="photo" id="photo"  onmouseover="overinput();" onmouseout="outinput();"/>' +
				'</div>';
	
	return html;
}

function overinput() {
	$('#fbutton').addClass('file_input_button_hover');
}


function outinput() {
	$('#fbutton').removeClass('file_input_button_hover');
}
