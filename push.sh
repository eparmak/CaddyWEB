#!/bin/bash

# Read the current version
CURRENT_VERSION=$(cat version)

# Split the version into major and minor components
IFS='.' read -r MAJOR MINOR <<< "$CURRENT_VERSION"

if [ -z "$MAJOR" ]; then
  MAJOR=0
fi
echo "$MINOR"
# Increment the minor version
MINOR=$((MINOR + 1))

# Construct the new version string
NEW_VERSION="$MAJOR.$MINOR"
# Write the new version back to the file
echo "$NEW_VERSION" > version
echo "Pushing new version -> $NEW_VERSION"
git push origin master
