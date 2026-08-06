# 🍽️ Aplikasi Reservasi Restoran - Eronas

Sistem reservasi restoran modern dengan frontend web dan backend API yang dibangun dengan teknologi terkini.

## 📋 Daftar Isi

- [🏗️ Arsitektur](#-arsitektur)
- [🚀 Quick Start](#-quick-start)
- [📁 Struktur Project](#-struktur-project)
- [🔧 Technologies](#-technologies)
- [📚 Documentation](#-dokumentasi)
- [🐳 Docker](#-docker)
- [💡 Development](#-development)
- [📝 API](#-api)

## 🏗️ Arsitektur

```
┌─────────────────────────────────────────────────────────────┐
│                    USER BROWSER                              │
│              http://localhost:8000                           │
└────────────────────────┬────────────────────────────────────┘
                         │
        ┌────────────────┼────────────────┐
        │                │                │
        ▼                ▼                ▼
   ┌─────────────┐  ┌────────────┐  ┌─────────────┐
   │   Nginx     │  │   PHP-FPM  │  │  Static     │
   │             │  │            │  │  Files      │
   │ Port 8000   │──┤ Port 9000  │──┤             │
   └─────────────┘  └────────────┘  └─────────────┘
   
        FRONTEND SERVICE (eronas-frontend)
        Docker Container | Alpine + Node + PHP
        
        ▼
        
   API Calls via Internal Network
   http://backend:8080
        
        ▼
        
   ┌──────────────────────────────────────┐
   │     BACKEND SERVICE (eronas-backend)  │
   │     http://localhost:8080            │
   │                                      │
   │  ┌──────────────────────────────┐   │
   │  │   PHP 8.3 + Laravel          │   │
   │  │   RESTful API Endpoints      │   │
   │  │                              │   │
   │  │  • Authentication            │   │
   │  │  • User Management           │   │
   │  │  • Reservation Processing    │   │
   │  │  • Table Management          │   │
   │  │  • Menu Management           │   │
   │  └──────────────────────────────┘   │
   │                                      │
   │  ┌──────────────────────────────┐   │
   │  │   Database (SQLite)          │   │
   │  │   Persistent Storage         │   │
   │  └──────────────────────────────┘   │
   └──────────────────────────────────────┘
   
   Docker Container | PHP 8.3 Alpine
```

## 🚀 Quick Start

### Prerequisites
- Docker Desktop installed ([Download](https://www.docker.com/products/docker-desktop))
- Git (untuk clone project)

### 1. Clone Project
```bash
git clone <repository-url>
cd AplikasiReservasiRestoran
```

### 2. Build & Run
```bash
# Start all services
docker-compose up -d --build

# Wait for services to be ready (30 seconds)
docker-compose logs -f
```

### 3. Access Application
| Service  | URL                    | Port |
|----------|------------------------|------|
| Frontend | http://localhost:8000  | 8000 |
| Backend  | http://localhost:8080  | 8080 |

## 📁 Struktur Project

```
AplikasiReservasiRestoran/
│
├── 📄 docker-compose.yml          # Docker orchestration
├── 📄 QUICKSTART.md              # Quick start guide
├── 📄 DOCKER_SETUP.md            # Docker documentation
├── 📄 DOCKER_IMPLEMENTATION.md   # Implementation details
├── 📄 .env.example               # Environment template
│
├── 📁 frontend/                   # Frontend Application
│   ├── 📄 Dockerfile             # Frontend Docker image
│   ├── 📁 docker/                # Docker configurations
│   │   ├── entrypoint.sh        # Container startup
│   │   ├── nginx.conf           # Nginx config
│   │   ├── default.conf         # Site config
│   │   ├── php-fpm.conf         # PHP config
│   │   └── supervisord.conf     # Service manager
│   ├── 📁 src/                   # Source code
│   │   ├── views/               # PHP pages
│   │   ├── layouts/             # Layout templates
│   │   ├── components/          # Reusable components
│   │   ├── styles/              # CSS & Tailwind
│   │   ├── assets/              # Images & media
│   │   ├── config/              # Configuration
│   │   ├── utils/               # Utilities
│   │   └── js/                  # JavaScript modules
│   ├── 📁 public/                # Static files
│   ├── 📄 package.json           # Dependencies
│   └── 📄 README.md              # Frontend docs
│
└── 📁 backend/                    # Backend Application
    ├── 📄 Dockerfile             # Backend Docker image
    ├── 📁 app/                   # Laravel app
    │   ├── Http/                # Controllers & Requests
    │   ├── Models/              # Database models
    │   └── Providers/           # Service providers
    ├── 📁 database/             # Database files
    ├── 📄 composer.json         # PHP dependencies
    └── 📄 README.md             # Backend docs
```

## 🔧 Technologies

### Frontend
- **Server**: Nginx + PHP-FPM
- **Styling**: Tailwind CSS
- **Build Tool**: Node.js + Tailwind CLI
- **Base Image**: Alpine Linux (minimal footprint)

### Backend
- **Framework**: Laravel 11
- **Language**: PHP 8.3
- **Database**: SQLite
- **API Style**: RESTful

### DevOps
- **Containerization**: Docker
- **Orchestration**: Docker Compose
- **Process Manager**: Supervisor (frontend only)

## 📚 Dokumentasi

### 📖 Quick References
- **QUICKSTART.md** - Panduan cepat (5 menit setup)
- **DOCKER_SETUP.md** - Dokumentasi Docker lengkap
- **DOCKER_IMPLEMENTATION.md** - Detail implementasi

### 📖 Component Documentation
- **frontend/README.md** - Frontend documentation
- **frontend/STRUCTURE.md** - Frontend folder structure
- **backend/README.md** - Backend documentation

## 🐳 Docker Commands

### Start Application
```bash
# Build dan start semua services
docker-compose up -d --build

# Start tanpa build (jika sudah ada image)
docker-compose up -d

# Start dengan log output
docker-compose up --build
```

### View Logs
```bash
# Semua services
docker-compose logs -f

# Specific service
docker-compose logs -f frontend
docker-compose logs -f backend

# Last 100 lines
docker-compose logs --tail=100
```

### Stop Services
```bash
# Stop tanpa remove
docker-compose stop

# Stop dan remove containers
docker-compose down

# Stop, remove, dan remove volumes
docker-compose down -v
```

### Execute Commands
```bash
# Frontend
docker-compose exec frontend sh
docker-compose exec frontend npm run build:css

# Backend
docker-compose exec backend sh
docker-compose exec backend php artisan migrate
docker-compose exec backend php artisan tinker
```

### Check Status
```bash
# List running services
docker-compose ps

# Show service stats
docker stats
```

## 💡 Development

### Making Changes

#### Frontend (Live Reload)
```bash
# Edit files in frontend/src/
# Changes instantly reflect in browser

# If modifying CSS:
docker-compose exec frontend npm run build:css
```

#### Backend (Live Reload)
```bash
# Edit files in backend/app/
# Changes instantly available

# If adding migrations:
docker-compose exec backend php artisan make:migration <name>
docker-compose exec backend php artisan migrate
```

### Database Operations
```bash
# Run migrations
docker-compose exec backend php artisan migrate

# Run seeders
docker-compose exec backend php artisan db:seed

# Create new migration
docker-compose exec backend php artisan make:migration <table_name>

# Interactive shell
docker-compose exec backend php artisan tinker
```

### SSH into Containers
```bash
# Frontend container
docker-compose exec frontend sh

# Backend container
docker-compose exec backend sh
```

## 📝 API

### Base URL
```
http://localhost:8080/api
```

### Available Endpoints

#### Authentication
- `POST /api/auth/login` - User login
- `POST /api/auth/register` - User registration
- `POST /api/auth/logout` - User logout

#### Users
- `GET /api/users` - List users
- `POST /api/users` - Create user
- `GET /api/users/:id` - Get user detail
- `PUT /api/users/:id` - Update user
- `DELETE /api/users/:id` - Delete user

#### Reservations
- `GET /api/reservations` - List reservations
- `POST /api/reservations` - Create reservation
- `GET /api/reservations/:id` - Get reservation
- `PUT /api/reservations/:id` - Update reservation
- `DELETE /api/reservations/:id` - Cancel reservation

#### Tables
- `GET /api/tables` - List available tables
- `POST /api/tables` - Create table
- `GET /api/tables/:id` - Get table detail
- `PUT /api/tables/:id` - Update table

#### Menu
- `GET /api/menus` - List menu items
- `POST /api/menus` - Create menu item
- `PUT /api/menus/:id` - Update menu item
- `DELETE /api/menus/:id` - Delete menu item

#### Categories
- `GET /api/categories` - List categories
- `POST /api/categories` - Create category
- `PUT /api/categories/:id` - Update category
- `DELETE /api/categories/:id` - Delete category

## ⚙️ Environment Variables

Lihat `.env.example` untuk daftar lengkap environment variables yang dapat dikonfigurasi.

### Important Variables
```
APP_ENV=production           # Production mode
APP_DEBUG=false             # Disable debug output
API_URL=http://backend:8080 # Backend URL (internal)
DB_CONNECTION=sqlite        # Database type
```

## 🤝 Contributing

1. Fork repository
2. Create feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to branch (`git push origin feature/AmazingFeature`)
5. Open Pull Request

## 📋 Checklist Deployment

- [ ] Docker installed
- [ ] Project cloned
- [ ] Environment variables configured
- [ ] `docker-compose up -d --build` executed
- [ ] Frontend accessible (http://localhost:8000)
- [ ] Backend API accessible (http://localhost:8080)
- [ ] Database migrations run
- [ ] Test authentication working
- [ ] Test API endpoints working

## 🆘 Troubleshooting

### Port Already in Use
```bash
# Change ports in docker-compose.yml
docker-compose down
docker-compose up -d --build
```

### Services Won't Start
```bash
# Check logs
docker-compose logs backend
docker-compose logs frontend

# Rebuild clean
docker-compose down -v
docker-compose up -d --build
```

### Database Not Persisting
```bash
# Check volumes
docker volume ls

# Recreate
docker-compose down -v
docker-compose up -d --build
```

## 📞 Support

Untuk pertanyaan atau issue:
1. Check dokumentasi di `DOCKER_SETUP.md`
2. Check logs: `docker-compose logs -f`
3. Buat issue di repository

## 📄 License

Project ini dilisensikan di bawah MIT License - lihat LICENSE file untuk detail.

## 🙏 Acknowledgments

- Laravel Framework
- Tailwind CSS
- Docker & Docker Compose
- Community contributors

---

## 🚀 Next Steps

1. **Setup Database**
   ```bash
   docker-compose exec backend php artisan migrate
   ```

2. **Test Application**
   - Open http://localhost:8000
   - Try login/register
   - Test API at http://localhost:8080

3. **Start Development**
   - Edit files in frontend/src/
   - Edit files in backend/app/
   - Changes auto-reflect

4. **Deploy**
   - Check DOCKER_SETUP.md for production tips
   - Configure environment variables
   - Run on target server

---

**Version**: 1.0.0  
**Last Updated**: August 6, 2026  
**Status**: ✅ Ready for Development & Deployment

Happy Coding! 🎉
