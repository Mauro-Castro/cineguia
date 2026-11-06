<?php
/**
 * API simples em JSON.
 * Usada pelo JavaScript do catálogo para filtrar sem recarregar a página.
 * Também funciona abrir no navegador: api/titulos.php?q=nolan
 */
declare(strict_types=1);

require_once __DIR__ . '/../includes/funcoes.php';

header('Content-Type: application/json; charset=UTF-8');
header('Cache-Control: no-store');

$lista = filtrarTitulos([
    'q' => $_GET['q'] ?? '',
    'tipo' => $_GET['tipo'] ?? 'todos',
    'genero' => $_GET['genero'] ?? '',
    'ordenar' => $_GET['ordenar'] ?? 'nota',
]);

$saida = [];
foreach ($lista as $item) {
    $saida[] = [
        'id' => $item['id'] ?? '',
        'titulo' => $item['titulo'] ?? '',
        'tipo' => $item['tipo'] ?? 'filme',
        'tipo_rotulo' => rotuloTipo((string) ($item['tipo'] ?? 'filme')),
        'ano' => $item['ano'] ?? null,
        'nota' => $item['nota'] ?? null,
        'generos' => $item['generos'] ?? [],
        'poster' => urlPoster((string) ($item['id'] ?? '')),
        'url' => 'titulo.php?id=' . rawurlencode((string) ($item['id'] ?? '')),
    ];
}

echo json_encode([
    'total' => count($saida),
    'resultados' => $saida,
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
