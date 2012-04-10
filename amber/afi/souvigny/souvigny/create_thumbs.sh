#!/bin/sh


for i in `ls *V.jpg`; do convert -resize 400x594\! -gravity East -chop 10x0 $i thumbs/$i;done
for i in `ls *V_*.jpg`; do convert -resize 400x594\! -gravity East -chop 10x0 $i thumbs/$i;done
for i in `ls *0000_*.jpg`; do convert -resize 400x594\! -chop 10x0 $i thumbs/$i;done

for i in `ls *R.jpg`; do convert -resize 400x594\! -chop 10x0 $i thumbs/$i;done

for i in `ls *.jpg`; do convert -geometry 2400 -quality 80 $i big/$i; done
