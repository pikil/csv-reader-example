#!/bin/bash

PUBLIC_FOLDER=/var/www/html
PORT=8089

# Copying the built Vue project into the public folder
rm -r $PUBLIC_FOLDER
mv /app/dist $PUBLIC_FOLDER

echo -e "\n\nThe build is ready!\nNode version: $(node -v)\n$(php -v)\n\n"
echo -e "\nThe app is available on http://localhost:$PORT"

# Starting just http server (all files are built)
apachectl -DFOREGROUND
