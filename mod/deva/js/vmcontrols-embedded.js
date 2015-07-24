var getTimeAttempts = 0;
var isTimeSet = false;
var debugtime = 5;

var currentAppointmentId;
var currentAppointmentEndDate;
var useCertCSS = false;

var stateInterval = null;
var rdpInterval = null;
var rdpIsReady = null;
var rdpTabInfo = [];
var buttonFunc = null;

var currentTabSelected; 
var tabAlreadyLoaded = false;

var isSpinning = false;

var wasRDPMachineReady = false;

var timeOverIsSet = false;

var timeOptionsInSettings = false;
var vmOptionsInSettings = false;
var bpOptionsInSettings = false;
var screenOptionsInSettings = false;

var screenoptionswidth = 305;
var timetoolswidth = 170;
var toolbarswidth = 230;

var padding = 20;
var navbarwidth = 0;
var breadcrumbwidth = 0;
var navbuttonwidth = 0;
var clockwidth = 0;
var settingswidth = 0;

var resizeTimeOut = false;

function verifyIframe(){
	
}

function vmc_init(){
	
	//Set number of attempts
	getTimeAttempts = 5;
	
	//Remove unused update button
	$("#page .navbar .navbutton").empty();
	
	//$("#page .navbar .navbutton").append("<div id='screenOptions'></div>");
	//$("#page .navbar .navbutton").append("<div id='vmControlPanel'></div>");
	//$("#page .navbar .navbutton").append("<div class='vmControlContainer'><div id='vmControlPanel'></div></div>");
	//$("#page .navbar .navbutton .vmControlContainer").append(getTimeControlHTML());
	//$("#page .navbar .navbutton").append(getTimeControlHTML);
	
	$("#page .navbar .navbutton").append("<div class='clock'>" + getTimeClockHTML() + "</div>");
	
	//$("#page .navbar .navbutton").append("<div class='settings'><button id='vmOptions' class='optionsButton'></button><button id='bpOptions' class='optionsButton'></button><button id='timeOptions' class='optionsButton'></button></div>");
	$("#page .navbar .navbutton").append("<div class='settings'><button id='timeOptions' class='optionsButton'></button><button id='bpOptions' class='optionsButton'></button><button id='vmOptions' class='optionsButton'></button></div>");
	
	$("#page .navbar .navbutton").append("<div class='vmControlContainer'>"+ getTimeControlHTML() +"<div id='screenOptions'></div><div id='vmControlPanel'></div></div>");
	
	// Show options Button
	$(".settings button#vmOptions").button({ icons: {primary:'ui-icon-gear'} });	//ui-icon-wrench
	$(".settings button#timeOptions").button({ icons: {primary:'ui-icon-clock'} });
	$(".settings button#bpOptions").button({ icons: {primary:'ui-icon-image'} });
	$(".settings button#vmOptions, .settings button#bpOptions").hide();
	
	//#timetools, #screenOptions, #vmControlPanel
	//$('#timetools').show();
	$("#screenOptions, #vmControlPanel").hide();
	$(".settings button#vmOptions").click(function(){
		//$('#vmControlPanel').toggle();
		
		hideShowOptions($("#vmControlPanel"),this);
		//checkOptionsScreenVisible();
	});
	//if(!iscerttest){
		$(".settings button#timeOptions").click(function(){
			//$('#timetools').toggle();
			hideShowOptions($("#timetools"),this);
			//checkOptionsScreenVisible();
		});
	//}else{
	//	$(".settings button#timeOptions").button("disable");
	//}
	$(".settings button#bpOptions").click(function(){
		//$('#screenOptions').toggle();
		hideShowOptions($("#screenOptions"),this);
		//checkOptionsScreenVisible();
	});
	
	$(window).resize(function(){
		$(".navbar .navbutton").hide();
		if(resizeTimeOut !== false)
			clearTimeout(resizeTimeOut);
			
		resizeTimeOut = setTimeout(function(){		
			optionsResizing();
			$("#devaTabs a.devaTabs.selected").each(function(){
				$(this).click();
			});
			trace('resize Complete');
		}, 500);  // miliseconds
	});
	
	setupTimeControlButtons();
	//$('#screenOptions').hide();
	
	padding = 20;
	navbarwidth = $("div.navbar.timer-navbar").width();
	breadcrumbwidth = $(".breadcrumb ul").width();
	navbuttonwidth = $(".timer-navbutton").width();
	clockwidth = $(".clock").width();
	settingswidth = 0;
	
	setupScreenOptions();	// JAM: 03/17/2012
	
	//$("#vmControlPanel").append(getVMControlHTML());
	//setupVMControlButtons();
	/*
	setTimeControl();
	// Every 1 minute(s) the loadAppointments function is called
	// 1 min = 60000, 5 min = 300000 
	setInterval("setTimeControl();",60000);
	*/
	
	//setTimeout(optionsResizing, 500);
}

function optionsResizing() {
	trace('optionsResizing'+isControlOnTab);
	$(".navbar .navbutton").show();
	navbarwidth = $("div.navbar.timer-navbar").width();
	breadcrumbwidth = $(".breadcrumb ul").width();
	navbuttonwidth = $(".timer-navbutton").width();
	clockwidth = $(".clock").width();
	
	clearNavButtons();
	settingswidth = 0;
	if(isControlOnTab){
		adjustVMControlPanel();
		adjustScreenOptions();
	}
	//if(!iscerttest){
		adjustTimetools();
	//}
	//recalculateOptionsDisplay();
	//isControlOnTab


}
function clearNavButtons(){
	//console.log('vmOptionsInSettings:'+vmOptionsInSettings);
	//console.log('bpOptionsInSettings:'+bpOptionsInSettings);
	//console.log('timeOptionsInSettings:'+timeOptionsInSettings);
	
	if(vmOptionsInSettings){
		$("#vmControlPanel").hide();
		$('.vmControlContainer').append($('#vmControlPanel'));
		$(".settings button#vmOptions").removeClass('open').show();
		
		var newwidthvalue = (settingswidth + clockwidth + padding) + "px";
		//$(".timer-navbutton").css('width', newwidthvalue);
		
		vmOptionsInSettings = false;
	}
	if(bpOptionsInSettings){
		$("#screenOptions").hide();
		$('.vmControlContainer').append($('#screenOptions'));
		$(".settings button#bpOptions").removeClass('open').show();
		
		var newwidthvalue = (settingswidth + clockwidth + padding) + "px";
		//$(".timer-navbutton").css('width', newwidthvalue);
		
		bpOptionsInSettings = false;
	}
	if(timeOptionsInSettings){
		$("#timetools").hide();
		$('.vmControlContainer').append($('#timetools'));
		$(".settings button#timeOptions").removeClass('open').show();
		
		var newwidthvalue = (settingswidth + clockwidth + padding) + "px";
		//$(".timer-navbutton").css('width', newwidthvalue);
		
		timeOptionsInSettings = false;
	}		
}

