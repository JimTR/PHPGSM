function initializeJS() {

    //tool tips
    jQuery('.tooltips').tooltip();

    //popovers
    jQuery('.popovers').popover();

    //custom scrollbar
        //for html
    jQuery("html").niceScroll({styler:"fb",cursorcolor:"#007AFF", cursorwidth: '6', cursorborderradius: '10px', background: '#F7F7F7', cursorborder: '', zindex: '1000'});
        //for sidebar
    jQuery("#sidebar").niceScroll({styler:"fb",cursorcolor:"#007AFF", cursorwidth: '3', cursorborderradius: '10px', background: '#F7F7F7', cursorborder: ''});
        // for scroll panel
    jQuery(".scroll-panel").niceScroll({styler:"fb",cursorcolor:"#007AFF", cursorwidth: '3', cursorborderradius: '10px', background: '#F7F7F7', cursorborder: ''});
    
    //sidebar dropdown menu
    jQuery('#sidebar .sub-menu > a').click(function () {
        var last = jQuery('.sub-menu.open', jQuery('#sidebar'));        
        jQuery('.menu-arrow').removeClass('arrow_carrot-right');
        jQuery('.sub', last).slideUp(200);
        var sub = jQuery(this).next();
        if (sub.is(":visible")) {
            jQuery('.menu-arrow').addClass('arrow_carrot-right');            
            sub.slideUp(200);
        } else {
            jQuery('.menu-arrow').addClass('arrow_carrot-down');            
            sub.slideDown(200);
        }
        var o = (jQuery(this).offset());
        diff = 200 - o.top;
        if(diff>0)
            jQuery("#sidebar").scrollTo("-="+Math.abs(diff),500);
        else
            jQuery("#sidebar").scrollTo("+="+Math.abs(diff),500);
    });

    // sidebar menu toggle
    jQuery(function() {
        function responsiveView() {
            var wSize = jQuery(window).width();
            if (wSize <= 768) {
                jQuery('#container').addClass('sidebar-close');
                jQuery('#sidebar > ul').hide();
            }

            if (wSize > 768) {
                jQuery('#container').removeClass('sidebar-close');
                jQuery('#sidebar > ul').show();
            }
        }
        jQuery(window).on('load', responsiveView);
        jQuery(window).on('resize', responsiveView);
    });

    jQuery('.toggle-nav').click(function () {
        if (jQuery('#sidebar > ul').is(":visible") === true) {
            jQuery('#main-content').css({
                'margin-left': '0px'
            });
            jQuery('#sidebar').css({
                'margin-left': '-180px'
            });
            jQuery('#sidebar > ul').hide();
            jQuery("#container").addClass("sidebar-closed");
        } else {
            jQuery('#main-content').css({
                'margin-left': '180px'
            });
            jQuery('#sidebar > ul').show();
            jQuery('#sidebar').css({
                'margin-left': '0'
            });
            jQuery("#container").removeClass("sidebar-closed");
        }
    });

    //bar chart
    if (jQuery(".custom-custom-bar-chart")) {
        jQuery(".bar").each(function () {
            var i = jQuery(this).find(".value").html();
            jQuery(this).find(".value").html("");
            jQuery(this).find(".value").animate({
                height: i
            }, 2000)
        })
    }

}

jQuery(document).ready(function(){
    initializeJS();
    updateClock();
    setInterval('updateClock()', 1000);
    //fetchboot();
    //fetchload();
    fetchgames();
    fetchservers();	

});
 $( function() {
  var icons = {
      header: "ui-icon-circle-arrow-e",
      activeHeader: "ui-icon-circle-arrow-s"
    };
    $( "#accordion" )
      .accordion({
      icons: icons,
      collapsible: true,
      active: false ,
      heightStyle: "content",
        header: "> div > h4"
      })
      .sortable( {
        axis: "y",
        handle: "i",
        stop: function( event, ui ) {
          // IE doesn't register the blur when sorting
          // so trigger focusout handlers to remove .ui-state-focus
          ui.item.children( "h3" ).triggerHandler( "focusout" );
 
          // Refresh accordion to handle new order
          $( this ).accordion( "refresh" );
        }
      });
  } );
   $( function() {
    $( "#accordion2" )
      .accordion({
      collapsible: true,
      active: false ,
      heightStyle: "content",
        header: "> div > h4"
      })
      .sortable( {
        axis: "y",
        handle: "i",
        stop: function( event, ui ) {
          // IE doesn't register the blur when sorting
          // so trigger focusout handlers to remove .ui-state-focus
          ui.item.children( "h3" ).triggerHandler( "focusout" );
 
          // Refresh accordion to handle new order
          $( this ).accordion( "refresh" );
        }
      });
  } );
