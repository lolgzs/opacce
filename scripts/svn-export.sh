#!/bin/bash

if [ ! $1 ] || [ ! $2 ]; then
    echo "Please enter an SVN repository, target directory and optionally a revision from"
    exit
fi

# set up nice names for the incoming parameters to make the script more readable
repository=$1
target_directory=$2
revision_from=$3

# if revision_from is not already set, get it from the .revision file if it exists
if [ ! $revision_from ] && [ -f $target_directory/.revision ]; then
    revision_from=`cat $target_directory/.revision`
fi

# now either get it from a specific revision, or everything
if [ $revision_from ]; then

    # the grep is needed so we only get added/modified files and not the deleted ones or anything else
    # if it's a modified directory it's " M" so won't show with this command (good)
    # if it's an added directory it's still "A" so will show with this command (not so good)

    for line in `svn diff --summarize -r$revision_from:HEAD $repository | grep "^[AM]"`
    do
        # each line in the above command in the for loop is split into two:
        # 1) the status line (containing A, M, AM, D etc)
        # 2) the full repository and filename string
        # so only export the file when it's not the status line
        if [ $line != "A" ] && [ $line != "AM" ] && [ $line != "M" ]; then
            # use sed to remove the repository from the full repo and filename
            filename=`echo "$line" |sed "s|$repository||g"`
            # don't export if it's a directory we've already created
            if [ ! -d $target_directory$filename ]; then
                directory=`dirname $filename`
                mkdir -p $target_directory$directory
                svn export $line $target_directory$filename
            fi
        fi
    done

    # to summarize any deleted files or directories at the end of the script uncomment the following line
    #svn diff --summarize -r$revision_from:HEAD $repository | grep "^[D]"

else

    svn export --force $repository $target_directory

fi

# get the current revision and write to .revision file
echo `svn info $repository | grep ^Revision | sed 's/Revision: *//'` > $target_directory/.revision