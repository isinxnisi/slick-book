# Refresh
php artisan migrate:fresh
php artisan db:seed
php artisan db:seed --class=HierarchySeeder
php artisan db:seed --class=SiteSeeder
php artisan db:seed --class=CategorySeeder