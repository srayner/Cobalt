function FileDragHover(e) {
	    e.stopPropagation();
	    e.preventDefault();
	    e.target.className = (e.type === "dragover" ? "hover" : "");
            console.log('ok');
        }


function FileSelectHandler(e) {

	// cancel event and hover styling
	FileDragHover(e);

	// fetch FileList object
	var files = e.target.files || e.dataTransfer.files;

	// process all File objects
	for (var i = 0, f; f = files[i]; i++) {
		//ParseFile(f);
		UploadFile(f);
	}

}

// upload JPEG files
function UploadFile(file) {

    console.log('upload');
    var xhr = new XMLHttpRequest();
    if (xhr.upload && file.type == "image/jpeg" && file.size <= 300000) {
            
        // start upload
        xhr.open("POST", document.getElementById("upload").action, true);
	xhr.setRequestHeader("X_FILENAME", file.name);
	xhr.send(file);
    }
}

$(document).ready(function() {

    // Sticky tabs
    $('#tabs a').click(function (e) {
        e.preventDefault();
        $(this).tab('show');
    });
    
    $('#tabs').stickyTabs();
    
    // Monitoring checkboxes
    $('input[type=checkbox]').change(function() {

        var id = $(this).val();
        var check = $(this).prop('checked');
        console.log(check);
        var url = '/network-adapter/monitor/' + id;
        data = {
            'id': id,
            'monitor' : check
        };
        $.post(url, data);
    });


    filedrag = document.getElementById('filedrag');
    filedrag.addEventListener("dragover", FileDragHover, false);
    filedrag.addEventListener("dragleave", FileDragHover, false);
    filedrag.addEventListener("drop", FileSelectHandler, false);
            
    
});