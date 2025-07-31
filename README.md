[![Docker](https://img.shields.io/badge/Docker-Container-blue?logo=docker)](https://www.docker.com)
[![PHP](https://img.shields.io/badge/PHP-8.x-blueviolet?logo=php)](https://www.php.net)
[![Laravel](https://img.shields.io/badge/Laravel-Framework-red?logo=laravel)](https://laravel.com)
[![MySQL](https://img.shields.io/badge/MySQL-Database-blue?logo=mysql)](https://www.mysql.com)
[![Redis](https://img.shields.io/badge/Redis-Cache%20Store-darkred?logo=redis)](https://redis.io)
[![PHPUnit](https://img.shields.io/badge/PHPUnit-Testing-6c78af?logo=php)](https://phpunit.de)
[![Makefile](https://img.shields.io/badge/Makefile-Build-lightgrey?logo=gnu)](https://www.gnu.org/software/make/)
[![Postman](https://img.shields.io/badge/Postman-Collection-orange?logo=postman)](postman/your-api-name.postman_collection.json)

# 🧑‍💻 **Laravel Showcase API**

This project is an evolving API with robust features, containerized with Docker for easy deployment, and equipped with various tools to make your development experience smooth.

---

## 📋 **Project Overview**

This API provides a set of endpoints designed for **company registration**, **state capacity management**, and **registered agent management**. Built with **Laravel**, the application integrates seamlessly with **MySQL** and **Redis**, ensuring high performance and scalability.

---

## 🛠️ **Technologies Used**
- 🧱 **Laravel** - PHP framework for robust backend
- 🗄️ **MySQL** - Relational database management
- 🚀 **Redis** - Caching for high-performance services
- 🐳 **Docker** - Containerized dev and prod environments
- 🎭 **FakerPHP/Faker** - For seeding realistic data during development
- 🔧 **Makefile** - Automates and simplifies development commands

---

## 🌟 **Features & Highlights**
- 🔥 **API Endpoints**:
    - `POST /companies/register` - Register a new company
    - `POST /companies/{company_id}/registered-agent` - Assign a registered agent to an existing company
    - `GET /states/{iso_code}/registered-agent/capacity` - Retrieve capacity for given state (includes list of related agents)


- 📬 **Postman Collection**: Downloadable and ready to use, with dynamic variable management and sample data available in `/postman/Laravel Showcase.postman_collection.json`.


- 📢 **Real-time Notifications**:
    - State capacity threshold notifications
    - Registered agent notifications when assigned




- 🌱 **Dynamic Seeder**:
    - Auto-run when `make up-dev` or `make up-prod` is used
    - Preloaded data for review (production should disable this)

- 🧱 **Container Separation**:
    - MySQL (db)
    - Laravel API (api)
    - Redis
    - NGINX Proxy (nginx)
    - Worker for Queue handling (queue-worker)

---

## ⚙️ **Setup & Installation**

### 📦 1. **Clone the Repository**
```bash
git clone https://github.com/abnermoralesr/laravel-showcase.git
cd laravel-showcase
```

### 📝 2. **Set Up Environment Variables**
```bash
cp .env.example .env
```

### 🔑 3. **Generate APP_KEY**
```bash
php artisan key:generate
```

### 🔐  4. **Set EMAIL Credentials**
```
MAIL_USERNAME=<your_gmail>
MAIL_PASSWORD=<your_app_password>
```
> Make sure to update the rest of the mail .env variables in case you dont use gmail.

### 🐳 5. **Run Docker Compose**
For development:
```bash
make build-dev
make up-dev
```
For production:
```bash
make build-prod
make up-prod
```

### 📜 6. Logs
Use this to inspect container health and processes:
```bash
make log-dev
# or
make log-prod
```

### 🐳 7. **Shutdown Docker**
```bash
make down-dev
# or
make down-dev
```

---

## 🧾 **Environment Variables (.env)**

- `QUEUE_CONNECTION=redis`
- `DB_CONNECTION=mysql`
- `DB_HOST=db`
- `DB_PORT=3306`
- `DB_DATABASE=laravel`
- `DB_USERNAME=abner`
- `DB_PASSWORD=test`
- `REDIS_HOST=redis`
- `REDIS_PASSWORD=null`
- `REDIS_PORT=6379`
- `MAIL_DRIVER=smtp`
- `MAIL_HOST=smtp.gmail.com`
- `MAIL_PORT=587`
- `MAIL_USERNAME=<your_gmail>`
- `MAIL_PASSWORD=<your_app_password>`
- `MAIL_ENCRYPTION=tls`
- `MAIL_FROM_ADDRESS=hello@example.com`

---

## 🚀 **Access the API**
Once up, visit:
```
http://localhost:80
```

---

## 🧠 **Commit Message Convention**
- `feat:` ➤ New feature
- `test:` ➤ Tests added or updated
- `chore:` ➤ Tooling or config changes
- `refactor:` ➤ Code restructuring

---

## 🧪 **Testing**
```bash
make test
```

- Dummy state `DM` is used for repeatable tests.
- Services & events also tested.
- Self-contained tests ensure consistency.
- Tests are only available in dev mode

---

## 📞 **Contact**
For feedback, questions, or collaborations:
📧 **developer@abnermoralesr.com**

---

### 💬 **Let's build awesome things together!** 🚀
