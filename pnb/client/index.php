<!DOCTYPE HTML>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
<title>sPHENIX Collaboration - PhoneBook</title>

<link rel="stylesheet" type="text/css" href="css/jquery.layout.css" >
<link rel="stylesheet" type="text/css" href="css/redmond/jquery.ui.min.css" > 
<link rel="stylesheet" type="text/css" href="css/jquery.dataTables.css" >
<link rel="stylesheet" type="text/css" href="css/styles.css" >
<link rel="stylesheet" type="text/css" href="css/jquery.jqplot.css" >
<link rel="stylesheet" type="text/css" href="css/leaflet.css" >

<script type="text/javascript" src="js/jquery.min.js"></script>
<script type="text/javascript" src="js/jquery.ui.min.js"></script>
<script type="text/javascript" src="js/jquery.layout.min.js"></script>
<script type="text/javascript" src="js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="js/jquery.chained.min.js"></script>
<script type="text/javascript" src="js/strtotime.js"></script>
<script type="text/javascript" src="js/download2.js"></script>
<script type="text/javascript" src="js/leaflet.js"></script>
<script src="https://maps.google.com/maps/api/js?v=3&sensor=false"></script>
<script type="text/javascript" src="js/Google.js"></script>
<script src="https://api-maps.yandex.ru/2.0/?load=package.map&lang=ru-RU" type="text/javascript"></script>
<script type="text/javascript" src="js/Yandex.js"></script>

<!--[if lt IE 9]><script language="javascript" type="text/javascript" src="js/excanvas.min.js"></script><![endif]-->
<script type="text/javascript" src="js/jquery.jqplot.js"></script>
<SCRIPT type="text/javascript" src="js/plugins/jqplot.pieRenderer.min.js"></SCRIPT>
<SCRIPT type="text/javascript" src="js/plugins/jqplot.barRenderer.min.js"></SCRIPT>
<SCRIPT type="text/javascript" src="js/plugins/jqplot.categoryAxisRenderer.min.js"></SCRIPT>
<SCRIPT type="text/javascript" src="js/plugins/jqplot.pointLabels.min.js"></SCRIPT>
<SCRIPT type="text/javascript" src="js/plugins/jqplot.canvasAxisLabelRenderer.min.js"></SCRIPT>
<SCRIPT type="text/javascript" src="js/plugins/jqplot.canvasAxisTickRenderer.min.js"></SCRIPT>
<SCRIPT type="text/javascript" src="js/plugins/jqplot.canvasTextRenderer.min.js"></SCRIPT>

<script type="text/javascript" src="js/restful-client.js"></script>

<script type="text/javascript">
  var client = new RCLIENT.Phonebook();

  var myLayout;
  $(document).ready(function() { 
  	myLayout = $('body').layout({
    	applyDefaultStyles: true,
		north__spacing_open: 0,
		south__spacing_open: 0,
		east__spacing_open: 2,
		west__spacing_open: 2,
        west__size: 260,
        east__size: 0.2,
        center__onresize: function (pane, $Pane, paneState) {}
	});
	client.tabs = $('#tabs').tabs({ "active": 0 }).css({
		// 'min-height': $('.ui-layout-center').height() - $('ul.ui-tabs-nav').height(),
   		'min-height': $('.ui-layout-center').height() - 10,
   		'overflow': 'auto'
	});
	var theight = $('#tabs').height() - $('ul.ui-tabs-nav').height();
	$('#tabs-1').css({
		'padding': 0,
   		'min-height': theight,
   		'overflow': 'auto'
	});
	$('#tabs').delegate( "span.ui-icon-close", "click", function() {
		var panelId = $( this ).closest( "li" ).remove().attr( "aria-controls" );
		$( "#" + panelId ).remove();
		$('#tabs').tabs("refresh");
	});
 	client.initialize();
  });

</script>