function adjustVMControlPanel(){
	trace('Calculating VMControls -----');
	var resetOptions = false;
	/*
	var padding = 20;
	var navbarwidth = $("div.navbar.timer-navbar").width();
	var breadcrumbwidth = $(".breadcrumb ul").width();
	var navbuttonwidth = $(".timer-navbutton").width();
	var clockwidth = $(".clock").width();
	//var settingswidth = getSettingsWidth();	//$(".settings").width();
	//var toolbarswidth = 230;	//$('#vmControlPanel').width();
	*/
	var availablewidth = navbarwidth - (breadcrumbwidth +  clockwidth + padding);
	//var navarea = availablewidth - toolbarswidth;
	
	//recalculateOptionsDisplay();
	trace('availablewidth: '+availablewidth);
	//trace('settingswidth: '+settingswidth);
	
	if(availablewidth > 0){
		//trace("------------- > 0 --------------");
		//trace('MOVE TO navbar - availablewidth: '+ availablewidth);
		
		//alert(newwidthvalue);
		if(availablewidth >= toolbarswidth){
			if(!vmOptionsInSettings){
				$("#vmControlPanel").show();
				$('.settings').append($('#vmControlPanel'));
				$(".settings button#vmOptions").hide();
				//$('#vmControlPanel').show();
				//alert('here');
				//var newwidthvalue = (clockwidth + toolbarswidth + padding) + "px";
				settingswidth += toolbarswidth;
				var newwidthvalue = (settingswidth + clockwidth + padding) + "px";
				//$(".timer-navbutton").css('width', newwidthvalue);
				
				vmOptionsInSettings = true;
			}
		}else{
			//trace("------------- ELSE 1 --------------");
			resetOptions = true;
		}
	}else{
		//trace("------------- ELSE 2 --------------");
		resetOptions = true;
	}
	
	if(resetOptions){
		//trace("------------- reset options --------------");
		//trace('MOVE TO vmControlContainer - availablewidth: '+ availablewidth);
		
		
		if(vmOptionsInSettings){
			$("#vmControlPanel").hide();
			$('.vmControlContainer').append($('#vmControlPanel'));
			$(".settings button#vmOptions").show();
			
			settingswidth -= toolbarswidth;
			var newwidthvalue = (settingswidth + clockwidth + padding) + "px";
			//$(".timer-navbutton").css('width', newwidthvalue);
			
			vmOptionsInSettings = false;
		}
	}
	
	//recalculateOptionsDisplay();
	
	//trace("navbarwidth: "+navbarwidth);
	//trace("breadcrumbwidth: "+breadcrumbwidth);
	//trace("navbuttonwidth: "+navbuttonwidth);
	//trace("settingswidth: "+settingswidth);
	//trace("clockwidth: "+clockwidth);
	trace("toolbarswidth: "+toolbarswidth);
	
	trace("vmOptionsInSettings: "+vmOptionsInSettings);	
}
function adjustScreenOptions(){
	trace('Calculating ScreenOptions -----');
	var resetOptions = false;

	//var settingswidth = getSettingsWidth();	//$(".settings").width();
	//var screenoptionswidth = 305;	//$('#vmControlPanel').width();
	
	var availablewidth = navbarwidth - (breadcrumbwidth + settingswidth + clockwidth + padding);
	
	//recalculateOptionsDisplay();
	trace('availablewidth: '+availablewidth);
	//trace('settingswidth: '+settingswidth);
	
	if(availablewidth > 0){
		//trace("------------- bp: 0 --------------");
		//trace('MOVE TO navbar - availablewidth: '+ availablewidth);
		
		//alert(newwidthvalue);
		if(availablewidth >= screenoptionswidth){
			if(!bpOptionsInSettings && vmOptionsInSettings){
				$("#screenOptions").show();
				$('.settings').append($('#screenOptions'));
				$(".settings button#bpOptions").hide();
				
				settingswidth += screenoptionswidth;
				var newwidthvalue = (settingswidth + clockwidth + padding) + "px";
				//$(".timer-navbutton").css('width', newwidthvalue);
				
				bpOptionsInSettings = true;
			}
		}else{
			//trace("------------- bp: ELSE 1 --------------");
			resetOptions = true;
		}
	}else{
		//trace("------------- bp: ELSE 2 --------------");
		resetOptions = true;
	}
	
	if(resetOptions){
		//trace("------------- bp: reset options --------------");
		//trace('MOVE TO vmControlContainer - availablewidth: '+ availablewidth);
		
		
		if(bpOptionsInSettings){
			$("#screenOptions").hide();
			$('.vmControlContainer').append($('#screenOptions'));
			$(".settings button#bpOptions").show();
			
			settingswidth -= screenoptionswidth;
			var newwidthvalue = (settingswidth + clockwidth + padding) + "px";
			//$(".timer-navbutton").css('width', newwidthvalue);
			
			bpOptionsInSettings = false;
		}
	}
	
	//recalculateOptionsDisplay();
	
	trace("navbarwidth: "+navbarwidth);
	trace("breadcrumbwidth: "+breadcrumbwidth);
	trace("navbuttonwidth: "+navbuttonwidth);
	trace("settingswidth: "+settingswidth);
	trace("clockwidth: "+clockwidth);
	trace("screenoptionswidth: "+screenoptionswidth);
	
	trace("bpOptionsInSettings: "+bpOptionsInSettings);
		
}
function adjustTimetools(){
	trace('Calculating Timetools -----');
	var resetOptions = false;
	
	//var settingswidth = getSettingsWidth();	//$(".settings").width();
	//var timetoolswidth = 170;	//$('#timetools').width();
	
	var availablewidth = navbarwidth - (breadcrumbwidth + settingswidth + clockwidth + padding);
	//var navarea = availablewidth - timetoolswidth;
	
	//recalculateOptionsDisplay();
	trace('availablewidth: '+availablewidth);
	//trace('settingswidth: '+settingswidth);
	
	if(availablewidth > 0){
		trace('MOVE TO navbar - availablewidth: '+ availablewidth);
		
		//alert(newwidthvalue);
		if(availablewidth >= timetoolswidth){
			if((!isControlOnTab && !timeOptionsInSettings) || (!timeOptionsInSettings && bpOptionsInSettings)){
				$("#timetools").show();
				$('.settings').append($('#timetools'));
				$(".settings button#timeOptions").hide();
				//$('#timetools').show();
				
				//var newwidthvalue = (clockwidth + timetoolswidth + padding) + "px";
				settingswidth += timetoolswidth;
				var newwidthvalue = (settingswidth + clockwidth + padding) + "px";
				//$(".timer-navbutton").css('width', newwidthvalue);
				
				timeOptionsInSettings = true;
			}
		}else{
			resetOptions = true;
		}
	}else{
		resetOptions = true;
	}
	
	if(resetOptions){
		//trace('MOVE TO vmControlContainer - availablewidth: '+ availablewidth);
		
		
		if(timeOptionsInSettings){
			$("#timetools").hide();
			$('.vmControlContainer').append($('#timetools'));
			$(".settings button#timeOptions").show();
			
			settingswidth -= timetoolswidth;
			var newwidthvalue = (settingswidth + clockwidth + padding) + "px";
			//$(".timer-navbutton").css('width', newwidthvalue);
			
			timeOptionsInSettings = false;
		}
	}
	
	//recalculateOptionsDisplay();
	
	//trace("navbarwidth: "+navbarwidth);
	//trace("breadcrumbwidth: "+breadcrumbwidth);
	//trace("navbuttonwidth: "+navbuttonwidth);
	//trace("settingswidth: "+settingswidth);
	//trace("clockwidth: "+clockwidth);
	trace("timetoolswidth: "+timetoolswidth);
	
	trace("timeOptionsInSettings: "+timeOptionsInSettings);	
}

