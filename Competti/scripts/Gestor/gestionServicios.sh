#!/bin/bash

# Menú de Gestión de Servicios
echo "Menú de Gestión de Servicios"
echo "=============================="
echo "1. Listar servicios"
echo "2. Iniciar servicio"
echo "3. Detener servicio"
echo "4. Reiniciar servicio"
echo "5. Habilitar servicio"
echo "6. Deshabilitar servicio"
echo "7. Volver al menú principal"
echo "8. Salir"
echo "=============================="
read -p "Seleccione una opción: " -r opcion

case $opcion in
    1)
        # Listar estado de servicios principales
        echo
        echo "=== Estado de los servicios del sistema ==="
        # Obtiene el estado y el PID de httpd, mariadb y sshd
        systemctl show httpd mariadb sshd -p Id -p ActiveState -p SubState -p MainPID
        echo
        
        read -p "Presione [Enter] para continuar..."
        ./gestionServicios.sh
        exit 0
    ;;

    2)
        # Iniciar un servicio
        read -p "Ingrese el nombre del servicio a iniciar: " -r servicio

        # Validar campo vacío
        while [[ -z "$servicio" ]]; do
            echo "El campo no debe estar vacío."
            read -p "Ingrese un nombre de servicio válido: " -r servicio
        done

        # Validar si el servicio existe
        if ! systemctl list-unit-files "$servicio.service" &>/dev/null; then
            echo "Error: El servicio '$servicio' no existe en el sistema."
            read -p "Presione [Enter] para continuar..."
            ./gestionServicios.sh
            exit 0
        fi

        # Validar si ya está activo
        if systemctl is-active --quiet "$servicio"; then
            echo "El servicio '$servicio' ya está activo."
        else
            echo "Iniciando servicio '$servicio'..."
            if systemctl start "$servicio" 2>/dev/null; then
                echo "Servicio '$servicio' iniciado con éxito."
            else
                echo "Error: No se pudo iniciar el servicio '$servicio'."
            fi
        fi

        read -p "Presione [Enter] para continuar..."
        ./gestionServicios.sh
        exit 0
    ;;

    3)
        # Detener un servicio
        read -p "Ingrese el nombre del servicio a detener: " -r servicio

        # Validar campo vacío
        while [[ -z "$servicio" ]]; do
            echo "El campo no debe estar vacío."
            read -p "Ingrese un nombre de servicio válido: " -r servicio
        done

        # Validar si el servicio existe
        if ! systemctl list-unit-files "$servicio.service" &>/dev/null; then
            echo "Error: El servicio '$servicio' no existe en el sistema."
            read -p "Presione [Enter] para continuar..."
            ./gestionServicios.sh
            exit 0
        fi

        # Validar si ya está detenido
        if ! systemctl is-active --quiet "$servicio"; then
            echo "El servicio '$servicio' ya se encuentra detenido."
        else
            echo "Deteniendo servicio '$servicio'..."
            if systemctl stop "$servicio" 2>/dev/null; then
                echo "Servicio '$servicio' detenido con éxito."
            else
                echo "Error: No se pudo detener el servicio '$servicio'."
            fi
        fi

        read -p "Presione [Enter] para continuar..."
        ./gestionServicios.sh
        exit 0
    ;;

    4)
        # Reiniciar un servicio
        read -p "Ingrese el nombre del servicio a reiniciar: " -r servicio

        # Validar campo vacío
        while [[ -z "$servicio" ]]; do
            echo "El campo no debe estar vacío."
            read -p "Ingrese un nombre de servicio válido: " -r servicio
        done

        # Validar si el servicio existe
        if ! systemctl list-unit-files "$servicio.service" &>/dev/null; then
            echo "Error: El servicio '$servicio' no existe en el sistema."
            read -p "Presione [Enter] para continuar..."
            ./gestionServicios.sh
            exit 0
        fi

        echo "Reiniciando servicio '$servicio'..."
        if systemctl restart "$servicio" 2>/dev/null; then
            echo "Servicio '$servicio' se reinició correctamente."
        else
            echo "Error: No se pudo reiniciar el servicio '$servicio'."
        fi

        read -p "Presione [Enter] para continuar..."
        ./gestionServicios.sh
        exit 0
    ;;

    5)
        # Habilitar servicio (arranque automático)
        read -p "Ingrese el nombre del servicio a habilitar: " -r servicio

        # Validar campo vacío
        while [[ -z "$servicio" ]]; do
            echo "El campo no debe estar vacío."
            read -p "Ingrese un nombre de servicio válido: " -r servicio
        done

        # Validar si el servicio existe
        if ! systemctl list-unit-files "$servicio.service" &>/dev/null; then
            echo "Error: El servicio '$servicio' no existe en el sistema."
            read -p "Presione [Enter] para continuar..."
            ./gestionServicios.sh
            exit 0
        fi

        echo "Habilitando el servicio '$servicio' en el inicio del sistema..."
        if systemctl enable "$servicio" 2>/dev/null; then
            echo "Servicio '$servicio' habilitado con éxito."
        else
            echo "Error: No se pudo habilitar el servicio '$servicio'."
        fi

        read -p "Presione [Enter] para continuar..."
        ./gestionServicios.sh
        exit 0
    ;;

    6)
        # Deshabilitar servicio
        read -p "Ingrese el nombre del servicio a deshabilitar: " -r servicio

        # Validar campo vacío
        while [[ -z "$servicio" ]]; do
            echo "El campo no debe estar vacío."
            read -p "Ingrese un nombre de servicio válido: " -r servicio
        done

        # Validar si el servicio existe
        if ! systemctl list-unit-files "$servicio.service" &>/dev/null; then
            echo "Error: El servicio '$servicio' no existe en el sistema."
            read -p "Presione [Enter] para continuar..."
            ./gestionServicios.sh
            exit 0
        fi

        echo "Deshabilitando el servicio '$servicio' del inicio del sistema..."
        if systemctl disable "$servicio" 2>/dev/null; then
            echo "Servicio '$servicio' deshabilitado con éxito."
        else
            echo "Error: No se pudo deshabilitar el servicio '$servicio'."
        fi

        read -p "Presione [Enter] me para continuar..."
        ./gestionServicios.sh
        exit 0
    ;;

    7)
        # Volver al menú principal
        clear
        echo "Volviendo al menú principal..."
        ./gestorGeneral.sh
        exit 0
    ;;

    8)
        # Salir del programa
        clear
        echo "Saliendo del programa..."
        exit 0
    ;;

    *)
        # Opción inválida
        echo "Opción inválida. Por favor, seleccione una opción válida."
        read -p "Presione [Enter] para continuar..."
        ./gestionServicios.sh
        exit 0
    ;;
esac