# File Importer (CSV → MySQL)

A lightweight PHP CLI application that imports large CSV files into a MySQL database using a modular and extensible architecture.

It follows a clean pipeline design and can be extended with queue-based processing (Redis) for scalability.

---

## Features

- CSV file parsing
- Data mapping layer
- Type inference
- Validation system
- PDO-based MySQL writer
- Import reporting (processed / imported / errors)
- Docker support
- CLI execution

---

##  Architecture

The system is built as a pipeline:

---

##  Requirements

- PHP 8.3+
- MySQL 8+
- Composer
- Docker & Docker Compose (recommended)

---

## Run the Project

### Start Docker

```bash
docker compose up -d --build
```
```bash
composer install
```
## 🗄️ Database Setup

Before running the project, make sure your MySQL database exists.

##  Example Usage

If your CSV file is named `feed.csv`, run:

```bash
docker exec -it file-importer-app php bin/import-products.php feed.csv
```


