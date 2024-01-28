```
RewriteEngine On
RewriteBase /

RewriteCond %{DOCUMENT_ROOT}/lightuna/public/%{REQUEST_URI} -f
RewriteRule (.*) /lightuna/public/$1 [L]

RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^(.*)$ /lightuna/public/index.php [L,NC]
```