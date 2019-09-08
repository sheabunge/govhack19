import csv
import os
from os import path
from urllib.request import urlretrieve
from urllib.error import HTTPError
from urllib.parse import urlparse

with open('sets/Significant_tree_point.csv', mode='r', encoding='utf-8-sig') as csv_file:
    reader = csv.DictReader(csv_file)
    records = list(reader)

dest_dir = path.join(path.abspath(path.curdir), 'datasheets')

if not path.exists(dest_dir):
    os.makedirs(dest_dir)

for record in records:
    url = record.get('Data_Sheet_URL')

    filename = path.basename(urlparse(url).path)
    url = url.replace(' ', '%20')

    if path.exists(filename):
        print(f'{filename} already exists')
        continue

    print(f'downloading {url} to {filename}')
    try:
        urlretrieve(url, path.join(dest_dir, filename))
    except HTTPError as e:
        print(e.msg)
        continue
