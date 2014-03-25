#!/bin/bash

# Check we have the comment for the commit.
if [ "$1" == "" -o "$1" == "-h" ]; then
    echo ""
    echo "You have to specify a version for your release."
    echo ""
    echo "./scripts/release 1.0.2"
    echo ""
    exit 1;
fi

git checkout master
git pull origin master
git tag -a "$1"
git push --tags

echo ""
echo "Released version $1 successfully."
