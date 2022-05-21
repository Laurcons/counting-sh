#!/bin/bash

oldd=`pwd`
mkdir -p ~/bin
cd ~/bin
if [ -f counting ]; then rm counting; fi
if [ -f counting.sh ]; then rm counting.sh; fi
wget -q https://scs.ubbcluj.ro/~plie3204/counting/counting.sh
mv counting.sh counting
chmod u+x counting
cd $oldd
echo "Successfully installed."
echo "Use \"counting\" to count."
