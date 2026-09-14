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

# Validar existencia de carpeta de backup diario
if [ ! -d "/var/backup/daily_backup" ]; then
    echo "Error: La carpeta /var/backup/daily_backup no existe. Por favor, revise sus backups antes de ejecutar el script."
    exit 1
fi

# Definir variable de fecha para nombrar los archivos de respaldo
FECHA=$(date +"%Y-%m-%d_%H-%M-%S")

# ====================================================

# Respaldar datos de MariaDB
mysqldump -u uits -pUsuarioITS --single-transaction --quick --all-databases > /var/backup/daily_backup/MariaDB/comppettiBD.sql

# Empaquetar respaldo de la BD
tar -czf /var/backup/daily_backup/MariaDB/comppettiBD_${FECHA}.tar.gz -C /var/backup/daily_backup/MariaDB comppettiBD.sql

# Borrar volcado .sql plano
rm /var/backup/daily_backup/MariaDB/comppettiBD.sql

# ====================================================

# Respaldar archivos de configuración del sistema
rsync -a -h -X -A --delete-after --max-delete=10 -q /etc/ /var/backup/daily_backup/Infraestructura/etc/

# Respaldar archivos del servicio Web
rsync -a -h -X -A --delete-after --max-delete=10 -q /var/www/ /var/backup/daily_backup/www/

# Respaldar scripts y crontab
rsync -a -h -X -A --delete-after -q /var/scripts/ /var/backup/daily_backup/Scripts/scripts/

# Exportar e integrar la tabla cron
crontab -l > /var/backup/daily_backup/Scripts/crontab_backup 2>/dev/null

# Parametros de rsync
# -a               Modo de archivo
# -h               muestra tramaños de archivo en formato entendible (Kb, MB, GB)
# -x               Preserva atributos extendidos del sistema de archivos
# -A               Preserva las Listas de Control de Acceso (ACLs) POSIX definidas en los archivos y carpetas origen.
# -q               Modo silencioso
# --delete-after   Elimina los archivos en el destino que ya no existan en el origen, pero lo hace después de completar toda la transferencia de los archivos nuevos o modificados.

exit 0