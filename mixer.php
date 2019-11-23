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

			#mixerCanvas {
				position: relative;
				top: 0;
				width: calc(80vw);
				height: auto;
				box-shadow: 10px 10px 50px 20px rgba(0, 0, 0, .1);
				border-radius: 100%;
			}

		</style>

		<title>Mixer - Surprise 2019</title>
	</head>
	<body>
		<div id="mainContentHolder">
			<div class="centerAligner" id="mainContent">
				<canvas id="mixerCanvas" width="500" height="500"></canvas>
			</div>
		</div>


		<div id="bottomBar">
			<div class="centerAligner" style="max-width: 160px">
				<div class="button bDefault clickable">Voeg ingredient toe</div>
			</div>
		</div>
		<a id="info" style="position: fixed;">hey</a>


		<script>

			const Drawer = new function() {
				const Canvas = mixerCanvas;
				const ctx = Canvas.getContext("2d");
				
				ctx.constructor.prototype.circle = function(x, y, size) {
				    if (size < 0) return;

				    this.beginPath();
				    this.ellipse(
				      x, 
				      y, 
				      size,
				      size,
				      0,
				      0,
				      2 * Math.PI
				    );
				    this.closePath();
				}

				function drawPan() {
					const stepSize = 5;
					ctx.fillStyle = "#888";
					for (let i = 0; i < 100; i += stepSize)
					{
						ctx.lineWidth = stepSize;
						let grayScaleVal = -i / 200 * 100 + 190;

						ctx.strokeStyle = "rgb(" + grayScaleVal + ", " + grayScaleVal + ", " + grayScaleVal + ")";
						ctx.circle(Canvas.width / 2, Canvas.height / 2, 248 - i);
						ctx.stroke();
					}

					ctx.fill();
				}

				drawPan();
			};



			(function() {
				let progress = 0;
				const target = 12000;
				const minimumChange = .3;
				let finished = false;

				window.addEventListener('devicemotion', function(event) {  
				  let vx = event.acceleration.x;
				  let vy = event.acceleration.y;

				  if (vx < minimumChange && vx > -minimumChange) vx = 0;
				  if (vy < minimumChange && vy > -minimumChange) vy = 0;

				  Canvas.style.marginLeft = vx * 20 + "px";
				  Canvas.style.marginTop = vy * 20 + "px";

				  progress += Math.abs(vx) + Math.abs(vy);
				  if (progress / target > 1) finished = true;
				  if (finished) return;
				  info.innerHTML = Math.round(progress / target * 1000) / 10 + "%";
				});
			})();
		</script>


	</body>
</html>