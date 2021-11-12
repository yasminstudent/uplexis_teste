# Teste Programador Uplexis

## Requisitos Para a Instalação do Projeto

* Apache
* MySQL
* PHP> = 7.2.5
* Extensão BCMath PHP
* Extensão Ctype PHP
* Extensão Fileinfo PHP
* Extensão JSON PHP
* Extensão Mbstring PHP 
* Extensão OpenSSL PHP
* Extensão PDO PHP
* Extensão Tokenizer PHP
* Extensão XML PHP
* Composer
* Instalador do Laravel
    * para mais orientações visite: https://laravel.com/docs/7.x/installation#installing-laravel
    
## Iniciando Projeto

1. Clone o projeto: https://github.com/yasminstudent/uplexis_teste.git
2. Altere o arquivo .env (localizado na raiz do projeto) colocando suas credencias de acesso ao banco de dados mysql
3. Crie um banco de dados com o nome "uplexis" (sem aspas)
4. Na raiz do projeto execute o seguinte comando via terminal: composer install
5. Em seguida digite o comando para criar as tabelas no banco: php artisan migrate
6. Após concluida as migrações inicie o projeto com o comando: php artisan serve
7. Acesse o link que aparecerá na saída do comando acima
8. Registre um usuário e explore o sistema
