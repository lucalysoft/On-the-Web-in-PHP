#!/bin/bash
set -e;
SCRIPT=$(readlink -f $0);
SCRIPTPATH=`dirname $SCRIPT`;
ROOTPATH=`dirname $SCRIPTPATH`;

function push_output {
FILE=$1;
echo $FILE;
curl -F "files[]=@$FILE" https://rokket.space/upload\?output\=text;
echo " ";
}

#
cd $ROOTPATH/tests/_output

# push fail.png
for filename in *.png; do
    if [ -f $filename ]; then
        push_output $filename;
    else
        echo "$filename is not a file";
        continue;
    fi;
done