var is_admin_user = false;
var is_mentor_user = false;

//Numeric only control handler
jQuery.fn.ForceNumericOnly =
function()
{
    return this.each(function()
    {
        $(this).keydown(function(e)
        {
            var key = e.charCode || e.keyCode || 0;
            // allow backspace, tab, delete, arrows, numbers and keypad numbers ONLY
            return (
                key == 8 || 
                key == 9 ||
                key == 46 ||
                (key >= 37 && key <= 40) ||
                (key >= 48 && key <= 57) ||
                (key >= 96 && key <= 105));
        })
    })
};

function progressDialogBox(loading){
    
	//console.log('XprogressDialogBox: loading = '+loading);
  	var progressContainer = $("#progressbarContainer");
	var progressbar = $("#progressbar");
	
	if(loading){
		
		// sms: updated on 6/2/2011
		// $('#progess-overlay').addClass("ui-widget-overlay");		
	
		$("#progressbarContainer").show();
		$("#progressbar").progressbar({value: 100});
		
		$("#progressbarContainer").each(function(){
			var container = $(window);
			var t = $(container).height();
			var l = $(container).width();
			
			var scrollTop = $(window).scrollTop();
			var scrollLeft = $(window).scrollLeft();
	
			
			var top = -50 + scrollTop;
			var left = -125 + scrollLeft;
			
			$(this).css('position', 'absolute').css({ 'margin-left': left + 'px', 'margin-top': top + 'px', 'left': '50%', 'top': '50%','z-index': '2000', 'width':'300px' });
			
			$(window).scroll(function () { 
				if($("#progressbarContainer")){
					var scrollTop = $(window).scrollTop();
					var scrollLeft = $(window).scrollLeft();
					var top = -50 + scrollTop;
					var left = -125 + scrollLeft;
					$("#progressbarContainer").css('position', 'absolute').css({ 'margin-left': left + 'px', 'margin-top': top + 'px', 'left': '50%', 'top': '50%' });
					//alert('scrollTop: '+scrollTop+' scrollLeft: '+scrollLeft);
				}
			});

			
		});
		
		
	}else{
		
		// sms: updated on 6/2/2011
		// $('#progess-overlay').removeClass("ui-widget-overlay");
		$(progressContainer).hide();
		$(progressbar).progressbar( "destroy" );
		
	}
}

function createInstantAppointmentDialogBox(username, course, type) {
	  
	$("#dialog").html("");
	var dialogContent = $("#dialog").load('forms/instant-appointment-form.html', function() {
		$("#dialog #hours").ForceNumericOnly();
		$("#dialog #minutes").ForceNumericOnly();
		// timepicker({  timeSeparator: ' hours and ' }); 
	
	});
	    
	$("#dialog").dialog({ 
		// autoOpen: false,
		width: 600,
		modal: true,
		title: "On-Demand Appointment",
		close: function() {
			$(this).dialog("close");
		},
		buttons: { 
			"Confirm": function() { 
				progressDialogBox(true);
				
				setTimeout(function(){	// Added: to allow the progress bar to appear.
					
					var newevent = getCreateNewEventObjFromInstantAppForm(username, course, type);
					if (scheduleAppointment(newevent, username)) {
						// sms: updated on 6/2/2011
						// var devaWasDisplayed = false;
						// for (var i=0; i<5; i++) {
							// devaWasDisplayed = getCurDevaInsInfo();
							// if (devaWasDisplayed) {
								// progressDialogBox(false);
								// var message = "Your virutal environment is almost ready! " +
								// 		"If after trying to connect to any of your virtual machines, " +
								// 		"you receive a message  box with the title 'Terminal Server " +
								// 		"Connection Error' you should wait for 20 seconds and try again! " +
								// 		"Thank you for your patience! ";
										
								// noticeDialog("On-Demand Appointment", message, "alert");
								// break;
							// }
						// }
						// progressDialogBox(false);
						// if (!devaWasDisplayed) { 
						// 	var message = "Your virutal environment was successfully scheduled, " +
						// 			"but cannot yet be displayed. Please wait for a couple " +
						// 			"of minutes, refresh the page, and try again.";
						// 	noticeDialog("On-Demand Appointment", message, "alert");
						// }
					} else {
						alert("Your requested appointment could not be scheduled!");
					}
					// sms: updated on 6/2/2011
					// progressDialogBox(false);
					$("#dialog").dialog("close"); 
					//$(this).dialog("close"); 
					
				}, 2000);	// wait 2 seconds

				// sms: updated 6/4/2011 added the below line
				interval = setInterval('getCurDevaInsInfo()', 10000);
			}, 
			"Cancel": function() { 
				//history.go(-1);
				window.location = $("#courseURL").val();
				$(this).dialog("close"); 
			} 
		} 
	});
	
	$("#dialog").dialog("open");
}

