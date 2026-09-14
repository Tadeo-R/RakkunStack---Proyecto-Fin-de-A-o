#!/bin/bash

# Validar ejecución del script con Bash y usando './'
if [ -z "$BASH_VERSION" ]; then
    echo "Error: Este script debe ser ejecutado exclusivamente con Bash." >&2
    exit 1
fi

if [[ "$0" != "./"* ]]; then
    echo "Error: Este script solo se puede ejecutar usando './'" >&2
    exit 1
fi

# Validar ejecución como sudo  
if [ "$EUID" -ne 0 ]; then
    echo "Error: Este script requiere privilegios de administrador (sudo)." >&2
    exit 1
fi


echo "Creando directorios de respaldo..."

# Crear subdirectorios de cada módulo para backup diario (Módulos 1 a 4)
mkdir -p /var/backup/daily_backup/{MariaDB,Infraestructura,www,Scripts}

# Crear subdirectorios de cada módulo para backup externo (Módulos 1 a 5)
mkdir -p /var/backup/external_backup/{MariaDB,Infraestructura,www,Scripts,Otros}

# Crear subdirectorioS  de cada módulo para backup de la nube (Módulos 1 a 4)
mkdir -p /var/backup/cloud_backup/{MariaDB,Infraestructura,www,Scripts}


# Establecer permisos de lectura, escritura y ejecución para grupo backup en la carpeta de backups
chown -R root:backup /var/backup/
chmod -R 770 /var/backup


# ================= Crear respaldo completo para todos los backups ==================

# Módulo 1: Base de Datos MariaDB
echo "[1/4] Realizando respaldo de MariaDB"
mysqldump -u uits -pUsuarioITS --single-transaction --quick --all-databases > /var/backup/daily_backup/MariaDB/comppettiBD.sql

# Empaquetar respaldo de la BD indicando con -c que se posicione en la carpeta especifica antes de ejecutar la acción
tar -czf /var/backup/daily_backup/MariaDB/comppettiBD.tar.gz -C /var/backup/daily_backup/MariaDB comppettiBD.sql

# Copiar el empaquetado en las carpetas de backup externo y de la nube
cp /var/backup/daily_backup/MariaDB/comppettiBD.tar.gz /var/backup/external_backup/MariaDB/comppettiBD.tar.gz
cp /var/backup/daily_backup/MariaDB/comppettiBD.tar.gz /var/backup/cloud_backup/MariaDB/comppettiBD.tar.gz

# Borrar archivo .sql después de empaquetar
rm /var/backup/daily_backup/MariaDB/comppettiBD.sql

# ==============

# Módulo 2: Infraestructura y Configuraciones del Sistema
echo "[2/4] Realizando respaldo de archivos de configuración"

# Copia la carpeta etc a la carpeta de backup diario
cp -r /etc /var/backup/daily_backup/Infraestructura

# Empaquetar respaldo de la infraestructura indicando con -c que se posicione en la carpeta especifica antes de ejecutar la acción
tar -czf /var/backup/daily_backup/Infraestructura/infraestructura.tar.gz -C /var/backup/daily_backup/Infraestructura etc

# Borrar archivo sin descomprimir después de empaquetar
rm -r /var/backup/daily_backup/Infraestructura/etc

# Copiar el empaquetado en las carpetas de backup externo y de la nube
cp /var/backup/daily_backup/Infraestructura/infraestructura.tar.gz /var/backup/external_backup/Infraestructura/infraestructura.tar.gz
cp /var/backup/daily_backup/Infraestructura/infraestructura.tar.gz /var/backup/cloud_backup/Infraestructura/infraestructura.tar.gz

# ==============

# Módulo 3: Archivos de la aplicación
echo "[3/4] Realizando respaldo de archivos de la aplicación"

# Copia el sistema de archivos de la aplicación web a la carpeta de backup diario
cp -r /var/www/ /var/backup/daily_backup/www/

# Empaquetar respaldo de la aplicación web
tar -czf /var/backup/daily_backup/www/www.tar.gz -C /var/backup/daily_backup/www www