function recalculateOptionsDisplay(){
	var padding = 20;
	var navbarwidth = $("div.navbar.timer-navbar").width();
	var breadcrumbwidth = $(".breadcrumb ul").width();
	var navbuttonwidth = $(".timer-navbutton").width();
	var clockwidth = $(".clock").width();
	//var settingswidth = $(".settings").width();
	var timetoolswidth = 170;	//$('#timetools').width();
	/*
	$('.vmControlContainer').append($('#timetools'));
	$('.vmControlContainer').append($('#screenOptions'));
	$('.vmControlContainer').append($('#vmControlPanel'));
	
	if($("#timetools").css('display') != 'none')
		$(".settings button#timeOptions").show();
	if($("#screenOptions").css('display') != 'none')
		$(".settings button#bpOptions").show();
	if($("#vmControlPanel").css('display') != 'none')
		$(".settings button#vmOptions").show();
	*/
	settingswidth = getSettingsWidth();	//$(".settings").width();
	var newwidthvalue = (settingswidth + settingswidth + clockwidth + padding) + "px";
	//$(".timer-navbutton").css('width', newwidthvalue);
	trace("recalculateOptionsDisplay: "+newwidthvalue);
	
}



function getSettingsWidth(){
	var settingwidth = 0;
	
	$(".settings #timetools, .settings #screenOptions, .settings #vmControlPanel").each(function(){
		if($(this).css('display') != 'none'){
			settingwidth += $(this).width();
		}
	});
	return settingwidth;
}
function checkOptionsScreenVisible(){
	var hasElementsDisplayed = false;
	
	$("#timetools, #screenOptions, #vmControlPanel").each(function(){
		if($(this).css('display') != 'none'){
			hasElementsDisplayed = true;
		}
	});
	trace('hasElementsDisplayed: '+hasElementsDisplayed);
	
	if(hasElementsDisplayed){
		$('#page .navbar .navbutton .vmControlContainer').show();
	}else{
		$('#page .navbar .navbutton .vmControlContainer').hide();
	}
}
function hideShowOptions(element,buttonEle){
	//#timetools, #screenOptions, #vmControlPanel
	var hasElementsDisplayed = false;
	var showIt = false;
	
	$(".settings button#vmOptions,.settings button#bpOptions,.settings button#timeOptions").each(function(){

		if($(this).attr('id') == $(buttonEle).attr('id')){
			trace($(this).attr('id'));
			if($(buttonEle).hasClass('open')){
				trace($(this).attr('id')+ " - hasclass");
				$(buttonEle).removeClass('open');
			}else{
				trace($(this).attr('id')+ " - does have class");
				$(buttonEle).addClass('open');
			}
		}else{
			$(this).removeClass('open');
			
		}
		
	});
	
	if($(element).css('display') == 'none')
		showIt = true;
		
	$(".vmControlContainer #timetools").hide();
	$(".vmControlContainer #screenOptions").hide();
	$(".vmControlContainer #vmControlPanel").hide();
	
	if(showIt)
		$(element).show();
	else
		$(element).hide();
	
	$(".vmControlContainer #timetools, .vmControlContainer #screenOptions, .vmControlContainer #vmControlPanel").each(function(){
		if($(this).css('display') != 'none'){
			hasElementsDisplayed = true;
		}
	});
	
	if(hasElementsDisplayed)
		$('#page .navbar .navbutton .vmControlContainer').show();
	else
		$('#page .navbar .navbutton .vmControlContainer').hide();
}
function setTimeControl(){
	var instanceid = $('#veInsId').html();
	
	if(!timeOverIsSet){
		
		if(instanceid){
			$.ajax({
				type: 'POST',
				url: 'php/vmcontrols.php',
				dataType: 'json',
				async: true,
				data: {
					action: 'getAppointmentTimer',
					instanceId: instanceid
				},
				success: function(data){
				
					if(data){
						if(data.success){
							$("#settings").show();
							if(data.resourceType == "CERTIFICATE"){
								useCertCSS = true;
								
								$("#timetools button.addtime").addClass('certificate-nav');
								$("#timetools button.minustime").addClass('certificate-nav');
								$("#timetools button.cancel").addClass('certificate-nav');
								$("#timetools span.minutesform").addClass('certificate-nav');
								$("#timetools input.minutesform").addClass('certificate-nav');
								
								$("div.navbar div.navbutton").removeClass("timer-navbutton");
								$("div.navbar div.navbutton").addClass("timer-navbutton-cert");
								
								$("#timetools button.addtime").button("option", "disabled", false);
								$("#timetools button.minustime").button("option", "disabled", true);
								$("#timetools button.cancel").button("option", "disabled", true);
								
							}else{
								
								$("#timetools button.addtime").button("option", "disabled", false);
								$("#timetools button.minustime").button("option", "disabled", false);
								$("#timetools button.cancel").button("option", "disabled", false);
							}
							
							var curDate = $.fullCalendar.parseISO8601(data.curDate);
							currentAppointmentEndDate = $.fullCalendar.parseISO8601(data.endDate);
							currentAppointmentId = data.veInsSchId;
							
							//$("#vmcDebug").html("appointment ends: "+currentAppointmentEndDate);
							
							// Set up Countdown
							$('span.timetools-counter').stop();
							
							var timerControl = $('span.timetools-counter').countDown({
								//startNumber: appointmentDate.getTime(),
								startNumber: curDate,
								endNumber: currentAppointmentEndDate,
								returnDate: true,
								callBack: function(me) {
									$("#settings").hide();
									//$(me).text('Your Session is Over.').css('color','#b00');
									$(me).text('00:00:00').css('color','#b00');
									checkAppointmentOver(me);
								}
							});
							
							//$('span.timetools-counter').getNewID();
							timeOverIsSet = true;
							isTimeSet = true;
							
							//Append select to page
							//$("#page .navbar .navbutton").append(tzSelect);
						}else{
							
							isTimeSet = false;
							
							$("#timetools button.addtime").button("option", "disabled", true);
							$("#timetools button.minustime").button("option", "disabled", true);
							$("#timetools button.cancel").button("option", "disabled", true);
						}
					}
				},
				error: function(XMLHttpRequest, textStatus, errorThrown){
					var header = "Timer Sync Error";
					var message = "The time may not be in sync with the server.";
					
					popDownErrorNoticeBox("<b>"+header+":</b> "+message);
				}
			});
		}
	}else{
		
		// Set up Countdown
		$('span.timetools-counter').stop();
		var timerControl = $('span.timetools-counter').countDown({
			//startNumber: appointmentDate.getTime(),
			startNumber: new Date(),
			endNumber: currentAppointmentEndDate,
			returnDate: true,
			callBack: function(me) {
				$("#settings").hide();
				//$(me).text('Your Session is Over.').css('color','#b00');
				$(me).text('00:00:00').css('color','#b00');
				checkAppointmentOver(me);
			}
		});
		
	}

}

