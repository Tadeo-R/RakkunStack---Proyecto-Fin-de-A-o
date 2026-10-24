#!/bin/bash

# Menú gestor de Grupos
echo Menú de gestión de grupos
echo ===================================
echo 1. Crear grupo
echo 2. Modificar grupo
echo 3. Eliminar grupo
echo 4. Ver grupos
echo 5. Volver al menú principal
echo 6. Salir del programa
echo ===================================
read -p "Ingrese su opción: " -r opcion

case $opcion in
    1)
        # Crear grupo
        echo === Crear grupo ===
        read -p "Ingrese el nombre del grupo: " -r groupName
        
        # Validación de campo vacio y caracteres especiales
        while [[ -z "$groupName" || ! "$groupName" =~ ^[a-z0-9._-]+$ ]]; do
            if [ -z "$groupName" ]; then
                echo "El campo no debe estar vacío"
            else
                echo "El nombre de grupo no puede contener caracteres especiales. Solo letras minusculas, números, puntos, guiones y guiones bajos."
            fi
            read -p "Ingrese un nombre de grupo válido: " -r groupName
        done
        
        # Validar existencia del grupo
        if getent group "$groupName" > /dev/null; then
            echo "El grupo $groupName ya existe."
            echo "Volviendo al menú de gestión de grupos..."
            ./gestionGrupos.sh
            exit 0
        else
            # Crear grupo
            groupadd $groupName
            echo "El grupo $groupName ha sido creado."
            
            # Volver al menú de grupos
            echo "Volviendo al menú de gestión de grupos..."
            ./gestionGrupos.sh
        fi
        
    ;;
    2)
        # modificar grupo
        echo === Modificar grupo ===
        echo "1. Cambiar nombre de grupo"
        echo "2. Cambiar permisos de un archivo/directorio (chmod)"
        echo "3. Volver al menu de grupos"
        echo ========================
        read -p "Ingrese su opcion: " -r optionModify
        
        case $optionModify in
            1)
                # Cambiar nombre de usuario
                read -p "Ingrese nombre del grupo: " -r oldgroup
                
                # Validar campo vacio
                while [[ -z "$oldgroup" ]]; do
                    echo "El campo no debe estar vacío."
                    read -p "Ingrese un nombre de grupo actual válido: " -r oldgroup
                done
                
                # Validar existencia del grupo
                if ! getent group "$oldgroup" > /dev/null; then
                    echo "El grupo '$oldgroup' no existe en el sistema."
                    echo "Volviendo al menú de gestión de grupos..."
                    ./gestionGrupos.sh
                    exit 0
                fi
                
                # Solicitar el nuevo nombre de grupo
                read -p "Ingrese el nombre nuevo para el grupo '$oldgroup': " -r newgroup
                
                # Validar campo vacío y caracteres especiales
                while [[ -z "$newgroup" || ! "$newgroup" =~ ^[a-z0-9._-]+$ ]]; do
                    if [ -z "$newgroup" ]; then
                        echo "El campo no debe estar vacío."
                    else
                        echo "El nuevo nombre no puede contener caracteres especiales. Solo minúsculas, números, puntos, guiones y guiones bajos."
                    fi
                    read -p "Ingrese un nuevo nombre válido: " -r newgroup
                done
                
                # Validar disponibilidad del nombre
                if getent group "$newgroup" > /dev/null; then
                    echo "Error: El nombre de grupo '$newgroup' ya está siendo usado por otro grupo."
                    echo "Volviendo al menú de gestión de grupos..."
                    ./gestionGrupos.sh
                    exit 0
                fi
                
                # Cambiar nombre de grupo
                groupmod -n "$newgroup" "$oldgroup"
                echo "El grupo '$oldgroup' fue renombrado a '$newgroup' exitosamente."
                
                # Volver al menu de grupos
                ./gestionGrupos.sh
                exit 0
            ;;
            2)
                # Cambiar permisos de un archivo/directorio
                echo "=== Cambiar permisos y asignación de grupo a una ruta ==="
                read -p "Ingrese la ruta del archivo o directorio: " -r ruta
                
                # Validar campo vacío
                while [[ -z "$ruta" ]]; do
                    echo "El campo no debe estar vacío."
                    read -p "Ingrese una ruta válida: " -r ruta
                done
                
                # Validar existencia de ruta (archivo o directorio)
                if [[ ! -e "$ruta" ]]; then
                    echo "La ruta '$ruta' no existe en el sistema."
                    echo "Volviendo al menú de gestión de grupos..."
                    ./gestionGrupos.sh
                    exit 0
                fi
                
                # Pedir nombre de grupo
                read -p "Ingrese el nombre del grupo al que pertenecerá la ruta: " -r groupName
                
                # Validar existencia del grupo
                if ! getent group "$groupName" > /dev/null; then
                    echo "Error: El grupo '$groupName' no existe en el sistema."
                    echo "Volviendo al menú de gestión de grupos..."
                    ./gestionGrupos.sh
                    exit 0
                fi
                
                # Pedir los permisos en formato octal
                read -p "Ingrese los permisos en formato octal (ej: 770, 750, 640): " -r permisos
                
                # Validar formato
                while [[ ! "$permisos" =~ ^[0-7]{3,4}$ ]]; do
                    echo "Debe usar entre 3 y 4 dígitos numéricos (del 0 al 7)."
                    read -p "Ingrese un formato de permiso válido (ej: 770): " -r permisos
                done
                
                # Asignar el grupo propietario y aplicar el chmod
                chown :"$groupName" "$ruta"
                chmod "$permisos" "$ruta"
                
                echo "Permisos actualizados"
                echo "Ruta: '$ruta' | Grupo Propietario: '$groupName' | Permisos: '$permisos'"
                
                # Volver al menú de gestión de grupos
                ./gestionGrupos.sh
                exit 0
                
            ;;
            3)
                echo "Saliendo al menú de gestión de grupos..."
                ./gestionGrupos.sh
                exit 0
        esac
    ;;
    3)
        # Eliminar grupo
        echo "=== Eliminar grupo ==="
        read -p "Ingrese el nombre del grupo a eliminar: " -r groupName
        
        # Validar campo vacío
        while [[ -z "$groupName" ]]; do
            echo "El campo no debe estar vacío."
            read -p "Ingrese un nombre de grupo válido: " -r groupName
        done
        
        # Validar existencia del grupo
        if ! getent group "$groupName" > /dev/null; then
            echo "El grupo '$groupName' no existe en el sistema."
            echo "Volviendo al menú de gestión de grupos..."
            
            #Volver al menú de Grupos
            ./gestionGrupos.sh
            exit 0
        fi
        
        # Validar si es grupo primario de algún usuario existente
        if getent passwd | cut -d: -f4 | grep -q "^$(getent group "$groupName" | cut -d: -f3)$"; then
            echo "El grupo '$groupName' es el grupo primario de un usuario, cambie el grupo primario para poder eliminarlo"
        fi
        
        # Advertencia de seguridad antes de borrar
        echo "¡Está a punto de eliminar el grupo '$groupName' del sistema!"
        read -p "¿Está seguro que desea continuar? (s/n): " -r confirmacion
        
        # Validar opción s/n
        if [[ "$confirmacion" =~ ^[sS]$ ]]; then
            # Eliminar el grupo
            groupdel "$groupName"
            echo "El grupo '$groupName' ha sido eliminado exitosamente del sistema."
        else
            echo "Operación cancelada. El grupo no sufrió ninguna modificación."
            echo "Volviendo al menú de gestión de grupos..."
            ./gestionGrupos.sh
            exit 0
        fi
        
        # Volver al menú de gestión de grupos
        ./gestionGrupos.sh
        exit 0
    ;;
    4)
        # Ver grupos
        echo === Ver grupos ===
        echo "1. Ver todos los grupos"
        echo "2. Buscar un grupo"
        echo "3. Volver al menu"
        echo ========================
        read -p "Ingrese su opcion: " -r ViewGroup
        case $ViewGroup in
            
            1)
                # Ver todos los grupos
                echo "La lista de los grupos es: "
                cut -d: -f1 /etc/group
            ;;
            2)
                # Buscar un grupo
                read -p "Ingrese el nombre del grupo cual busca: " -r tagGroup
                
                if [ -z "$tagGroup" ]; then
                    echo "El campo no debe estar vacío"
                else
                    echo "El nombre de usuario no puede contener caracteres especiales. Solo letras minusculas, números, puntos, guiones y guiones bajos."
                fi
                
                if cat /etc/group | grep -q $tagGroup; then
                    echo "El grupo $tagGroup existe."
                else
                    echo "El grupo $tagGroup no existe."
                fi
            ;;
            3)
                # Volver al menu de grupos
                ./gestionGrupos.sh
                exit 0
            ;;
            *)
                echo "Opcion Invalida. Por favorm seleciona una opcion valida."
            ;;
        esac
    ;;
    5)
        # Volver al menú principal
        echo "Volviendo al menú principal..."
        ./gestorGeneral.sh
        exit 0
    ;;
    6)
        # Salir del programa
        clear
        echo "Saliendo del programa..."
        exit 0
        
    ;;
    *)
        # Opción inválida
        echo Opción inválida. Por favor, seleccione una opción válida.
        ./GestionGrupos.sh
        exit 0
    ;;
esac