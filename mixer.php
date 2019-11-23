<!DOCTYPE html>
<html>
	<head>
		<link rel="stylesheet" type="text/css" href="css/mainUI.css">
		<meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0' name='viewport'/>
		<meta name="theme-color" content="#636ad5">
		<style>

			#mainContentHolder {
				height: calc(100vh - 30px * 1 - 30px);
			}

			#mixPanCanvas {
				position: relative;
				top: calc(((100vh - 30px * 2 - 10px * 2 * 2 - 30px) - 80vw) / 2);
				width: calc(60vw);
				height: auto;
			}
			
			#ingredientDropIn {
				position: absolute;
				left: calc(50vw - 20vw);
				top: -50vh;
				width: calc(40vw);
				height: auto;
				transition: all .3s;
			}

			#ingredientDropIn.drop {
				top: 30vh;
			}

		</style>

		<title>Mixer - Surprise 2019</title>
	</head>
	<body>
		<div id="mainContentHolder">
			<div class="centerAligner" id="mainContent">
				<img id="ingredientDropIn" class="drop">
				<canvas id="mixPanCanvas" width="500" height="500"></canvas>
			</div>
		</div>


		<div id="bottomBar">
			<div class="centerAligner" style="max-width: 160px">
				<div class="button bDefault clickable" onclick="dropIngredient()">Voeg ingredient toe</div>
			</div>
		</div>
		<a id="info" style="position: fixed;">hey</a>


		<script>
			const Mixer = new function() {
				this.progress = 0;

			}

			const Drawer = new function() {
				const Canvas = mixPanCanvas;
				const ctx = Canvas.getContext("2d");

				let This = {
					drawPan: drawPan,
				}
				return This;

				function drawPan(_progress) {
					const sideWidth = 25;
					const panHeight = Canvas.height - sideWidth;

					ctx.lineWidth = sideWidth * 2;
					ctx.strokeStyle = "#000";
					ctx.strokeRect(0, 0, Canvas.width, Canvas.height);
					ctx.stroke();

					ctx.fillStyle = "#fff";
					ctx.fillRect(sideWidth, 0, Canvas.width - 2 * sideWidth, Canvas.height - sideWidth);
					ctx.fill();

					ctx.fillStyle = "#f00";
					ctx.fillRect(sideWidth, panHeight * (1 - _progress), Canvas.width - 2 * sideWidth, panHeight * _progress);
					ctx.fill();
				}

			}


			function dropIngredient() {
				ingredientDropIn.style.transition = "all 0s";
				ingredientDropIn.classList.remove("drop");
				ingredientDropIn.setAttribute("src", "images/egg.png");
				setTimeout(function () {
					ingredientDropIn.style.transition = "";
					ingredientDropIn.classList.add("drop");
				}, 100);
			}



			(function() {
				let progress = 0;
				const target = 12000;
				const minimumChange = .8;
				let finished = false;

				window.addEventListener('devicemotion', function(event) {  
				  let vx = event.acceleration.x;
				  let vy = event.acceleration.y;

				  if (vx < minimumChange && vx > -minimumChange) vx = 0;
				  if (vy < minimumChange && vy > -minimumChange) vy = 0;

				  mixPanCanvas.style.marginLeft = vx * 20 + "px";
				  mixPanCanvas.style.transform = "rotateZ(" + vy * 2 + "deg)";

				  progress += Math.abs(vx) + Math.abs(vy);
				  if (progress / target > 1) finished = true;
				  if (finished) return;

				  Mixer.progress = progress / target;
				  info.innerHTML = Math.round(Mixer.progress * 1000) / 10; + "%";
				  Drawer.drawPan(Mixer.progress);
				});
			})();
		</script>


	</body>
</html>