function checkAppointmentOver(me) {
	var instanceid = $('#veInsId').html();
	
	$.ajax({
		type: 'POST',
		url: 'php/vmcontrols.php',
		dataType: 'json',
		async: true,
		data: {
			action: 'getAppointmentTimer',
			instanceId: instanceid
		},
		success: function(data){
		
			if(data){
				if(data.success){
					$(me).text('calculating...').css('color','#fff');
					timeOverIsSet = false;
					setTimeControl();
					
					//var endDate = data.endDate;
				}else{
					$(me).text('Your session is over.').css('color','#b00');
					if(iscerttest){
						window.frames[0].document.getElementById('timeup').value = 1;
						var ourForm = window.frames[0].document.getElementById('responseform');
						ourForm.submit();
					}else{
						// Masoud Sadjadi (SMS): Jan. 27, 2015
						// Added to make sure the appointment is not 
						// automatically renewed for the embedded version, if deva page is 
						// on when the time goes out!
						window.location = $("#courseURL").val();
						setTimeout(function(){ window.location.reload(); },3000);	
					}
					//closeQuizLeftOpen();
					//window.location.reload();
				}
			}
		}
	});
	
}
// JAM: 03/17/2012
function setupScreenOptions(){
	
	$("#screenOptions").html('<span class="minutesform">&nbsp;resolution:&nbsp;</span><select id="resolution"></select>');
	$("#screenOptions").append('<span class="minutesform">&nbsp;&nbsp;color depth:&nbsp;</span><select id="bpp"></select>');
	$("#screenOptions").append('<input id="savedBpp" type="hidden" value="16">');
	$("#screenOptions").append('<input id="savedRes" type="hidden" value="default">');
	
	// Color Depth Options
	$("#bpp").append('<option value="15">15</option>');
	$("#bpp").append('<option value="16">16</option>');
	$("#bpp").append('<option value="24" selected="selected">24</option>');
	
	// Resolution Options
	$("#resolution").append('<option value="default" selected="selected">default</option>');
	$("#resolution").append('<option value="640x480">640x480</option>');
	$("#resolution").append('<option value="800x600">800x600</option>');
	$("#resolution").append('<option value="1024x768">1024x768</option>');
	
	$.ajax({
		type: 'POST',
		url: 'php/vmcontrols.php',
		dataType: 'json',
		async: true,
		data: {
			action: 'getBpp',
			userid: $('#userid').val()
		},
		success: function(data){
			var changed = false;
			if(data){
				$("#bpp option").each(function () {
					var text = $(this).text();
	
					if(text == data.data){
						changed = true;
						$(this).attr("selected","selected");
					}else
						$(this).attr("selected","");
				});
				$("#savedBpp").val(data.data);
			}
		}
	});
	$.ajax({
		type: 'POST',
		url: 'php/vmcontrols.php',
		dataType: 'json',
		async: true,
		data: {
			action: 'getResolution',
			userid: $('#userid').val()
		},
		success: function(data){
			var changed = false;
			if(data){
				$("#resolution option").each(function () {
					var text = $(this).text();
	
					if(text == data.data){
						changed = true;
						$(this).attr("selected","selected");
					}else
						$(this).attr("selected","");
				});
				$("#savedRes").val(data.data);
			}
		}
	});
	
	$("#bpp").change(function(){
		var bpp = $(this).val();
		$.ajax({
			type: 'POST',
			url: 'php/vmcontrols.php',
			dataType: 'text',
			async: true,
			data: {
				action: 'setBpp',
				userid: $('#userid').val(),
				bpp: bpp
			},
			success: function(data){
				var changed = false;
				var oldBpp = $("#savedBpp").val();
				if(data.trim() != "changed"){
					$("#bpp option").each(function () {
						var text = $(this).text();
		
						if(text == oldBpp){
							$(this).attr("selected","selected");
						}else
							$(this).attr("selected","");
					});
				}else{
					refreshScreen();
				}
			}
		});
	});
	
	$("#resolution").change(function(){
		var resolution = $(this).val();
		$.ajax({
			type: 'POST',
			url: 'php/vmcontrols.php',
			dataType: 'text',
			async: true,
			data: {
				action: 'setResolution',
				userid: $('#userid').val(),
				resolution: resolution
			},
			success: function(data){
				var changed = false;
				var oldResolution = $("#savedRes").val();
				if(data.trim() != "changed"){
					$("#resolution option").each(function () {
						var text = $(this).text();
		
						if(text == oldResolution){
							$(this).attr("selected","selected");
						}else
							$(this).attr("selected","");
					});
				}else{
					refreshScreen();
				}
			}
		});
	});
	
	
}
// JAM: 03/17/2012
function refreshScreen(){
	$("#devaTabs a.devaTabs.selected").click();
}

function setupTimeControlButtons(){
/*
	setInterval(function(){
		alert(window.frames.mainscreen.pageIsLoaded);
	}, 10000);
*/

	// Buttons for the Time Tools
	$("#timetools button.addtime").button({
		icons: {
			primary: "ui-icon-plus"
		},
		label: "Add time to your virtual-lab appointment.",
		text: false,
		disabled: true
	}).click(function(){
		//trace("#timetools button.addtime");
		var addmins = parseInt($("#editapptime").val());
		
		if(addmins){
			if(addmins >= 5){
				modifyAppointment(addmins,true);
			}else{
				noticeDialog("Extend Appointment", "You must enter 5 minutes or more.", "alert");	
			}
		}else{
			noticeDialog("Extend Appointment", "Please enter a valid number.", "alert");		
		}
	});

	$("#timetools button.minustime").button({
		icons: {
			primary: "ui-icon-minus"
		},
		label: "Remove time from your virtual-lab appointment.",
		text: false,
		disabled: true
	}).click(function(){
		trace("#timetools button.minustime");
		var addmins = parseInt($("#editapptime").val());
		
		if(addmins){
			if(addmins >= 5){
				modifyAppointment(addmins, false);
			}else{
				noticeDialog("Extend Appointment", "You must enter 5 minutes or more.", "alert");	
			}
		}else{
			noticeDialog("Extend Appointment", "Please enter a valid number.", "alert");		
		}
	});

	$("#timetools button.cancel").button({
		icons: {
			primary: "ui-icon-eject"
		},
		label: "Cancel the remaining virtual-lab appointment.",
		text: false,
		disabled: true
	}).click(function(){
		trace("#timetools button.cancel");
		cancelAppointment();
	
	});

	$("#timetools").removeClass("ui-widget-header");
	$("#timetools").removeClass("ui-corner-all");
	
	$("div.navbar").addClass("timer-navbar");
	$("div.navbar div.breadcrumb").addClass("timer-breadcrumb");
	$("div.navbar div.navbutton").addClass("timer-navbutton");
	$("div.navbar div.navbutton").addClass("navbar");
	
    //$("div.timer-navbutton").hide();

}

