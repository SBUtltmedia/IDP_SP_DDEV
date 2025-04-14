#!/bin/bash
set -e

# Generate signing key and certificate if missing
if [ ! -f /etc/shibboleth/sp-signing-key.pem ]; then
  echo "Generating sp-signing-key.pem..."
  sudo openssl genrsa -out /etc/shibboleth/sp-signing-key.pem 2048
fi

if [ ! -f /etc/shibboleth/sp-signing-cert.pem ]; then
  echo "Generating sp-signing-cert.pem..."
  sudo openssl req -new -x509 -days 3650 -key /etc/shibboleth/sp-signing-key.pem -out /etc/shibboleth/sp-signing-cert.pem -subj "/CN=localhost"
fi

# Generate encryption key and certificate if missing
if [ ! -f /etc/shibboleth/sp-encrypt-key.pem ]; then
  echo "Generating sp-encrypt-key.pem..."
  sudo openssl genrsa -out /etc/shibboleth/sp-encrypt-key.pem 2048
fi

if [ ! -f /etc/shibboleth/sp-encrypt-cert.pem ]; then
  echo "Generating sp-encrypt-cert.pem..."
  sudo openssl req -new -x509 -days 3650 -key /etc/shibboleth/sp-encrypt-key.pem -out /etc/shibboleth/sp-encrypt-cert.pem -subj "/CN=localhost"
fi

# Uncomment if dynamic metadata fetching is needed
# if [ ! -f /var/cache/shibboleth/idp-metadata.xml ]; then
#   echo "Fetching metadata from ddev-mocksaml..."
#   curl http://ddev-mocksaml:4000/api/saml/metadata -o /var/cache/shibboleth/idp-metadata.xml
# fi

exec "$@" 