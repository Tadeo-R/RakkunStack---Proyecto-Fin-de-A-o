#!/bin/bash

# Menú de información grupos/usuarios
echo "Menú de información grupos/usuarios"
echo "==================================="
echo "1. Ver información de usuarios"
echo "2. Ver información de los grupos"
echo "3. Volver al menú principal"
echo "4. Salir"
echo "==================================="
read -p "Ingrese su opción: " -r option

case $option in
    1)
        # Ver info usuarios
        read -p "Ingrese nombre de usuario: " -r user
        
        # Validar campo vacio
        while [[ -z "$user" ]]; do
            echo "El campo no debe estar vacío."
            read -p "Ingrese un nombre de usuario actual válido: " -r user
        done
        
        # Validar existencia del usuario
        if ! grep -q "^$user:" /etc/passwd; then
            echo "Error: El usuario '$user' no existe en el sistema."
            
            # Volver al menú de información
            ./infoGruposUsuarios.sh
            exit 0
        fi
        
        # Obtener ID de usuario
        uid=$(id -u "$user")
        
        # Obtener grupo principal y gid
        mainGroup=$(id -gn "$user")
        gid=$(id -g "$user")
        
        # Obtener grupos secundarios
        secundaryGroup=$(id -Gn "$user" | sed -E "s/(^| )$mainGroup( |$)/\1/g" | xargs)
        
        # En caso de no tener grupos secundarios
        if [ -z "$secundaryGroup" ]; then
            secundaryGroup="Ninguno"
        fi
        
        # Obtener home del usuario
        dirhome=$(getent passwd "$user" | cut -d: -f6)
        
        # Obtener shell asignado
        userShell=$(getent passwd "$user" | cut -d: -f7)
        
        echo
        echo " Información de usuario "
        echo "==================================="
        echo " Nombre de usuario: $user"
        echo " ID de usuario (UID): $uid"
        echo " Grupo principal: $mainGroup (GID: $gid)"
        echo " Grupos secundarios: $secundaryGroup"
        echo " Directorio Home: $dirhome"
        echo " Shell asignado: $userShell"
        echo "==================================="
        echo
        
        # Esperar para continuar
        read -p "¿Continuar? [Enter]..."
        ./infoGruposUsuarios.sh
        exit 0
    ;;
    2)
        # Ver info grupos
        read -p "Ingrese nombre de grupo: " -r groupName
        
        # Validar campo vacio (Corregido a $groupName)
        while [[ -z "$groupName" ]]; do
            echo "El campo no debe estar vacío."
            read -p "Ingrese un nombre de grupo actual válido: " -r groupName
        done
        
        # Validar existencia del grupo
        if ! getent group "$groupName" > /dev/null; then
            echo "Error: El grupo '$groupName' no existe en el sistema."
            
            # Volver al menú de información
            ./infoGruposUsuarios.sh
            exit 0
        fi
        
        # Obtener el GID
        gid=$(getent group "$groupName" | cut -d: -f3)
        
        # Obtener usuarios primarios
        primaryUsers=$(awk -F: -v gid="$gid" '$4 == gid {print $1}' /etc/passwd | xargs)
        
        # En caso de no tener usuarios primarios
        if [ -z "$primaryUsers" ]; then
            primaryUsers="Ninguno"
        fi
        
        # Obtener usuarios secundarios (Cambiamos comas por espacios con tr)
        secondaryUsers=$(getent group "$groupName" | cut -d: -f4 | tr ',' ' ')
        
        # En caso de no tener usuarios secundarios
        if [ -z "$secondaryUsers" ]; then
            secondaryUsers="Ninguno"
        fi
        
        # Obtener todos los miembros
        if [[ "$primaryUsers" == "Ninguno" && "$secondaryUsers" == "Ninguno" ]]; then
            allMembers="Ninguno"
            elif [ "$primaryUsers" == "Ninguno" ]; then
            allMembers="$secondaryUsers"
            elif [ "$secondaryUsers" == "Ninguno" ]; then
            allMembers="$primaryUsers"
        else
            # Unir listas y borrar duplicados
            allMembers=$(echo "$primaryUsers $secondaryUsers" | tr ' ' '\n' | sort -u | xargs)
        fi
        
        echo
        echo " Información del Grupo "
        echo "==================================="
        echo " Nombre del grupo: $groupName"
        echo " ID de grupo (GID): $gid"
        echo " Usuarios primarios: $primaryUsers"
        echo " Miembros secundarios: $secondaryUsers"
        echo " Total de miembros: $allMembers"
        echo "==================================="
        echo
        
        # Esperar para continuar
        read -p "¿Continuar? [Enter]..."
        ./infoGruposUsuarios.sh
        exit 0
        
    ;;
    3)
        # Volver al menú principal
        clear
        echo "Volviendo al menú principal..."
        ./gestorGeneral.sh
        exit 0
        
    ;;
    4)
        # Salir del programa
        clear
        echo "Saliendo del programa..."
        exit 0
        
    ;;
    *)
        # Opción inválida
        echo "Opción inválida. Por favor, seleccione una opción válida."
        ./infoGruposUsuarios.sh
        exit 0
    ;;
esac