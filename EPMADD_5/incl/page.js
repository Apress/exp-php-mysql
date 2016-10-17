function trim(str) {
    return str.replace(/^\s+|\s+$/g,"");
}

function DeleteConfirm(name, pk) {
    if (confirm('Delete ' + name + '?')) {
        event.preventDefault();
        window.location = document.URL +
          '?action_delete=1&pk=' + pk;
    }
}

// for FK only
	
function ClearField(id) {
	$('#' + id).val('');
	$('#' + id + '_label').val('');
}

function ChooseSpecialty(id) {
    window.open("specialty.php?choose=yes&id=" + id, "_blank",
      "height=600, width=800, top=100, left=100, tab=no, " +
      "location=no, menubar=no, status=no, toolbar=no", false);
}

function MadeChoice(id, result, label) { // executes in popup
    window.opener.HaveChoice(id, result, label);
    window.close();
}

function HaveChoice(id, result, label) { // executes in main window
    $('#' + id).val(result);
    $('#' + id + '_label').val(label);
}

