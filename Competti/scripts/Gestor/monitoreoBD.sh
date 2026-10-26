#!/bin/bash

# Validar ejecución como con ./
if [[ "$0" != "./"* ]]; then
    echo "Error: Este script solo se puede ejecutar usando './'" >&2
    exit 1
fi

# Menu de monitoreo de base de datos
echo "Menú de monitoreo de base de datos"
<<<<<<< HEAD
=======

echo "================================================"
echo "1. Verificar estado del servicio MariaDB"
echo "2. Verificar integridad de tablas de la BD"
echo "3. Consultar uso del disco de una BD "
echo "4. Ver logs de MariaDB"
echo "5. Ver procesos y conexiones activas de una BD"
echo "6. Comprobar disponibilidad de la BD (Ping)"
echo "7. Salir"
echo "================================================"
read -p "Seleccione una opción: " option

case $option in
    1)
        # Verificar estado del servicio MariaDB
        echo "Verificando estado del servicio MariaDB..."
        systemctl status mariadb --no-pager
        read -p "Presione [Enter] para continuar..."
        ./monitoreoBD.sh
        exit 0
    ;;
    2)
        # Verificar integridad de tablas de la BD
        echo "Verificando integridad de tablas de la BD..."
        mysqlcheck -u uits -pUsuarioITS --all-databases
        read -p "Presione [Enter] para continuar..."
        ./monitoreoBD.sh
        exit 0
    ;;
    3)
        # Consultar uso del disco de una BD
        echo "Consultando uso del disco de una BD..."
        read -p "Ingrese el nombre de la base de datos (Ingrese el nombre exacto): " db_name

        if [ -d "/var/lib/mysql/$db_name" ]; then
            echo "Uso del disco de la base de datos '$db_name':"
            du -sh /var/lib/mysql/"$db_name"
        else
            echo "Error: La base de datos '$db_name' no existe."
        fi

        read -p "Presione [Enter] para continuar..."
        ./monitoreoBD.sh
        exit 0
    ;;
    4)
        # Ver logs de MariaDB
        echo "Mostrando logs recientes de MariaDB..."
        journalctl -u mariadb -n 30 --no-pager
        read -p "Presione [Enter] para continuar..."
        ./monitoreoBD.sh
        exit 0
    ;;
    5)
        # Ver procesos y conexiones activas de una BD
        echo "Verificando procesos y conexiones activas de la BD..."
        mysql -u uits -pUsuarioITS -e "SHOW PROCESSLIST;"
        read -p "Presione [Enter] para continuar..."
        ./monitoreoBD.sh
        exit 0
    ;;
    6)
        # Comprobar disponibilidad de la BD (Ping)
        echo "Comprobando disponibilidad de la BD (Ping)..."
        mysqladmin ping -u uits -pUsuarioITS
        read -p "Presione [Enter] para continuar..."
        ./monitoreoBD.sh
        exit 0
    ;;
    7)
        # Salir del menú de monitoreo de base de datos
        echo "Saliendo del menú de monitoreo de base de datos."
        exit 0
    ;;
    *)
        # Opción inválida
        echo "Opción inválida. Por favor, seleccione una opción válida."
        ./monitoreoBD.sh
        exit 0
    ;;
esac