# Borrar carpeta sin descomprimir después de empaquetar
rm -r /var/backup/daily_backup/www/

# Copiar el empaquetado en las carpetas de backup externo y de la nube
cp /var/backup/daily_backup/www/www.tar.gz /var/backup/external_backup/www/www.tar.gz
cp /var/backup/daily_backup/www/www.tar.gz /var/backup/cloud_backup/www/www.tar.gz

# ==============

# Módulo 4: Scripts de respaldo y Cron table
echo "[4/4] Realizando respaldo de scripts de respaldo y Cron table"

# Copia los scripts de respaldo a la carpeta de backup diario
cp -r /var/scripts /var/backup/daily_backup/Scripts

# Copia la tabla de cron a la carpeta de backup diario
crontab -l > /var/backup/daily_backup/Scripts/crontab_backup

# Empaquetar respaldo de los scripts de respaldo y cron table 
tar -czf /var/backup/daily_backup/Scripts/scripts.tar.gz -C /var/backup/daily_backup/Scripts scripts crontab_backup

# Borrar carpeta y archivo crontab sin descomprimir después de empaquetar
rm -r /var/backup/daily_backup/Scripts/scripts
rm /var/backup/daily_backup/Scripts/crontab_backup

# Copiar el empaquetado en las carpetas de backup externo y de la nube
cp /var/backup/daily_backup/Scripts/scripts.tar.gz /var/backup/external_backup/Scripts/scripts.tar.gz
cp /var/backup/daily_backup/Scripts/scripts.tar.gz /var/backup/cloud_backup/Scripts/scripts.tar.gz

# ==============

# Módulo 5: otros (Solo backup externo)
echo "[5/5] Realizando respaldo de otros archivos (Solo backup externo)"

# Copiar /home en backup externo
cp -r /home /var/backup/external_backup/Otros

# Copiar carpeta /opt en backup externo
cp -r /opt /var/backup/external_backup/Otros

# Empaquetar respaldo de otros archivos (Solo backup externo)
tar -czf /var/backup/external_backup/Otros/otros.tar.gz -C /var/backup/external_backup/Otros home opt

# Eliminar las carpetas sin comprimir después de empaquetar
rm -r /var/backup/external_backup/Otros/home
rm -r /var/backup/external_backup/Otros/opt

# ==============

# Asignar propiedad a root y grupo backup
chown -R root:backup /var/backup/

# Asignar permisos 770 para carpetas (permitiendo entrar y navegar)
chmod -R 770 /var/backup/

# Asignar permisos 660 a los archivos .tar.gz generados
chmod 660 /var/backup/daily_backup/*/*.tar.gz
chmod 660 /var/backup/external_backup/*/*.tar.gz
chmod 660 /var/backup/cloud_backup/*/*.tar.gz

echo "Respaldo completo finalizado. Archivos empaquetados en sus respectivas ubicaciones, listos para ser tranferidos"
echo " "
echo "Configurando reglas Sudoers para el grupo 'backup'..."

# Crear archivo de regla sudoers específico para el grupo backup
# Permite ejecutar los scripts de respaldo (diario, externo y nube) sin solicitar contraseña
cat << 'EOF' > /etc/sudoers.d/backup
%backup ALL=(ALL) NOPASSWD: /var/scripts/daily_backup.sh
%backup ALL=(ALL) NOPASSWD: /var/scripts/external_backup.sh
%backup ALL=(ALL) NOPASSWD: /var/scripts/cloud_backup.sh
EOF

# Aplicar permisos estrictos de lectura (requeridos por sudoers: root r-- r-- ---)
chmod 440 /etc/sudoers.d/backup

# Validar la sintaxis de la regla sudoers para evitar bloquear el sistema sudo
if visudo -c -f /etc/sudoers.d/backup > /dev/null 2>&1; then
    echo "Regla de sudoers configurada y validada correctamente."
    exit 0
else
    echo "Error: La regla de sudoers contiene un error de sintaxis." >&2
    rm -f /etc/sudoers.d/backup
    exit 1
fi