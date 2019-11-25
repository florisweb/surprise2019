
const OvenPinger = new function() {
	this.wekkerType = false; // pre, cake
	this.timeText = "";
	

	this.setup = function() {
		setInterval(function () {OvenPinger.update()}, 1000);
	}

	function getStartTimeFromLocalStorage() {
		OvenPinger.wekkerLength = Infinity;
		if (localStorage.ElisaSurprise_preStartTime) 
		{
			OvenPinger.wekkerType = "Voorverwarm"; 
			OvenPinger.wekkerLength = 100;
			return new Date(localStorage.ElisaSurprise_preStartTime);
		}

		if (localStorage.ElisaSurprise_cakeStartTime) 
		{
			OvenPinger.wekkerType = "Bak"; 
			OvenPinger.wekkerLength = 30;
			return new Date(localStorage.ElisaSurprise_cakeStartTime);
		}

		return false;
	}	


	let lastSlowUpdate = new Date();
	this.update = function() {
		if (new Date() - lastSlowUpdate < 500) return;
		lastSlowUpdate = new Date();

		let startTime = getStartTimeFromLocalStorage();

		this.progress = 1 - (OvenPinger.wekkerLength - (new Date() - startTime) / 1000) / OvenPinger.wekkerLength;
		if (this.progress > 1) this.progress = 1;

		this.timeText = secondsToText((1 - this.progress) * this.wekkerLength);
		
		let text = this.wekkerType + "-wekker: " + this.timeText;
		try {
			alarmHeaderHolder.innerHTML = text;
		} catch (e) {}
		
		if (new Date() - startTime < this.wekkerLength * 1000 ) return;
		this.playPingSound();
	}


	this.playPingSound = function() {
		var audio = new Audio('audio/ping.mp3');
		audio.play();
	}


	function secondsToText(seconds) {
		if (seconds < 0) seconds = 0;
		let minutes = Math.floor(seconds / 60);
		seconds = Math.floor(seconds % 60);

		if (minutes < 10) minutes = "0" + minutes;
		if (seconds < 10) seconds = "0" + seconds;
		return minutes + ":" + seconds;
	}


	this.setup();	
}




		