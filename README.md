<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

<!-- Project Details -->
1. Pattern Used: Service Pattern


2. Made use of API resource


3. Create Form Request for validation


4. Made use of laravel resource for returning data


5. Test file for Task is in the Test/Feature folder {TaskServiceTest.php}


6. Policy and Gate was used to prevent unauthorized request (like updating and deleting a task you didn't create)


7. Created an  exception app\Exceptions\UnauthorizedException.php to handle the unauthorized request of updating a task


8. If you clone this project, follow the steps below:

   Before this, if you want to clone this project clone it directly in your laragon/www folder, that is if you are using laragon. The steps are here https://medium.com/@chimaeze223/creating-your-own-local-domain-using-laragon-laravel-d08d692e8c2c  So, no need of you running php artisan serve.
   
   PS: I'm the author, kindly check it out 🙏.
   
   i => git clone https://github.com/lanna-code00/task_manager.git


   ii => Navigate to the directory, run cd task_manager


   iii => Run composer install (this is to install dependencies)


   iv => Create a copy of the .env file, run cp .env.example .env or copy .env.example .env


   v => generate your application key; php artisan key:generate


   vi => Make sure you connect your databse and then run your migrations: php artisan migrate or run it together with the seed "php artisan migrate --seed"


   vii => Serve your project using php artisan serve or use laragon (like I highlighted before)


8. Used phpUnit for test. Please run "php artisan test" for see if the tests passed


9. Used Enum for the task Statues


10. The routes are in the v1 folder, I created this folder and registered it in the bootstrap/app.php file, since laravel 11 no longer make use of RouteServiceProvider 🤦


Additional Feature: I created a class(HtmlSanitize in the Utils folder) that will handle HTML tags incase if the frontend will later need it

Thanks!


Postman collection: https://grey-station-604424.postman.co/workspace/Task-Manager-API~32040a47-8b8b-46e6-a028-a92bce230228/collection/15167821-78481277-e7a0-4a75-a217-2a6a7a3d023b?action=share&creator=15167821&active-environment=15167821-ea054077-4f64-4a81-8288-5fda63d00d10
