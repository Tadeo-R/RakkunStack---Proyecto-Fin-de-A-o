#!/bin/bash

# 1. VALIDACIONES DE EJECUCIÓN
if [ -z "$BASH_VERSION" ]; then
    echo "Error: Este script debe ser ejecutado exclusivamente con Bash." >&2
    exit 1
fi

if [[ "$0" != "./"* ]]; then
    echo "Error: Este script solo se puede ejecutar usando './'" >&2
    exit 1
fi

# Validar que se ejecute como root (necesario para crear usuarios e instalar apache)
if [ "$EUID" -ne 0 ]; then
    echo "Error: Este script requiere privilegios de administrador (sudo)." >&2
    exit 1
fi

# 2. GESTIÓN DE GRUPOS
echo "Creando grupos de administración..."
groupadd -f sysadmin
groupadd -f backupadmin
groupadd -f dbadmin
groupadd -f securityadmin
groupadd -f webadmin

# 3. GESTIÓN DE USUARIOS Y CONTRASEÑAS DISTINTAS
echo "Creando usuarios con contraseñas personalizadas..."

# Lista de usuarios con sus respectivas contraseñas (formato usuario:contraseña)
lista_usuarios=(
    "tadeo:T4d3o#2026"
    "benja:B3nj4!987"
    "mati:itzmati#9342"
    "alan:Al4n¿DbAdm"
)

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


# 4. ASIGNACIÓN CORRECTA DE ROLES (Grupos sin espacios intermedios)
echo "Asignando roles y grupos secundarios..."

# Tadeo: sysadmin, backupadmin, securityadmin y wheel (sudo)
usermod -aG sysadmin,backupadmin,securityadmin,wheel tadeo
# Benjamin: sysadmin, webadmin y wheel (sudo)
usermod -aG sysadmin,webadmin,wheel benja
# Matías: backupadmin, webadmin
usermod -aG backupadmin,webadmin mati
# Alan: dbadmin, securityadmin, webadmin
usermod -aG dbadmin,securityadmin,webadmin alan

# 5. INSTALACIÓN Y CONFIGURACIÓN DE APACHE
echo "Actualizando paquetes e instalando Apache HTTPD..."
dnf update -y
dnf install httpd -y

echo "Iniciando y habilitando el servicio Apache..."
systemctl start httpd
systemctl enable httpd

# 6. CONFIGURACIÓN ESTRICA DEL FIREWALL
echo "Configurando reglas de Firewall..."
firewall-cmd --permanent --add-service=http
firewall-cmd --permanent --add-service=https
firewall-cmd --reload

echo "=== Proceso finalizado con éxito ==="
echo "Estado actual del servicio Apache:"
systemctl status httpd --no-pager

# Configuración de permisos de grupos
