<?php

define('ROOT_WEB', __DIR__);
@include_once __DIR__ .'/../app/bootstrap.php';

if (!defined('ROOT_APP') OR !isset($app)) {
	// header('HTTP/1.0 500 Internal Server Error');
	echo <<<EOB
<!DOCTYPE HTML>
<html>
<head>
	<meta charset="UTF-8">
	<title>Internal Server Error</title>
	<style type="text/css" media="screen">
		body { background-color: #eee; }
		#msg { width:250px; height:200px; 
			margin:10% auto; padding:30px 20px;
			text-align:center;
			background-color:#f7f7f7;
			border:1px solid #ddd;
		}
	</style>
</head>
<body>

<div id="msg">
<h2>Internal Server Error</h2>
<p>
Obviously something went<br/> terribly wrong.
</p><p>
Our apologies for the inconvenience, we'll fix this as soon as possible.
</p>
</div>

</body>
</html>
EOB;
}