<?php
require 'classes/BSCProcessor.php';
$input_url = $_GET['url'];
$input_format = $_GET['format'];

?>

<HTML> 

  <head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title> Bioschemas structured data testing tool | IFB Bioschemas Validator </title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="raw_crawler.css">
    <link rel="stylesheet" href="font-awesome/css/font-awesome.min.css">
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <!-- Optional theme -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
    <!-- Latest compiled and minified JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
  </head>

  <body class="page-bioschemas-crawler">
    <header class="jumbotron">
      <div class="container">
        <div class="navbar-header">
          <a class="logo" href="/BSC/" title="Home"> <img src="http://www.france-bioinformatique.fr/sites/default/files/ifb-logo_1.png" alt="Home"> </a>
          <a class="name" href="/BSC/" title="Home">Bioschemas Validation Tool</a> 
        </div>
      </div>
    </header>

    <div class="container">
      <form action="process_json.php" method="get">
        <input type="text" id="url" name="url" value="<?php echo $input_url; ?>"/>
        <select id="format" name="format">
          <option value="jsonld">JSON-LD</option>
          <!--<option value="rdfa">RDFa</option>-->
        </select>
        <button type="submit" class="btn btn-success">Submit</button>
      </form>
    </div>



<?php
  
  if (is_url($input_url)){
    echo process_json($input_url);
  }

  else {
    echo '
    <div class="container">
      <div class="alert alert-danger">
        <strong>Error: </strong> Please provide a valid URL string
      </div>
    </div>';
  }

  function is_url($val){
    if(filter_var($val, FILTER_VALIDATE_URL)){
      return true;
    }
    else {
      return false;
    }
  }

  function process_json($url){
    $message = '';
    $context = stream_context_create(array('http' => array('ignore_errors' => true)));
    $html = file_get_contents($url);
    //$html = file_get_contents('http://localhost/rsat/supported-organisms.cgi');
    $dom = new DOMDocument();
    libxml_use_internal_errors( 1 );
    $dom->loadHTML( $html );
    $xpath = new DOMXpath( $dom );

    $script = $dom->getElementsByTagName( 'script' );
    $script = $xpath->query( '//script[@type="application/ld+json"]' );

    foreach ($script as $item) {    
      $json = $item->nodeValue;

      /* THis bit is for an IFB webpage where the json-ld is not valid (should be removed once the page is updated*/
      if (json_decode($json)==NULL){
        $json = substr($json, 0, -5);
        $json = preg_replace('/[\x00-\x1F-\xFF]/', '', $json);
      }

      if (isset(json_decode($json)->{'@graph'})){
        foreach (json_decode($json)->{'@graph'} as $newtool){
          $tool = new BSCProcessor($newtool);
          $insert_message = $tool->make_table();
          $message .= "<div class='bs_output'>".$insert_message."</div>";
          $message .= '<div class="json_display"><h4> Json code:</h4><pre contenteditable="true">'.json_encode(json_decode($json)->{'@graph'}, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES).'</pre></div>';
        }
      }     
      else {
        $tool = new BSCProcessor(json_decode($json));
        $insert_message = $tool->make_table();
        $message .= "<div class='bs_output'>".$insert_message."</div>";
        $message .= '<div class="json_display"><h4> Json code:</h4><pre contenteditable="true">'.json_encode(json_decode($json), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES).'</pre></div>';
      }
    }
    return '<div class="container">'.$message.'</div>';
  }
?>

</body>
<HTML>