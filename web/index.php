<?php

define('ROOT_WEB', __DIR__);
@include_once __DIR__ .'/../bootstrap.php';

if (!defined('ROOT_DIR') OR !isset($app)) {
	header('HTTP/1.0 500 Internal Server Error');
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
Visiblement quelque chose s'est vraiment mal passé.
</p><p>
Toutes nos excuses pour la gène occasionée, nous corrigerons ça dans les plus brefs délais
</p>
</div>

</body>
</html>
EOB;
}