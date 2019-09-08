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
			path: 'M216 23.86c0-23.8-30.65-32.77-44.15-13.04C48 191.85 224 200 224 288c0 35.63-29.11 64.46-64.85 63.99-35.17-.45-63.15-29.77-63.15-64.94v-85.51c0-21.7-26.47-32.23-41.43-16.5C27.8 213.16 0 261.33 0 320c0 105.87 86.13 192 192 192s192-86.13 192-192c0-170.29-168-193-168-296.14z',
			// scale: 0.65
			scale: 0.07,
			strokeWeight: 0.2,
			strokeColor: 'black',
			strokeOpacity: 1,
			fillColor: '#fd7e14',
			fillOpacity: 1,
		};

		const tree_icon = {
			path: 'M378.31 378.49L298.42 288h30.63c9.01 0 16.98-5 20.78-13.06 3.8-8.04 2.55-17.26-3.28-24.05L268.42 160h28.89c9.1 0 17.3-5.35 20.86-13.61 3.52-8.13 1.86-17.59-4.24-24.08L203.66 4.83c-6.03-6.45-17.28-6.45-23.32 0L70.06 122.31c-6.1 6.49-7.75 15.95-4.24 24.08C69.38 154.65 77.59 160 86.69 160h28.89l-78.14 90.91c-5.81 6.78-7.06 15.99-3.27 24.04C37.97 283 45.93 288 54.95 288h30.63L5.69 378.49c-6 6.79-7.36 16.09-3.56 24.26 3.75 8.05 12 13.25 21.01 13.25H160v24.45l-30.29 48.4c-5.32 10.64 2.42 23.16 14.31 23.16h95.96c11.89 0 19.63-12.52 14.31-23.16L224 440.45V416h136.86c9.01 0 17.26-5.2 21.01-13.25 3.8-8.17 2.44-17.47-3.56-24.26z',
			scale: 0.05,
			strokeWeight: 0.2,
			strokeColor: 'black',
			strokeOpacity: 1,
			fillColor: '#2ECC40',
			fillOpacity: 1,
		};

		new google.maps.Marker({
			position: position,
			map: map,
			clickable: false,
			icon: alert_icon
		});

		const trees = window.trees_data;

		for (let i = 0; i < trees.length; i++) {
			new google.maps.Marker({
				position: {lat: trees[i].lat, lng: trees[i].long},
				map: map,
				icon: tree_icon
			});
		}
	};
})();
