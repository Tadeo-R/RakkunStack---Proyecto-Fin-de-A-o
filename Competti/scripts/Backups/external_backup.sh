#!/bin/bash

# Validar existencia de carpeta de backup externo
if [ ! -d "/var/backup/external_backup" ]; then
    echo "Error: La carpeta /var/backup/external_backup no existe. Por favor, revise sus backups antes de ejecutar el script." >&2
    exit 1
fi

# Definir variable de fecha para nombrar los archivos de respaldo
FECHA=$(date +"%Y-%m-%d_%H-%M-%S")

# ====================================================

# Respaldar datos de MariaDB
mysqldump -u uits -pUsuarioITS --single-transaction --quick -d --all-databases > /var/backup/external_backup/MariaDB/comppettiBD.sql

# Empaquetar respaldo de la BD
tar -czf /var/backup/external_backup/MariaDB/comppettiBD_${FECHA}.tar.gz -C /var/backup/external_backup/MariaDB comppettiBD.sql

# Borrar volcado .sql plano
rm /var/backup/external_backup/MariaDB/comppettiBD.sql

# ====================================================

# Módulo 2: Infraestructura
tar -czf /var/backup/external_backup/Infraestructura/infraestructura_${FECHA}.tar.gz -C / etc

# Módulo 3: aplicación Web
tar -czf /var/backup/external_backup/web/competti_${FECHA}.tar.gz -C /var/ www

# Módulo 4: Scripts y Cron table

# Exportar temporalmente crontab 
crontab -l > /tmp/crontab_backup 2>/dev/null

tar -czf /var/backup/external_backup/Scripts/scripts_${FECHA}.tar.gz -C /var scripts -C /tmp crontab_backup

#Borrar la copia temporal del crontab
rm -f /tmp/crontab_backup

# Módulo 5: Otros
tar -czf /var/backup/external_backup/Otros/otros_${FECHA}.tar.gz -C / home opt



# Asignar propiedad a root:backup y permisos de grupo
chown -R root:backup /var/backup/external_backup/
chmod -R 770 /var/backup/external_backup/
chmod 660 /var/backup/external_backup/*/*.tar.gz 2>/dev/null

exit 0