<?php
session_start();
require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/Cartao.php';
require_once __DIR__ . '/../classes/Conta.php';
require_once __DIR__ . '/../classes/helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$token = $_POST['csrf_token'] ?? '';
if (!validarTokenCSRF($token)) {
    $_SESSION['atm_erro'] = 'Sessão inválida.';
    header('Location: index.php');
    exit;
}

if (!verificarRateLimit('atm_login')) {
    $_SESSION['atm_erro'] = 'Demasiadas tentativas. Aguarde.';
    header('Location: index.php');
    exit;
}

$numeroCartao = preg_replace('/\s+/', '', trim($_POST['numero_cartao'] ?? ''));
$pin = $_POST['pin'] ?? '';

if (empty($numeroCartao) || empty($pin)) {
    $_SESSION['atm_erro'] = 'Preencha todos os campos.';
    header('Location: index.php');
    exit;
}

$cartao = Cartao::buscarPorNumero($numeroCartao);

if (!$cartao || !$cartao->validarPin($pin)) {
    registrarTentativa('atm_login');
    registarLogAcesso('atm', $numeroCartao, false);
    $_SESSION['atm_erro'] = 'Cartão ou PIN inválidos.';
    header('Location: index.php');
    exit;
}

$conta = Conta::buscarPorId($cartao->getContaId());

if (!$conta) {
    $_SESSION['atm_erro'] = 'Conta não encontrada.';
    header('Location: index.php');
    exit;
}

unset($_SESSION['atm_login']);
registarLogAcesso('atm', $numeroCartao, true);

$_SESSION['atm_cartao_id'] = $cartao->getId();
$_SESSION['atm_cartao_numero'] = $cartao->getNumeroCartao();
$_SESSION['atm_conta_id'] = $conta->getId();
$_SESSION['atm_conta_tipo'] = $conta->getTipo();

header('Location: menu.php');
exit;