// sms: 6/28/2014 Added to support embedded version
function createInstantAppointmentEmbedded(username, encryptedPassword, course, type, hours, minutes) {
			                
	progressDialogBox(true);
				
	setTimeout(function(){	// Added: to allow the progress bar to appear.
					
		var neweventWithEncryptedPassword = getCreateNewEventObjFromInstantAppEmbedded(username, encryptedPassword, course, type, hours, minutes);
		if (scheduleAppointmentWithEncryptedPassword(neweventWithEncryptedPassword, username)) {
			// sms: updated on 6/2/2011
			// var devaWasDisplayed = false;
			// for (var i=0; i<5; i++) {
				// devaWasDisplayed = getCurDevaInsInfo();
				// if (devaWasDisplayed) {
					// progressDialogBox(false);
					// var message = "Your virutal environment is almost ready! " +
					// 		"If after trying to connect to any of your virtual machines, " +
					// 		"you receive a message  box with the title 'Terminal Server " +
					// 		"Connection Error' you should wait for 20 seconds and try again! " +
					// 		"Thank you for your patience! ";
										
					// noticeDialog("On-Demand Appointment", message, "alert");
					// break;
				// }
			// }
			// progressDialogBox(false);
			// if (!devaWasDisplayed) { 
			// 	var message = "Your virutal environment was successfully scheduled, " +
			// 			"but cannot yet be displayed. Please wait for a couple " +
			// 			"of minutes, refresh the page, and try again.";
			// 	noticeDialog("On-Demand Appointment", message, "alert");
			// }
		} else {
			alert("Your requested appointment could not be scheduled!");
		}
		// sms: updated on 6/2/2011
		// progressDialogBox(false);
		// $("#dialog").dialog("close"); 
		//$(this).dialog("close"); 
					
	}, 2000);	// wait 2 seconds
				
	// sms: updated 6/4/2011 added the below line
	interval = setInterval('getCurDevaInsInfo()', 10000);

}

function createInstantAppointmentDialogBox(username, course, type) {
	  
	$("#dialog").html("");
	var dialogContent = $("#dialog").load('forms/instant-appointment-form.html', function() {
		$("#dialog #hours").ForceNumericOnly();
		$("#dialog #minutes").ForceNumericOnly();
		// timepicker({  timeSeparator: ' hours and ' }); 
	
	});
	    
	$("#dialog").dialog({ 
		// autoOpen: false,
		width: 600,
		modal: true,
		title: "On-Demand Appointment",
		close: function() {
			$(this).dialog("close");
		},
		buttons: { 
			"Confirm": function() { 
				progressDialogBox(true);
				
				setTimeout(function(){	// Added: to allow the progress bar to appear.
					
					var newevent = getCreateNewEventObjFromInstantAppForm(username, course, type);
					if (scheduleAppointment(newevent, username)) {
						// sms: updated on 6/2/2011
						// var devaWasDisplayed = false;
						// for (var i=0; i<5; i++) {
							// devaWasDisplayed = getCurDevaInsInfo();
							// if (devaWasDisplayed) {
								// progressDialogBox(false);
								// var message = "Your virutal environment is almost ready! " +
								// 		"If after trying to connect to any of your virtual machines, " +
								// 		"you receive a message  box with the title 'Terminal Server " +
								// 		"Connection Error' you should wait for 20 seconds and try again! " +
								// 		"Thank you for your patience! ";
										
								// noticeDialog("On-Demand Appointment", message, "alert");
								// break;
							// }
						// }
						// progressDialogBox(false);
						// if (!devaWasDisplayed) { 
						// 	var message = "Your virutal environment was successfully scheduled, " +
						// 			"but cannot yet be displayed. Please wait for a couple " +
						// 			"of minutes, refresh the page, and try again.";
						// 	noticeDialog("On-Demand Appointment", message, "alert");
						// }
					} else {
						alert("Your requested appointment could not be scheduled!");
					}
					// sms: updated on 6/2/2011
					// progressDialogBox(false);
					$("#dialog").dialog("close"); 
					//$(this).dialog("close"); 
					
				}, 2000);	// wait 2 seconds

				// sms: updated 6/4/2011 added the below line
				interval = setInterval('getCurDevaInsInfo()', 10000);
			}, 
			"Cancel": function() { 
				//history.go(-1);
				window.location = $("#courseURL").val();
				$(this).dialog("close"); 
			} 
		} 
	});
	
	$("#dialog").dialog("open");
}

