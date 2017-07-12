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
  $tool = new BSCProcessor(json_decode($json));
  $message .= "<div class='bs_output'>".$tool->make_table()."</div>";
    }
  echo $message;
  ```
