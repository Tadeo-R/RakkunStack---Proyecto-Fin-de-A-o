#!/bin/bash

echo Menú de gestión de usuarios
echo ===================================
echo 1. Crear usuario
echo 2. Modificar usuario
echo 3. Eliminar usuario
echo 4. Volver al menú principal
echo 5. Salir
echo ===================================
read -p "Ingrese su opción: " -r option

case $option in
    
    1)
        # Crear usuario
        echo === Crear usuario ==
        read -p "Ingrese el nombre del usuario: " -r userName
        
        # Validar campo vacio y caracteres especiales
        while [[ -z "$userName" || ! "$userName" =~ ^[a-z0-9._-]+$ ]]; do
            if [ -z "$userName" ]; then
                echo "El campo no debe estar vacío"
            else
                echo "El nombre de usuario no puede contener caracteres especiales. Solo letras minusculas, números, puntos, guiones y guiones bajos."
            fi
            read -p "Ingrese un nombre de usuario válido: " -r userName
        done
        
        # Validar de existencia del usuario
        if cat /etc/passwd | grep -q "^$userName:"; then
            echo "El usuario '$userName' ya existe."
            sleep 2
            ./gestionUsuarios.sh
            exit 0
        else
            # Seleccionar tipo de cuenta
            echo "Seleccione el tipo de cuenta:"
            echo ===================================
            echo "1. Cuenta humana: La cuenta humana es para usuarios que interactúan con el sistema de manera directa."
            echo "2. Cuenta de servicio: La cuenta de servicio es para aplicaciones o servicios y no requieren interacción directa con el sistema."
            echo ===================================
            read -p "Ingrese el tipo de cuenta (1 o 2): " -r typeAccount
            
            # Validar tipo de cuenta
            while [[ "$typeAccount" != "1" && "$typeAccount" != "2" ]]; do
                echo "Opción inválida, seleccione una opción válida."
                read -p "Ingrese el tipo de cuenta (1 o 2): " -r typeAccount
            done
            
            # Crear usuario
            if [ "$typeAccount" == "1" ]; then
                # 1 = Cuenta humana
                
                # Solicitar contraseña y confirmación
                while true; do
                    # Solicitar contraseña al usuario
                    read -p "Ingrese la contraseña para el usuario '$userName': " -s password
                    echo
                    
                    # Validar de campo vacío y caracteres especiales
                    while [[ -z "$password" || ! "$password" =~ ^[a-z0-9._-]+$ ]]; do
                        if [ -z "$password" ]; then
                            echo "El campo no debe estar vacío."
                        else
                            echo "La contraseña contiene caracteres especiales inválidos."
                        fi
                        read -p "Ingrese una contraseña válida: " -s password
                        echo
                    done
                    
                    # Confirmar contraseña
                    read -p "Confirme la contraseña: " -s passwordConfirm
                    echo
                    
                    # Verificar coincidencia de contraseñas
                    if [ "$password" == "$passwordConfirm" ]; then
                        # Si las contraseñas coinciden, salir del bucle
                        break
                    else
                        echo "Las contraseñas no coinciden. Intente configurar la contraseña nuevamente."
                        echo "========================================================="
                    fi
                done
                
                # Crear usuario humano y asignar permisos básicos
                useradd -s /bin/bash -m "$userName"
                echo "$userName:$password" | chpasswd > /dev/null
                chmod 700 "/home/$userName"
                echo "Usuario humano '$userName' creado exitosamente."
            else
                # 2 = Cuenta de servicio
                useradd -r -s /sbin/nologin -M "$userName"
                passwd --lock "$userName" > /dev/null
                echo "Usuario de servicio '$userName' creado exitosamente."
            fi
            
            # Volver al menú de gestión de usuarios
            ./gestionUsuarios.sh
            exit 0
        fi
    ;;
    2)
        # Modificar usuario
        echo "=== Modificar usuario ==="
        read -p "Ingrese el nombre del usuario a modificar: " -r userName
        
        # Validar campo vacío
        while [[ -z "$userName" ]]; do
            echo "El campo no debe estar vacío."
            read -p "Ingrese un nombre de usuario válido: " -r userName
        done
        
        # Validar existencia del usuario en el sistema
        if ! grep -q "^$userName:" /etc/passwd; then
            echo "El usuario '$userName' no existe en el sistema."
            ./gestionUsuarios.sh
            exit 0
        fi
        
        # Menú de modificación de usuarios
        echo "=== Modificar usuario ==="
        echo "Menú de modificación de usuario '$userName' "
        echo "==================================="
        echo "1. Cambiar contraseña"
        echo "2. Cambiar permisos"
        echo "3. Asignar grupos"
        echo "4. Cambiar nombre de usuario"
        echo "5. Volver al menú de gestión de usuarios"
        echo "6. Volver al menú principal"
        echo "7. Salir del programa"
        echo "=================================="
        read -p "Ingrese su opción: " -r optionGestion
        
        case $optionGestion in
            1)
                # Cambiar contraseña
                echo "=== Cambiar contraseña ==="
                
                while true; do
                    # Solicitar nueva contraseña
                    read -p "Ingrese la contraseña para el usuario '$userName': " -s newPassword
                    echo
                    
                    # Validar campo vacío y caracteres especiales
                    while [[ -z "$newPassword" || ! "$newPassword" =~ ^[a-z0-9._-]+$ ]]; do
                        if [ -z "$newPassword" ]; then
                            echo "El campo no debe estar vacío."
                        else
                            echo "La nueva contraseña contiene caracteres especiales inválidos."
                        fi
                        read -p "Ingrese una contraseña válida: " -s newPassword
                        echo
                    done
                    
                    # Confirmar contraseña
                    read -p "Confirme la nueva contraseña: " -s newPasswordConfirm
                    echo
                    
                    # Verificar coincidencia
                    if [ "$newPassword" == "$newPasswordConfirm" ]; then
                        # Si Las contraseñas coinciden, salir del bucle
                        break
                    else
                        echo "Las contraseñas no coinciden. Intente cambiar la contraseña nuevamente."
                        echo "========================================================="
                    fi
                done
                
                # Cambiar la contraseña del usuario
                echo "$userName:$password" | chpasswd "$userName" > /dev/null
                # Establecer unico uso de contraseña
                chage -d 0 "$userName"
                echo "Contraseña temporal del usuario '$userName' cambiada exitosamente."
                
                # Volver al menú de gestión de usuarios
                ./gestionUsuarios.sh
                exit 0
                
            ;;
            
            2)
                # Cambiar permisos y propietario de un archivo/directorio
                echo "=== Cambiar permisos y asignación de usuario a una ruta ==="
                read -p "Ingrese la ruta del archivo o directorio: " -r ruta
                
                # Validar campo vacío
                while [[ -z "$ruta" ]]; do
                    echo "El campo no debe estar vacío."
                    read -p "Ingrese una ruta válida: " -r ruta
                done
                
                # Validar existencia de la ruta (archivo o directorio)
                if [[ ! -e "$ruta" ]]; then
                    echo "Error: La ruta '$ruta' no existe en el sistema."
                    read -p "Presione [Enter] para continuar..."
                    ./gestionUsuarios.sh
                    exit 0
                fi
                
                # Validar campo vacío de usuario
                while [[ -z "$userName" ]]; do
                    echo "El campo no debe estar vacío."
                    read -p "Ingrese un nombre de usuario válido: " -r userName
                done
                
                # Validar existencia del usuario en el sistema
                if ! getent passwd "$userName" > /dev/null; then
                    echo "Error: El usuario '$userName' no existe en el sistema."
                    read -p "Presione [Enter] para continuar..."
                    ./gestionUsuarios.sh
                    exit 0
                fi
                
                # Pedir los permisos en formato octal
                read -p "Ingrese los permisos en formato octal (ej: 755, 700, 644): " -r permisos
                
                # Validar formato de permisos (3 o 4 dígitos numéricos del 0 al 7)
                while [[ ! "$permisos" =~ ^[0-7]{3,4}$ ]]; do
                    echo "Formato inválido. Debe usar entre 3 y 4 dígitos numéricos (del 0 al 7)."
                    read -p "Ingrese un permiso octal válido (ej: 755): " -r permisos
                done
                
                # Preguntar si se aplica de forma recursiva (solo si es un directorio)
                recursivo=""
                if [[ -d "$ruta" ]]; then
                    read -p "¿Desea aplicar los cambios de forma recursiva a todo el contenido del directorio? (s/n): " -r recOption
                    if [[ "$recOption" =~ ^[sS]$ ]]; then
                        recursivo="-R"
                    fi
                fi
                
                # Aplicar el propietario y los permisos
                if chown $recursivo "$userName" "$ruta" 2>/dev/null && chmod $recursivo "$permisos" "$ruta" 2>/dev/null; then
                    echo
                    echo "¡Permisos y propietario actualizados con éxito!"
                    echo "=================================================="
                    echo " Ruta                 : $ruta"
                    echo " Usuario Propietario  : $userName"
                    echo " Permisos asignados   : $permisos"
                    if [[ -n "$recursivo" ]]; then
                        echo " Modo de aplicación   : Recursivo (-R)"
                    fi
                    echo "=================================================="
                    echo "Volviendo al menú..."
                else
                    echo "Error: No se pudieron cambiar los permisos o el propietario en '$ruta'."
                    echo "Asegúrese de ejecutar el script con privilegios de superusuario (sudo)."
                fi
                
                read -p "Presione [Enter] para continuar..."
                ./gestionUsuarios.sh
                exit 0
                
            ;;
            
            3)
                # Asignar grupo al usuario
                echo "=== Asignar grupo al usuario ==="
                read -p "Ingrese nombre del grupo que desea asignar a '$userName': " -r groupName
                
                # Validar campo vacio
                while [[ -z "$groupName" ]]; do
                    echo "El nombre del grupo no debe estar vacío."
                    read -p "Ingrese nombre de grupo a asignar a '$userName': " -r groupName
                done
                
                # Validar existencia del grupo en el sistema
                if ! getent group "$groupName" > /dev/null; then
                    echo "Error: El grupo '$groupName' no existe en el sistema."
                    echo "Por favor, cree el grupo primero desde el menú de gestión de grupos."
                    ./gestionUsuarios.sh
                    exit 0
                fi
                
                # 2. Asignar grupo al usuario
                usermod -aG "$groupName" "$userName"
                echo "El usuario '$userName' ha sido asignado al grupo '$groupName' con éxito."
                ./gestionUsuarios.sh
                exit 0
                
            ;;
            4)
                # Cambiar nombre de usuario
                echo "=== Cambiar nombre de usuario ==="
                echo "Usuario actual a renombrar: '$userName'"
                read -p "Ingrese el nuevo nombre de usuario: " -r newUserName
                
                # Validar campo vacío y caracteres especiales
                while [[ -z "$newUserName" || ! "$newUserName" =~ ^[a-z0-9._-]+$ ]]; do
                    if [ -z "$newUserName" ]; then
                        echo "El campo no debe estar vacío."
                    else
                        echo "El nuevo nombre contiene caracteres especiales inválidos."
                    fi
                    read -p "Ingrese un nombre de usuario válido: " -r newUserName
                done
                
                # Validar existencia del nombre de usuario
                if grep -q "^$newUserName:" /etc/passwd; then
                    echo "Error: El nombre de usuario '$newUserName' ya existe en el sistema."
                    ./gestionUsuarios.sh
                    exit 0
                fi
                
                # Validar tipo de usuario (Revisando si posee directorio home)
                if [ -d "/home/$userName" ]; then
                    # ==Cuenta Humana==
                    
                    # Modifica el nombre de inicio de sesión, define nueva ruta para la carpeta personal y mueve todo el contenido a la nueva ruta
                    usermod -l "$newUserName" -d "/home/$newUserName" -m "$userName"
                    chmod 700 "/home/$newUserName"
                    echo "El usuario humano '$userName' fue renombrado a '$newUserName' y su directorio home fue trasladado."
                else
                    # ==Cuenta servicio==
                    
                    usermod -l "$newUserName" "$userName"
                    echo "La cuenta de servicio '$userName' fue renombrada a '$newUserName' exitosamente."
                fi
                
                # Volver al menú de gestión de usuarios
                ./gestionUsuarios.sh
                exit 0
                
            ;;
            5)
                # volver al menú de gestión de usuarios
                ./gestionUsuarios.sh
                exit 0
                
            ;;
            6)
                # volver al menú principal
                ./Gestor.sh
                exit 0
                
            ;;
            7)
                # Salir del programa
                clear
                echo "Saliendo del programa..."
                exit 0
                
            ;;
            *)
                # Opción inválida
                echo "Opción inválida. Intente nuevamente."
                ./gestionUsuarios.sh
                
            ;;
        esac
        # Termina el case de la opción 2
        
        
    ;;
    3)
        # Eliminar usuario
        echo "=== Eliminar usuario ==="
        read -p "Ingrese el nombre del usuario a eliminar: " -r userName
        
        # Validar campo vacío
        while [[ -z "$userName" ]]; do
            echo "El campo no debe estar vacío."
            read -p "Ingrese un nombre de usuario válido: " -r userName
        done
        
        # Validar existencia del usuario en el sistema
        if ! grep -q "^$userName:" /etc/passwd; then
            echo "El usuario '$userName' no existe en el sistema."
            ./gestionUsuarios.sh
            exit 0
        fi
        
        # Advertencia de seguridad
        echo " ¡Está a punto de eliminar al usuario '$userName'!."
        read -p "¿Está seguro de que desea continuar? (s/n): " -r confirm
        
        # Validar opción
        if [[ "$confirm" =~ ^[sS]$ ]]; then
            if [ -d "/home/$userName" ]; then
                # Eliminar la cuenta forzando el cierre de sesión y procesos vinculados
                if userdel -r -f "$userName" 2>/dev/null; then
                    echo "El usuario humano '$userName' y sus archivos han sido eliminados."
                else
                    echo "Ocurrió un problema al intentar eliminar al usuario. Intente denuevo"
                fi
            else
                if userdel -f "$userName" 2>/dev/null; then
                    echo "La cuenta de servicio '$userName' ha sido eliminada con éxito."
                else
                    echo "Ocurrió un problema al intentar eliminar la cuenta. Intente denuevo"
                fi
            fi
        else
            echo "Operación cancelada. El usuario no ha sufrido modificaciones."
        fi
        
        # Volver al menu de gestión
        ./gestionUsuarios.sh
        exit 0
        
    ;;
    4)
        # Volver al menú principal
        echo "Volviendo al menú principal..."
        ./gestorGeneral.sh
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
        echo "Opción inválida. Intente nuevamente."
        ./gestionUsuarios.sh
        
    ;;
esac