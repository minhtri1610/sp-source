### Rewrite by javac team
### Add config to composer.json
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

### Add config to requied 
"javac/sendportal-core": "*"

### Build
composer install

### Publish Assets
php artisan vendor:publish --provider=Sendportal\\Base\\SendportalBaseServiceProvider

### Publish Assets with force
php artisan vendor:publish --provider=Sendportal\\Base\\SendportalBaseServiceProvider --force

### Set up account admin
php artisan sp:install


### Note migrate
```php
php artisan make:migration drop_columns_from_sendportal_subscribers_table --table=sendportal_subscribers

php artisan make:migration add_columns_from_sendportal_tags_table --table=sendportal_tags

php artisan make:migration create_course_of_subscribers_table

php artisan make:migration change_column_name_in_sendportal_subscribers --table=sendportal_subscribers

php artisan make:migration add_columns_from_sendportal_subscribers_table --table=sendportal_subscribers

php artisan make:migration add_columns_from_course_of_subscribers_table --table=course_of_subscribers

php artisan make:migration create_info_of_corporates_table

php artisan make:migration create_setting_of_account_table
```