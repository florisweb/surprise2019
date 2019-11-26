<!DOCTYPE html>
<html>
	<head>
		<link rel="stylesheet" type="text/css" href="css/mainUI.css">
		<meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0' name='viewport'/>
		<meta name="theme-color" content="#636ad5">
		<style>
			.iconHolder {
				position: relative;
				height: calc((100vh - 100px * 2 - 10px * 4) / 2);
				padding-bottom: 20px;
			}

			.icon {
				position: relative;
				height: calc(100% - 50px);
				width: auto;
			}
			.iconHolder .centerAligner {
				margin-top: -5px;
			}

		</style>
		<title>Surprise 2019</title>
	</head>
	<body>
		<div class="text" id="alarmHeaderHolder"></div>
		
		<div class="popup ovenPinger hide">
			<div class="text"></div>
			<a href="oven.php">
				<img class="toOvenIcon" src="images/toOvenIcon.png">
			</a>
		</div>

		<script src="js/ovenPinger.js"></script>

		
		<div id="mainContentHolder">
			<div class="centerAligner" id="mainContent">
				<a class="text mainHeader">Elisa's digi-keuken</a>
				<br>

				<div class="iconHolder">
					<a href="mixer.php" style="text-decoration: none">
						<img class="icon" src="images/pan.png">
						<br>
						<br>
						<div class="centerAligner" style="max-width: 160px">
							<div class="button bDefault clickable">Naar de mixer</div>
						</div>
					</a>
				</div>
				<br>

				<div class="iconHolder">
					<a href="oven.php" style="text-decoration: none">
						<img class="icon" src="images/oven.png">
						<br>
						<br>
						<div class="centerAligner" style="max-width: 160px">
							<div class="button bWarn clickable">Naar de oven</div>
						</div>
					</a>
				</div>
				
			</div>
		</div>
	</body>
</html>