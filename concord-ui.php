<html>
	<head>
		<title>Hello Test</title>
		<meta name="copyright" content="Copyright 2013, Small Picture, Inc.">
		<script src="http://www.example.com/concord/libraries/jquery-1.9.1.min.js"></script>  
		<script src="http://www.example.com/concord/libraries/bootstrap.min.js"></script>
		<script src="http://www.example.com/concord/concord.js"></script>
		<script src="http://www.example.com/concord/concordUtils.js"></script>
		<link href="http://www.example.com/concord/concord.css" rel="stylesheet" />
		<link href="http://www.example.com/concord/libraries/bootstrap.css" rel="stylesheet" />
		<link href="//netdna.bootstrapcdn.com/font-awesome/4.0.1/css/font-awesome.css" rel="stylesheet" />
		
		<script>
			
			// Dev notes:
			// We're using Bootstrap for most buttons, tooltips, and menus: http://getbootstrap.com
			// And Font Awesome for icons: http://fortawesome.github.io/Font-Awesome/
			// This page is modified from Concord Example 1: https://github.com/scripting/concord/blob/master/example1/index.html
			// Here's a good resource for localStorage: http://diveintohtml5.info/storage.html
						
			var name1 = localStorage.getItem('name1'); //call last stored title - BK
			
			var appConsts = {
				"productname": "Hello Test",
				"productnameForDisplay": "Hello Test",
				"domain": "http://www.example.com", 
				"version": "0.52"
				}
			var appPrefs = {
				"outlineFont": "Arial", "outlineFontSize": 16, "outlineLineHeight": 24,
				"authorName": "",
				"authorEmail": "",
				"title1": name1 //name this outline - BK
				};
			var whenLastKeystroke = new Date (), whenLastAutoSave = new Date ();  
			var flReadOnly = false, flRenderMode = false;
			var cmdKeyPrefix = "Ctrl+";
			
			// related docs - BK
			//var urlConcordSource = "http://raw.github.com/scripting/concord/master/opml/concord.opml";
			//var urlConcordCssSource = "http://raw.github.com/scripting/concord/master/opml/concordCss.opml";
			//var urlConcordDocs = "http://raw.github.com/scripting/concord/master/opml/concordDocs.opml";
			//var urlConcordUtilsSource = "http://raw.github.com/scripting/concord/master/opml/concordUtils.opml";
			//var urlHelloOutliner = "http://raw.github.com/scripting/concord/master/example1/source.opml";
			//var urlExample0 = "http://raw.github.com/scripting/concord/master/example0/source.opml";
			//var urlExample2 = "http://static.smallpicture.com/tacoma/wo/admin/2013/09/18/archive056.opml";
			//var urlWorknotes = "http://static.smallpicture.com/tacoma/wo/admin/2013/09/18/archive057.opml";
			
			function initLocalStorage () {
				if (localStorage.savedOpmltext == undefined) {
					localStorage.savedOpmltext = initialOpmltext;
					editSource (urlConcordDocs); //9/14/13 by DW
					}
				if (localStorage.ctOpmlSaves == undefined) {
					localStorage.ctOpmlSaves = 0;
					}
				if (localStorage.whenLastSave == undefined) {
					localStorage.whenLastSave = new Date ().toString ();
					}
				if (localStorage.flTextMode == undefined) {
					localStorage.flTextMode = "true";
					}
				}
			function setInclude () { //used to test includes
				opSetOneAtt ("type", "include");
				opSetOneAtt ("url", "http://smallpicture.com/states.opml");
				}
			function editSource (url) {
				opXmlToOutline (initialOpmltext); //empty the outline display
				readText (url, function (opmltext, op) {
					opXmlToOutline (opmltext);
					saveOutlineNow ();
					}, undefined, true);
				}
			function nukeDom () {
				var summit, htmltext = "", indentlevel = 0;
				$(defaultUtilsOutliner).concord ().op.visitToSummit (function (headline) {
					summit = headline;
					return (true);
					});
				var visitSub = function (sub) {
					if (sub.attributes.getOne ("isComment") != "true") { 
						htmltext += filledString ("\t", indentlevel) + sub.getLineText () + "\r\n"
						if (sub.countSubs () > 0) {
							indentlevel++;
							sub.visitLevel (visitSub); 
							indentlevel--;
							}
						}
					};
				summit.visitLevel (visitSub);
				
				var t = new Object ();
				t.text = summit.getLineText ();
				htmltext = multipleReplaceAll (htmltext, t, false, "<" + "%", "%" + ">");
				
				document.open ();
				document.write (htmltext);
				document.close ();
				}
			function opExpandCallback (parent) {
				var type = parent.attributes.getOne ("type"), url = parent.attributes.getOne ("url"), xmlUrl = parent.attributes.getOne ("xmlUrl");
				//link nodes
					if ((type == "link") && (url != undefined)) {
						window.open (url);
						return;
						}
				//rss nodes
					if ((type == "rss") && (xmlUrl != undefined)) {
						window.open (xmlUrl);
						return;
						}
				//include nodes
					if ((type == "include") && (url != undefined)) {
						parent.deleteSubs ();
						parent.clearChanged ();
						readText (url, function (opmltext, op) {
							op.insertXml (opmltext, right); 
							op.clearChanged ();
							}, parent, true);
						}
				}
			function opInsertCallback (headline) { 
				headline.attributes.setOne ("created", new Date ().toUTCString ());
				}
			function opCollapseCallback (parent) {
				if (parent.attributes.getOne ("type") == "include") {
					parent.deleteSubs ();
					parent.clearChanged ();
					}
				}
			function opHoverCallback (headline) { 
				var atts = headline.attributes.getAll (), s = "";
				//set cursor to pointer if there's a url attribute -- 3/24/13  by DW prost
					if ((atts.url != undefined) || (atts.xmlUrl != undefined)) {
						document.body.style.cursor = "pointer";
						}
					else {
						document.body.style.cursor = "default";
						}
				}
			function opCursorMovedCallback (headline) {
				}
			function opKeystrokeCallback (event) { 
				whenLastKeystroke = new Date (); 
				}
			function runSelection () {
				var value = eval (opGetLineText ());
				opDeleteSubs ();
				opInsert (value, "right");
				opGo ("left", 1);
				}
				
				
			function setOutlinerPrefs (id, flRenderMode, flReadonly) { 
				$(id).concord ({
					"prefs": {
						"outlineFont": appPrefs.outlineFont, 
						"outlineFontSize": appPrefs.outlineFontSize, 
						"outlineLineHeight": appPrefs.outlineLineHeight,
						"renderMode": flRenderMode,
						"readonly": flReadonly,
						"typeIcons": appTypeIcons
						},
					"callbacks": {
						"opInsert": opInsertCallback,
						"opCursorMoved": opCursorMovedCallback,
						"opExpand": opExpandCallback,
						"opHover": opHoverCallback, 
						"opKeystroke": opKeystrokeCallback
						}
					});
				}	
				
			function saveOutlineNow () { //grab any edits to the title - BK 
				localStorage.savedOpmltext = opOutlineToXml (localStorage.getItem('name1'), appPrefs.authorName, appPrefs.authorEmail, appPrefs.authorEmail);
				localStorage.ctOpmlSaves++; // the GMT time
				opClearChanged ();
				console.log ("saveOutlineNow: " + localStorage.savedOpmltext.length + " chars.");
				}
			function backgroundProcess () {
				if (opHasChanged ()) {
					if (secondsSince (whenLastKeystroke) >= 1) { 
						saveOutlineNow ();
						}
					}
				}
			function startup () {
				initLocalStorage ();
				$("#idMenuProductName").text (appConsts.productname);
				$("#idProductVersion").text ("v" + appConsts.version);
				//init menu keystrokes
					if (navigator.platform.toLowerCase ().substr (0, 3) == "mac") {
						cmdKeyPrefix = "&#8984;";
						}
					$("#idMenubar .dropdown-menu li").each (function () {
						var li = $(this);
						var liContent = li.html ();
						liContent = liContent.replace ("Cmd-", cmdKeyPrefix);
						li.html (liContent);
						});
				setOutlinerPrefs ("#outliner", true, false); //9/20/13 by DW -- change initial value for renderMode from false to true
				opSetFont (appPrefs.outlineFont, appPrefs.outlineFontSize, appPrefs.outlineLineHeight); 
				opXmlToOutline (localStorage.savedOpmltext);
				self.setInterval (function () {backgroundProcess ()}, 1000); //call every second
				}
				
				// BK FUNCTIONS ---------------------------------------
				
				// Save title
				
			function store () {
				var inputTitle = document.getElementById("name1");
				localStorage.setItem("name1", inputTitle.value);
				};
	
				// Show title
	
			var auto_refresh = setInterval(
			function () {
				$("#showtitle").text('');
				var storedValue = localStorage.getItem("name1");
				$('#showtitle').text(storedValue).fadeIn("slow");
				}, 300); // refresh in milliseconds

				// Save Work
				
			function saveWork () { //grab any edits to the title - BK
				location.reload(false); // reload page  
				localStorage.savedOpmltext = opOutlineToXml (localStorage.getItem('name1'), appPrefs.authorName, appPrefs.authorEmail, appPrefs.authorEmail);
				localStorage.ctOpmlSaves++; // the GMT time
				opClearChanged ();
				console.log ("saveOutlineNow: " + localStorage.savedOpmltext.length + " chars.");
				}
				
				// Clear LocalStorage
				
			function clearall () {
				localStorage.removeItem('name1');
				opWipe (); // from concordUtils.js
				//localStorage.clear();
				localStorage.setItem('name1','New Title');
				};	
				
				// ----------------------------------------------------
				
			</script>
		<style>
			body {
				background-color:#FFCC33;
				margin-bottom: 80px;
				/*background-color: whitesmoke;*/
				}
			.OutlinerSection {
				width: 76%;
				margin-top: 70px;
				margin-left: auto;
				margin-right: auto;
				background-color: transparent;
				}
			#toolmenu {
				margin-top: 10px;
				}
			#actionmenu {
				margin-top: 5px;
				}	
			/* #toolmenu { for left side float
				margin-top: 140px;
				width: 50px;
				margin-left: 70px;
				margin-right: 20px;
				float: left;
				} */
			.btn, .btn:hover, .btn-group a {
				/*float: right;*/
				margin-left: 0px;
				}	
			.OutlinerTitle {
				width: 87%;
				margin-top: 14px;
				margin-left: 0px;
				padding: 0px;
				}
			#showtitle {
				padding: 6px;
				padding-top: 10px;
				padding-bottom: 10px;
				font-size: 20px;
				font-family: "Arial";
				font-weight: normal;
				font-style: normal;
				background: white;
				-moz-border-radius-topright: 5px;
				border-top-right-radius: 5px;
				-moz-border-radius-topleft: 5px;
				border-top-left-radius: 5px;
				-moz-border-radius-bottomright: 0px;
				border-bottom-right-radius: 0px;
				-moz-border-radius-bottomleft: 0px;
				border-bottom-left-radius: 0px;
				}
			.OutlinerTitle form {
				margin-top: 0px;
				}
			.OutlinerTitle input {
				height: 30px;
				width: 100%;
				font-size: 16px;
				font-family: "Arial";
				font-weight: normal;
				font-style: normal;
				}	
			.OutlinerTitle .btn {
				/*float: right;*/
				width: 120px;
				margin-right: 0px;
				/*margin-top: -23px;*/
				/*margin-right: 10px;*/
				}
			#opmsg {
				font-style: italic;
				float: right;
				margin-top: 14px;
				margin-right: 158px;
				}
			#codeview {
				width: 75%;
				margin-top: 30px;
				margin-left: auto;
				margin-right: auto;
				border:1px solid gainsboro;
				min-height: 550px;
				max-height: 800px;
				overflow: auto;
				padding: 6px;
				background-color: white;
				}	
			.divOutlinerContainer {
				width: 75%;
				margin-top: 0px;
				margin-left: auto;
				margin-right: auto;
				border:1px solid gainsboro;
				min-height: 550px;
				max-height: 800px;
				overflow: auto;
				padding: 6px;
				background-color: white;
				}
			.divSubtext {
				width: 65%;
				margin-top: 3px;
				margin-left: auto;
				margin-right: auto;
				}
			/* menubar */
				.divMenubar .container { 
					margin-left: auto;
					margin-right: auto;
					width: 75%;
					}
				.divMenubar .navbar .nav > li > a { 
					font-size: 15px;
					padding-top: 12px;
					padding-left: 8px; padding-right: 8px; /* 6/3/13 by DW */
					outline: none !important;
					}
				.dropdown-menu > li > a {
					cursor: pointer;
					}
				.navbar-inner { 
					-moz-border-radius: 0;
					-moz-border-radius: none; 
					-moz-box-shadow: none; 
					background-image: none; 
					border-radius: 0;  
					}
				.divMenubar .brand { 
					margin-top: 0;
					}
				.divMenubar .nav li {
					font-family: Arial;
					font-size: 14px;
					font-weight: bold;
					}
				.menuKeystroke {
					float: right;
					margin-left: 25px;
					}
				.menuKeystroke:before {
					content: "";
					}
				 #idMenuProductName {
					font-family: "Arial";
					font-size: 24px;
					font-weight: bold;
					font-style: italic;
					}
			</style>
		</head>
	<body>
		
		<div class="divMenubar" id="idMenubar">
			<div class="topbar-wrapper" style="z-index: 0; opacity: 1;">
				<div class="navbar navbar-fixed-top" data-dropdown="dropdown">
					<div class="navbar-inner">
						<div class="container">
							<a class="brand" href="/"><span id="idMenuProductName"></span></a>
							<ul class="nav" id="idMainMenuList">
								<li class="dropdown" id="idFileMenu">
									<a href="#" class="dropdown-toggle" data-toggle="dropdown">File&nbsp;<b class="caret"></b></a>
									<ul class="dropdown-menu">
										<li><a onClick="saveWork ();" id="save" href="#"><span class="menuKeystroke"><i class="fa fa-save"></i></span>Save Work</a></li>
										</ul>
									</li>
								<li class="dropdown" id="idOutlinerMenu"> 
									<a href="#" class="dropdown-toggle" data-toggle="dropdown">Outliner&nbsp;<b class="caret"></b></a>
									<ul class="dropdown-menu">
										<li><a onClick="opExpand ();"><span class="menuKeystroke">Cmd-,</span>Expand</a></li>
										<li><a onClick="opExpandAllLevels ();">Expand All Subs</a></li>
										<li><a onClick="opExpandEverything ();">Expand Everything</a></li>
										
										<li class="divider"></li>
										<li><a onClick="opCollapse ();"><span class="menuKeystroke">Cmd-.</span>Collapse</a></li>
										<li><a onClick="opCollapseEverything ();">Collapse Everything</a></li>
										
										<li class="divider"></li>
										<li><a onClick="opReorg (up, 1);"><span class="menuKeystroke">Cmd-U</span>Move Up</a></li>
										<li><a onClick="opReorg (down, 1);"><span class="menuKeystroke">Cmd-D</span>Move Down</a></li>
										<li><a onClick="opReorg (left, 1);"><span class="menuKeystroke">Cmd-L</span>Move Left</a></li>
										<li><a onClick="opReorg (right, 1);"><span class="menuKeystroke">Cmd-R</span>Move Right</a></li>
										
										<li class="divider"></li>
										<li><a onClick="opPromote ();"><span class="menuKeystroke">Cmd-[</span>Promote</a></li>
										<li><a onClick="opDemote ();"><span class="menuKeystroke">Cmd-]</span>Demote</a></li>
										
										<li class="divider"></li>
										<li><a onClick="runSelection ();"><span class="menuKeystroke">Cmd-/</span>Run Selection</a></li>
										<li><a onClick="toggleComment ();"><span class="menuKeystroke">Cmd-\</span>Toggle Comment</a></li>
										
										<li class="divider"></li>
										<li><a onClick="toggleRenderMode ();"><span class="menuKeystroke">Cmd-`</span>Toggle Render Mode</a></li>
										</ul>
									</li>
								<li class="dropdown" id="idTextMenu">
									<a href="#" class="dropdown-toggle" data-toggle="dropdown">Text&nbsp;<b class="caret"></b></a>
									<ul class="dropdown-menu">
										<li><a onClick="opBold ();" href="#"><span class="menuKeystroke">Cmd-B</span>Bold</a></li>
										<li><a onClick="opItalic ();" href="#"><span class="menuKeystroke">Cmd-I</span>Italic</a></li>
										<li><a onClick="opStrikethrough ();" href="#">Strikethrough</a></li>							
										</ul>
									</li>
								<li class="dropdown" id="idDocsMenu">
									<a href="#" class="dropdown-toggle" data-toggle="dropdown">Links&nbsp;<b class="caret"></b></a>
									<ul class="dropdown-menu">
										<li><a href="http://docs.fargo.io/outlinerHowto" target="_blank">Outliner Howto</a></li>
										<li><a href="http://github.com/scripting/concord" target="_blank">GitHub Repo</a></li>
										<li><a href="https://groups.google.com/forum/?fromgroups#!forum/smallpicture-concord" target="_blank">Mail List</a></li>
										</ul>
									</li>
								<li class="dropdown" id="PublishMenu">							
									<button id="publish" class="btn disabled" style="margin-top:7px; margin-left: 6px;">YOLO (publish)</button>
								</li>
							</ul>
							<ul class="nav pull-right">
								<li>
									<a href="#" class="dropdown-toggle" data-toggle="dropdown"><span id="idProductVersion"></span></a>
									</li>
								</ul>
							</div>
						</div>
					</div>
				</div>
			</div>
		
		<!-- begin OutlinerSection -->
		
		<div class="OutlinerSection"> 		
		
		<div class="btn-group" id="toolmenu">
			<a data-func="1" class="btn btn-inverse" href="#" data-title="Bold"><i class="fa fa-bold fa-lg"></i></a>
			<a data-func="2" class="btn btn-inverse" href="#" data-title="Italic"><i class="fa fa-italic fa-lg"></i></a>
			<a data-func="3" class="btn btn-inverse" href="#" data-title="Strikethrough"><i class="fa fa-strikethrough fa-lg"></i></a>
			<a data-func="4" class="btn btn-inverse" href="#myModal" role="button" data-toggle="modal" data-title="Add Link"><i class="fa fa-link fa-lg"></i></a>
			<a data-func="0" class="btn btn-inverse" href="#" data-title="Refresh"><i class="fa fa-refresh fa-lg"></i></a>
			<a class="btn btn-inverse" href="#codeview" data-title="View Code"><i class="fa fa-code fa-lg"></i></a>
			<a data-func="5" class="btn btn-default disabled" href="#" data-title="Add Image"><i class="fa fa-picture-o fa-lg"></i></a>
			<a class="btn btn-default disabled" href="#" data-title="Open File"><i class="fa fa-folder-open fa-lg"></i></a>
			<a class="btn btn-default disabled" href="#" data-title="Export"><i class="fa fa-file-text-o fa-lg"></i></a>
			
		</div>
		
		<div class="btn-group" id="actionmenu">
			<button class="btn btn-primary" type="button" onClick="clearall ();">Clear All Data | New</button>
			<button data-func="0" class="btn btn-success" type="button">Save Work</button>
			<button class="btn btn-success" href="#" id="saveit" type="button">Export OPML</button>
		</div>
		
		<!-- bootstrap: button to trigger modal -->

		<!-- Modal -->
		<div id="myModal" class="modal hide fade" role="dialog">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">x</button>
				<h3 id="myModalLabel">Enter URL for link:</h3>
			</div>
			<div class="modal-body">
				<div class="input-group input-group-lg">
					<input name="url1" id="url1" class="input-xlarge" type="text" placeholder="Type URL here">
				</div>
			</div>
			<div class="modal-footer">
				<button class="btn" data-dismiss="modal">Cancel</button>
				<button onclick="addLink ();" class="btn btn-primary">Ok</button>
			</div>
		</div>
		
		<!-- display message inside this div -->
		<div id="opmsg"></div>
		
		<div class="OutlinerTitle input-append">
			<div id="showtitle"></div>
			<form method="post" /> 
				<input name="name1" id="name1" class="input-append" type="text" />
			<button class="btn btn-success" onclick="store();" type="button">Save Title</button>
			</form>
		</div>
		
		</div>
		
		<!-- end OutlinerSection -->
		
		<!-- begin divOutlinerContainer -->
		
		<div class="divOutlinerContainer">
			<div id="outliner">
				</div>
			</div>
	
		<!-- end divOutlinerContainer -->	
		
		<script>
			$(document).ready (function () {
				startup ();
				});
			</script>
		
		<script>
			
			// BK SCRIPTS ---------------------------------------
			
			var inputImage = document.getElementById("image1");
			
			// Bootstrap tooltips

			$('#toolmenu').tooltip({
				'selector': 'a',
				'placement': 'left',
				});

			$('#toolmenu').tooltip('toggle');
	
			// Add Link button
	
			function addLink () {
				var inputURL = document.getElementById("url1").value;
				opLink (inputURL);
				$("#myModal").modal("hide");				
				}
				
			// Toolmenu & actionmenu buttons	

			var solutions = [
				function () { saveWork (); }, //0
				function () { opBold (); }, //1
				function () { opItalic (); }, //2
				function () { opStrikethrough (); }, //3
				function () { opLink (inputURL); }, //4
				function () { opInsertImage (inputImage); }, //5
				];

			$("[data-func]").on("click", function () {
				solutions[parseInt($(this).attr("data-func"))]();
				});
			
			// Save OPML button
			
			// clear result div  
			$("#opmsg").html('');
			//var data = opOutlineToXml (localStorage.savedOpmltext); // use to print ompl to page, as this: &lt;head&gt;
			var data = localStorage.savedOpmltext; // use to export raw opml from localStorage, as this: <head>
			$(document).ready(function(){
			$('#saveit').click(function() {

			$.ajax({        
				type: "POST",
				url: "saveopml.php",
				//data: {testData : data },
				data: {testData : data, name: name1},
				success: function(data) {
					alert("Data received");
					$("#opmsg").html('<span class="alert alert-success">The file was created successfully.</span>');
					},
				error:function(){
					alert("failure");
					$("#opmsg").html('<span class="alert alert-danger">There was an error while submitting.</span>');
					} 
				});      
				return false; 
				});  
				});			
			
		</script>
		
			<a name="codeview"></a>
			<div id="codeview">
		
		<script>
					
				// PRINTS LIVE OPML FEED UPDATES TO DIV
				// clear div
				var auto_refresh = setInterval(
				function () {
					$("#showopml").text('');
					var lostore = localStorage.savedOpmltext;
					$('#showopml').text(lostore).fadeIn("slow");
					}, 5000); // refresh in milliseconds
		</script>
				
				<h4>OPML Output</h4>
				
				<div id="showopml"></div>

			
			</div>
		</body>
	</html>
