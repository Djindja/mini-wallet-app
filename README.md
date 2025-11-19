# Mini Wallet App

A simple wallet application for transferring money between users.

## Requirements

- PHP 8.1+
- Composer
- Node.js 22.12+
- MySQL
- Pusher account

## Setup Instructions

### 1. Install dependencies
```bash
composer install
npm install
```

### 2. Configure environment
```bash
cp .env.example .env
php artisan key:generate
```

Update `.env` with your database and Pusher credentials:
```env
DB_DATABASE=mini_wallet
DB_USERNAME=your_username
DB_PASSWORD=your_password

BROADCAST_DRIVER=pusher
PUSHER_APP_ID=your_app_id
PUSHER_APP_KEY=your_app_key
PUSHER_APP_SECRET=your_app_secret
PUSHER_APP_CLUSTER=your_cluster
```

### 3. Setup database
```bash
php artisan migrate
php artisan db:seed
```

### 4. Run the application

Start Laravel server:
```bash
php artisan serve
```

Start Vite (in new terminal):
```bash
npm run dev
```

Visit: `http://localhost:8000/transfer`

## Features

- Send money between users
- 1.5% commission fee
- Transaction history
- Real-time updates via Pusher