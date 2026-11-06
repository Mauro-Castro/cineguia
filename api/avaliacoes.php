<?php
/**
 * Recebe o formulário de avaliação (POST) e grava em data/avaliacoes.json.
 */
declare(strict_types=1);

require_once __DIR__ . '/../includes/funcoes.php';

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    header('Location: ../catalogo.php');
    exit;
}

$id = (string) ($_POST['id'] ?? '');
$nome = (string) ($_POST['nome'] ?? '');
$comentario = (string) ($_POST['comentario'] ?? '');
$nota = (int) ($_POST['nota'] ?? 0);

$erro = salvarAvaliacao($id, $nome, $nota, $comentario);
$destino = '../titulo.php?id=' . rawurlencode($id);

if ($erro !== null) {
    $destino .= '&erro=' . rawurlencode($erro);
} else {
    $destino .= '&ok=1';
}

header('Location: ' . $destino);
exit;
