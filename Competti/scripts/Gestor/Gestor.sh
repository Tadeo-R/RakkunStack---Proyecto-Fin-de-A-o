#!/bin/bash

# Verificar si el script se ejecuta como sudo o root
if [ "$EUID" -ne 0 ]; then
    echo "Por favor, ejecute el script como root o usando sudo."
    exit 1
fi

# Menú principal del programa
echo "Menú de gestión de sistema"
echo "==================================="
echo "1. Gestión de usuarios"
echo "2. Gestión de grupos"
echo "3. Ver usuarios y grupos creados"
echo "4. Gestión de servicios"
echo "5. Salir del programa"
echo "==================================="
read -p "Ingrese su opción: " -r option

case $option in
    
    1)
        # Menú de gestión de usuarios
        echo "=== Gestión de usuarios ==="
        ./gestionUsuarios.sh
        exit 0
        
    ;;
    2)
        # Menú de gestión de grupos
        echo "=== Gestión de grupos ==="
        ./gestionGrupos.sh
        exit 0
        
    ;;
    3)
        # Menú de información de usuarios y grupos
        echo "=== Información de usuarios y grupos ==="
        ./infoGruposUsuarios.sh
        exit 0
        
    ;;
    4)
        # Menú de gestión de servicios
        echo "=== Gestión de servicios ==="
        ./gestionServicios.sh
        exit 0

    ;;
    5)
        # Salir del programa
        clear
        echo "Saliendo del programa..."
        exit 0
        
    ;;
    *)
        # Opción inválida
        echo "Opción inválida. Por favor, seleccione una opción válida."
        ./Gestor.sh
        exit 0
        
    ;;
esac