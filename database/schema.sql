-- database/schema.sql

-- Criação da base de dados (o utilizador deve criar a BD 'crm_database' manualmente antes ou remover este comentário)
-- CREATE DATABASE IF NOT EXISTS crm_database;
-- USE crm_database;

SET FOREIGN_KEY_CHECKS=0;

-- 1. Tabela de Administradores/Utilizadores do Sistema
DROP TABLE IF EXISTS `admin_user`;
CREATE TABLE `admin_user` (
  `admin_id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `senha_hash` varchar(255) NOT NULL,
  `permissao` enum('super_admin','gestor','vendas') NOT NULL DEFAULT 'vendas',
  `data_criacao` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`admin_id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Password para o admin default é 'admin123'
INSERT INTO `admin_user` (`nome`, `email`, `senha_hash`, `permissao`) VALUES
('Administrador', 'admin@crm.local', '$2y$10$BYFH.eLFX81g7I8VT4mVUOu4fuCO5JP8dYBXQkJj44dZ6l.V0p5/u', 'super_admin');

-- 2. Tabela de Clientes
DROP TABLE IF EXISTS `cliente`;
CREATE TABLE `cliente` (
  `cliente_id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `empresa` varchar(100) DEFAULT NULL,
  `nif` varchar(20) DEFAULT NULL,
  `morada` text DEFAULT NULL,
  `senha_hash` varchar(255) DEFAULT NULL, -- Acesso opcional à área de cliente
  `data_criacao` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`cliente_id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `cliente` (`nome`, `email`, `telefone`, `empresa`, `nif`, `morada`) VALUES
('João Silva', 'joao@cliente.com', '912345678', 'Tech Lda', '123456789', 'Rua das Flores, 123'),
('Maria Santos', 'maria@cliente.com', '961234567', 'Design Solutions', '987654321', 'Avenida da Liberdade, 456');

-- 3. Tabela de Leads (Funnel de Vendas) - NOVO
DROP TABLE IF EXISTS `lead`;
CREATE TABLE `lead` (
  `lead_id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `empresa` varchar(100) DEFAULT NULL,
  `fonte` varchar(50) DEFAULT NULL, -- ex: LinkedIn, Site, Referência
  `status` enum('novo','contactado','qualificado','proposta','negociacao','ganho','perdido') NOT NULL DEFAULT 'novo',
  `data_criacao` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`lead_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `lead` (`nome`, `email`, `telefone`, `empresa`, `fonte`, `status`) VALUES
('Carlos Pereira', 'carlos@potencial.com', '933333333', 'Construções CP', 'Site', 'novo'),
('Ana Sousa', 'ana@startup.com', '911111111', 'Startup X', 'LinkedIn', 'contactado');

-- 4. Tabela de Interações (Registo de chamadas, emails) - NOVO
DROP TABLE IF EXISTS `interacao`;
CREATE TABLE `interacao` (
  `interacao_id` int(11) NOT NULL AUTO_INCREMENT,
  `tipo` enum('chamada','email','reuniao','nota') NOT NULL,
  `descricao` text NOT NULL,
  `data_interacao` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `cliente_id` int(11) DEFAULT NULL,
  `lead_id` int(11) DEFAULT NULL,
  `admin_id` int(11) NOT NULL, -- Quem registou
  PRIMARY KEY (`interacao_id`),
  KEY `cliente_id` (`cliente_id`),
  KEY `lead_id` (`lead_id`),
  KEY `admin_id` (`admin_id`),
  CONSTRAINT `fk_interacao_cliente` FOREIGN KEY (`cliente_id`) REFERENCES `cliente` (`cliente_id`) ON DELETE SET NULL,
  CONSTRAINT `fk_interacao_lead` FOREIGN KEY (`lead_id`) REFERENCES `lead` (`lead_id`) ON DELETE SET NULL,
  CONSTRAINT `fk_interacao_admin` FOREIGN KEY (`admin_id`) REFERENCES `admin_user` (`admin_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 5. Tabela de Estados de Encomenda
DROP TABLE IF EXISTS `estado_encomenda`;
CREATE TABLE `estado_encomenda` (
  `estado_id` int(11) NOT NULL AUTO_INCREMENT,
  `nome_estado` varchar(50) NOT NULL,
  `ordem` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`estado_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `estado_encomenda` (`nome_estado`, `ordem`) VALUES
('Pendente', 1),
('Em Processamento', 2),
('Enviado', 3),
('Entregue', 4),
('Cancelado', 5);

-- 6. Tabela de Encomendas
DROP TABLE IF EXISTS `encomenda`;
CREATE TABLE `encomenda` (
  `encomenda_id` int(11) NOT NULL AUTO_INCREMENT,
  `cliente_id` int(11) NOT NULL,
  `valor_total` decimal(10,2) NOT NULL DEFAULT 0.00,
  `data_encomenda` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`encomenda_id`),
  KEY `cliente_id` (`cliente_id`),
  CONSTRAINT `fk_encomenda_cliente` FOREIGN KEY (`cliente_id`) REFERENCES `cliente` (`cliente_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `encomenda` (`cliente_id`, `valor_total`) VALUES
(1, 150.00),
(2, 500.50);

-- 7. Histórico de Estados da Encomenda
DROP TABLE IF EXISTS `historico_estado`;
CREATE TABLE `historico_estado` (
  `historico_id` int(11) NOT NULL AUTO_INCREMENT,
  `encomenda_id` int(11) NOT NULL,
  `estado_id` int(11) NOT NULL,
  `data_estado` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `observacoes` text DEFAULT NULL,
  PRIMARY KEY (`historico_id`),
  KEY `encomenda_id` (`encomenda_id`),
  KEY `estado_id` (`estado_id`),
  CONSTRAINT `fk_hist_encomenda` FOREIGN KEY (`encomenda_id`) REFERENCES `encomenda` (`encomenda_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_hist_estado` FOREIGN KEY (`estado_id`) REFERENCES `estado_encomenda` (`estado_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `historico_estado` (`encomenda_id`, `estado_id`, `observacoes`) VALUES
(1, 1, 'Encomenda recebida'),
(2, 1, 'Encomenda recebida'),
(2, 2, 'Pagamento confirmado');

SET FOREIGN_KEY_CHECKS=1;
