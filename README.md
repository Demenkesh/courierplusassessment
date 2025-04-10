# 🛠️ Project Setup & Migration

Follow the steps below to properly set up the database and seed initial data for your Laravel project.

## 1. Clone the Repository

```bash
git clone https://github.com/Demenkesh/courierplusassessment.git
```

```bash
cd your-repo-name
```

## 2. Php verdion

Use the latest php Version 8.3 upper

## 3. Install Dependencies

```bash
composer update
```

## 4. Set Up Environment Configuration

Copy the .env.example file and update the necessary environment variables:

```bash
cp .env.example .env
```

Then generate the application key:

```bash
php artisan key:generate
```

Update your .env file with your database and other environment-specific configurations.

## 5. Run Migrations

```bash
php artisan migrate
```

## 6. Seed the Database

Seed the default user and tenant data:

```bash
php artisan db:seed
```

## 7. View the documentation

Replace {{your-app-url}} with your actual domain or local environment URL (e.g., http://localhost:8000/api/documentation).
To access the api of the project.

## 8. View the documentation

Replace {{ tenant123 }} with your tenant name you create in your env file

L5_SWAGGER_CONST_HOST1="http://${APP_URLs}/tenant123"