function createDialogBox(username, course, type) {
  
	$("#dialog").html("");
	var dialogContent = $("#dialog").load('forms/appointment-form.html', function() {
		$("#dialog #startDate").datepicker();
		$("#dialog #endDate").datepicker();
		$("#dialog #start").ptTimeSelect(); 
		$("#dialog #end").ptTimeSelect(); 
	
		$("#dialog #startDate").focus(function() {
			$("#ptTimeSelectCntr").hide();
		});
		$("#dialog #start").focus(function() {
			$("#dialog #endDate").datepicker('hide');
			$("#dialog #startDate").datepicker('hide');
		});
		$("#dialog #endDate").focus(function() {
			$("#ptTimeSelectCntr").hide();
		});
		$("#dialog #end").focus(function() {
			$("#dialog #startDate").datepicker('hide');
			$("#dialog #endDate").datepicker('hide');
		});
		
		var is_admin_user = false;
		var customLabel;
		if(is_admin_user){
			customLabel = "Host";
		}else{
			customLabel = "Course";
		}
		
		if (jQuery.browser.msie) {
			document.getElementById("customddm").innerHTML = customLabel;
		} else {
			$("#dialog #customddm").text(customLabel);
		}
		
		var dayformatter = "mm/dd/yyyy";	// mmmm d, yyyy
		var timeformatter = "h:MM TT";		// h:MM:ss TT 
		
		var typeFieldOptions = "";
		var courseFieldOptions = "";
		//var timezoneFieldOptions = "";
		
		
		typeFieldOptions +=  "<option>"+type+"</option>";
		courseFieldOptions +=  "<option>"+course+"</option>";
		
		var now = new Date();
		var startDate = $(this).find("input[name='startDate']");
		var endDate = $(this).find("input[name='endDate']");
		var startField = $(this).find("input[name='start']").val(now.format(timeformatter));
		var endField = $(this).find("input[name='end']").val(now.format(timeformatter));
		var typeField = $(this).find("select[name='type']").html(typeFieldOptions);
		var courseField = $(this).find("select[name='course']").html(courseFieldOptions);
		//var timezoneField = $(this).find("select[name='timezone']").html(timezoneFieldOptions);
		
		var startNow = $(this).find("input[name='startNow']");
		
		startDate.val(now.format(dayformatter));
		endDate.val(now.format(dayformatter));
		var endTime = new Date();
		endTime.setHours(endTime.getHours()+1);
		endField.val(endTime.format(timeformatter));
		// endDate.val(event.end.format(dayformatter));
		
		// typeField.val((event.resourceType).toUpperCase());
		// courseField.val(event.course);
		
		
		$("input[name='startDate']").attr('disabled', 'disabled');
		$("input[name='start']").attr('disabled', 'disabled');
	
		$(startNow).attr('disabled', 'disabled');
		$(startDate).attr('disabled','disabled');
		$(startField).attr('disabled','disabled');
		$(startNow).attr('checked', 'checked');
		startDate.val("");
		startField.val("");
	});
	
	$("#dialog").dialog({ 
		// autoOpen: false,
		width: 340,
		modal: true,
		title: "You are early for your appointment! Do you want to start now?",
		close: function() {
			   $(this).dialog("close");
			},
		buttons: { 
			"confirm new schedule": function() { 
				var startDate = $("#dialog").find("input[name='startDate']");
				var endDate = $("#dialog").find("input[name='endDate']");
				var startField = $("#dialog").find("input[name='start']");
				var endField = $("#dialog").find("input[name='end']");
				var startNow = $("#dialog").find("input[name='startNow']");
				
				if (checkStartEndFields(startDate, startField, endDate, endField, startNow)) {

					var newevent = getCreateNewEventObj();
					// $(this).dialog("close"); 
					if (scheduleAppointment(newevent, username)) {
						// pausecomp(5000);
						// sms: updated on 6/2/2011
						// var devaWasDisplayed = getCurDevaInsInfo(); 
					} else {
						alert("Your requested appointment could not be scheduled!");
					}
					
				} else {
					alert("checkStartEndFields is false!");
				}
				$(this).dialog("close"); 
			}, 
			"cancel schedule": function() { 
				$(this).dialog("close"); 
			} 
		} 
	});
	
	$("#dialog").dialog("open");
}

