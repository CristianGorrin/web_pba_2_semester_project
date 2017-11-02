<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
		<meta name="apple-mobile-web-app-status-bar-style" content="black" />
		<meta name="apple-mobile-web-app-capable" content="yes" />

		<link rel="stylesheet" href="./css/main.css?t=<?=time()?>" media="screen" />

		<script src="http://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
		<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
		<script src="./js/main.js?t=<?=time()?>"></script>
		<title>EAL Check-in</title>
	</head>
	<body>
		<video id="camera" src="./img/camera.mp4" autoplay></video>
		<div class="top-bar">EAL Check-in</div>
		<img class="menu" src="./img/menu.png" />
		<div class="status">
			<img class="loading" src="./img/loading.gif" />
			<img class="done" src="./img/check.png" />
			<p class="status-text">Checked in!</p>
		</div>
	</body>
</html>