function setupVMControlButtons(instanceId, vmname){
	var state = getRdpTabInfo('state', currentTabSelected);
/*
	
	$("#vmControls li.powerOff").click(function(){
		vmInstanceCmd('powerOff');
	});
	$("#vmControls li.shutdown").click(function(){
		vmInstanceCmd('shutdown');
	});
	$("#vmControls li.pause").click(function(){
		vmInstanceCmd('suspend');
	});
	$("#vmControls li.powerOn").click(function(){
		vmInstanceCmd('powerOn');
	});
	$("#vmControls li.restart").click(function(){
		vmInstanceCmd('restart');
	});
	
	*/


	$("#toolbar button.powerOff").button({
		icons: {
			primary: "ui-icon-power"
		},
		label: "Power Off",
		text: false,
		disabled: true
	}).click(function(){
		trace("#toolbar button.powerOff");
		//$("#vmcDebug").html('wait');
	//	clearRDPScreen();
		popDownInfoNoticeBox("<b>Instance Command:</b><br/> Power Off");
		markCurrentInstanceState('disabled');
		
		var bvObj = new vmcObj();
		setTimeout(function(){ buttonBundleClick('powerOff', instanceId, vmname, bvObj); },3000);
		
		//setTimeout("buttonBundleClick('powerOff', '"+instanceId+"', '"+vmname+"');",3000);
		//vmInstanceCmd('powerOff', instanceId, vmname);
		//vmInstanceCmd('getState', instanceId, vmname);
		//if(getInstanceState(instanceId, vmname) == "off"){
		
		//alert("off");
		//showCmdMessages('powerOff');
	});

	$("#toolbar button.shutdown").button({
		icons: {
			primary: "ui-icon-stop"
		},
		text: false,
		label: "Shutdown",
		disabled: true
	}).click(function(){
		trace("#toolbar button.shutdown");
	//	clearRDPScreen();
		popDownInfoNoticeBox("<b>Instance Command:</b><br/> Shutdown");
		markCurrentInstanceState('disabled');
		
		var bvObj = new vmcObj();
		setTimeout(function(){ buttonBundleClick('shutdown', instanceId, vmname, bvObj); },3000);
		
		//setTimeout("buttonBundleClick('shutdown', '"+instanceId+"', '"+vmname+"')",3000);
		//vmInstanceCmd('shutdown', instanceId, vmname);
		//vmInstanceCmd('getState', instanceId, vmname);
		//showCmdMessages('shutdown');
	});
	
	$("#toolbar button.pause").button({
		icons: {
			primary: "ui-icon-pause"
		},
		label: "Pause",
		text: false,
		disabled: true
	}).click(function(){
		trace("#toolbar button.pause");
	//	clearRDPScreen();
		popDownInfoNoticeBox("<b>Instance Command:</b><br/> Suspend");
		markCurrentInstanceState('disabled');
		
		var bvObj = new vmcObj();
		setTimeout(function(){ buttonBundleClick('suspend', instanceId, vmname, bvObj); },3000);
		
		//setTimeout("buttonBundleClick('suspend', '"+instanceId+"', '"+vmname+"')",3000);
		//vmInstanceCmd('suspend', instanceId, vmname);
		//vmInstanceCmd('getState', instanceId, vmname);
		//showCmdMessages('suspend');
	});
	
	$("#toolbar button.powerOn").button({
		icons: {
			primary: "ui-icon-play"
		},
		label: "Power On",
		text: false,
		disabled: true
	}).click(function(){
		trace("#toolbar button.powerOn");
	//	clearRDPScreen();
		popDownInfoNoticeBox("<b>Instance Command:</b><br/> Power On");
		markCurrentInstanceState('disabled');
		
		var bvObj = new vmcObj();
		setTimeout(function(){ buttonStartClick('powerOn', instanceId, vmname, bvObj); },3000);
		
		//setTimeout("buttonStartClick('powerOn', '"+instanceId+"', '"+vmname+"')",3000);
		//vmInstanceCmd('powerOn', instanceId, vmname);
		////vmInstanceCmd('getState', instanceId, vmname);
		
	});
	
	$("#toolbar button.restart").button({
		icons: {
			primary: "ui-icon-refresh"
		},
		label: "Restart",
		text: false,
		disabled: true
	}).click(function(){
		trace("#toolbar button.restart");
	//	clearRDPScreen();
		popDownInfoNoticeBox("<b>Instance Command:</b><br/> Restart");
		markCurrentInstanceState('disabled');
		
		var bvObj = new vmcObj();
		setTimeout(function(){ buttonStartClick('restart', instanceId, vmname, bvObj); },3000);
		
		//setTimeout("buttonStartClick('restart', '"+instanceId+"', '"+vmname+"')",3000);
		//vmInstanceCmd('restart', instanceId, vmname);
		////vmInstanceCmd('getState', instanceId, vmname);
		
	});
	
	$("#toolbar button.refresh").button({
		icons: {
			primary: "ui-icon-trash"
		},
		label: "Refresh",
		text: true,
		disabled: true
	}).click(function(){
		trace("#toolbar button.refresh");
		//clearRDPScreen();
		//setTimeout("confirmRefresh('"+instanceId+"', '"+vmname+"')",5000);
		//vmInstanceCmd('refresh');
		//vmInstanceCmd('getState');
		var bvObj = new vmcObj();
		confirmRefresh(instanceId, vmname, bvObj);
	});

	//markCurrentInstanceState("off");
	//markCurrentInstanceState("suspended");
	//markCurrentInstanceState("on");
	//markCurrentInstanceState("error");
	
	$("#toolbar").removeClass("ui-widget-header");
	$("#toolbar").removeClass("ui-corner-all");
	
	if($("div.navbar div.navbutton").hasClass("timer-navbutton-cert")){
		$("div.navbar div.navbutton").addClass("timer-navbutton");
		$("div.navbar div.navbutton").removeClass("timer-navbutton-cert");	
	}
	
	if(state)
		markCurrentInstanceState(state, 'setupVMControlButtons');
}

function clearRDPScreen(){
	
	var message = "please wait... ";
	
	$("#mainscreenid").attr('src','webRDPMessage.php?tab='+currentTabSelected+'&message='+message);

}

function buttonBundleClick(command, instanceId, vmname, obj){
	//var vbobj = new vmcObj();
	obj.vmInstanceCmd(command, instanceId, vmname);
	//parseStatefromCommand(command, currentTabSelected);
	
	//clearInterval(stateInterval);
	//checkRDPMachineStatus(true);
	
	//showCmdMessages(command);
}

function buttonStartClick(command, instanceId, vmname, obj){
	//var vsobj = new vmcObj();
	obj.vmInstanceCmd(command, instanceId, vmname);
	//parseStatefromCommand(command, currentTabSelected);
	
	//clearInterval(stateInterval);
	//checkRDPMachineStatus(true);
}
/*
function startSpinner(){
	var message = "please wait... ";
	
	//if(showSpinner){
		$.loading(true,{ delay: 5000, align: 'top-center', img: 'css/images/loading.gif', text: message });
		//$.loading(false);
		isSpinning = true;
		$("#vmcDebug").html('isSpinning:'+isSpinning);
	//}

}
function stopSpinner(){

	if(isSpinning){
		$.loading(false);
		isSpinning = false;
	}
	//$("#vmcDebug").html('isSpinning:'+isSpinning);

}
*/
function getTimeControlHTML(){
	
	var html = '<span id="timetools" class="ui-widget-header ui-corner-all">' +
				'<span class="minutesform">minutes:</span> <input type="text" name="editapptime" id="editapptime" value="5" maxlength="2" class="minutesform ui-corner-all" />' +
				'<button class="addtime">Add</button>' +
				'<button class="minustime">Minus</button>' +
				'<button class="cancel">Cancel</button>' +
				'</span>';
	//if(iscerttest)
	//	html = '<span id="timetools" class="ui-widget-header ui-corner-all"></span>';
				
	return html;
}
function getTimeClockHTML(){
	
	var html = '<span class="timetools-counter"></span>';
	return html;
}

