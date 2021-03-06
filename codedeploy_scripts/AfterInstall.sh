#!/bin/bash
rm -rf /var/www/ativos/producao.webservices.ativo.com_temp/codedeploy_scripts
rm -f /var/www/ativos/producao.webservices.ativo.com_temp/appspec.yml

# Downtime = 0 - mantem aplicacao antiga no ar ate o deploy da aplicacao nova finalizar
[ -d /var/www/ativos/producao.webservices.ativo.com_old ] && rm -rf /var/www/ativos/producao.webservices.ativo.com_old || echo "Diretorio nao existe"
[ -d /var/www/ativos/producao.webservices.ativo.com ] && mv /var/www/ativos/producao.webservices.ativo.com /var/www/ativos/producao.webservices.ativo.com_old || echo "Diretorio nao existe"
mv /var/www/ativos/producao.webservices.ativo.com_temp /var/www/ativos/producao.webservices.ativo.com
# Copia o arquivo de configuração do banco de dados
[ -f /var/www/ativos/producao.webservices.ativo.com_old/config/snappy.php ] && cp -prf /var/www/ativos/producao.webservices.ativo.com_old/config/snappy.php /var/www/ativos/producao.webservices.ativo.com/config/ || echo "Diretorio nao existe"
[ -f /var/www/ativos/producao.webservices.ativo.com_old/.env ] && cp -prf /var/www/ativos/producao.webservices.ativo.com_old/.env /var/www/ativos/producao.webservices.ativo.com/ || echo "Arquivo não existe"
# Copia o arquivo .htaccess
[ -f /var/www/ativos/producao.webservices.ativo.com_old/public/.htaccess ] && cp -pf /var/www/ativos/producao.webservices.ativo.com_old/public/.htaccess /var/www/ativos/producao.webservices.ativo.com/public/ || echo "Arquivo nao existe"