# FROM ddev/ddev-webserver:latest

# # Add the Shibboleth repository
# RUN apt-get update && apt-get install -y curl gnupg2
# RUN curl -sSL https://pkg.switch.ch/switchaai/debian/switchaai.gpg | apt-key add -
# RUN echo "deb http://pkg.switch.ch/switchaai/debian/ bookworm main" > /etc/apt/sources.list.d/shibboleth.list

# # Install the Shibboleth Apache module
# RUN apt-get update && apt-get install -y libapache2-mod-shib2
# RUN a2enmod shib2
# Start from the default DDEV web image
# Start from the default DDEV web image
FROM ddev/ddev-webserver:latest

# Add the Garr mirror APT sources
RUN bash -c 'cat > /etc/apt/sources.list.d/garr.sources <<EOF
Types: deb deb-src
URIs: https://debian.mirror.garr.it/debian/
Suites: bookworm bookworm-updates bookworm-backports
Components: main

Types: deb deb-src
URIs: https://debian.mirror.garr.it/debian-security/
Suites: bookworm-security
Components: main
EOF'

# Update the package list (optional, but recommended)
RUN apt-get update -y