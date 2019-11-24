#!/bin/sh

BACKUP_FOLDER=/opt/mysql/backup
NOW=$(date '+%Y-%m-%d')

GZIP=$(which gzip)
MYSQLDUMP=$(which mysqldump)

### MySQL Server Login info ###

[ ! -d $BACKUP_FOLDER ] && mkdir --parents $BACKUP_FOLDER

FILE=$BACKUP_FOLDER/backup-$NOW.sql.gz
$MYSQLDUMP -h $MYSQL_CONTAINER_NAME -u $MYSQL_USER -p$MYSQL_PASSWORD --databases $MYSQL_DATABASE | $GZIP -9 > $FILE
