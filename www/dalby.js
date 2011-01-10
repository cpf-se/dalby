
function initLogin() {
	var year  = document.getElementById('year');
	var month = document.getElementById('month');
	var day   = document.getElementById('day');
	var useq  = document.getElementById('useq');

	year.disabled = false;
	year.focus();
	if (year.value.length > 0) {
		month.disabled = false;
		year.blur();
		month.focus();
	} else month.disabled = true;

	if (month.value.length > 0) {
		day.disabled = false;
		month.blur();
		day.focus();
	} else day.disabled = true;

	if (day.value.length > 0) {
	       	useq.disabled = false;
		day.blur();
		useq.focus();
	} else useq.disabled = true;
}

function selectionInsert(sel, text, value) {
	if (sel.length == 0) {
		var opt = new Option(text, value);
		sel.options[0] = opt;
		sel.selectedIndex = 0;
	} else if (sel.selectedIndex != -1) {
		var selText   = new Array();
		var selValues = new Array();
		var selIsSel  = new Array();
		var count     = -1;
		var selected  = -1;
		var i;
		for (i = 0; i < sel.length; i++) {
			count++;
			if (count == sel.selectedIndex) {
				selText[count]   = text;
				selValues[count] = value;
				selIsSel[count]  = false;
				count++;
				selected = count;
			}
			selText[count]   = sel.options[i].text;
			selValues[count] = sel.options[i].value;
			selIsSel[count]  = sel.options[i].selected;
		}
		for (i = 0; i <= count; i++) {
			var opt = new Option(selText[i], selValues[i]);
			sel.options[i] = opt;
			sel.options[i].selected = selIsSel[i];
		}
	}
}

function selectionAppend(sel, text, value) {
	if (sel.length == 0) {
		var opt = new Option(text, value);
		sel.options[0] = opt;
		sel.selectedIndex = 0;
	} else if (sel.selectedIndex != -1) {
		var selText   = new Array();
		var selValues = new Array();
		var selIsSel  = new Array();
		var count     = -1;
		var selected  = -1;
		var i;
		for (i = 0; i < sel.length; i++) {
			count++;
			selText[count]   = sel.options[i].text;
			selValues[count] = sel.options[i].value;
			selIsSel[count]  = sel.options[i].selected;
			if (count == sel.selectedIndex) {
				count++;
				selText[count]   = text;
				selValues[count] = value;
				selIsSel[count]  = false;
				selected = count - 1;
			}
		}
		for (i = 0; i <= count; i++) {
			var opt = new Option(selText[i], selValues[i]);
			sel.options[i] = opt;
			sel.options[i].selected = selIsSel[i];
		}
	}
}

function selectionRemove(sel) {
	var selIndex = sel.selectedIndex;
	if (selIndex != -1) {
		for (i = sel.length - 1; i >= 0; i--) {
			if (sel.options[i].selected) {
				sel.options[i] = null;
			}
		}
		if (sel.length > 0) {
			sel.selectedIndex = selIndex == 0 ? 0 : selIndex - 1;
		}
	}
}

function selectionClear(sel) {
	while (sel.selectedIndex != -1) {
		selectionRemove(sel);
	}
}

function yearChanged() {
	var year  = document.getElementById('year');
	var month = document.getElementById('month');
	year.blur();
	month.disabled = false;
	month.focus();
}

function monthChanged() {
	var month = document.getElementById('month');
	var year  = document.getElementById('year');
	var m     = month.options[month.selectedIndex].value;
	var y     =  year.options[ year.selectedIndex].value;
	if (m == 1 || m == 3 || m == 5 || m == 7 || m == 8 || m == 10 || m == 12) days = 31;
	else if (m == 4 || m == 6 || m == 9 || m == 11) days = 30;
	else if (y % 400 == 0 || (y % 100 != 0 && y % 4 == 0)) days = 29;
	else days = 28;

	var day = document.getElementById('day');
	var d;
	selectionClear(day);
	selectionInsert(day, '', '');
	for (d = days ; d > 0 ; --d) {
		selectionAppend(day, d, d);
	}
	day.disabled = false;
	month.blur();
	day.focus();
}

function dayChanged() {
	var useq = document.getElementById('useq');
	useq.disabled = false;
	this.blur();
	useq.focus();
}

