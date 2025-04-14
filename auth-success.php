<?php
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Authentication Successful</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .success-message {
            color: #28a745;
            font-size: 24px;
            margin-bottom: 20px;
        }
        .attributes {
            margin: 20px 0;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 4px;
        }
        .attribute {
            margin: 10px 0;
            padding: 8px;
            border-bottom: 1px solid #dee2e6;
        }
        .back-link {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.3s;
        }
        .back-link:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="success-message">✅ Authentication Successful!</div>
        
        <?php if (isset($_SERVER['Shib-Session-ID'])): ?>
            <p>You have been successfully authenticated via Shibboleth.</p>
            
            <div class="attributes">
                <h3>Shibboleth Attributes:</h3>
                <?php
                foreach ($_SERVER as $key => $value) {
                    if (strpos($key, 'Shib-') === 0) {
                        echo "<div class='attribute'><strong>$key:</strong> $value</div>";
                    }
                }
                ?>
            </div>
        <?php else: ?>
            <p>No Shibboleth session detected. Please try logging in again.</p>
        <?php endif; ?>

        <a href="/" class="back-link">← Return to Home Page</a>
    </div>
</body>
</html> 