function getVMControlHTML(){

	var html = '<span id="toolbar" class="ui-widget-header ui-corner-all">' +
				'<button class="powerOff">Power Off</button>' +
				'<button class="powerOn">Power On</button>' +
				'<button class="shutdown">Shutdown</button>' +
				'<button class="restart">Reset</button>' +
				'<button class="pause">Suspend</button>' +
				'<button class="refresh">Refresh</button>' +
				'</span>';
	
	return html;
}


	//http://jquery-ui.googlecode.com/svn/tags/1.6rc5/tests/static/icons.html

function startStatusInterval(){

	//alert(currentTabSelected);
	checkRDPMachineStatus(true);

}


/*

function checkRDPStatus(instanceId, vmname, hostName, hostPort, init, repeat){

	
	var state = getInstanceState(instanceId, vmname);
	
	if(repeat){
		if(state == 'on'){
			clearInterval(stateInterval);
			isRDPReady(instanceId, vmname, hostName, hostPort, init);
		}else{
			if(init){
				stateInterval = setInterval("checkRDPStatus('"+instanceId+"','"+vmname+"','"+hostName+"','"+hostPort+"',false,true)",10000);	
			}
		}
	}else{
		if(state == 'on'){
			isRDPReady(instanceId, vmname, hostName, hostPort, init);
			//alert('here');
		}else{
			//alert("showCmdMessages(command): "+state);	
			markCurrentInstanceState(state);
			
			showCmdMessages(state);
			//vmInstanceCmd(command, instanceId, vmName);
		}
	}
	
}
*/
/*
DEFAULTS
-----------------------------------------
tabId:		'tab'+i, 
ready:		false,
showing:	false, 
state:		null,
veInsId:	vms.vmInfo[0].veInsId,
veInsAddr:	vms.vmInfo[0].accessAddress,
veInsPort:	vms.vmInfo[i].accessPort,
veInsURL:	linkURL

*/

function setRdpTabInfo(pair, tabId, value){
	//if(pair == 'state')
	//	alert(tabId + " = " +value);
	//if(pair == 'state')
		//$("#vmcDebug2").html('setRdpTabInfo: '+tabId + ' value: '+value);
	for(var i = 0; i < rdpTabInfo.length; i++){
		if(rdpTabInfo[i].tabId == tabId)
			rdpTabInfo[i][pair] = value;
	}
}
function getRdpTabInfo(pair, tabId){
	//$("#vmcDebug").html('getRdpTabInfo: '+tabId);
	var value;
	for(var i = 0; i < rdpTabInfo.length; i++){
		if(rdpTabInfo[i].tabId == tabId)
			value = rdpTabInfo[i][pair];
	}
	return value;
}

/*
function isRDPReady(instanceId, vmname, hostName, hostPort, init) {
 	var debug = "";
 
	$.ajax({
		type: 'POST',
		url: 'php/vmcontrols.php',
		dataType: 'json',
		async: false,
		data: {
			action: 'isRDPReady',
			hostName: hostName,
			hostPort: hostPort
		},
		success: function(data) {
			if(data.success){
				if(data.ready){
					debug = "debug 1";
					rdpIsReady = true;
					if(!init){
						clearInterval(rdpInterval);
					}
					
					//var url = $("#"+currentTabSelected).attr('href');
					var url = $("#veInsURL-"+currentTabSelected).html();
						
					$("#mainscreenid").attr('src',url.replace(/&amp;/g,"&"));
					tabAlreadyLoaded = true;
					
					markCurrentInstanceState('on');
					
				}else{
					debug = "debug 2";
					// Shows a message while waiting
					var message = "This virtual machine is not ready!<br/> Please be patient while the RDP server loads.";
					markCurrentInstanceState('disabled');

					$("#mainscreenid").attr('src','webRDPMessage.php?tab='+currentTabSelected+'&message='+message);
					
					rdpIsReady = false;
					if(init){
						rdpInterval = setInterval("isRDPReady('"+instanceId+"','"+vmname+"','"+hostName+"','"+hostPort+"',false)",10000);
					}
					
				}
			}else{
				debug = "debug 3";
				noticeDialog("RDP Validation", data.reason, "alert");	
				clearInterval(rdpInterval);
				
				wasRDPMachineReady = false;
			}
			
			wasRDPMachineReady = rdpIsReady;
		},
		error: function(XMLHttpRequest, textStatus, errorThrown){
			var vobj = new vmcObj();
			vobj.vmInstanceCmd('getState',instanceId, vmname);
			var header = "Instance Command Error: isRDPReady";
			var message = "Command could not be completed.";
			var icon = "alert";
			//noticeDialog(header, message, icon);
			popDownErrorNoticeBox("<b>"+header+"</b><br/> "+message);
			
			wasRDPMachineReady = false;
		}

	});
	
	//popDownErrorNoticeBox(debug);

}

*/


function parseStatefromCommand(command, workingTab){
	if(command == 'powerOff' || command == 'shutdown'){
		setRdpTabInfo('state', workingTab, 'off', 'parseStatefromCommand');
	}else if(command == 'suspend'){
		setRdpTabInfo('state', workingTab, 'suspended', 'parseStatefromCommand');
	}else if(command == 'powerOn'){
		setRdpTabInfo('state', workingTab, 'on', 'parseStatefromCommand');
	}else{
		// if 'restart' or 'refresh'	
		setRdpTabInfo('state', workingTab, null, 'parseStatefromCommand');
	}
}
	



