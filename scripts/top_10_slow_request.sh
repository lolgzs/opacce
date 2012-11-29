#!/bin/sh
grep -o "\/[^?]\+?[^&]\+" $1|sort|uniq -c|sort|tail -n 10
