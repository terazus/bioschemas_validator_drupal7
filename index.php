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
        <input type="text" id="url" name="url"/>
        <select id="format" name="format">
          <option value="jsonld">JSON-LD</option>
          <!--<option value="rdfa">RDFa</option>-->
        </select>
        <button type="submit" class="btn btn-success">Submit</button>
      </form>
    </div>

  </body>
</HTML>
