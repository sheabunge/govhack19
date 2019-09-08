#!/usr/bin/env bash

# process each PDF in the datasheets directory
for path in datasheets/*.pdf
do
  # extract just the filename portion from the path
  filename=$(basename -- "$path")
  filename="${filename%.*}"

  echo "$filename"

  # use pdfimages to extract all images embedded in the PDF as png files
  pdfimages -png "$path" "images/$filename"
done

# images smaller than 35kb are likely not useful (formatting decorations)
find images/* -size -35k -delete