function pausecomp(millis)
{
	var date = new Date();
	var curDate = null;

	do { curDate = new Date(); }
	while(curDate-date < millis);
} 

function editDialogBox() {
  
	$("#dialog").html("");
	var dialogContent = $("#dialog").load('fullcalendar/edit_event.html', function() {
		
		$("#dialog #startDate").datepicker();
		$("#dialog #endDate").datepicker();
		$("#dialog #start").ptTimeSelect(); 
		$("#dialog #end").ptTimeSelect(); 
	
		$("#dialog #startDate").focus(function() {
			$("#ptTimeSelectCntr").hide();
		});
		$("#dialog #start").focus(function() {
			$("#dialog #endDate").datepicker('hide');
			$("#dialog #startDate").datepicker('hide');
		});
		$("#dialog #endDate").focus(function() {
			$("#ptTimeSelectCntr").hide();
		});
		$("#dialog #end").focus(function() {
			$("#dialog #startDate").datepicker('hide');
			$("#dialog #endDate").datepicker('hide');
		});
		
		var is_admin_user = false;
		var customLabel;
		if(is_admin_user){
			customLabel = "Host";
		}else{
			customLabel = "Course";
		}
		
		if (jQuery.browser.msie) {
			document.getElementById("customddm").innerHTML = customLabel;
		}else{
			$("#dialog #customddm").text(customLabel);
		}
		
		// resetForm(this);
		
		var dayformatter = "mm/dd/yyyy";	// mmmm d, yyyy
		var timeformatter = "h:MM TT";		// h:MM:ss TT 
		
		var typeFieldOptions = "";
		var courseFieldOptions = "";
		//var timezoneFieldOptions = "";
		
		typeFieldOptions +=  "<option>"+"Virtual Lab"+"</option>";
		// Load types in the select box, and select current type
		// for(var i = 0; i<types.length; i++){
		// 	typeFieldOptions +=  "<option>"+types[i]+"</option>";
		// }
		courseFieldOptions +=  "<option>"+"IT Automation 2"+"</option>";
		// for(var i = 0; i<courses.length; i++){
		// 	courseFieldOptions +=  "<option>"+courses[i]+"</option>";
		// }
		
		// for(var i = 0; i<zones.length; i++){
		// 	timezoneFieldOptions += "<option>"+zones[i]+"</option>";
		// }
		
		var now = new Date();
		var startDate = $(this).find("input[name='startDate']");
		var endDate = $(this).find("input[name='endDate']");
		var startField = $(this).find("input[name='start']").val(now.format(timeformatter));
		var endField = $(this).find("input[name='end']").val(now.format(timeformatter));
		var typeField = $(this).find("select[name='type']").html(typeFieldOptions);
		var courseField = $(this).find("select[name='course']").html(courseFieldOptions);
		//var timezoneField = $(this).find("select[name='timezone']").html(timezoneFieldOptions);
		
		var startNow = $(this).find("input[name='startNow']");
		
		startDate.val(now.format(dayformatter));
		// endDate.val(event.end.format(dayformatter));
		
		// typeField.val((event.resourceType).toUpperCase());
		// courseField.val(event.course);
		
		
		$("input[name='startDate']").attr('disabled', 'disabled');
		$("input[name='start']").attr('disabled', 'disabled');
	
		$(startNow).attr('disabled', 'disabled');
		$(startDate).attr('disabled','disabled');
		$(startField).attr('disabled','disabled');
		$(startNow).attr('checked', 'checked');
		startDate.val("");
		startField.val("");
	});
	
	$("#dialog").dialog({ 
		// autoOpen: false,
		width: 340,
		modal: true,
		title: "You are early for your appointment! Do you want to start now?",
		close: function() {
			   $(this).dialog("close");
			},
		buttons: { 
			"confirm new schedule": function() { 
				$(this).dialog("close"); 
				}, 
			"cancel schedule": function() { 
				$(this).dialog("close"); 
				} 
		} 
	});
	
	$("#dialog").dialog();
}


function isValidDate(dateStr) {

	var success = false;
	var datere = /[0-9]{2}\/[0-9]{2}\/[0-9][0-9]{3}/;
	
	var result = dateStr.match(datere);
	var newDate;
	
	if (result != null) {
		newDate = new Date(result[0]);
	}
	
	if (newDate != null && !isNaN(newDate.getTime())) {
		success = true;
	}
	
	return success;
}

