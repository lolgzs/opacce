#!/bin/bash

# Open iPhone Simulator on default location for XCode 4.3 if found

[[ -d /Applications/Xcode.app/Contents/Developer/Platforms/iPhoneSimulator.platform/Developer/Applications/iPhone\ Simulator.app/ ]] &&
	open /Applications/Xcode.app/Contents/Developer/Platforms/iPhoneSimulator.platform/Developer/Applications/iPhone\ Simulator.app

# Open iPhone Simulator on default location for XCode 4.2 if found
[[ -d /Developer/Platforms/iPhoneSimulator.platform/Developer/Applications/iPhone\ Simulator.app/ ]] &&
	open /Developer/Platforms/iPhoneSimulator.platform/Developer/Applications/iPhone\ Simulator.app

# Open mobile safari
echo Open mobile safari on emulator and press return
read

# Plug debug to MobileSafari.app
echo Debugging
MobileSafari_PID=$(ps x | grep "MobileSafari" | grep -v grep | awk '{ print $1 }')

if [ "$MobileSafari_PID" == "" ]; then
  echo "Mobile Safari.app must be running in the Simulator to enable the remote inspector."
  exit
else

  cat <<EOM | gdb -quiet > /dev/null
  attach $MobileSafari_PID
  p (void *)[WebView _enableRemoteInspector]
  detach
EOM
fi

# Open debugger in Safari.app
open -a /Applications/Safari.app http://localhost:9999
