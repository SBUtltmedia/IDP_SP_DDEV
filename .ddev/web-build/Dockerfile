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

RUN apt-get update -y && apt-get install -y libapache2-mod-shib ca-certificates vim openssl

# Copy the configuration files into the container
COPY shibboleth2.xml /etc/shibboleth/shibboleth2.xml
COPY partner-metadata.xml /etc/shibboleth/partner-metadata.xml

RUN chown -R ddevuser:ddevuser /etc/shibboleth

# RUN curl http://ddev-mocksaml:4000/api/saml/metadata -o /var/cache/shibboleth/idp-metadata.xml

# Copy entrypoint script and make it executable
COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh


ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["apachectl", "-D", "FOREGROUND"]
