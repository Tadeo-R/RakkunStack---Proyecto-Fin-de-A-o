#!/bin/bash

# Validar ejecución con Bash
if [ -z "$BASH_VERSION" ]; then
    echo "Error: Este script debe ser ejecutado exclusivamente con Bash." >&2
    exit 1
fi

# Mensaje bienvenida
echo "Bienvenido al panel de control del servidor."

read -p "Seleccione una opción: " option
echo "1. Ver estado del servidor"
echo "2. Abrir gestor de servicios"
echo "3. Abrir gestor de usuarios"
echo "4. Abrir gestor de grupos"
echo "5. Abrir menú de información"
echo "6. Abrir gestor de respaldos"
echo "7. Reiniciar servidor"
echo "8. Salir"

case $option in
    1)
        # Mostrar el estado del servidor (Uso de CPU, RAM y disco)
        echo "Estado del servidor:"
        
        # Mostrar uso de CPU
        echo "Uso de CPU y Ram:"
        top -h
        
        # Mostrar uso de disco (-h para formato legible)
        echo "Uso de disco:"
        df -h /
        
        echo "=========================================="
        # Volver al panel de control o salir
        while true; do
            read -p "Presione 0 para volver al panel de control: " volver
            
            if [[ "$volver" == "0" ]]; then
                echo "Volviendo al panel de control..."
                clear
                ./GestorG.sh
                exit 0
            else
                echo "Error: Entrada no válida. Ingrese '0' para volver al panel de control."
            fi
        done
        
    ;;
    2)
        echo "Abriendo gestor de servicios."
    ;;
    3)
        echo "Saliendo del panel de control."
        exit 0
    ;;
    *)
        echo "Opción inválida. Por favor, seleccione una opción válida."
    ;;
esac

