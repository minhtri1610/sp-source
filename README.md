### rewrite by javac team
### add config to composer.json
"repositories": [
    {
            "type": "path",
            "url": "packages/javac/sendportal-core",
            "options": {
                "symlink": true
            }
    
    }],

### add config to requied 
"javac/sendportal-core": "*"

### build
composer install

### Publish Assets
php artisan vendor:publish --provider=Sendportal\\Base\\SendportalBaseServiceProvider

### Publish Assets with force
php artisan vendor:publish --provider=Sendportal\\Base\\SendportalBaseServiceProvider --force

### set up account admin
php artisan sp:install

