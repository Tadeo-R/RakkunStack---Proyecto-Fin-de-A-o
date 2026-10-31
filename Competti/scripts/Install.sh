#!/bin/bash

# Validar ejecución como con ./
if [[ "$0" != "./"* ]]; then
    echo "Error: Este script solo se puede ejecutar usando './'" >&2
    exit 1
fi

# Validar ejecución como root (necesario para crear usuarios e instalar apache)
if [ "$EUID" -ne 0 ]; then
    echo "Error: Este script requiere privilegios de administrador (sudo)." >&2
    exit 1
fi

# Crear grupos del sistema
echo "Creando grupos de administración..."
groupadd -f sysadmin
groupadd -f backup
groupadd -f dbadmin
groupadd -f security
groupadd -f webdev

# 3. Crear usuarios especificos iniciales
echo "Creando usuarios con contraseñas personalizadas..."

# Lista de usuarios con contraseñas (formato usuario:contraseña)
lista_usuarios=(
    "tadeo:T4d3o#2026"
    "benja:B3nj4!987"
    "mati:itzmati#9342"
    "alan:Al4n¿DbAdm"
    "gahel:G4h3l?dev"
)


# Crear usuarios y asignar contraseñas
for elemento in "${lista_usuarios[@]}"; do
    
    # Separar el nombre de usuario y la contraseña usando ':' como delimitador
    usuario="${elemento%%:*}"
    password="${elemento#*:}"
    
    if ! id "$usuario" &>/dev/null; then
        useradd -m "$usuario"
        
        # Asignar la contraseña específica de forma no interactiva
        echo "$usuario:$password" | chpasswd
        echo "Usuario '$usuario' creado y configurado con su contraseña única."
    else
        echo "El usuario '$usuario' ya existe. Omitiendo creación."
    fi
done

# Asignar grupos a usuarios
echo "Asignando grupos a cada usuario..."

# Tadeo: sysadmin, backup, security
usermod -aG sysadmin,backup,security tadeo
# Benjamin: sysadmin, webdev
usermod -aG sysadmin,webdev benja
# Matías: sysadmin, backup, webdev
usermod -aG sysadmin,backup,webdev mati
# Alan: dbadmin, security
usermod -aG dbadmin,security alan
# Gahel: webdev
usermod -aG webdev gahel


# Crear instancias de trabajo
echo "Creando instancias de trabajo..."

# Instancia de trabajo sysadmin 
mkdir -p /var/sysadmin/Gestor
chown -R uits:sysadmin /var/scripts
chmod -R 770 /var/sysadmin

# 2. Instancia de trabajo para security
mkdir -p /var/security
chown -R uits:security /var/security
chmod -R 770 /var/security

# Directorio de auditoría
mkdir -p /var/log/auditor
chown -R uits:security /var/log/auditor
chmod -R 770 /var/log/auditor

#  Instancia de trabajo para DBAdmin
mkdir -p /var/db-manager
chown -R uits:dbadmin /var/db-manager
chmod -R 770 /var/db-manager

# Instalar y configurar apache HTTPD
echo "Instalando Apache (HTTPD)..."
dnf install httpd -y

# Configurar permisos en la carpeta www
sudo chown -R apache:apache /var/www/html/
sudo chmod -R 755 /var/www/html/


# Habilitar e iniciar el servicio Apache
echo "Iniciando y habilitando el servicio Apache..."
systemctl start httpd
systemctl enable httpd

# Configurar reglas de firewall
echo "Configurando reglas de Firewall..."
firewall-cmd --permanent --add-service=http
firewall-cmd --permanent --add-service=https
firewall-cmd --permanent --add-service=ssh
firewall-cmd --reload

echo "Estado actual del servicio Apache:"
systemctl status httpd --no-pager

# Configuración de SELinux para permitir conexiones de red desde Apache
echo "Configurando SELinux"
setsebool -P httpd_can_network_connect 1

# Instalar IPS fail2ban
dnf install fail2ban -y
systemctl start fail2ban
systemctl enable fail2ban

# Crear el archivo jail.local con las configuraciones esenciales de forma limpia
cat << 'EOF' > /etc/fail2ban/jail.local
[DEFAULT]
bantime = 1h
findtime = 10m
maxretry = 3

[sshd]
enabled = true

[apache-auth]
enabled = true

[apache-badbots]
enabled = true
EOF

echo "Configuración de fail2ban completada. Reiniciando el servicio..."
# Reiniciar fail2ban para aplicar cambios
systemctl restart fail2ban


#Instalar MariaDB
echo "Instalando MariaDB..."
dnf install mariadb-server -y

# Iniciar y habilitar el servicio de MariaDB
echo "Iniciando y habilitando el servicio de MariaDB..."
systemctl start mariadb
systemctl enable mariadb

# Instalar repositorio oficial de Docker y herramientas de gestión
echo "Configurando repositorio e instalando Docker..."
dnf install -y dnf-plugins-core
dnf config-manager --add-repo https://download.docker.com/linux/centos/docker-ce.repo
dnf install -y docker-ce docker-ce-cli containerd.io docker-compose-plugin

# Iniciar y habilitar el servicio Docker
echo "Iniciando y habilitando Docker..."
systemctl start docker
systemctl enable docker

# Añadir a los usuarios administradores (ej. grupo sysadmin o wheel) al grupo docker para que puedan gestionarlo
usermod -aG docker tadeo
usermod -aG docker benja
usermod -aG docker mati

echo "=== Proceso finalizado con éxito ==="