</head>
<body>
 
  <div class="ui-layout-west">
	<div class="ui-layout-content ui-widget-content">
		<h2>Phonebook</h2>
		<ul style="list-style-type: none;">
			<li onClick="client.display_institutions()" style="cursor: pointer"><img src="images/icons/list.png" border=0 style="vertical-align: middle;"> Institutions</li>
			<li onClick="client.display_members()" style="cursor: pointer"><img src="images/icons/list.png" border=0 style="vertical-align: middle;"> Members</li>
			<li onClick="client.display_board()" style="cursor: pointer"><img src="images/icons/list.png" border=0 style="vertical-align: middle;"> sPHENIX IB</li>
		</ul>
		<ul style="list-style-type: none;">
			<li onClick="client.display_search_institutions()" style="cursor: pointer"><img src="images/icons/find.png" border=0 style="vertical-align: middle;"> Institutions</li>
			<li onClick="client.display_search_members()" style="cursor: pointer"><img src="images/icons/find.png" border=0 style="vertical-align: middle;"> Members</li>
		</ul>
		<ul style="list-style-type: none;">
			<li onClick="client.display_statistics()" style="cursor: pointer"><img src="images/icons/stat.png" border=0 style="vertical-align: middle;"> Statistics / Graphs</li>
			<li onClick="client.display_worldmap()" style="cursor: pointer"><img src="images/icons/stat.png" border=0 style="vertical-align: middle;"> World Map</li>
		</ul>
	</div>
  </div>

  <div class="ui-layout-south ui-widget-header">
    <h4 class="ui-widget-header" style="font-family: 'Oswald', verdana;">sPHENIX Collaboration, 2016</h4>
  </div>

  <div class="ui-layout-north">
	<div id="close_all_tabs" style="position: absolute; top: 10px; right: 10px; color: #FFF !important;">[close all tabs]</div>
	<div style="position: absolute; top: 10px; left: 10px; color: #FFF !important;" ><a href="https://www.sphenix.bnl.gov/pnb_admin/"
	style="color: #AAA; text-decoration: none;">[ to ADMIN version ]</a></div>
	<div id="notification" style="position:relative; float:left; color: red;"></div>
    <h3 class="ui-widget-header" style="font-weight: normal; font-size: 24px; color: #5CB8E6; font-family: 'Oswald', verdana;">
		<span style="color: #FFF;">PhoneBook:</span> <a href="https://www.sphenix.bnl.gov/" style="text-decoration: none;"><span style="color: #ECE0D8; text-shadow: 1px 1px #CCC;">sPHENIX Collaboration</span></a>
		<!-- <img src="images/icons/star.png" border=0 style="vertical-align: middle;"> <span style="color: red; text-shadow: 2px 2px #6666ff;">STAR</span> <span style="color: white; text-shadow: 2px 2px #777;">PhoneBook</span> <sup><span style="font-size: 9px;">2.0</span></sup> -->
	</h3>
  </div>

 <div class="ui-layout-center">
        <div id="tabs">
            <ul>
                <li><a href="#tabs-1">Intro</a></li>
            </ul>
        	<div id="tabs-1">
				<h2>I. General Information</h2>
				<p>sPHENIX Collaboration's PhoneBook allows one to find out information about sPHENIX institutions and members of each institution, as well as statistical information on the sPHENIX Collaboration.
				Menu on the left contains clickable links. Click it, see new tab appear in the central pane. 
				Tabs could be closed by click on the [x] sign at upper-right corner.
				</p>
				<p>Main options:
				<ul>
					<li>List institutions, members</li>
					<li>Search institutions and members using several algorithms including fuzzy match</li>
					<li>Explore statistical data and various visualisations</li>
				</ul>
				</p>
				<h2>II. Contact Information</h2>
				<p>Please send you comments and suggestions to Dave Morrison, <a style="text-decoration: underline" href="mailto:dave@bnl.gov?subject=sphenix phonebook issue">dave@bnl.gov</a></p>
            </div>
        </div>

 </div>

</body>
</html>
