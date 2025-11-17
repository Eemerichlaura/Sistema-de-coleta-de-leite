-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 13/11/2025 às 15:35
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `bancodeleite`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `bebes`
--

CREATE TABLE `bebes` (
  `id` int(11) NOT NULL,
  `nome_bebe` varchar(150) NOT NULL,
  `sexo_bebe` enum('masculino','feminino') NOT NULL,
  `cpf_bebe` varchar(14) NOT NULL,
  `data_nascimento_bebe` date NOT NULL,
  `unidade_saude` varchar(100) NOT NULL,
  `situacao_clinica` varchar(100) NOT NULL,
  `observacoes_bebe` text DEFAULT NULL,
  `nome_responsavel` varchar(150) NOT NULL,
  `sexo_responsavel` enum('masculino','feminino','outro') NOT NULL,
  `pronomes_responsavel` varchar(20) NOT NULL,
  `cpf_responsavel` varchar(14) NOT NULL,
  `data_nascimento_responsavel` date NOT NULL,
  `telefone_responsavel` varchar(20) NOT NULL,
  `cep_responsavel` varchar(9) NOT NULL,
  `endereco_responsavel` varchar(255) NOT NULL,
  `numero_responsavel` varchar(10) NOT NULL,
  `bairro_responsavel` varchar(100) NOT NULL,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `bebes`
--

INSERT INTO `bebes` (`id`, `nome_bebe`, `sexo_bebe`, `cpf_bebe`, `data_nascimento_bebe`, `unidade_saude`, `situacao_clinica`, `observacoes_bebe`, `nome_responsavel`, `sexo_responsavel`, `pronomes_responsavel`, `cpf_responsavel`, `data_nascimento_responsavel`, `telefone_responsavel`, `cep_responsavel`, `endereco_responsavel`, `numero_responsavel`, `bairro_responsavel`, `criado_em`) VALUES
(22, 'pattotie', 'masculino', '541.262.118-02', '2025-11-03', 'UBS Central', 'Dificuldade de sucção', '', 'jose', 'feminino', 'ela-dela', '306.469.048-50', '2025-11-12', '(12) 13123-1333', '15905-020', 'Rua Doutor Joaquim Machado Faro Rolemberg', '12', 'Jardim Bela Vista', '2025-11-12 14:12:56');

-- --------------------------------------------------------

--
-- Estrutura para tabela `doacoes`
--

CREATE TABLE `doacoes` (
  `id` int(11) NOT NULL,
  `doadora_id` int(11) NOT NULL,
  `data_doacao` date NOT NULL,
  `quantidade_ml` decimal(10,2) NOT NULL,
  `tipo_leite` enum('leite_maduro','leite_transicao','leite_colostro','leite_humano_pasteurizado','leite_cru') NOT NULL,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `doadoras`
--

CREATE TABLE `doadoras` (
  `id` int(11) NOT NULL,
  `nome` varchar(150) NOT NULL,
  `cpf` varchar(14) NOT NULL,
  `data_nascimento` date NOT NULL,
  `telefone` varchar(20) NOT NULL,
  `cep` varchar(9) NOT NULL,
  `endereco` varchar(255) NOT NULL,
  `numero` varchar(10) NOT NULL,
  `bairro` varchar(100) NOT NULL,
  `observacoes` text DEFAULT NULL,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `doadoras`
--

INSERT INTO `doadoras` (`id`, `nome`, `cpf`, `data_nascimento`, `telefone`, `cep`, `endereco`, `numero`, `bairro`, `observacoes`, `criado_em`) VALUES
(45, 'adryan macaco 3', '519.872.868-39', '2025-11-15', '(16) 99308-9219', '15905-020', 'Rua Doutor Joaquim Machado Faro Rolemberg', '12', 'Jardim Bela Vista', '', '2025-11-12 14:12:04');

-- --------------------------------------------------------

--
-- Estrutura para tabela `estoqueleite`
--

CREATE TABLE `estoqueleite` (
  `id` int(11) NOT NULL,
  `tipo` varchar(50) NOT NULL,
  `quantidade` int(11) DEFAULT 0,
  `atualizado` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `estoqueleite`
--

INSERT INTO `estoqueleite` (`id`, `tipo`, `quantidade`, `atualizado`) VALUES
(1, 'leitemaduro', 1000, '2025-11-12 14:24:41'),
(2, 'leitetransicao', 1000, '2025-11-12 14:24:34'),
(3, 'colostro', 1000, '2025-11-08 16:30:52'),
(4, 'leitepasteurizado', 0, '2025-11-08 16:28:00'),
(5, 'leitecru', 0, '2025-11-08 16:28:09');

-- --------------------------------------------------------

--
-- Estrutura para tabela `logleite`
--

CREATE TABLE `logleite` (
  `id` int(11) NOT NULL,
  `tipo` varchar(50) NOT NULL,
  `acao` enum('entrada','saida') NOT NULL,
  `quantidade` int(11) NOT NULL,
  `funcionario` varchar(150) NOT NULL,
  `data` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `logleite`
--

INSERT INTO `logleite` (`id`, `tipo`, `acao`, `quantidade`, `funcionario`, `data`) VALUES
(1, 'colostro', 'entrada', 1000, 'roberto', '2025-11-08 16:30:52'),
(2, 'leitetransicao', 'saida', 1000, 'Laura Emerich', '2025-11-12 14:24:28'),
(3, 'leitetransicao', 'entrada', 1000, 'Laura Emerich', '2025-11-12 14:24:34'),
(4, 'leitemaduro', 'entrada', 1000, 'Laura Emerich', '2025-11-12 14:24:41');

-- --------------------------------------------------------

--
-- Estrutura para tabela `retiradas`
--

CREATE TABLE `retiradas` (
  `id` int(11) NOT NULL,
  `bebe_id` int(11) NOT NULL,
  `data_retirada` date NOT NULL,
  `quantidade_ml` decimal(10,2) NOT NULL,
  `tipo_leite` enum('leite_maduro','leite_transicao','leite_colostro','leite_humano_pasteurizado','leite_cru') NOT NULL,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `cpf` varchar(14) DEFAULT NULL,
  `senha` varchar(255) NOT NULL,
  `nivel` enum('admin','funcionario') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `nome`, `email`, `cpf`, `senha`, `nivel`) VALUES
(33, 'Laura Emerich', 'emerich.laura@gmail.com', '541.262.118-02', '$2y$10$hOqtJwoSYZoJ6vHetzOjZ.VR2waCltCxKYHCyNzFG3v7/l48j.T.W', 'admin');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `bebes`
--
ALTER TABLE `bebes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cpf_bebe` (`cpf_bebe`);

--
-- Índices de tabela `doacoes`
--
ALTER TABLE `doacoes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_doadora` (`doadora_id`);

--
-- Índices de tabela `doadoras`
--
ALTER TABLE `doadoras`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cpf` (`cpf`);

--
-- Índices de tabela `estoqueleite`
--
ALTER TABLE `estoqueleite`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `logleite`
--
ALTER TABLE `logleite`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `retiradas`
--
ALTER TABLE `retiradas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_bebe` (`bebe_id`);

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `cpf` (`cpf`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `bebes`
--
ALTER TABLE `bebes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT de tabela `doacoes`
--
ALTER TABLE `doacoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT de tabela `doadoras`
--
ALTER TABLE `doadoras`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT de tabela `estoqueleite`
--
ALTER TABLE `estoqueleite`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `logleite`
--
ALTER TABLE `logleite`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `retiradas`
--
ALTER TABLE `retiradas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `doacoes`
--
ALTER TABLE `doacoes`
  ADD CONSTRAINT `fk_doadora` FOREIGN KEY (`doadora_id`) REFERENCES `doadoras` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `retiradas`
--
ALTER TABLE `retiradas`
  ADD CONSTRAINT `fk_bebe` FOREIGN KEY (`bebe_id`) REFERENCES `bebes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
