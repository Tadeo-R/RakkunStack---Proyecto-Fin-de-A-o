# Despliegue con Docker

El stack se compone de dos servicios: Apache con PHP y MariaDB. Compose los conecta en una red privada; la aplicación llega a la base usando el nombre de servicio `db`. Solo el puerto web se publica en el host.

## Requisitos

- Docker Engine y Docker Compose v2 (`docker compose`).
- Ejecutar los comandos desde `RakkunStack---Proyecto-Fin-de-A-o/Competti/wwwDev`.
- Si el host usa SELinux en modo enforcing, el montaje del código ya incluye `:z` para que el contenedor pueda leerlo.

## Configuración inicial

Compose exige `MYSQL_ROOT_PASSWORD`. Crea `wwwDev/.env` con contraseñas propias para el entorno:

```dotenv
MYSQL_ROOT_PASSWORD=una_clave_root_larga_y_unica
DB_USER=dbadmin
DB_PASSWORD=una_clave_de_aplicacion
HOST_GID=1000
WEB_PORT=8080
```

Sustituye `HOST_GID` por el resultado de `id -g` en el host. `DB_USER` y `DB_PASSWORD` se comparten entre MariaDB y PHP, por lo que deben coincidir. `WEB_PORT` puede cambiarse si el 8080 ya está ocupado. Protege el archivo porque contiene secretos y no lo subas al repositorio:

```sh
chmod 600 .env
```

No hay `.env.example` en este proyecto; crea `.env` manualmente con esas variables. Los valores por defecto actuales son `DB_USER=dbadmin`, `DB_PASSWORD=Dbadmin`, `HOST_GID=1000` y `WEB_PORT=8080`; `Dbadmin` es solo una clave de desarrollo, cámbiala antes de desplegar en un servidor. No omitas `MYSQL_ROOT_PASSWORD`.

## Arranque

Desde la carpeta `wwwDev`:

```sh
docker compose config --quiet
docker compose up --build -d
```

Abre `http://localhost:8080` (o el puerto que hayas configurado). Para confirmar el estado:

```sh
docker compose ps
docker compose logs -f web db
```

La aplicación guarda los avatares en `Comppeti/html/uploads`. El directorio ya existe; para permitir que Apache escriba en Linux, prepara el grupo del host antes del primer arranque:

```sh
chgrp "$(id -g)" Comppeti/html/uploads
chmod 2775 Comppeti/html/uploads
```

Compose agrega ese GID al contenedor mediante `HOST_GID`. El código se monta desde `wwwDev` en `/var/www/competti`, así que los cambios en PHP, CSS, JS y HTML se reflejan sin reconstruir la imagen. Si cambias un Dockerfile o la configuración de PHP/Apache, reconstruye y recrea los servicios:

```sh
docker compose up --build -d
```

## Cómo se organiza

- `compose.yaml` declara `web`, `db`, sus variables, el puerto, el volumen persistente y la comprobación de salud de MariaDB. `web` espera a que `db` esté saludable.
- `Dockerfile` construye PHP 8.2 con Apache y las extensiones `pdo_mysql` y `fileinfo`. Copia la configuración desde `Docker/`.
- `Docker/apache/000-default.conf` sirve `Comppeti/html` como raíz web.
- `Docker/php/upload.ini` configura las subidas de archivos.
- `Docker/mysql/Dockerfile` carga `Comppeti/config/CompettiDB.sql` en la carpeta de inicialización de MariaDB.
- `Comppeti/config/connection.php` toma `DB_HOST`, `DB_NAME`, `DB_USER` y `DB_PASSWORD` del entorno. En Docker, `DB_HOST` es `db` y la base es `CompettiDB`.
- El volumen `database_data` conserva MariaDB aunque se eliminen y creen de nuevo los contenedores.

El dump SQL se importa automáticamente solo cuando MariaDB inicializa un volumen vacío. Cambiar ese archivo no vuelve a importar los datos si `database_data` ya contiene una base.

## Comandos habituales

```sh
# Reconstruir y arrancar

docker compose up --build -d

# Ver estado y logs

docker compose ps
docker compose logs -f web
docker compose logs -f db

# Abrir una consola SQL

docker compose exec db sh -c 'mariadb -uroot -p"$MARIADB_ROOT_PASSWORD" CompettiDB'

# Detener y quitar contenedores/red; conserva la base

docker compose down
```

Para ejecutar comandos desde otro puerto sin cambiar `.env`, por ejemplo:

```sh
WEB_PORT=8081 docker compose up --build -d
```

## Copias de seguridad

Exporta la base a un archivo en el host:

```sh
docker compose exec -T db sh -c 'mariadb-dump -uroot -p"$MARIADB_ROOT_PASSWORD" CompettiDB' > CompettiDB-$(date +%F).sql
```

Restaura un dump con:

```sh
docker compose exec -T db sh -c 'mariadb -uroot -p"$MARIADB_ROOT_PASSWORD" CompettiDB' < CompettiDB-respaldo.sql
```

Incluye `Comppeti/html/uploads` en las copias del proyecto para conservar los avatares. Guarda los respaldos y `.env` en ubicaciones protegidas.

## Datos y seguridad

`docker compose down` conserva el volumen de MariaDB. `docker compose down -v` también elimina `database_data` y borra la base persistida; úsalo solo si quieres reinicializarla y tienes un respaldo. No publiques el puerto de MariaDB en Internet. En un servidor, coloca HTTPS en un proxy inverso y define contraseñas únicas en `.env`.
