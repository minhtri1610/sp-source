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


### migrate

php artisan make:migration drop_columns_from_sendportal_subscribers_table --table=sendportal_subscribers
php artisan make:migration create_course_of_subscribers_table
