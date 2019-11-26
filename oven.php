<!DOCTYPE html>
<html>
	<head>
		<link rel="stylesheet" type="text/css" href="css/mainUI.css">
		<meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0' name='viewport'/>
		<meta id="themeColour" name="theme-color" content="#636ad5">
		<style>

			#mainContentHolder {
				height: calc(100vh - 30px * 1 - 30px);
			}

			#mixPanCanvas {
				position: relative;
				top: calc((100vh - 30px * 2 - 10px * 2 * 2 - 30px) / 2 - 60vw / 2 - 10vh);
				width: calc(60vw);
				height: auto;
			}

			#actionIndicator {
				position: relative;
				animation: actionIndicatorAnimation 2s 100;
			}
			@keyframes actionIndicatorAnimation {
			    0%   {opacity: .6}
			    50%  {opacity: 1}
			  	100% {opacity: .6}
			}



			
			#ingredientDropIn {
				position: fixed;
				left: calc(50vw - 40vw / 2);
				top: calc(50vh - 40vw / 2 - 10vh);
				width: calc(40vw);
				height: auto;
			}

			#ingredientDropIn.drop {
				animation: dropAnimation .5s 1;
				animation-fill-mode: forwards;
			}

			@keyframes dropAnimation {
			    0%   {transform: scale(1); opacity: 0}
			    50%   {transform: scale(1.8); opacity: 1;}
			  	100%   {transform: scale(0)}
			}



			
			.popup .inputField {
				position: relative;
				width: calc(100% - 5px * 2);
				padding: 0 5px;
			}
			#imageHolder {display: none}

			
			#bakeButton {
				top: -50px;
			}
		
		</style>

		<title>Oven - Surprise 2019</title>
	</head>
	<body>
		<img id="imageHolder" src="images/oven.png" onload="Drawer.drawOven()">



		<div id="mainContentHolder">
			<a class="text mainHeader" id="percentageHolder"></a>
			<br>
			<a class="text" id="actionIndicator">Verwarm de oven eerst voor</a>

			<div class="centerAligner" id="mainContent">
				<canvas id="mixPanCanvas" width="500" height="500"></canvas>
				
				<div class="popup hide">
					<input class="inputField iBoxy text" placeholder="Taart-code">
					<br>
					<br>
					<div class="button bDefault clickable bBoxy" style="width: 60%; margin: auto" onclick="Popup.putCakeInOven()">Taart in oven</div>
				</div>
			</div>
			<img id="ingredientDropIn" class="drop">
		</div>

		<div id="bottomBar">
			<div class="centerAligner" style="max-width: 200px;" id="prepareButton">
				<div class="button bDefault clickable" onclick="Oven.startHeatingUp()">Voorverwarmen (255*C)</div>
			</div>
			<br>
			<div class="centerAligner hide" style="max-width: 200px;" id="bakeButton">
				<div class="button bDefault clickable" onclick="Popup.open()">Taart in oven doen</div>
			</div>
		</div>


		<script src="js/ovenPinger.js"></script>
		<br>
		<button onclick="localStorage.removeItem('ElisaSurprise_status');">Reset</button>

		<script>
			const Oven = new function() {
				this.cakeCode = "12342";
				this.isBurning = false;
				this.status;

				let lastSlowUpdate = new Date();
				this.update = function() {
					Drawer.drawOven();
					if (!this.isBurning) return;

					if (new Date() - lastSlowUpdate < 1000) return;
					lastSlowUpdate = new Date();
					
					percentageHolder.innerHTML = OvenPinger.timeText;
					this.updateOvenStatus();
				}

			



				this.startHeatingUp = function() {
					this.isBurning = true;
					
					localStorage.ElisaSurprise_status = JSON.stringify({stage: "heating", startTime: new Date()});
					this.updateOvenStatus();
				}

				this.startBaking = function() {
					Popup.close();
					this.isBurning = true;

					localStorage.ElisaSurprise_status = JSON.stringify({stage: "baking", startTime: new Date()});
					this.updateOvenStatus();
				}

				this.updateOvenStatus = function() {
					if (!localStorage.ElisaSurprise_status) return;
					this.status = JSON.parse(localStorage.ElisaSurprise_status);

					prepareButton.classList.add("hide");
					bakeButton.classList.add("hide");

					switch (this.status.stage) 
					{
						case "heating": 
							actionIndicator.innerHTML = "Aan het voorverwarmen<br><strong style='color: red'>Zorg dat het geluid aan staat</strong>";
							this.isBurning = true;
						break;
						case "finishedHeating": 
							if (!Popup.openState) bakeButton.classList.remove("hide");
							actionIndicator.innerHTML = "Stop de taart in de oven<br><strong style='color: red'>Zorg dat het geluid aan staat</strong>";
							this.isBurning = true;
						break;
						case "baking": 
							this.isBurning = true;
							actionIndicator.innerHTML = "Aan het bakken<br><strong style='color: red'>Zorg dat het geluid aan staat</strong>";
						break;
					}
					return true;
				}	
			}
			











			const Popup = new function() {
				this.openState = false;
				let HTML = {
					Self: document.getElementsByClassName("popup")[0],
				}
				HTML.inputField = HTML.Self.children[0];


				this.open = function() {
					this.openState = true;
					HTML.Self.classList.remove("hide");
					
					HTML.inputField.focus();
					HTML.inputField.value = null;
					HTML.inputField.classList.remove("invalid");
					
					bakeButton.classList.add("hide");
				}

				this.close = function() {
					this.openState = false;
					HTML.Self.classList.add("hide");
					bakeButton.classList.remove("hide");
				}

				this.putCakeInOven = function() {
					let cakeCode = HTML.inputField.value.replace(/[^a-zA-Z0-9]/g, "");
					if (cakeCode == Oven.cakeCode) return Oven.startBaking();
					HTML.inputField.classList.add("invalid");
					HTML.inputField.focus();
					return false;
				}
			}





			const Drawer = new function() {
				const Canvas = mixPanCanvas;
				const ctx = Canvas.getContext("2d");


				const This = {
					drawOven: drawOven,
				}

				const sideWidth = 25;
				const panHeight = Canvas.height - sideWidth;

				function drawOven() {
					ctx.clearRect(0, 0, Canvas.width, Canvas.height);
					drawOvenContents();

					var img = document.getElementById("imageHolder");
  					ctx.drawImage(img, 0, 0, Canvas.width, Canvas.height);
  					
  					ctx.fillStyle = "#444";
  					ctx.font = "26px Verdana";
  					ctx.textAlign = "center";
  					let text = OvenPinger.timeText;
  					if (!text) text = "00:00";
  					ctx.fillText("00:" + text, 250, 85);
  					ctx.fill();
				}


				const startX = 67;
				const startY = 142;
				const width = Canvas.width - startX * 2;
				const height = 291;

				const flameWidth = 5;
				const maxFlameHeight = height * .3;
				
				let prevFlameHeight = [];
				for (let i = 0; i < width; i += flameWidth) prevFlameHeight[i] = maxFlameHeight * Math.random();

				function drawOvenContents() {
					if (!Oven.status) return;
					drawFlames();

					if (Oven.status.stage == "baking") drawCake();
				}

				function drawFlames() {
					let progress = 0;
					if (Oven.status.stage == "heating") progress = OvenPinger.progress;
					if (Oven.status.stage == "finishedHeating" || Oven.status.stage == "baking") progress = 1;

					let maxHeight = maxFlameHeight * progress;
					for (let i = 0; i < width; i += flameWidth)
					{	
						let flameRange = height * .05;
						let flameHeight = prevFlameHeight[i] + flameRange - 2 * flameRange * Math.random();
						
						if (flameHeight > maxHeight) flameHeight = maxHeight;
						if (flameHeight < 0) flameHeight = 0;

						prevFlameHeight[i] = flameHeight;
						ctx.fillStyle = "rgba(" + 
							(200 + 55 * Math.random()) + ", " + 
							(50 + 80 * Math.random()) + ", " + 
						"0, " +  (.5 + progress / 2) + ")";

						ctx.fillRect(startX + i, startY + height - flameHeight, flameWidth, flameHeight);
						ctx.fill();
					}
				}
				
				function drawCake() {
					ctx.fillStyle = "#222";
					ctx.fillRect(startX, startY + 150, width, 5);
					ctx.fillRect(startX, startY + 157, width, 5);
					ctx.fill();

					const cakeWidth = 200;
					const cakeY = startY + 98;
					const cakePerspective = 5;


					ctx.fillStyle = "#777";
					ctx.fillRect(startX + (width - cakeWidth) / 2, cakeY, cakeWidth, 50);

					ctx.fillStyle = "#c80";
					ctx.strokeStyle = "#888";
					ctx.lineWidth = 3;
					ctx.beginPath();
					ctx.ellipse(startX + width / 2, cakeY, cakeWidth / 2, cakePerspective, 0, -Math.PI, Math.PI);
					ctx.closePath();
					ctx.fill();
					ctx.stroke();

					ctx.fillStyle = "#777";			
					ctx.beginPath();
					ctx.ellipse(startX + width / 2, cakeY + 50, cakeWidth / 2, cakePerspective, 0, 0, Math.PI);
					ctx.closePath();
					ctx.fill();
				}

				return This;
			};


			



				function finishedMixing() {
					actionIndicator.innerHTML = "De deeg-code is <strong>" + Oven.deegCode + "</strong>";
					themeColour.content = "#5ad583";
				}



			setInterval(function () {Oven.update();}, 100);
			Oven.updateOvenStatus();
		</script>

	</body>
</html>