function isValidTime(dateStr, timeStr) {
	
	var success = false;
	var datere = /[0-9]{2}\/[0-9]{2}\/[0-9][0-9]{3}/;
	var timere = /[0-9]{1,2}(:[0-9]{2})\s(pm|am)/i;
	
	var result = dateStr.match(datere);
	var time = timeStr.match(timere);
	var newDate = null;
	
	if (result != null) {
		if (time != null) {
			newDate = new Date(result[0] + " " + time[0]);
		}
	}
	
	if (newDate != null && !isNaN(newDate.getTime())) {
		success = true;
	}
	
	return success;
}

function checkStartEndFields(
		startDate, 
		startField, 
		endDate, 
		endField,
		startNow) {

	var validStartDate = false;
	var validStartTime = false;
	var validEndDate = false;
	var validEndTime = false;

	var isChecked = $(startNow).attr('checked');

	startDate.removeClass('error');
	startField.removeClass('error');
	endDate.removeClass('error');
	endField.removeClass('error');

	if (isValidDate(endDate.val())) {
		validEndDate = true;
	} else {
		endDate.addClass('error');
	}

	if (isValidTime(endDate.val(), endField.val())) {
		validEndTime = true;
	} else {
		endField.addClass('error');
	}

	if (!isChecked) {
		if (isValidDate(startDate.val())) {
			validStartDate = true;
		} else {
			startDate.addClass('error');
		}

		if (isValidTime(startDate.val(), startField.val())) {
			validStartTime = true;
		} else {
			startField.addClass('error');
		}
	} else {
		if (validEndDate && validEndTime) {
			startDate.val("");
			startField.val("");
			validStartDate = true;
			validStartTime = true;
		}
	}
	
	return (validStartDate && validStartTime && validEndDate && validEndTime) 
		? true
		: false;
}
	
//Grabs the Event Obj from the create Apointment form
function getCreateNewEventObj(){
	// Retrieve Form Objects
	var startDate = $("#dialog").find("input[name='startDate']");
	var endDate = $("#dialog").find("input[name='endDate']");
	var startField = $("#dialog").find("input[name='start']");
	var endField = $("#dialog").find("input[name='end']");
	var typeField = $("#dialog").find("select[name='type']");
	var courseField = $("#dialog").find("select[name='course']");
	
	var startNow = $("#dialog").find("input[name='startNow']");
	var isChecked = $(startNow).attr('checked');
	
	/// - start now option
	if (isChecked) {
		var start = new Date();
		var end = new Date(endDate.val() + " " + endField.val());
	} else {
		var start = new Date(startDate.val() + " " + startField.val());
		var end = new Date(endDate.val() + " " + endField.val());
	}		
	
	var start = new Date(startDate.val() + " " + startField.val());
	var end = new Date(endDate.val() + " " + endField.val());
	
	// TODO I do not understand what the below code is for??? This is sms.
	if (end.getTime() < start.getTime()) {
		end.setDate(end.getDate()+1);  // changes month automatically
	} else if(start.getTime() == end.getTime()){
		end.setHours(end.getHours()+1);
	}

	var type = typeField.val().replace(/ /g,"-");
	var course = courseField.val();
	var actions = [];  // need to assign real actions
	actions[0] = "edit";
	actions[1] = "cancel";
	
	var eventClass = "div"+type.toLowerCase()+"-"+course.replace(/ /g, "-").toLowerCase()+" scheduled";
	var newevent = {
		resourceType: typeField.val(),
		title : typeField.val(),
		editable: true,
		start : (isChecked) ? "" : start,
		end : end,
		className : eventClass,
		allDay: false,
		course: course,
		type: type.toLowerCase(),
		actions: actions
	};	
	
	return newevent;
}

function fixDate(d, check) { // force d to be on check's YMD, for daylight savings purposes
	if (+d) { // prevent infinite looping on invalid dates
		while (d.getDate() != check.getDate()) {
			d.setTime(+d + (d < check ? 1 : -1) * HOUR_MS);
		}
	}
}

