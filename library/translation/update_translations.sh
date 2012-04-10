#!/bin/sh
export LC_ALL="fr_FR.UTF-8"

for i in {2..4}
do
		find \
				../../application \
				../../library \
				-type f \( -name "*.php" -o -name "*.phtml" \) \
				-exec  xgettext \
				-j \
				-L PHP \
				-o fr.pot \
				--from-code=utf-8 \
				--keyword=traduire \
				--keyword=openBoite \
				--keyword=_plural:$i \
				{} \;
done

for i in `ls *.po`
do
		msgmerge -s $i fr.pot -o $i
		msgfmt -o `echo $i|cut -d '.' -f1`.mo $i
done
