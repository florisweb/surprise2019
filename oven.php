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

		
		</style>

		<title>Oven - Surprise 2019</title>
	</head>
	<body>
		<img id="imageHolder" src="images/oven.png" onload="Drawer.drawOven(360)">

		<div id="mainContentHolder">
			<a class="text mainHeader" id="percentageHolder"></a>
			<br>
			<a class="text" id="actionIndicator">Verwarm de oven eerst voor</a>

			<div class="centerAligner" id="mainContent">
				<canvas id="mixPanCanvas" width="500" height="500"></canvas>
				
				<div class="popup hide">
					<input class="inputField iBoxy text" placeholder="Temperatuur" 
						style="width: calc(100% - 50px); float: left"
						onkeyup="this.value = this.value.replace(/[^0-9]/g, '');">
					<div class="text" style="float: left; margin-left: 5px; margin-top: 7px">*C</div>
					<br>
					<br>
					<br>
					<div class="button bDefault clickable bBoxy" style="width: 60%; margin: auto" onclick="Popup.addIngredient()">Start voorverwarmen</div>
				</div>
			</div>
			<img id="ingredientDropIn" class="drop">
		</div>

		<div id="bottomBar">
			<div class="centerAligner" style="max-width: 200px;">
				<div class="button bDefault clickable" onclick="Oven.startHeatingUp()">Voorverwarmen (255*C)</div>
			</div>
		</div>

		<script src="js/ovenPinger.js"></script>
		<br>
		<button onclick="localStorage.ElisaSurprise_preStartTime = localStorage.ElisaSurprise_cakeStartTime = '';">Reset</button>

		<script>
			const Oven = new function() {
				this.isHeatingUp = false;

				const stepSize = 1;



				let lastSlowUpdate = new Date();
				this.update = function() {
					if (!this.isHeatingUp) return;
					Drawer.drawOven();

					if (new Date() - lastSlowUpdate < 1000) return;
					lastSlowUpdate = new Date();
					
					percentageHolder.innerHTML = OvenPinger.timeText;
				}

			



				this.startHeatingUp = function() {
					this.isHeatingUp = true;
					
					if (!localStorage.ElisaSurprise_preStartTime)
					{
						localStorage.ElisaSurprise_preStartTime = new Date();
					}

					bottomBar.classList.add("hide");
					actionIndicator.innerHTML = "Kookwekker is gezet<br><strong style='color: red'>Zorg er voor dat je je geluid aan hebt staan</strong>";
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
					
					bottomBar.classList.add("hide");
				}

				this.close = function(_showBottomBar = true) {
					this.openState = false;
					HTML.Self.classList.add("hide");
					
					if (!_showBottomBar) return actionIndicator.innerHTML = "Schud om te mixen";
					bottomBar.classList.remove("hide");
				}

				this.addIngredient = function() {
					let ingredientCode = HTML.inputField.value.replace(/[^a-zA-Z0-9]/g, "");
					for (ingredient of Oven.ingredients)
					{
						if (ingredient.code != ingredientCode) continue;
						this.close(Oven.ingredients.length != Oven.addedIngredients.length + 1);
						setTimeout(function () {Oven.addIngredient(ingredient);}, 350);
						return true;
					}

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
  					ctx.fillText("00:" + OvenPinger.timeText, 250, 85);
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
					let maxHeight = maxFlameHeight * OvenPinger.progress;
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
						"0, " +  (.5 + OvenPinger.progress / 2) + ")";

						ctx.fillRect(startX + i, startY + height - flameHeight, flameWidth, flameHeight);
						ctx.fill();
					} 


				}

				return This;
			};


			



				function finishedMixing() {
					actionIndicator.innerHTML = "De deeg-code is <strong>" + Oven.deegCode + "</strong>";
					themeColour.content = "#5ad583";
				}

			setInterval(function () {Oven.update();}, 100);
		</script>

	</body>
</html>