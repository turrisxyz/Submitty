import  * as submission from './submission.js';

let bulkUploadOptions = {};
let bulkUploadLabels = {};

function init(){
	document.getElementById('submit').addEventListener('click', handleBulkUpload);

	['num-pages', 'use-qr', 'use-ocr', 'qr-prefix', 'qr-suffix']
		.forEach(id => bulkUploadOptions[id] = document.getElementById(id) );

	['prefix-prompt', 'pages-prompt', 'use-ocr-id']
		.forEach(id => bulkUploadLabels[id] = document.getElementById(id));

	bulkUploadOptions['use-qr'].addEventListener('change', (event) => {
		if(event.target.checked){
			sessionStorage.setItem(`use-qr-${gradeable_id}`, true);
		}else{
			sessionStorage.removeItem(`use-qr-${gradeable_id}`);
			bulkUploadOptions['use-ocr'].checked = event.target.checked;
		}

		bulkUploadLabels['use-ocr-id'].style.display = event.target.checked ? 'inline-block' : 'none';
		bulkUploadOptions['use-ocr'].style.display = event.target.checked ? 'inline-block' : 'none';
	});


	bulkUploadOptions['use-ocr'].addEventListener('change', (event) => {
		if(event.target.checked){
			sessionStorage.setItem(`use-ocr-${gradeable_id}`, true);
		}else{
			sessionStorage.removeItem(`use-ocr-${gradeable_id}`);
		}

		bulkUploadOptions.checked = event.target.checked;
	})
}


/**
 * collect bulk upload parameters from user 
 * called when user presses submit button
 */ 
function handleBulkUpload(){
	if(!document.getElementById('radio-bulk').checked){
		return;
	}

    handleBulk(
    	gradeable_id, max_file_size, max_post_size, 
    	bulkUploadOptions['num-pages'].value, 
    	bulkUploadOptions['use-qr'].checked, 
    	bulkUploadOptions['use-ocr'].checked, 
    	bulkUploadOptions['qr-prefix'].value, 
    	bulkUploadOptions['qr-suffix'].value
    );
}


export function openBulkUploadSettings(){
	document.getElementById('pdf-submit-button').style.display = 'block';
	bulkUploadOptions['use-qr'].style.display = 'inline-block';

	if(sessionStorage.getItem(`use-qr-${gradeable_id}`) && false){
		openBulkQRUploadSettings();
	}else{
		openBulkNumericUploadSettings();
	}
}


export function hideBulkUploadSettings(){
	Object.values(bulkUploadOptions).forEach(element => element.style.display = 'none');
	Object.values(bulkUploadLabels).forEach(element => element.style.display = 'none');
	document.getElementById('pdf-submit-button').style.display = 'none';
}


function openBulkQRUploadSettings(){
	['qr-prefix', 'qr-suffix', 'use-ocr'].forEach(id => bulkUploadOptions[id].style.display = 'inline-block');

	bulkUploadOptions['use-qr'].checked = true;
	bulkUploadOptions['use-ocr'].checked = sessionStorage.getItem(`use-ocr-${gradeable_id}`);
	if(bulkUploadOptions['use-ocr'].checked){
		bulkUploadOptions['qr-prefix'].value = sessionStorage.getItem(`qr-prefix-${gradeable_id}`);
		bulkUploadOptions['qr-suffix'].value = sessionStorage.getItem(`qr-suffix-${gradeable_id}`);
	}

	sessionStorage.setItem(`use-qr-${gradeable_id}`, true);
}


function openBulkNumericUploadSettings(){
	bulkUploadOptions['num-pages'].style.display = 'block';
}


document.addEventListener('DOMContentLoaded', () => init());

