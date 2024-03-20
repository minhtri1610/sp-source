### rewrite by javac team
### add config to composer.json
```javascript
"repositories": [
    {
            "type": "path",
            "url": "packages/javac/sendportal-core",
            "options": {
                "symlink": true
            }
    
    }],
```

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


### note migrate

php artisan make:migration drop_columns_from_sendportal_subscribers_table --table=sendportal_subscribers
php artisan make:migration add_columns_from_sendportal_tags_table --table=sendportal_tags
php artisan make:migration create_course_of_subscribers_table
php artisan make:migration change_column_name_in_sendportal_subscribers --table=sendportal_subscribers
php artisan make:migration add_columns_from_sendportal_subscribers_table --table=sendportal_subscribers
php artisan make:migration add_columns_from_course_of_subscribers_table --table=course_of_subscribers
php artisan make:migration create_info_of_corporates_table
php artisan make:migration create_setting_of_account_table