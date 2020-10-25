#!/bin/bash
set -e

NC='\033[0m'
RED='\033[0;31m'
GREEN='\033[1;32m'

echo -e "${GREEN}==================================================${NC}"
echo -e "${GREEN}===           CRIANDO BANCO DE DADOS           ===${NC}"
echo -e "${GREEN}==================================================${NC}"

mysql -u root -p$MYSQL_ROOT_PASSWORD << EOF
/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

DROP DATABASE IF EXISTS \`$MYSQL_DATABASE\`;
DROP DATABASE IF EXISTS \`unit_test\`;
CREATE DATABASE /*!32312 IF NOT EXISTS*/\`$MYSQL_DATABASE\` /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/\`unit_test\` /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci */;
GRANT ALL ON \`$MYSQL_DATABASE\`.* TO 'root'@'%';
GRANT ALL ON \`$MYSQL_DATABASE\`.* TO '$MYSQL_USER'@'%';
GRANT ALL ON \`unit_test\`.* TO 'root'@'%';
GRANT ALL ON \`unit_test\`.* TO '$MYSQL_USER'@'%';
FLUSH PRIVILEGES;
EOF

echo -e "${GREEN}==================================================${NC}"
echo -e "${GREEN}===               CRIANDO TABELAS              ===${NC}"
echo -e "${GREEN}==================================================${NC}"

mysql -u root -p$MYSQL_ROOT_PASSWORD $MYSQL_DATABASE < /docker-entrypoint-initdb.d/migrations/initialdbtables.sql.temp

echo -e "${GREEN}==================================================${NC}"
echo -e "${GREEN}===              IMPORTANDO DADOS              ===${NC}"
echo -e "${GREEN}==================================================${NC}"

mysql -u root -p$MYSQL_ROOT_PASSWORD $MYSQL_DATABASE < /docker-entrypoint-initdb.d/seeds/initialdbtablesdata.sql.temp
#mysql -u root -p$MYSQL_ROOT_PASSWORD $MYSQL_DATABASE < /docker-entrypoint-initdb.d/seeds/initialdbtablesdata_cidades.sql.temp

echo -e "${RED}==================================================${NC}"
echo -e "${RED}===            IMPORTAÇÃO CONCLUÍDA            ===${NC}"
echo -e "${RED}==================================================${NC}"
