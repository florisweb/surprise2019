
const OvenPinger = new function() {
	this.wekkerType = false; // pre, cake
	this.timeText = "";

	const HTML = {
		ovenPingerPopup: document.getElementsByClassName("popup ovenPinger")[0]
	}
	

	this.setup = function() {
		setInterval(function () {OvenPinger.update()}, 1000);
	}

	function getStartTimeFromLocalStorage() {
		OvenPinger.wekkerLength = Infinity;

		if (!localStorage.ElisaSurprise_status) return false;
		let status = JSON.parse(localStorage.ElisaSurprise_status);

		switch (status.stage) 
		{
			case "heating":
			case "finishedHeating": 
				OvenPinger.wekkerType = "Voorverwarm"; 
				OvenPinger.wekkerLength = 20;
				return new Date(status.startTime);
			break;
			case "baking": 
				OvenPinger.wekkerType = "Bak"; 
				OvenPinger.wekkerLength = 20;
				return new Date(status.startTime);
			break;
			default: 
				return false;
			break
		}
	}	


	let lastSlowUpdate = new Date();
	this.update = function() {
		if (new Date() - lastSlowUpdate < 500) return;
		lastSlowUpdate = new Date();

		let startTime = getStartTimeFromLocalStorage();
		if (!startTime) return setAlarmHeaderHolderText("");


		this.progress = 1 - (OvenPinger.wekkerLength - (new Date() - startTime) / 1000) / OvenPinger.wekkerLength;
		if (this.progress > 1) this.progress = 1;

		this.timeText = secondsToText((1 - this.progress) * this.wekkerLength);
		
		let text = this.wekkerType + "-wekker: " + this.timeText;
		setAlarmHeaderHolderText(text);
		
		if (new Date() - startTime < this.wekkerLength * 1000) return;

		
		sendAlarmNotification();

		let status = JSON.parse(localStorage.ElisaSurprise_status);
		if (status.stage == "heating") status.stage = "finishedHeating";
		localStorage.ElisaSurprise_status = JSON.stringify(status);
	}

	function setAlarmHeaderHolderText(_text) {
		try {
			alarmHeaderHolder.innerHTML = _text;
		} catch (e) {}
	}

	function sendAlarmNotification() {
		OvenPinger.playPingSound();
		
		if (!HTML.ovenPingerPopup) return;
		HTML.ovenPingerPopup.classList.remove("hide");
		HTML.ovenPingerPopup.children[0].innerHTML = OvenPinger.wekkerType + "en voltooid";
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




		