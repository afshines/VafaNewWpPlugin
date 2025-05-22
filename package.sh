#!/bin/bash

# Package Vafa Chat WordPress Plugin
echo "Packaging Vafa Chat WordPress Plugin..."

# Set variables
PLUGIN_DIR=$(pwd)
BUILD_DIR="${PLUGIN_DIR}/build"
ZIP_NAME="vafa-chat-widget.zip"

# Create build directory if it doesn't exist
mkdir -p "${BUILD_DIR}"

# Create zip file
echo "Creating zip file..."
cd "${PLUGIN_DIR}"
zip -r "${BUILD_DIR}/${ZIP_NAME}" . -x "*.git*" "build/*" "*.sh"

echo "Plugin packaged successfully: ${BUILD_DIR}/${ZIP_NAME}"
