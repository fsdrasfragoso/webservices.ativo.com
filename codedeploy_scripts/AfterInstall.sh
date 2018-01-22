#!/bin/bash
rm -rf /var/www/ativos/webservices.ativo.com_temp/codedeploy_scripts
rm -f /var/www/ativos/webservices.ativo.com_temp/appspec.yml

# Downtime = 0 - mantem aplicacao antiga no ar ate o deploy da aplicacao nova finalizar
[ -d /var/www/ativos/webservices.ativo.com_old ] && rm -rf /var/www/ativos/webservices.ativo.com_old || echo "Diretorio nao existe"
[ -d /var/www/ativos/webservices.ativo.com ] && mv /var/www/ativos/webservices.ativo.com /var/www/ativos/webservices.ativo.com_old || echo "Diretorio nao existe"
mv /var/www/ativos/webservices.ativo.com_temp /var/www/ativos/webservices.ativo.com
# Copia o arquivo de configuração do banco de dados
[ -f /var/www/ativos/webservices.ativo.com_old/.env ] && cp -pf /var/www/ativos/webservices.ativo.com_old/.env /var/www/ativos/webservices.ativo.com/ || echo "Arquivo nao existe"
# Copia o arquivo .htaccess
[ -f /var/www/ativos/webservices.ativo.com_old/.htaccess ] && cp -pf /var/www/ativos/webservices.ativo.com_old/.htaccess /var/www/ativos/webservices.ativo.com/ || echo "Arquivo nao existe"