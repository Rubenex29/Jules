<?php
// includes/db.php

$host = '127.0.0.1'; // ou o host do seu servidor de base de dados
$dbname = 'crm_database'; // substitua pelo nome da sua base de dados
$user = 'root'; // substitua pelo seu nome de utilizador
$pass = ''; // substitua pela sua password

try {
    // Criar uma nova instância de PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);

    // Definir o modo de erro do PDO para exceção
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Opcional: Desativar a emulação de prepared statements para maior segurança
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

} catch (PDOException $e) {
    // Em caso de erro na conexão, termina o script.
    // Em um ambiente de produção, é crucial não exibir detalhes do erro ao utilizador.
    // O erro deve ser registado num ficheiro de log no servidor.
    error_log("Erro de conexão com a base de dados: " . $e->getMessage());
    die("Não foi possível estabelecer a conexão com a base de dados. Por favor, tente novamente mais tarde.");
}
?>