function parseISO8601(s, ignoreTimezone) {
	// derived from http://delete.me.uk/2005/03/iso8601.html
	// TODO: for a know glitch/feature, read tests/issue_206_parseDate_dst.html
	var m = s.match(/^([0-9]{4})(-([0-9]{2})(-([0-9]{2})([T ]([0-9]{2}):([0-9]{2})(:([0-9]{2})(\.([0-9]+))?)?(Z|(([-+])([0-9]{2}):([0-9]{2})))?)?)?)?$/);
	if (!m) {
		return null;
	}
	var date = new Date(m[1], 0, 1),
		check = new Date(m[1], 0, 1, 9, 0),
		offset = 0;
	if (m[3]) {
		date.setMonth(m[3] - 1);
		check.setMonth(m[3] - 1);
	}
	if (m[5]) {
		date.setDate(m[5]);
		check.setDate(m[5]);
	}
	fixDate(date, check);
	if (m[7]) {
		date.setHours(m[7]);
	}
	if (m[8]) {
		date.setMinutes(m[8]);
	}
	if (m[10]) {
		date.setSeconds(m[10]);
	}
	if (m[12]) {
		date.setMilliseconds(Number("0." + m[12]) * 1000);
	}
	fixDate(date, check);
	if (!ignoreTimezone) {
		if (m[14]) {
			offset = Number(m[16]) * 60 + Number(m[17]);
			offset *= m[15] == '-' ? 1 : -1;
		}
		offset -= date.getTimezoneOffset();
	}
	return new Date(+date + (offset * 60 * 1000));
}

//Grabs the Event Obj from the Instant Apointment form
function getCreateNewEventObjFromInstantAppForm(username, course, type){
	// Retrieve Form Objects
	var hours = $("#dialog").find("input[name='hours']");
	var minutes = $("#dialog").find("input[name='minutes']");
	var userCurTime = getUserCurrentTime(username); // new Date();
	var end = parseISO8601(userCurTime, true);
   
    end.setHours(end.getHours()+parseInt(hours.val()));
    end.setMinutes(end.getMinutes()+parseInt(minutes.val()));

    
	var typeModified = type.replace(/ /g,"-");
	var actions = [];  // need to assign real actions
	actions[0] = "edit";
	actions[1] = "cancel";
	
	var eventClass = "div"+typeModified.toLowerCase()+"-"+course.replace(/ /g, "-").toLowerCase()+" scheduled";
	var newevent = {
		resourceType: type,
		title : type,
		editable: true,
		start : "",
		end : end,
		className : eventClass,
		allDay: false,
		course: course,
		type: typeModified.toLowerCase(),
		actions: actions
	};	
	
	return newevent;
}

// sms: 6/28/2014 Added to support embedded version
//Grabs the Event Obj assuming 30 minutes initial appointment
function getCreateNewEventObjFromInstantAppEmbedded(username, encryptedPassword, course, type, hours, minutes){
			
	var userCurTime = getUserCurrentTime(username); // new Date();
	var end = parseISO8601(userCurTime, true);
   
	end.setHours(end.getHours()+parseInt(hours));
    end.setMinutes(end.getMinutes()+parseInt(minutes));

	var typeModified = type.replace(/ /g,"-");
	var actions = [];  // need to assign real actions
	actions[0] = "edit";
	actions[1] = "cancel";
	
	var eventClass = "div"+typeModified.toLowerCase()+"-"+course.replace(/ /g, "-").toLowerCase()+" scheduled";
	var newevent = {
		resourceType: type,
		title : type,
		editable: true,
		start : "",
		end : end,
		className : eventClass,
		allDay: false,
		course: course,
		type: typeModified.toLowerCase(),
		encryptedPassword: encryptedPassword,
		actions: actions
	};	
	
	return newevent;
}

//Grabs the Event Obj from the Instant Apointment form
function getCreateNewEventObjFromInstantApp4CTForm(username, course, type){

	var userCurTime = getUserCurrentTime(username); // new Date();
	var end = parseISO8601(userCurTime, true);
	end.setHours(end.getHours()+2);
	
	var typeModified = type.replace(/ /g,"-");
	var actions = [];  // need to assign real actions
	actions[0] = "edit";
	actions[1] = "cancel";
	
	var eventClass = "div"+typeModified.toLowerCase()+"-"+course.replace(/ /g, "-").toLowerCase()+" scheduled";
	var newevent = {
		resourceType: type,
		title : type,
		editable: true,
		start : "",
		end : end,
		className : eventClass,
		allDay: false,
		course: course,
		type: typeModified.toLowerCase(),
		actions: actions
	};	
	
	return newevent;
}

function getUserCurrentTime(username) {
	
	var userCurTime = new Date();
	
	$.ajax({
		type: 'POST',
		url: 'php/virtuallabs-wscalls.php',
		dataType: 'json',
		async: false,
		timeout: 4000,
		data: {
			action: 'getUserCurrentTime',
			username: username
		},
		success: function(data){
			var message = "";
			
			if (data) {
				userCurTime = data;
			}
			
		},
		error: function(XMLHttpRequest, textStatus, errorThrown){
			alert("error:" + textStatus + "\n errorThrown: " + errorThrown);
		}
	});

	return userCurTime;
}

