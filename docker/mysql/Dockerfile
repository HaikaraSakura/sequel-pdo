# ベースイメージを指定
FROM mysql:8.0

ENV MYSQL_DATABASE=test_db \
    MYSQL_ROOT_PASSWORD=root \
    LANG=C.UTF-8 \
    LC_ALL=C.UTF-8

COPY ./create_database.sql /docker-entrypoint-initdb.d/
