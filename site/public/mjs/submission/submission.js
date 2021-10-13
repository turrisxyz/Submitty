import * as bulkUpload from './bulk-upload.js';

function init(){
	//load use's session settings
	if(sessionStorage.getItem(`radio-student-${gradeable_id}`)){
		bulkUpload.hideBulkUploadSettings();
		toggleSubmitForStudent(true);
	}else if(sessionStorage.getItem(`radio-bulk-${gradeable_id}`)){
		bulkUpload.openBulkUploadSettings();
	}else{
		bulkUpload.hideBulkUploadSettings();
	}

	document.getElementsByName('submission_type')
		.forEach(radio_btn => radio_btn.addEventListener('change', changeSubmissionMode));
}

/**
 * Delete all entries in the global file_array and remove any files on the submission box
 */ 
export function clearFileTable(){
	file_array = [];
	for(let box_idx = 1; box_idx <= num_submission_boxes; box_idx++){
		document.getElementById('file-upload-table-' + box_idx).innerHTML = "";
	}
}


/**
 * Toggle displaying the submit for a student mode and save the option to session storage
 * @param {Boolean} enabled - show or hide submission mode
 */
export function toggleSubmitForStudent(enabled){
	document.getElementById('user-id-input').style.display = enabled ? 'block' : 'none';
	document.getElementById('radio-student').checked = enabled;
	if(enabled){
		sessionStorage.setItem(`radio-student-${gradeable_id}`, true);
	}else{
		sessionStorage.removeItem(`radio-student-${gradeable_id}`);
	}
}


/**
 * handle switching between normal, submit for student, and bulk upload modes
 * @param {Event} event - handle change event for submission radio buttons
 */
function changeSubmissionMode(event){
	switch(event.target.id){
		case 'radio-normal':
			bulkUpload.hideBulkUploadSettings();
			loadPreviousFilesOnDropBoxes();
			toggleSubmitForStudent(false);
			break;
		case 'radio-student':
			bulkUpload.hideBulkUploadSettings();
			clearFileTable();
			toggleSubmitForStudent(true);
			break;
		case 'radio-bulk':
			clearFileTable();
			toggleSubmitForStudent(false);
			bulkUpload.openBulkUploadSettings();
	}
}

document.addEventListener('DOMContentLoaded', () => init());