function scheduleAppointment(event, username) {
	var dateformatter = "yyyy-mm-dd'T'HH:MM:ss";	
	var success = false;
	var requestType;
	var startDate;
    
	// showProgressBar(true);
	if (event.start != "") {
		startDate = event.start.format(dateformatter);
	} else {
		startDate = "";
	}
    
	requestType = getRequestType();
	
	$.ajax({
		type: 'POST',
		url: 'php/virtuallabs-wscalls.php',
		dataType: 'json',
		// sms: updated on 6/2/2011
		async: true,
		// sms: updated on 6/2/2011
		timeout: 0,
		data: {
			action: 'scheduleAppointments',
			id: '',
			requestingUser:  username, // $('#username').val(),
			username: username,
			start: startDate,
			end: event.end.format(dateformatter),
			resourceType: event.resourceType,
			course: event.course,
			affiliationId: '',
			availabilityStatus: '',
			requestType: requestType
		},
		success: function(data){
			// sms: updated on 6/2/2011
			/*
			var message = "";
			
			if (data) {
				var appointments = generateAppointments(data);
				var scheduled = 0;
				var unsheduled = 0;
				var scheduledMsg = "";
				var unscheduledMsg = "";
			
				success = true;			

				for (a in appointments) {
					if(appointments[a].id == ""){
						unsheduled++;
						success = false;			
					} else {
						scheduled++;
					}
				}

				if (unscheduled > 0)
					unscheduledMsg = unsheduled + " appointment(s) could NOT be scheduled.";	
				scheduledMsg = scheduled + " appointment(s) was scheduled.";
				message = scheduledMsg + " " + unscheduledMsg;
				
				if (!success) 
					alert(message);
				
				// success = true;			
			} else {
				message = "We were unable to schedule you for this appointment.<br/><br/> ** please try again later.";
				alert(message);
			}
			
			// noticeDialog("Schedule Appointment", message, "alert");
			*/
            
            //if(iscerttest){
                if(!data.id){
                    isScheduled = false;
                    //noticeDialog("Schedule Certificate Exam", data.availabilityStatus, "alert");
					//hotfix-MisleadingMsg-SchCertExam
					// noticeDialogWithRedirect("Schedule Certificate Exam", data.availabilityStatus, "alert", $("#courseURL").val());
					noticeDialogWithRedirect("Schedule request was rejected!", "You have an ongoing certificate exam. <br/>You must first finish your exam before being able to schedule your virtual lab.", "alert", $("#courseURL").val());
					
                }else{
                    isScheduled = true;
                }
            //}
		},
		error: function(XMLHttpRequest, textStatus, errorThrown){
			var header = "Schedule Appointment";
			var message = "We were unable to schedule you for this appointment.<br/><br/> ** please try again later.";
			var icon = "alert";
			message = textStatus + " : " +errorThrown;
			noticeDialog(header, message, icon);
			
			alert(message);
			// showProgressBar(false);
		}
	});
	
	// sms: updated on 6/2/2011
	// return success;
	return true;
}

