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
			#panImageHolder {display: none}
		</style>

		<title>Mixer - Surprise 2019</title>
	</head>
	<body>
		<img id="panImageHolder" src="images/pan.png">
		<div id="mainContentHolder">
			<a class="text mainHeader" id="mixPercentageHolder">0%</a>
			<div class="centerAligner" id="mainContent">
				<canvas id="mixPanCanvas" width="500" height="500"></canvas>
				
				<div class="popup hide">
					<input class="inputField iBoxy text" placeholder="Ingedient-code">
					<br>
					<br>
					<div class="button bDefault clickable bBoxy" style="width: 40%; margin: auto" onclick="Popup.addIngredient()">Toevoegen</div>
				</div>
			</div>
			<img id="ingredientDropIn" class="drop">
		</div>

		<div id="bottomBar">
			<div class="centerAligner" style="max-width: 160px;">
				<div class="button bDefault clickable" onclick="Popup.open()">Voeg ingredient toe</div>
			</div>
		</div>



		<script>
			const Mixer = new function() {
				this.mixPercentage = 0;

				this.ingredients = [
					{name: "Ei", code: "1233", imageUrl: "images/egg.png", substanceColour: "#fa0", size: 1},
					{name: "Boter", code: "1234", imageUrl: "images/boter.png", substanceColour: "#fc5", size: 2},
					{name: "Appeltaartmix", code: "1235", imageUrl: "images/mix.png", substanceColour: "#edc", size: 3}
				];
				this.addedIngredients = [];
				
				this.addIngredient = function(_ingredient) {
					ingredientDropIn.classList.remove("drop");
					ingredientDropIn.setAttribute("src", _ingredient.imageUrl);
					setTimeout(function () {
						ingredientDropIn.classList.add("drop");
					}, 1);

					if (getIngedientByCode(this.addedIngredients, _ingredient.code)) return;
					this.addedIngredients.push(_ingredient);
					
					setTimeout(function () {Drawer.drawPan(Mixer.mixPercentage);}, 250);
				}

				function getIngedientByCode(_list, _code) {
					for (ingredient of _list)
					{
						if (ingredient.code != _code) continue;
						return ingredient;
					}
					return false;
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
					
					if (!_showBottomBar) return;
					bottomBar.classList.remove("hide");
				}

				this.addIngredient = function() {
					let ingredientCode = HTML.inputField.value.replace(/[^a-zA-Z0-9]/g, "");
					for (ingredient of Mixer.ingredients)
					{
						if (ingredient.code != ingredientCode) continue;
						this.close(Mixer.ingredients.length != Mixer.addedIngredients.length + 1);
						setTimeout(function () {Mixer.addIngredient(ingredient);}, 350);
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
					drawPan: drawPan,
				}

				const sideWidth = 25;
				const panHeight = Canvas.height - sideWidth;

				function drawPan(_mixPercentage) {
					ctx.clearRect(0, 0, Canvas.width, Canvas.height);
					if (Mixer.addedIngredients.length) drawIngedients(_mixPercentage);

					var panImg = document.getElementById("panImageHolder");
  					ctx.drawImage(panImg, 0, 0, Canvas.width, Canvas.height);
				}

				function drawIngedients(_mixPercentage) {
					const fillHeight = panHeight * .7;
					
					let totalUnitHeight = 0;
					for (ingredient of Mixer.ingredients) totalUnitHeight += ingredient.size;
					let unitHeigth = fillHeight / totalUnitHeight;

					let addedUnits = 0;
					for (let i = 0; i < Mixer.addedIngredients.length; i++) 
					{
						let ingredient = Mixer.addedIngredients[i];
						addedUnits += ingredient.size;

						ctx.fillStyle = ingredient.substanceColour;
						ctx.fillRect(
							sideWidth, 
							panHeight - addedUnits * unitHeigth,
							Canvas.width - 2 * sideWidth, 
							ingredient.size * unitHeigth
						);
						ctx.fill();
					}

					addedUnits = Mixer.addedIngredients[0].size;

					const gradientHeight = 190 * _mixPercentage;

					for (let i = 1; i < Mixer.addedIngredients.length; i++) 
					{
						let prevIngredient = Mixer.addedIngredients[i - 1];
						let ingredient = Mixer.addedIngredients[i];
						
						let gradientY = panHeight - addedUnits * unitHeigth - gradientHeight / 2;
						addedUnits += ingredient.size;

						var grd = ctx.createLinearGradient(
							sideWidth, 
							gradientY, 
						 	sideWidth, 
						 	gradientY + gradientHeight
						);

						grd.addColorStop(0, ingredient.substanceColour);
						grd.addColorStop(1, prevIngredient.substanceColour);
						
						ctx.fillStyle = grd;
						ctx.fillRect(
							sideWidth, 
							gradientY, 
							Canvas.width - 2 * sideWidth, 
							gradientHeight
						);
					}
				}


				drawPan(0);
				return This;
			};


			


			(function() {
				let progress = 0;
				const target = 12000;
				const minimumChange = .8;

				window.addEventListener('devicemotion', function(event) {  
				  let vx = event.acceleration.x;
				  let vy = event.acceleration.y;

				  if (vx < minimumChange && vx > -minimumChange) vx = 0;
				  if (vy < minimumChange && vy > -minimumChange) vy = 0;

				  mixPanCanvas.style.marginLeft = vx * 20 + "px";
				  mixPanCanvas.style.transform = "rotateZ(" + vy * 4 + "deg)";

				  if (Mixer.addedIngredients.length != Mixer.ingredients.length || progress == target) return;

				  progress += Math.abs(vx) + Math.abs(vy);

				  if (progress > target) progress = target;
				  Mixer.mixPercentage = progress / target;
				  
				  if (progress == target) themeColour.content = "#5ad583";
				  
				  mixPercentageHolder.innerHTML = Math.round(Mixer.mixPercentage * 1000) / 10 + "%";
				  Drawer.drawPan(Mixer.mixPercentage);
				});
			})();
		</script>


	</body>
</html>