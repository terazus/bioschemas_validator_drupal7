<?php
require 'classes/BSCProcessor.php';
$context = stream_context_create(array('http' => array('ignore_errors' => true)));
// $html = file_get_contents('https://www.france-bioinformatique.fr/fr/bioschemas_crawler_test');
$html = file_get_contents('http://localhost/rsat/supported-organisms.cgi');
$dom = new DOMDocument();
libxml_use_internal_errors( 1 );
$dom->loadHTML( $html );
$xpath = new DOMXpath( $dom );

$script = $dom->getElementsByTagName( 'script' );
$script = $xpath->query( '//script[@type="application/ld+json"]' );
$message = '<HEAD>
<link rel="stylesheet" href="crawler.css">
</HEAD>';
foreach ($script as $item) {		
  $json = $item->nodeValue;
  if (isset(json_decode($json)->{'@graph'})){
    foreach (json_decode($json)->{'@graph'} as $newtool){
      $tool = new BSCProcessor($newtool);
      $insert_message = $tool->make_table();
      $message .= "<div class='bs_output'>".$insert_message."</div>";
    }
  }			
  else {
    $tool = new BSCProcessor(json_decode($json));
    $insert_message = $tool->make_table();
    $message .= "<div class='bs_output'>".$insert_message."</div>";
  }
}

echo $message;
?>