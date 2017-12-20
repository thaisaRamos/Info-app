# Info-app
App for getting user information from Facebook using JavaScript SDK and PHP SDK 

<h3> Setup </h3>

1) Run componser update
2) Create a Facebook APP (https://developers.facebook.com/) and select Facebook login as product.
3) Inside your FB App go to the tab "Facebook Login" -> "Settings" and set the Valid OAuth redirect URIs to yourhost/info-app/fb-callback.php
3) Import the info_app.sql to your MySQL database.
4) Update the variables inside config folder.