function updateClock ()
    {
    var currentTime = new Date ( );
    var currentHours = currentTime.getHours ( );
    var currentMinutes = currentTime.getMinutes ( );
    var currentSeconds = currentTime.getSeconds ( );
 
    // Pad the minutes and seconds with leading zeros, if required
    currentMinutes = ( currentMinutes < 10 ? "0" : "" ) + currentMinutes;
    currentSeconds = ( currentSeconds < 10 ? "0" : "" ) + currentSeconds;
 
    // Choose either "AM" or "PM" as appropriate
    var timeOfDay = ( currentHours < 12 ) ? "AM" : "PM";
 
    // Convert the hours component to 12-hour format if needed
    currentHours = ( currentHours > 12 ) ? currentHours - 12 : currentHours;
 
    // Convert an hours component of "0" to "12"
    currentHours = ( currentHours == 0 ) ? 12 : currentHours;
 
    // Compose the string for display
    var currentTimeString = currentHours + ":" + currentMinutes + ":" + currentSeconds + " " + timeOfDay;
     
     
    $("#clock").html(currentTimeString);
    // $('#clock').css('color','red');
         
 }

function fetchgames(){
 $.ajax({
 url: 'ajax.php?action=rgames',
  type: 'post',
  success: function(data){
   // Perform operation on return value
   //alert(data);
   $("#rgames").html(data);
  },
  complete:function(data){
   setTimeout(fetchgames,3000);
  }
 });
}
function fetchservers(){
 $.ajax({
 url: 'https://noideersoftware.co.uk:7862/xml.php',
  type: 'post',
  dataType: "xml" ,
  success: function(xml,status){
   
    $(xml).find('Servers').children('base_server').each(function(){
	 var fname = $(this).find('fname').text(); //element name important	
	
	 	 var bfname = fname;
	 //console.log(bfname);
     var hname = $(this).find('name').text();
     var distro = $(this).find('distro').text();
     var boot = $(this).find('uptime').text();
     var load = $(this).find('load').text();
     var ip = $(this).find('ip').text();
     var cpu_model = $(this).find('cpu_model').text();
     var cpu_processors = $(this).find('cpu_processors').text();
     var cpu_cores = $(this).find('cpu_cores').text();
     var cpu_speed = $(this).find('cpu_speed').text();
     var cpu_cache = $(this).find('cpu_cache').text();
     var kernel = $(this).find('kernel').text();
     var php = $(this).find('php').text();
     var screen = $(this).find('screen').text();
     var glibc = $(this).find('glibc').text();
     var mysql = $(this).find('mysql').text();
     var apache = $(this).find('apache').text();
     var curl = $(this).find('curl').text();
     var nginx = $(this).find('nginx').text();
     var quota = $(this).find('quota').text();
     var postfix = $(this).find('postfix').text();
     var memTotal = $(this).find('memTotal').text();
     var memfree = $(this).find('memfree').text();
     var memcache = $(this).find('memcache').text();
     var memactive = $(this).find('memactive').text();
     var swaptotal = $(this).find('swaptotal').text();
     var swapfree = $(this).find('swapfree').text();
     var swapcache = $(this).find('swapcache').text();
     var boot_filesystem = $(this).find('boot_filesystem').text();
     var boot_mount = $(this).find('boot_mount').text();
     var boot_size = $(this).find('boot_size').text();
     var boot_used = $(this).find('boot_used').text();
     var boot_free = $(this).find('boot_free').text();
     //var x = sname'
     $("#boot"+fname).html(boot);
     $("#load"+fname).html(load);
     $("#memtotal"+fname).html(memTotal);
     $("#memfree"+fname).html(memfree);
     $("#memcached"+fname).html(memcache);
     $("#memactive"+fname).html(memactive);
     $("#cpu_model"+fname).html(cpu_model);
     $("#cpu_processors"+fname).html(cpu_processors);
     $("#cpu_cores"+fname).html(cpu_cores);
     $("#cpu_speed"+fname).html(cpu_speed);
     $("#cpu_cache"+fname).html(cpu_cache);
     $("#ip"+fname).html(ip);
     $("#boot_filesystem"+fname).html(boot_filesystem);
     $("#boot_mount"+fname).html(boot_mount);
     $("#boot_size"+fname).html(boot_size);
     $("#boot_used"+fname).html(boot_used);
     $("#boot_free"+fname).html(boot_free);
     $("#swaptotal"+fname).html(swaptotal);
     $("#swapfree"+fname).html(swapfree);
     $("#swapcache"+fname).html(swapcache);
     $("#distro"+fname).html(distro);
     $("#kernel"+fname).html(kernel);
     $("#hname"+fname).html(hname);
     $("#php"+fname).html(php);
     $("#screen"+fname).html(screen);
     $("#apache"+fname).html(apache);
     $("#glibc"+fname).html(glibc);
     $("#mysql"+fname).html(mysql);
     $("#curl"+fname).html(curl);
     $("#nginx"+fname).html(nginx);
     $("#quota"+fname).html(quota);
     $("#postfix"+fname).html(postfix);
     
     //alert ('app id\n'+ app);
     //console.log('fname '+ fname + ' boot '+ boot);
     //console.log(x);
     
    }); 
   var xmlDoc = xml;
    //console.log(bfname);  
    y=0;
    totalplayers=0; 
    totgames=0;
    activegames=0;   
$(xml).find('Servers').children('game_server').each(function(){
	 var fname = $(this).find('name').text(); //element name important
	 var bname = $(this).find('fname').text();
	 var sid = fname+" ("+$(this).find('ip').text()+")";
	 var mplayers = $(this).find('maxplayers').text();
	 var dm = $(this).find('defaultmap').text();
	 var sp = $(this).find('server_pass').text();
	 var rp = $(this).find('rcon_pass').text();
	 var gp = $(this).find('game_port').text();
	 var sp1 = $(this).find('source_port').text();
	 var cp = $(this).find('client_port').text();
	 var st = $(this).find('starttime').text();
	 console.log('st = '+st);
	 $("#dm"+fname).html(dm);
	 $("#sp"+fname).html(sp);
	 $("#rp"+fname).html(rp);
	 $("#mp"+fname).html(mplayers);
	 $("#sid"+fname).html(sid);
	 $("#gp"+fname).html(gp);
	 $("#sp1"+fname).html(sp1);
	 $("#cp"+fname).html(cp);
	 $("#lg"+fname).attr("src",$(this).find('logo').text());
	 $("#status"+fname).attr("src","img/offline1.png"); // set to not sure
	 var rt = $(this).find('rt').text();
	 var players = 0;
	 var online = $(this).find('online').text();
	 if (online === "Online") {
	  $('#'+fname+'response').html(fname+' has started') ; 
	  $('#'+fname+'qbutton').show();
      $('#'+fname+'sbutton').hide();
      $('#'+fname+'cbutton').show();
      $('#'+fname+'rbutton').show();
      $('#'+fname+'vbutton').hide();
      $('#'+fname+'ubutton').hide();
      $('#'+fname+'bbutton').hide();
      console.log (fname+' turn off start'); 
      $('#'+fname+'response').delay(5000).fadeOut('slow');
		 $("#status"+fname).attr("src","img/online.png"); // set to online
		 var cmap = $(this).find('currentmap').text();
	     var players = $(this).find('players').text();
	     var hn = $(this).find('host_name').text();
	     $("#po"+fname).html(players);
	     $("#to"+fname).html(rt);
	     $("#sn"+fname).html(hn);
	     $("#cm"+fname).html(cmap);
	     
	     totgames = parseInt(totgames)+1;
	     totalplayers = parseInt(totalplayers,10) + parseInt(players,10);
	     var start = $(this).find('starttime').text();
	 if (players >0 ){
		 activegames=parseInt(activegames)+1;
			var x = xmlDoc.getElementsByTagName("current_players")[y];
	        var corpName = $(this).find('pname').text();
            var result = corpName.split('|');
            var corpName = $(this).find('pscore').text();
            var score = corpName.split('|');
            var corpName = $(this).find('ponline').text();
            var time = corpName.split('|');
			//console.log("cn "+corpName);
			$.each(result, function (index, value) {
				if (time[index]===""){
					// this can happen .. ignore it
				}
				else if ( typeof time[index] === "undefined") {
					// throw away tat
					//console.log("Player "+value+" Score "+ score[index]+" Time "+time[index]);
				}
				else {
					//console.log("Player "+value+" Score "+ score[index]+" Time "+time[index]);
					// here process the playerlist 
	}
    });
           
 
 }
 else {
	 //console.log("no one is playing on "+fname+" Current Map "+cmap+ " started at "+start);
	 //here make sure playerlist is empty & update times etc
 }
 
 y=y+1; 
	
 }
 else {
	 //console.log(fname+" is off line");
	 // todo add screen session Id to xml 
	 if (st === "" ) {
		$('#'+fname+'response').html(fname+' has stopped') ; 
		$("#status"+fname).attr("src","img/offline.png"); // set to offline
		$('#'+fname+'response').delay(5000).fadeOut('slow');
		$('#'+fname+'qbutton').hide();
		$('#'+fname+'sbutton').show();
		$('#'+fname+'cbutton').hide();
		$('#'+fname+'rbutton').hide();
		$('#'+fname+'vbutton').show();
		$('#'+fname+'ubutton').show();
		$('#'+fname+'bbutton').show();
		
	}
	else {
		// mid state screen open but no response
		$("#status"+fname).attr("src","img/offline1.png"); // set to mid state
		//$('#'+fname+'response').delay(5000).fadeOut('slow');
	}
	 $("#to"+fname).html('N/A');
	 $("#cm"+fname).html('N/A');
	 $("#po"+fname).html('N/A');
	
     //
 }
  //add outer data 
  $("#gsr"+bname).html(totgames);
  $("#asr"+bname).html(activegames);
  $("#tp"+bname).html(totalplayers);
  
    });
    
  
  },
  complete:function(xml){
     setTimeout(fetchservers,4000);
  }
 });
}
