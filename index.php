<?php
header('Content-Type: text/plain');

echo "Shibboleth Test Page\n\n";

if (isset($_SERVER['Shib-Session-ID'])) {
    echo "Shibboleth Session ID: " . $_SERVER['Shib-Session-ID'] . "\n";
    echo "User authenticated via Shibboleth\n";
    
    // Display all Shibboleth attributes
    echo "\nShibboleth Attributes:\n";
    foreach ($_SERVER as $key => $value) {
        if (strpos($key, 'Shib-') === 0) {
            echo "$key: $value\n";
        }
    }
} else {
    echo "Not authenticated via Shibboleth\n";
    echo "Click <a href='/Shibboleth.sso/Login'>here</a> to login\n";
} 