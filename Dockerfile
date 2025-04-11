FROM ddev/ddev-webserver:v1.24.4

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

RUN echo exit 0 > /usr/sbin/policy-rc.d

RUN apt-get update -y && apt-get install -y libapache2-mod-shib ca-certificates vim openssl

# Copy the configuration files into the container
COPY shibboleth2.xml /etc/shibboleth/shibboleth2.xml
COPY partner-metadata.xml /etc/shibboleth/partner-metadata.xml

RUN chown -R ddevuser:ddevuser /etc/shibboleth

# COPY entrypoint script and make it executable
COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["apachectl", "-D", "FOREGROUND"] 