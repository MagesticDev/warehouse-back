# warehouse-back
In the WWW folder put the application at the root.

# configuration
To open consts/constantes.php
  
  # API constante 
  Replace the name with the name of your application in "AUDIENCE_CLAIM" and "ISSUER_CLAIM".
  Change "PORT" and "URL_API" or put the fields empty when the back is in production.
  
  # SMTP constante
  Enter your information concerning the SMTP connection (OVH or other ...).
  This allows the sending of mail via the application.
  
  # SQL constante
  Fill in your information about the MYSQL connection.
  
  # Tchat serveur socket
  In the folder api\controllers\tchat\includes\websocket open new terminal and execute "php -q serveur.php"
  
# PHP
Activate sockets extension via your php.ini
  
 
  
