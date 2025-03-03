FROM ddev/ddev-webserver:latest


RUN adduser --disabled-password --gecos "" --uid 501 ddevuser \
    && echo "ddevuser ALL=(ALL) NOPASSWD: ALL" >> /etc/sudoers
    

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

# This copies the file "shibboleth2.xml" from your build context into the container.
# COPY shibboleth2.xml /etc/shibboleth/shibboleth2.xml