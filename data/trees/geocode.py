import json
import requests
from csv import DictReader, DictWriter
from os import path
from urllib.parse import urlparse

with open('../mapskey') as key_file:
	MAPS_KEY = key_file.read().strip()

GEOCODE_URL = 'https://maps.googleapis.com/maps/api/geocode/json?key=' + MAPS_KEY + '&region=AU'

cache = {}

with open('significant_tree_point.csv', 'r') as read_file:
	reader = DictReader(read_file)
	reader.fieldnames.extend(['address', 'full_address', 'lat', 'long', 'place_id'])

	with open('trees.csv', 'w') as write_file:
		writer = DictWriter(write_file, reader.fieldnames)
		writer.writeheader()

		for record in reader:
			filename = record.get('Data_Sheet_URL')
			filename = path.basename(urlparse(filename).path)
			filename, _ = path.splitext(filename)

			address = filename.split()[1:]
			if address[0] in ('adj', 'adj.'):
				address = address[1:]

			address = ' '.join(address)
			record['address'] = address

			if not cache.get(address):
				response = requests.get(GEOCODE_URL + '&address=' + requests.utils.quote(address + ', TAS, Australia'))
				if response:
					response = json.loads(response.text)
					if response['status'] == 'OK':
						cache[address] = response['results'][0]

			result = cache.get(address)
			if result:
				record['full_address'] = result['formatted_address']
				record['lat'] = result['geometry']['location']['lat']
				record['long'] = result['geometry']['location']['lng']
				record['place_id'] = result['place_id']

			writer.writerow(record)
