#!/bin/sh
cat \
minplayer/src/minplayer.compatibility.js \
minplayer/src/minplayer.flags.js \
minplayer/src/minplayer.async.js \
minplayer/src/minplayer.plugin.js \
minplayer/src/minplayer.display.js \
minplayer/src/minplayer.js \
minplayer/src/minplayer.image.js \
minplayer/src/minplayer.file.js \
minplayer/src/minplayer.playLoader.js \
minplayer/src/minplayer.players.base.js \
minplayer/src/minplayer.players.html5.js \
minplayer/src/minplayer.players.flash.js \
minplayer/src/minplayer.players.minplayer.js \
minplayer/src/minplayer.players.youtube.js \
minplayer/src/minplayer.players.vimeo.js \
minplayer/src/minplayer.controller.js \
src/iscroll/src/iscroll.js \
src/osmplayer.js \
src/osmplayer.parser.default.js \
src/osmplayer.parser.youtube.js \
src/osmplayer.parser.rss.js \
src/osmplayer.parser.asx.js \
src/osmplayer.parser.xspf.js \
src/osmplayer.playlist.js \
src/osmplayer.pager.js \
src/osmplayer.teaser.js \
templates/default/js/osmplayer.default.js \
templates/default/js/osmplayer.controller.default.js \
templates/default/js/osmplayer.pager.default.js \
templates/default/js/osmplayer.playLoader.default.js \
templates/default/js/osmplayer.playlist.default.js \
templates/default/js/osmplayer.teaser.default.js \
> osmplayer.full.js

java -jar ~/Downloads/compiler-latest/compiler.jar --js=osmplayer.full.js > osmplayer.full.min.js 


