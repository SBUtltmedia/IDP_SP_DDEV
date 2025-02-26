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

# RUN adduser --disabled-password --gecos "" --uid 501 ddevuser \
#     && echo "ddevuser ALL=(ALL) NOPASSWD: ALL" >> /etc/sudoers
# USER root

RUN adduser --disabled-password --gecos "" --uid 501 ddevuser \
    && echo "ddevuser ALL=(ALL) NOPASSWD: ALL" >> /etc/sudoers

# RUN sudo su
    

RUN <<'EOF'
cat > /etc/apt/sources.list.d/garr.sources <<'EOL'
Types: deb deb-src
URIs: https://debian.mirror.garr.it/debian/
Suites: bookworm bookworm-updates bookworm-backports
Components: main

Types: deb deb-src
URIs: https://debian.mirror.garr.it/debian-security/
Suites: bookworm-security
Components: main
EOL
EOF

RUN apt-get update -y

RUN apt-get install -y libapache2-mod-shib 

# Replace the default shibboleth2.xml with your custom configuration.
# This copies the file "shibboleth2.xml" from your build context into the container.
# COPY shibboleth2.xml /etc/shibboleth/shibboleth2.xml