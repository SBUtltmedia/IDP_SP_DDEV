<?php
// Display all Shibboleth attributes
echo "<h1>Shibboleth Protected Page</h1>";
echo "<h2>User Attributes:</h2>";
echo "<pre>";
foreach ($_SERVER as $key => $value) {
    if (strpos($key, 'Shib-') === 0) {
        echo htmlspecialchars("$key: $value\n");
    }
}
echo "</pre>";
?> 