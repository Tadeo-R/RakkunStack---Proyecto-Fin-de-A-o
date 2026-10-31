FROM mariadb:11.4

COPY Comppeti/config/CompettiDB.sql /tmp/CompettiDB.sql

RUN { \
	printf 'SET FOREIGN_KEY_CHECKS=0;\n'; \
	sed '/^-- Current Database: `mysql`/,$d' /tmp/CompettiDB.sql; \
	printf '\nSET FOREIGN_KEY_CHECKS=1;\n'; \
	} > /docker-entrypoint-initdb.d/01-CompettiBD.sql \
	&& rm /tmp/CompettiDB.sql