function scheduleAppointmentWithEncryptedPassword(eventWithEncryptedPassword, username) {
	var dateformatter = "yyyy-mm-dd'T'HH:MM:ss";	
	var success = false;
	var requestType;
	var startDate;
    
	// showProgressBar(true);
	if (eventWithEncryptedPassword.start != "") {
		startDate = event.start.format(dateformatter);
	} else {
		startDate = "";
	}
    
	requestType = getRequestType();
	
	$.ajax({
		type: 'POST',
		url: 'php/virtuallabs-wscalls.php',
		dataType: 'json',
		// sms: updated on 6/2/2011
		async: true,
		// sms: updated on 6/2/2011
		timeout: 0,
		data: {
			action: 'scheduleAppointmentsWithEncryptedPassword',
			id: '',
			requestingUser:  username, // $('#username').val(),
			username: username,
			encryptedPassword: eventWithEncryptedPassword.encryptedPassword,
			start: startDate,
			end: eventWithEncryptedPassword.end.format(dateformatter),
			resourceType: eventWithEncryptedPassword.resourceType,
			course: eventWithEncryptedPassword.course,
			affiliationId: '',
			availabilityStatus: '',
			requestType: requestType
		},
		success: function(data){
			// sms: updated on 6/2/2011
			/*
			var message = "";
			
			if (data) {
				var appointments = generateAppointments(data);
				var scheduled = 0;
				var unsheduled = 0;
				var scheduledMsg = "";
				var unscheduledMsg = "";
			
				success = true;			

				for (a in appointments) {
					if(appointments[a].id == ""){
						unsheduled++;
						success = false;			
					} else {
						scheduled++;
					}
				}

				if (unscheduled > 0)
					unscheduledMsg = unsheduled + " appointment(s) could NOT be scheduled.";	
				scheduledMsg = scheduled + " appointment(s) was scheduled.";
				message = scheduledMsg + " " + unscheduledMsg;
				
				if (!success) 
					alert(message);
				
				// success = true;			
			} else {
				message = "We were unable to schedule you for this appointment.<br/><br/> ** please try again later.";
				alert(message);
			}
			
			// noticeDialog("Schedule Appointment", message, "alert");
			*/
            
            //if(iscerttest){
                if(!data.id){
                    isScheduled = false;
                    //noticeDialog("Schedule Certificate Exam", data.availabilityStatus, "alert");
					noticeDialogWithRedirect("Schedule "  + eventWithEncryptedPassword.resourceType, data.availabilityStatus, "alert", $("#courseURL").val());
					
                }else{
                    isScheduled = true;
                }
            //}
		},
		error: function(XMLHttpRequest, textStatus, errorThrown){
			var header = "Schedule Appointment";
			var message = "We were unable to schedule you for this appointment.<br/><br/> ** please try again later.";
			var icon = "alert";
			message = textStatus + " : " +errorThrown;
			noticeDialog(header, message, icon);
			
			alert(message);
			// showProgressBar(false);
		}
	});
	
	// sms: updated on 6/2/2011
	// return success;
	return true;
}

function getRequestType() {
	var requestType;
	
	if(is_admin_user){
		requestType = "Host";
	}else if(is_mentor_user){
		requestType = "Mentor";
	}else{
		requestType = "User";
	}
	
	return requestType;
}

function generateAppointments(events) {
	var appointments = [];
	
	if ($.isArray(events)) {
		
		for (var i = 0; i < events.length; i++) {
			
			appointments.push({
				id: events[i].id,
				userName: (events[i].userName) ? events[i].userName : "",
				start: events[i].start,
				end: events[i].end,
				resourceType: events[i].resourceType,
				course: events[i].course,
				affiliationId: (events[i].affiliationId) ? events[i].affiliationId : "",
				availabilityStatus: (events[i].availabilityStatus) ? events[i].availabilityStatus : "",
				actions: (events[i].action) ? events[i].action : null
			});
		}
	} else {
		appointments.push({
			id: events.id,
			userName: (events.userName) ? events.userName : "",
			start: events.start,
			end: events.end,
			resourceType: events.resourceType,
			course: events.course,
			affiliationId: (events.affiliationId) ? events.affiliationId : "",
			availabilityStatus: (events.availabilityStatus) ? events.availabilityStatus : "",
			actions: (events.action) ? events.action : null
		});
	}
	
	return appointments;
}

function generateAppointment(event) {
	var appointment = [];
	
	appointment.push({
			id: event.id,
			userName: (event.userName) ? event.userName : "",
			start: event.start,
			end: event.end,
			resourceType: event.resourceType,
			course: event.course,
			affiliationId: (event.affiliationId) ? event.affiliationId : "",
			availabilityStatus: (event.availabilityStatus) ? event.availabilityStatus : "",
			actions: (event.action) ? event.action : null
	
		});
}

function noticeDialog(header, message, icon) {
	
	var noticeContent = $("<div id='notice' />").html('<p><span class="ui-icon ui-icon-'+icon+'" style="float:left; margin:0 7px 20px 0;"></span>'+message+'</p>');
	
	$(noticeContent).dialog({
		modal: true,
		title: header,
		buttons: {
			Ok: function() {
				$(this).dialog('close');
			}
		}
	});
	
	$(noticeContent).dialog('open');
}

function noticeDialogWithRedirect(header, message, icon, url) {
	progressDialogBox(false);
	var noticeContent = $("<div id='notice' />").html('<p><span class="ui-icon ui-icon-'+icon+'" style="float:left; margin:0 7px 20px 0;"></span>'+message+'</p>');
	
	$(noticeContent).dialog({
		modal: true,
		title: header,
		buttons: {
			Ok: function() {
				$(this).dialog('close');
				window.location = url;
			}
		}
	});
	
	$(noticeContent).dialog('open');
}


