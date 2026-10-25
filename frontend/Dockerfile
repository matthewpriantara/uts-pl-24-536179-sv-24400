# ==========================================
# Tahap 1: Builder (Node.js Alpine)
# ==========================================
FROM node:20-alpine AS builder

WORKDIR /app

# Layer Caching dependensi npm
COPY package.json package-lock.json ./
RUN npm ci

# Salin kode sumber frontend
COPY . .

# Argument build untuk URL API (dapat dioverride via docker build arg)
ARG VITE_API_BASE_URL=http://localhost:8000/api
ENV VITE_API_BASE_URL=${VITE_API_BASE_URL}

# Bangun aplikasi Vue.js menjadi aset statis di folder dist/
RUN npm run build

# ==========================================
# Tahap 2: Runner (Nginx Alpine)
# Bebas node_modules, hanya menyajikan berkas dist/
# ==========================================
FROM nginx:1.27-alpine AS runner

# Salin hasil kompilasi statis dari builder
COPY --from=builder /app/dist /usr/share/nginx/html

# Salin konfigurasi Nginx SPA
COPY nginx.conf /etc/nginx/conf.d/default.conf

EXPOSE 80

CMD ["nginx", "-g", "daemon off;"]
