# ⚡ Quick Start - Eronas Aplikasi Reservasi Restoran

Panduan cepat untuk mulai mengembangkan dan menjalankan aplikasi.

## 🚀 Mulai dalam 5 Menit

### 1. Prerequisites
```bash
# Install Docker
# Download dari: https://www.docker.com/products/docker-desktop

# Verify installation
docker --version
docker-compose --version
```

### 2. Clone / Navigate to Project
```bash
cd /path/to/AplikasiReservasiRestoran
```

### 3. Start Everything
```bash
# Build & run all services
docker-compose up -d --build

# Wait ~30 seconds for services to be ready
# Check logs
docker-compose logs -f
```

### 4. Access Application
- **Frontend**: http://localhost:8000
- **Backend API**: http://localhost:8080

Done! 🎉

## 📍 Port Mapping

| Service  | Internal | External | URL                    |
|----------|----------|----------|------------------------|
| Frontend | 8000     | 8000     | http://localhost:8000  |
| Backend  | 8000     | 8080     | http://localhost:8080  |

## 🛠️ Common Tasks

### View Logs
```bash
# All services
docker-compose logs -f

# Specific service
docker-compose logs -f frontend
docker-compose logs -f backend
```

### Stop Application
```bash
docker-compose down
```

### Restart
```bash
docker-compose restart
```

### Run Backend Commands
```bash
# Run migrations
docker-compose exec backend php artisan migrate

# Run seeders
docker-compose exec backend php artisan db:seed

# Tinker shell
docker-compose exec backend php artisan tinker
```

### Make Frontend Changes
```bash
# Edit CSS/JS files in frontend/src/
# Changes will auto-reflect

# Rebuild Tailwind (if needed)
docker-compose exec frontend npm run build:css
```

### SSH into Container
```bash
# Frontend
docker-compose exec frontend sh

# Backend
docker-compose exec backend sh
```

## 🐛 Troubleshooting

### Ports in use?
```bash
# Change ports in docker-compose.yml
# Then rebuild
docker-compose down
docker-compose up -d --build
```

### Container won't start?
```bash
# Check logs
docker-compose logs backend
docker-compose logs frontend

# Rebuild
docker-compose down -v
docker-compose up -d --build
```

### Reset everything
```bash
# Remove containers, volumes, networks
docker-compose down -v

# Start fresh
docker-compose up -d --build
```

## 📚 More Information

- **Full Docker Guide**: See `DOCKER_SETUP.md`
- **Frontend Setup**: See `frontend/README.md`
- **Backend Setup**: See `backend/README.md`

## ✅ Next Steps

1. **Database Setup**
   ```bash
   docker-compose exec backend php artisan migrate
   docker-compose exec backend php artisan db:seed
   ```

2. **Test Connection**
   - Open http://localhost:8000 (Frontend)
   - Open http://localhost:8080 (API)

3. **Start Development**
   - Edit frontend files in `frontend/src/`
   - Edit backend files in `backend/app/`
   - Changes auto-reflect in running containers

4. **View API Endpoints** (if available)
   - API Documentation: http://localhost:8080/api/documentation

---

**Questions?** Check DOCKER_SETUP.md for detailed information.

Happy coding! 🚀