/*
//getter
var disabled = $( ".selector" ).button( "option", "disabled" );
//setter
$( ".selector" ).button( "option", "disabled", true );
*/
function markCurrentInstanceState(state,debug){
	//alert('markCurrentInstanceState:'+state);
	
	//$("#vmcDebug").html('markCurrentInstanceState: '+state+"<br/>Function: "+debug);
	
	if(state == "disabled"){
	
		$("#toolbar button.powerOff").button("option", "disabled", true);
		$("#toolbar button.powerOn").button("option", "disabled", true);
		$("#toolbar button.pause").button("option", "disabled", true);
		$("#toolbar button.shutdown").button("option", "disabled", true);
		$("#toolbar button.restart").button("option", "disabled", true);
		$("#toolbar button.refresh").button("option", "disabled", true);
	
	}else if(state == "off"){
	
		$("#toolbar button.powerOff").button("option", "disabled", true);
		$("#toolbar button.powerOn").button("option", "disabled", false);
		$("#toolbar button.pause").button("option", "disabled", true);
		$("#toolbar button.shutdown").button("option", "disabled", true);
		$("#toolbar button.restart").button("option", "disabled", true);
		$("#toolbar button.refresh").button("option", "disabled", false);
		
	}else if(state == "suspended"){
	
		$("#toolbar button.powerOff").button("option", "disabled", true);
		$("#toolbar button.powerOn").button("option", "disabled", false);
		$("#toolbar button.pause").button("option", "disabled", true);
		$("#toolbar button.shutdown").button("option", "disabled", true);
		$("#toolbar button.restart").button("option", "disabled", true);
		$("#toolbar button.refresh").button("option", "disabled", false);
	
	}else if(state == "on"){
		
		$("#toolbar button.powerOff").button("option", "disabled", false);
		$("#toolbar button.powerOn").button("option", "disabled", true);
		$("#toolbar button.pause").button("option", "disabled", false);
		$("#toolbar button.shutdown").button("option", "disabled", false);
		$("#toolbar button.restart").button("option", "disabled", false);
		$("#toolbar button.refresh").button("option", "disabled", false);
	
	}else{
	
		$("#toolbar button.powerOff").button("option", "disabled", true);
		$("#toolbar button.powerOn").button("option", "disabled", false);
		$("#toolbar button.pause").button("option", "disabled", true);
		$("#toolbar button.shutdown").button("option", "disabled", true);
		$("#toolbar button.restart").button("option", "disabled", true);
		$("#toolbar button.refresh").button("option", "disabled", false);
		
	}
	
	
}

function showCmdMessages(command){
	//alert('showCmdMessages:'+command);
	if(command == "powerOn"){
		var message = "This virtual machine is turning on! This process may take from 5 seconds up to 2 minutes. So, please be patient! If you see a Terminal Server Connection Error message, you should wait for 10 seconds and try again by clicking on the tab for this virtual machine.";
		
		//$("#mainscreenid").contents().find("body").html("<center><p style='padding:120px 20px 20px 20px; width:50%;'>"+message+"<p></center>");
		$("#mainscreenid").attr('src','webRDPMessage.php?tab='+currentTabSelected+'&message='+message);
		
	}else if(command == "powerOff"){
		
		var message = "This virtual machine has been turned off! If you want to turn it on, you would need to click the Power On button.";
		$("#mainscreenid").attr('src','webRDPMessage.php?tab='+currentTabSelected+'&message='+message);
		
	}else if(command == "shutdown"){
		var message = "This virtual machine has been shutdown! If you want to turn it on, you would need to click the Power On button.";
		$("#mainscreenid").attr('src','webRDPMessage.php?tab='+currentTabSelected+'&message='+message);
		
	}else if(command == "suspend"){
		var message = "This virtual machine has been suspended/paused! If you want to turn it on, you would need to click the Power On button.";
		$("#mainscreenid").attr('src','webRDPMessage.php?tab='+currentTabSelected+'&message='+message);
		
	}else if(command == "restart"){
		var message = "This virtual machine is being restarted! This process may take from 20 seconds up to 2 minutes. So, please be patient! If you see a Terminal Server Connection Error message, you should wait for 10 seconds and try again by clicking on the tab for this virtual machine.";
		$("#mainscreenid").attr('src','webRDPMessage.php?tab='+currentTabSelected+'&message='+message);
		
	}else if(command == "refresh"){
		markCurrentInstanceState('disabled');
		var message = "This virtual machine is being refreshed! This process may take from 20 seconds up to 2 minutes. So, please be patient! If you see a Terminal Server Connection Error message, you should wait for 10 seconds and try again by clicking on the tab for this virtual machine.";
		$("#mainscreenid").attr('src','webRDPMessage.php?tab='+currentTabSelected+'&message='+message);
		
	}else if(command == "off"){
		var message = "This virtual machine has been turned off! If you want to turn it on, you would need to click the Power On button.";
		$("#mainscreenid").attr('src','webRDPMessage.php?tab='+currentTabSelected+'&message='+message);
		
	}else if(command == "suspended"){
		var message = "This virtual machine has been suspended/paused! If you want to turn it on, you would need to click the Power On button.";

		$("#mainscreenid").attr('src','webRDPMessage.php?tab='+currentTabSelected+'&message='+message);
		
	}else if(command == "on"){
		var message = "This virtual machine is getting ready! This process may take from 5 seconds up to 2 minutes. So, please be patient! If you see a Terminal Server Connection Error message, you should wait for 10 seconds and try again, by clicking on the tab for this virtual machine.";
		noticeDialog("Virtual Machine Notification", message, "alert");
	}

}


function popDownInfoNoticeBox(message,time){
	
	$("#infoMessage span.buttonPanel").html('');
	$("#infoMessage").removeClass("confirmMessageBox");
	$("#infoMessage").addClass("infoMessageBox");	
	
	if(!time) time = 10000;

	$("#infoText").html(message);
	$("#infoMessage").slideDown('slow', function() {
		$(this).click(function(){
			trace("#infoMessage");
			$("#infoMessage").slideUp();
		});
		setTimeout('$("#infoMessage").slideUp();',time);
	});
}
function popDownErrorNoticeBox(message,time){
	if(!time) time = 10000;

	$("#errorText").html(message);
	$("#errorMessage").slideDown('slow', function() {
		$(this).click(function(){
			trace("#errorMessage");
			$("#errorMessage").slideUp();
		});
		setTimeout('$("#errorMessage").slideUp();',time);
	});
}

function callDynamicFunction(){
	buttonFunc(); 
}

function popDownButtonNoticeBox(header, message, callFunc){	
	
	var buttons = "<button class='okButton'>Ok</button><button class='cancelButton'>Cancel</button>";
	buttonFunc = function() {
		//alert('test');
		callFunc();
	};
	
	$("#infoMessage span.buttonPanel").html(buttons);
	
	$("#infoMessage").removeClass("infoMessageBox");
	$("#infoMessage").addClass("confirmMessageBox");
	
	$("#infoMessage button").button();
	$("button.okButton").click(function() { 
		trace("button.okButton");
		clearRDPScreen();
		$("#infoMessage").slideUp();
		markCurrentInstanceState('disabled');
		setTimeout("callDynamicFunction()",5000);
		
		//okFunc(); 
	});	
	$("button.cancelButton").click(function() {
		trace("button.cancelButton");
		$("#infoMessage").slideUp();
	});	
	
	$("#infoText").html("<b>"+header+"</b><br/> "+message);
	$("#infoMessage").slideDown('slow');

}

function noticeDialog(header, message, icon, returnObj, reloadWindow){
	
	var noticeContent = $("<div id='calendar-notice' />").html('<p><span class="ui-icon ui-icon-'+icon+'" style="float:left; margin:0 7px 20px 0;"></span>'+message+'</p>');
	
	$(noticeContent).dialog({
		modal: true,
		title: header,
		close: function() {
		   $(noticeContent).dialog("destroy");
		   $(noticeContent).hide();
		},
		buttons: {
			Ok: function() {
				$(this).dialog('close');
				if(reloadWindow){
					window.location.reload();	
				}
			}
		}
	});
	
	$(noticeContent).dialog('open');
	
	if(returnObj){
		return noticeContent;
	}
}


