# Vital Health Tracker - Deployment Guide

## 🚀 Deploy ke Railway

### 1. Setup Database
Di Railway Dashboard:
- Klik "New" → "Database" → "Add MySQL"
- Copy connection details dari MySQL plugin

### 2. Setup Environment Variables
Di Railway Dashboard → Your Service → Variables, tambahkan:

```bash
CI_ENVIRONMENT=production
DATABASE_HOST=<your-mysql-host>.railway.internal
DATABASE_PORT=3306
DATABASE_NAME=railway
DATABASE_USER=root
DATABASE_PASSWORD=<your-mysql-password>
JWT_SECRET=e8bb75365d5d48e936833842c1ebb778
```

**PENTING**: 
- Gunakan **Private Network URL** untuk DATABASE_HOST (yang berakhiran `.railway.internal`)
- **JANGAN** set variable `PORT` - Railway akan auto-set ini

### 3. Deploy
```bash
git add .
git commit -m "Deploy to Railway"
git push origin main
```

Railway akan otomatis:
- Detect Dockerfile
- Build image dengan PHP 8.2 + Apache
- Install semua dependencies
- Deploy aplikasi

### 4. Cek Deployment
- Tunggu build selesai (±5 menit)
- Akses URL: `https://vital-health-care-production.up.railway.app/`
- Cek logs jika ada error

### 5. Troubleshooting

#### Bad Gateway?
1. **Cek Logs**: Railway Dashboard → Deployments → View Logs
2. **Database Connection**: Pastikan menggunakan Private Network URL (`.railway.internal`)
3. **Environment Variables**: Verifikasi semua env vars sudah di-set
4. **Build Success**: Pastikan build berhasil tanpa error

#### Connection Refused?
- Pastikan DATABASE_HOST menggunakan internal URL
- Cek MySQL plugin sudah running

#### 502 Error?
- Tunggu 1-2 menit setelah deployment (container startup)
- Restart deployment dari Railway dashboard

## 📝 API Endpoints
- Base URL: `https://vital-health-care-production.up.railway.app/`
- Health Check: `/`
- API: `/api/...`

## 🔧 Local Development
```bash
php spark serve
```

Access: `http://localhost:8080`
