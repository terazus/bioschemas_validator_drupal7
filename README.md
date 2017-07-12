# bioschemas_validator_drupal7

Deploy the app localy:
Install Apache and PHP (WAMP/MAMP recommanded for non linux).
  In the diretory where Apache reads (by default, /var/www/ on Ubuntu and /Applications/MAMP/htdocs on MacOS), create your app directory and clone this code repo.
  Install font-awesome at the root of your site.
  Go to localhost:port/mysite and you are ready to go.  
  

PHP library usage example:  
  ```php
  $json = {'@type':'SoftwareApplication', 'name'='test'};
  $message = '';
  if (isset(json_decode($json)->{'@graph'})){
      foreach (json_decode($json)->{'@graph'} as $newtool){
        $tool = new BSCProcessor($newtool);
        $insert_message = $tool->make_table();
        $message .= "<div class='bs_output'>".$insert_message."</div>";
      }
    }			
    else{
      $tool = new BSCProcessor(json_decode($json));
      $insert_message = $tool->make_table();
      $message .= "<div class='bs_output'>".$insert_message."</div>";
    }
  echo $message;
  ```
