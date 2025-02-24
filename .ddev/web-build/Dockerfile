# FROM ddev/ddev-webserver:latest

# # Add the Shibboleth repository
# RUN apt-get update && apt-get install -y curl gnupg2
# RUN curl -sSL https://pkg.switch.ch/switchaai/debian/switchaai.gpg | apt-key add -
# RUN echo "deb http://pkg.switch.ch/switchaai/debian/ bookworm main" > /etc/apt/sources.list.d/shibboleth.list

# # Install the Shibboleth Apache module
# RUN apt-get update && apt-get install -y libapache2-mod-shib2
# RUN a2enmod shib2