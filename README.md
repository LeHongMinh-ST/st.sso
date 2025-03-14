
# ST SSO Project Setup Guide

## Prerequisites

- **Docker & Docker Compose** installed
- **Make** installed (for running commands easily)
- **mkcert** installed (for generating SSL certificates)

## Setting Up the Project with Docker

### 1. Clone the Repository
```sh
git clone git@github.com:LeHongMinh-ST/st.sso.git
cd st.sso
```

### 2. Install mkcert and Generate SSL Certificates

#### macOS
```sh
brew install mkcert nss
mkcert -install
```

#### Linux
```sh
sudo apt install libnss3-tools -y  # Debian/Ubuntu
sudo yum install nss-tools -y       # CentOS/RHEL
curl -JLO "https://github.com/FiloSottile/mkcert/releases/latest/download/mkcert-$(uname -s)-$(uname -m)"
chmod +x mkcert-$(uname -s)-$(uname -m)
sudo mv mkcert-$(uname -s)-$(uname -m) /usr/local/bin/mkcert
mkcert -install
```

#### Windows (PowerShell as Administrator)
```powershell
choco install mkcert -y
mkcert -install
```

### 3. Generate SSL Certificates
```sh
# Create the certs directory if it doesn't exist
mkdir -p .docker/local/certs

# Generate SSL certificates for the domain st.sso.dev
mkcert -key-file .docker/local/certs/st.sso.dev-key.pem -cert-file .docker/local/certs/st.sso.dev.pem st.sso.dev localhost 127.0.0.1 ::1
```

### 4. Add Domain to Hosts File
Add the following line to your system's hosts file:

**macOS/Linux:**
```sh
echo "127.0.0.1 st.sso.dev" | sudo tee -a /etc/hosts
```

**Windows (Run PowerShell as Administrator):**
```powershell
Add-Content -Path "C:\Windows\System32\drivers\etc\hosts" -Value "127.0.0.1 st.sso.dev"
```

### 5. Start Docker Containers

#### Mac/Linux (Using Make)
```sh
make up
```

#### Windows (Using Docker Compose Directly)
```powershell
docker-compose up -d --build
```
This will build and start the necessary services.

### 6. Access the Project
- App: [https://st.sso.dev:8882](https://st.sso.dev:8882)

### 7. Stopping the Containers

#### Mac/Linux (Using Make)
```sh
make down
```

#### Windows (Using Docker Compose Directly)
```powershell
docker-compose down

```

## Setting Up the Project basic

### 1. Clone the Repository
```sh
git clone git@github.com:LeHongMinh-ST/st.sso.git
cd st.sso
```
### 2. Copy .env.example to .env
```sh
cp .env.example .env
```
### 3. Run composer install
```sh
composer install
php artisan key:generate
```
### 4. Run npm install
```sh
npm install
```
### 5. Run npm run build
```sh
npm run build
```

### 6. Run migrations
```sh
php artisan migrate
php artisan storage:link
php artisan db:seed
```

### 7. Run dev
```sh
composer run dev  
```

