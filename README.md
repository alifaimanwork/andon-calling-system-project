# Andon Calling System

A real-time production monitoring and alerting system built with Laravel, designed to help manufacturing plants monitor production lines, track issues, and improve operational efficiency.

## Features

- **Real-time Monitoring**: Track production lines in real-time with WebSockets
- **OPC Server Integration**: Connect to OPC servers for industrial automation data
- **Production Tracking**: Monitor production orders, work centers, and line status
- **Quality Control**: Track OK/NG parts and rework operations
- **Downtime Management**: Record and analyze production line downtime
- **Multi-plant Support**: Manage multiple plants and regions from a single system
- **User Management**: Role-based access control for different user types
- **Dashboard & Reporting**: Visualize production data with charts and reports

## Technology Stack

- **Backend**: Laravel 8.x
- **Frontend**: Bootstrap 5, Chart.js
- **Real-time**: Laravel WebSockets
- **Database**: MySQL
- **OPC Integration**: Custom OPC DA/AE client

## Requirements

- PHP 8.0 or higher
- Composer
- Node.js & NPM
- MySQL 5.7+ or MariaDB 10.3+
- Redis (for queue and WebSockets)
- OPC Server (optional, for production data)

## Installation

1. Clone the repository:
   ```bash
   git clone [repository-url]
   cd calling-system-main
   ```

2. Install PHP dependencies:
   ```bash
   composer install
   ```

3. Install NPM dependencies:
   ```bash
   npm install
   npm run dev
   ```

4. Copy the environment file:
   ```bash
   cp .env.example .env
   ```

5. Generate application key:
   ```bash
   php artisan key:generate
   ```

6. Configure your database and other settings in the `.env` file:
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=your_database
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   
   BROADCAST_DRIVER=redis
   QUEUE_CONNECTION=redis
   
   # OPC Server Configuration
   OPC_SERVER_HOST=127.0.0.1
   OPC_SERVER_PROGID=Your.OPC.Server
   ```

7. Run database migrations and seed initial data:
   ```bash
   php artisan migrate --seed
   ```

8. Start the queue worker and WebSocket server:
   ```bash
   php artisan queue:work
   php artisan websockets:serve
   ```

9. Start the development server:
   ```bash
   php artisan serve
   ```

## Usage

1. Access the application at `http://localhost:8000`
2. Log in with the default admin credentials (if seeded):
   - Email: admin@example.com
   - Password: password
3. Configure your plants, production lines, and OPC server connections
4. Start monitoring your production lines

## Batch Files

The project includes two batch files for easier development:

- `run_websocket.bat`: Starts the WebSocket server
- `run_opcadapter.bat`: Starts the OPC adapter service

## Nginx Configuration

A sample Nginx configuration file is provided as `sample_nginx.conf` for production deployment.

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
