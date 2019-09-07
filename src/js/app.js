(function () {
	'use strict';

	window.load_incident_map = () => {
		const item = window.incident_info;
		const position = new google.maps.LatLng(item.lat, item.long);

		const map = new google.maps.Map(document.getElementById('map'), {
			zoom: 12,
			center: position,
			scaleControl: true,
			mapTypeId: google.maps.MapTypeId.ROADMAP
		});

		const alert_icon = {
			path: 'M504 256c0 136.997-111.043 248-248 248S8 392.997 8 256C8 119.083 119.043 8 256 8s248 111.083 248 248zm-248 50c-25.405 0-46 20.595-46 46s20.595 46 46 46 46-20.595 46-46-20.595-46-46-46zm-43.673-165.346l7.418 136c.347 6.364 5.609 11.346 11.982 11.346h48.546c6.373 0 11.635-4.982 11.982-11.346l7.418-136c.375-6.874-5.098-12.654-11.982-12.654h-63.383c-6.884 0-12.356 5.78-11.981 12.654z',
			// scale: 0.65
			scale: 0.05,
			strokeWeight: 0.2,
			strokeColor: 'black',
			strokeOpacity: 1,
			fillColor: '#E32831',
			fillOpacity: 1,
		};

		new google.maps.Marker({
			position: position,
			map: map,
			clickable: false,
			icon: alert_icon
		});
	};
})();
