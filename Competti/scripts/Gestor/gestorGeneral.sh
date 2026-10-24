#!/bin/bash

# Mensaje bienvenida y opciones del panel de control
echo "Bienvenido al panel de control del servidor."

echo "================================================"
echo "1. Ver estado del servidor"
echo "2. Abrir menú de gestión de servicios"
echo "3. Abrir gestor de usuarios"
echo "4. Abrir gestor de grupos"
echo "5. Abrir menú de información grupos-usuarios"
echo "6. Abrir menú de monitoreo de base de datos"
echo "7. Salir"
echo "================================================"

read -p "Seleccione una opción: " option

case $option in
    1)
        # Mostrar el estado del servidor (Uso de CPU, RAM y disco)
        echo "Estado del servidor:"
        
        # Mostrar uso de CPU
        echo "Uso de CPU y Ram:"
        top
        
        # Mostrar uso de disco (-h para formato legible)
        echo "Uso de disco:"
        df -h
        
        echo "=========================================="
        # Volver al panel de control o salir
        while true; do
            read -p "Presione 0 para volver al panel de control: " volver
            
            if [[ "$volver" == "0" ]]; then
                clear
                echo "Volviendo al panel de control..."
                ./gestorGeneral.sh
                exit 0
            else
                echo "Error: Entrada no válida. Ingrese '0' para volver al panel de control."
            fi
        done
    ;;
    2)
        # Abrir menú de gestión de servicios
        clear
        echo "Abriendo gestor de servicios."
        ./GestorServicios.sh
        exit 0
    ;;
    3)
        # Abrir gestor de usuarios
        clear
        echo "Abriendo gestor de usuarios."
        ./GestorUsuarios.sh
        exit 0
    ;;
    4)
        # Abrir gestor de grupos
        clear
        echo "Abriendo gestor de grupos."
        ./GestorGrupos.sh
        exit 0
    ;;
    5)
        # Abrir menú de información de grupos y usuarios
        clear
        echo "Abriendo gestor de información de grupos y usuarios."
        ./infoGruposUsuarios.sh
        exit 0
    ;;
    6)
        # Abrir menú de monitoreo de base de datos
        clear
        echo "Abriendo gestor de monitoreo de base de datos."
        ./monitoreoBD.sh
        exit 0
    ;;

    7)
        # Salir del panel de control
        clear
        echo "Saliendo del panel de control."
        exit 0
    ;;
    *)
        # Opción inválida
        echo "Opción inválida. Por favor, seleccione una opción válida."
    ;;
esac