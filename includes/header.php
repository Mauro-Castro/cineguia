<?php
declare(strict_types=1);

if (!isset($tituloPagina)) {
    $tituloPagina = 'CineGuia';
}
if (!isset($paginaAtual)) {
    $paginaAtual = '';
}
if (!isset($descricaoPagina)) {
    $descricaoPagina = 'Catálogo de filmes e séries no estilo IMDb, feito com HTML, CSS, JavaScript e PHP.';
}

require_once __DIR__ . '/funcoes.php';

$buscaAtual = isset($_GET['q']) ? (string) $_GET['q'] : '';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo e($descricaoPagina); ?>">
    <title><?php echo e($tituloPagina); ?> · CineGuia</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/estilo.css">
</head>
<body>
    <a class="pular-conteudo" href="#conteudo">Pular para o conteúdo</a>
    <header class="topo">
        <div class="topo-interno">
            <a class="logo" href="index.php" aria-label="Página inicial do CineGuia">
                <span class="logo-marca">CG</span>
                <span class="logo-nome">CineGuia</span>
            </a>

            <button class="botao-menu" type="button" data-menu-botao aria-expanded="false" aria-controls="menu-principal">
                Menu
            </button>

            <nav id="menu-principal" class="menu" data-menu>
                <a class="<?php echo $paginaAtual === 'inicio' ? 'ativo' : ''; ?>" href="index.php">Início</a>
                <a class="<?php echo $paginaAtual === 'catalogo' ? 'ativo' : ''; ?>" href="catalogo.php">Catálogo</a>
                <a class="<?php echo $paginaAtual === 'filmes' ? 'ativo' : ''; ?>" href="catalogo.php?tipo=filme">Filmes</a>
                <a class="<?php echo $paginaAtual === 'series' ? 'ativo' : ''; ?>" href="catalogo.php?tipo=serie">Séries</a>
                <a class="<?php echo $paginaAtual === 'lista' ? 'ativo' : ''; ?>" href="lista.php">Minha lista</a>
                <a class="<?php echo $paginaAtual === 'sobre' ? 'ativo' : ''; ?>" href="sobre.php">Sobre o projeto</a>
            </nav>

            <form class="busca-topo" action="catalogo.php" method="get" role="search">
                <label class="sr-only" for="busca-global">Buscar título</label>
                <input
                    id="busca-global"
                    type="search"
                    name="q"
                    placeholder="Buscar filmes, séries, elenco..."
                    value="<?php echo e($buscaAtual); ?>"
                    autocomplete="off"
                >
                <button type="submit">Buscar</button>
            </form>
        </div>
    </header>
    <main id="conteudo">
