#!/bin/sh

export LANG=C

mkdir -p ../lang/locale/en_EN/LC_MESSAGES/

while read i ; do

	MSGID=$(echo $i | grep msgid)
	STR=$(echo $MSGID | sed 's/msgid //g')
	MSGSTR=$(echo msgstr $STR)
	
	if [ ! -z "$MSGID" ] ; then
		echo "$MSGID\n$MSGSTR\n" >> ../lang/locale/en_EN/LC_MESSAGES/igestis.po
	fi

done < ../lang/locale/fr_FR/LC_MESSAGES/igestis.po
