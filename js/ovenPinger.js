
const OvenPinger = new function() {
	this.startTime = 0;
	this.wekkerType = false; // pre, cake
	this.wekkerLength = 0; //s
	

	this.setup = function() {
		setInterval(function () {OvenPinger.update()}, 1000);
		
		if (localStorage.ElisaSurprise_preStartTime) 
		{
			OvenPinger.wekkerType = "pre"; 
			this.wekkerLength = 60;
			this.startTime = new Date(localStorage.ElisaSurprise_preStartTime);
		}

		if (localStorage.ElisaSurprise_cakeStartTime) 
		{
			this.wekkerType = "cake"; 
			this.wekkerLength = 60;
			this.startTime = new Date(localStorage.ElisaSurprise_cakeStartTime);
		}
	}

	let lastSlowUpdate = new Date();
	this.update = function() {
		if (new Date() - lastSlowUpdate < 1000) return;
		lastSlowUpdate = new Date();

		

		if (new Date() - this.startTime < this.wekkerLength) return;
		console.log("ellapsed");
		this.playPingSound();
	}

	this.playPingSound = function() {
		var audio = new Audio('audio/ping.mp3');
		audio.play();
	}
}




		