function confirmRefresh(instanceId, vmname, obj){

	var header = "Refresh Virtual Machine";
	var message = "All your work on this virtual machine will be lost.<br/>Do you want to continue?";
	
	popDownButtonNoticeBox(header, message, function(){ buttonStartClick('refresh', instanceId, vmname, obj); });

/*
	var refreshNoticeContent = $("<div id='new-confirm' />").html('<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>WARNING! <br/>All your work on this virtual machine will be lost.<br/><br/> Do you want to continue?</p>');
	var success = false;
				
	$(refreshNoticeContent).dialog({
		autoOpen: false,
		resizable: false,
		width: 350,
		title: "Refresh Virtual Machine",
		modal: true,
		close: function() {
		   $(refreshNoticeContent).dialog("destroy");
		   $(refreshNoticeContent).hide();
		},
		buttons: {
			Ok: function() {
				$(this).dialog('close');
				//vmInstanceCmd('refresh', instanceId, vmname);
				//vmInstanceCmd('getState', instanceId, vmname);
				buttonStartClick('refresh', instanceId, vmname);
			},
			Cancel: function() {
				$(this).dialog('close');
			}
		}
	});
	
	$(refreshNoticeContent).dialog('open');
*/
}

function cancelAppointment(){

	var success = false;

	$.ajax({
		type: 'POST',
		url: '../scheduler/fullcalendar/calendar.php',
		dataType: 'xml',
		async: false,
		timeout: 4000,
		data: {
			action: 'cancelAppointment',
			requestingUser:  $('#username').val(),
			username: $('#username').val(),
			id: currentAppointmentId,
			requestType: "User"
		},
		success: function(data){
			
			var successText, reason, successInt;
			
			$(data).find('reason').each(function() {
				reason = $(this).text();					  
			});
			$(data).find('success').each(function() {
				successText = $(this).text();								  
			});
			
			successInt = parseInt(successText);
			
			if(successInt > 0){
				success = true;
				
			}else{
				success = false;
				
			}
			
			popDownInfoNoticeBox("<b>Delete Appointment:</b> "+reason);
			//noticeDialog("Delete Appointment", reason, "alert", false, true);
			//window.location.reload();
			
			window.location = $("#courseURL").val();
			
		},
		error: function(XMLHttpRequest, textStatus, errorThrown){
			var header = "Delete Appointment";
			var message = "We were unable to remove this appointment.<br/> **please try again later.";
			var icon = "alert";
			message = textStatus + " : " +errorThrown;
			//noticeDialog(header, message, icon);
			popDownErrorNoticeBox("<b>"+header+":</b> "+message);
		}
	});
	
	return success;

}

function modifyAppointment(mins,addmins){
	
	if (!addmins) {
		mins = -1 * mins;
	}
	
	// alert("mins: " + mins);
	success = false;
	
	$.ajax({
		type: 'POST',
		url: '../scheduler/fullcalendar/calendar.php',
		dataType: 'xml',
		async: true,
		//timeout: 4000,
		data: {
			action: 'extendAppointment',
			requestingUser:  $('#username').val(),
			username: $('#username').val(),
			id: currentAppointmentId,
			minutes: mins,
			requestType: "User"
		},
		success: function(data){
			
			var successText, reason, successInt;
			
			$(data).find('reason').each(function() {
				reason = $(this).text();					  
			});
			$(data).find('success').each(function() {
				successText = $(this).text();								  
			});
			
			successInt = parseInt(successText);
			if(successInt > 0){
				success = true;
				timeOverIsSet = false;	// flag: tell the timer control to cget time left over for WS
				setTimeControl();
				
			}else{
				success = false;
				
			}
			
			var header = "Extend Appointment";
			if (!addmins) {
				header = "Shorten Appointment";
			}
			popDownInfoNoticeBox("<b>"+header+":</b> "+reason);
			//noticeDialog("Confirm Appointment", reason, "alert");
		},
		error: function(XMLHttpRequest, textStatus, errorThrown){
			var header = "Extend Appointment";
			if (!addmins) {
				header = "Shorten Appointment";
			}
			var message = "We were unable to modify this appointment for you.<br/><br/> **please try again later.";
			var icon = "alert";
			message = textStatus + " : " +errorThrown;
			//noticeDialog(header, message, icon);
			popDownErrorNoticeBox("<b>"+header+":</b> "+message);
			
		}
	});
	
	return success;

}

/*
function modifyAppointment(mins,addmins){
	
	var dateformatter = "yyyy-mm-dd'T'HH:MM:ss";
	var success = false;
	var changedDate = new Date (currentAppointmentEndDate);

	if(addmins){
		changedDate.setMinutes(currentAppointmentEndDate.getMinutes() + mins);
	}else{
		changedDate.setMinutes(currentAppointmentEndDate.getMinutes() - mins);
	}
	
	$.ajax({
		type: 'POST',
		url: '../scheduler/fullcalendar/calendar.php',
		dataType: 'xml',
		async: true,
		//timeout: 4000,
		data: {
			action: 'modifyAppointment',
			requestingUser:  $('#username').val(),
			username: $('#username').val(),
			id: currentAppointmentId,
			start: '',
			end: changedDate.format(dateformatter),
			requestType: "User"
		},
		success: function(data){
			
			var successText, reason, successInt;
			
			$(data).find('reason').each(function() {
				reason = $(this).text();					  
			});
			$(data).find('success').each(function() {
				successText = $(this).text();								  
			});
			
			successInt = parseInt(successText);
			if(successInt > 0){
				success = true;
				timeOverIsSet = false;	// flag: tell the timer control to cget time left over for WS
				setTimeControl();
				
			}else{
				success = false;
				
			}
			
			popDownInfoNoticeBox("<b>Change Appointment:</b> "+reason);
			//noticeDialog("Confirm Appointment", reason, "alert");
		},
		error: function(XMLHttpRequest, textStatus, errorThrown){
			var header = "Modify Appointment";
			var message = "We were unable to modify this appointment for you.<br/><br/> **please try again later.";
			var icon = "alert";
			message = textStatus + " : " +errorThrown;
			//noticeDialog(header, message, icon);
			popDownErrorNoticeBox("<b>"+header+":</b> "+message);
			
		}
	});
	
	return success;

}
*/

function getUserCurApp(){

	var success = false;

	$.ajax({
		type: 'POST',
		url: 'php/vmcontrols.php',
		dataType: 'json',
		async: false,
		data: {
			action: 'getUserCurAppId',
			requestingUser:  $('#username').val(),
			username: $('#username').val(),
			course: $('#course').val(),
			resourceType: $('#resourcetype').val()
		},
		success: function(data){
			
			if(data.success){
				success = data.success;
			}
			
		},
		error: function(XMLHttpRequest, textStatus, errorThrown){
			var header = "Delete Appointment";
			var message = "We were unable to remove this appointment.<br/> **please try again later.";
			var icon = "alert";
			message = textStatus + " : " +errorThrown;
			//noticeDialog(header, message, icon);
			popDownErrorNoticeBox("<b>"+header+":</b> "+message);
		}
	});
	
	return success;

}
	
function trace(message){
	if(!$.browser.msie){
		//console.log(message);
	}
}
