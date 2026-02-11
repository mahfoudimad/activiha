#!/bin/bash

# Check if PHP is installed
if ! command -v php &> /dev/null; then
    echo "Error: PHP is not installed."
    echo "Please install PHP to run this server locally."
    echo "On Mac: brew install php"
    exit 1
fi

echo "Starting local server at http://localhost:8000"
echo "Press Ctrl+C to stop."
php -S localhost:8000 router.php
