# E-Lab
Web Based Programming Project Repository | E-Lab


## Description
E-Lab adalah aplikasi manajemen peminjaman barang di laboratorium berbasis web. Peminjaman barang dapat dilakukan oleh pengguna dengan mudah melalui antarmuka web yang responsif dan mudah digunakan.

## Features
- Peminjaman barang
- Inventaris barang lab
- Laporan peminjaman
- Pengelolaan status barang (rusak, baik, dll.)
- Login dengan SSO Mahasiswa.

## Tech Stack
- PHP 8
- MySQL

## Installation
1. Clone the repository:
   ```bash
   git clone https://github.com/E-Lab.git
   ```
2. Run
   ```bash
   cd E-Lab
   php -S localhost:8000 -t public/
   ```

## Configuration
### Setup environment variables
   ```env
   DB_HOST=
   DB_USER=
   DB_PASS=
   DB_NAME=
   ```

## License
This project is licensed under the MIT License.
