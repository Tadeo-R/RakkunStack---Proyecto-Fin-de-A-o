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

# Validar existencia de carpeta de backup externo
if [ ! -d "/var/backup/cloud_backup" ]; then
    echo "Error: La carpeta /var/backup/cloud_backup no existe. Por favor, revise sus backups antes de ejecutar el script." >&2
    exit 1
fi

# Definir variable de fecha para nombrar los archivos de respaldo
FECHA=$(date +"%Y-%m-%d_%H-%M-%S")

# ====================================================

# Respaldar datos de MariaDB
mysqldump -u uits -pUsuarioITS --single-transaction --quick --all-databases > /var/backup/cloud_backup/MariaDB/comppettiBD.sql

# Empaquetar respaldo de la BD
tar -czf /var/backup/cloud_backup/MariaDB/comppettiBD_${FECHA}.tar.gz -C /var/backup/cloud_backup/MariaDB comppettiBD.sql

# Borrar volcado .sql plano
rm /var/backup/cloud_backup/MariaDB/comppettiBD.sql

# ====================================================

# Módulo 2: Infraestructura
tar -czf /var/backup/cloud_backup/Infraestructura/infraestructura_${FECHA}.tar.gz -C / etc 

# Módulo 3: aplicación Web
tar -czf /var/backup/cloud_backup/www/competti_${FECHA}.tar.gz -C /var/www/html competti 

# Módulo 4: Scripts y Cron table

# Exportar temporalmente crontab 
crontab -l > /tmp/crontab_backup 2>/dev/null

tar -czf /var/backup/cloud_backup/Scripts/scripts_${FECHA}.tar.gz -C /var scripts -C /tmp crontab_backup

#Borrar la copia temporal del crontab
rm -f /tmp/crontab_backup

# Asignar propiedad a root:backup y permisos de grupo
chown -R root:backup /var/backup/cloud_backup/
chmod -R 770 /var/backup/cloud_backup/
chmod 660 /var/backup/cloud_backup/*/*.tar.gz 2>/dev/null

exit 0