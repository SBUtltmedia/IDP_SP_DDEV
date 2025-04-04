ARG BASE_IMAGE
FROM $BASE_IMAGE

# Add GARR repository for Shibboleth
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

# Install Shibboleth SP and required packages
RUN apt-get update -y && \
    apt-get install -y libapache2-mod-shib ca-certificates vim openssl sudo curl && \
    a2enmod shib && \
    mkdir -p /etc/shibboleth/sp-cert && \
    openssl req -x509 -newkey rsa:2048 -keyout /etc/shibboleth/sp-cert/sp-key.pem -out /etc/shibboleth/sp-cert/sp-cert.pem -nodes -days 365 -subj "/CN=shibbolethtest.ddev.site" && \
    chown -R _shibd:_shibd /etc/shibboleth/sp-cert

# Copy Shibboleth configuration
COPY shibboleth2.xml /etc/shibboleth/shibboleth2.xml
RUN chown _shibd:_shibd /etc/shibboleth/shibboleth2.xml

# Enable required Apache modules
RUN a2enmod ssl headers proxy proxy_http

# Copy and enable proxy configuration
COPY mocksaml-proxy.conf /etc/apache2/conf-enabled/mocksaml-proxy.conf

# Copy and enable secure directory configuration
COPY secure.conf /etc/apache2/conf-available/
RUN ln -s /etc/apache2/conf-available/secure.conf /etc/apache2/conf-enabled/

# Create secure directory and copy test page
RUN mkdir -p /var/www/html/secure
COPY secure/index.html /var/www/html/secure/
RUN chown -R www-data:www-data /var/www/html/secure

# Copy landing page
COPY index.html /var/www/html/
RUN chown www-data:www-data /var/www/html/index.html

# Create startup script
RUN echo '#!/bin/bash\n\
    sudo mkdir -p /run/shibboleth\n\
    sudo chown _shibd:_shibd /run/shibboleth\n\
    sudo service shibd start\n\
    apache2ctl graceful' > /startup.sh && \
    chmod +x /startup.sh

# Set startup script to run on container start
ENV APACHE_POST_FINI_SCRIPT=/startup.sh

# Allow the apache user to run sudo commands without password
RUN echo "www-data ALL=(ALL) NOPASSWD: /usr/sbin/service shibd start, /usr/sbin/service shibd status, /bin/mkdir, /bin/chown" >> /etc/sudoers

# Copy MockSAML configuration
COPY mocksaml-config.json /etc/mocksaml-config.json

# Create startup script for MockSAML
RUN echo '#!/bin/bash\nmkdir -p /run/shibboleth && chown _shibd:_shibd /run/shibboleth\nsudo service shibd start\napache2-foreground' > /start-apache.sh && \
    chmod +x /start-apache.sh

# Set MockSAML configuration as an environment variable
ENV MOCKSAML_CONFIG_FILE=/etc/mocksaml-config.json

CMD ["/start-apache.sh"] 