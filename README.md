# Client / Contact Management Application

A simple PHP MVC application to manage clients and their linked contacts.

## Features

- Add, edit, and delete clients
- Auto-generate unique client codes
- Link/unlink contacts to a client
- Add, edit, and delete contacts
- Link/unlink clients to a contact
- Clean interface with tabs
- Lightweight custom MVC architecture

## Requirements

- PHP >= 8.3
- MySQL or MariaDB
- Apache with mod_rewrite enabled

## Clone the project
```bash
git clone https://github.com/1Cansa/webapp.git
cd webapp
```

## Installation
- install dependencies
```bash
composer install
```
- generate the composer autoload file
```bash
composer dump-autoload
```
- start web server
```bash
php -S localhost:8080 -t public  
```