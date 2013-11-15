<?php

echo '<br />----- MULTIPLES USING DOM <br >';

$x = new DOMDocument();
$x->preserveWhiteSpace=false;
$xpath = new DOMXPath($x);

// load opml files
$urls = glob('concord-*.opml');

// sort by most recent modified first
usort($urls, function($a, $b) {
    return filemtime($a) < filemtime($b);
});

foreach ($urls as $url) {
    $x->load($url);
	
echo '<br />----- timestamp -----<br /><br />';	
	
	echo "$url was last modified: " . date ("F d Y H:i:s", filemtime($url)) . "<br />";
	
echo '<br />----- head -----<br /><br />';

	$headers = getheader($x->getElementsByTagName('head')->item(0));
	echo $headers;
	
	// how to pull a single node from DOM
	// $opmltitle = $x->getElementsByTagName('title')->item(0)->nodeValue; // print post title
	// echo "Title: " . $opmltitle . "<br>";

echo '<br />----- body -----<br />';

	$contents = getopml($x->getElementsByTagName('body')->item(0));
	echo $contents;
}
	
// first parser function
function getheader($x) {
 foreach ($x->childNodes as $h)
  if ($h->nodeType == XML_ELEMENT_NODE)
   echo " [". $h->nodeName . "] " . $h->nodeValue . "<br />";
}

// second parser function
function getopml($x) {
	echo "<ul>&lt;ul&gt;";
 foreach ($x->childNodes as $p)
  if (hasChild($p)) { // leaf nodes
	  echo "<li>&lt;li&gt;"  . " [". $p->nodeName . "]" . " [created] " . $p->getAttribute('created') . " [text] " . $p->getAttribute('text') . "&lt;&#47;li&gt;</li>";
	  getopml($p);
  } elseif ($p->nodeType == XML_ELEMENT_NODE)
   echo "<li>&lt;li&gt;"  . " [". $p->nodeName . "]" . " [created] " . $p->getAttribute('created') . " [text] " . $p->getAttribute('text') . " [icon] " . $p->getAttribute('icon') . "&lt;&#47;li&gt;</li>";
   echo "&lt;&#47;ul&gt;</ul>";
}
function hasChild($p) {
 if ($p->hasChildNodes()) {
  foreach ($p->childNodes as $c) {
   if ($c->nodeType == XML_ELEMENT_NODE)
    return true;
  }
 }
 return false;
}


?>