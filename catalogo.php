<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/funcoes.php';

$filtros = [
    'q' => isset($_GET['q']) ? trim((string) $_GET['q']) : '',
    'tipo' => isset($_GET['tipo']) ? (string) $_GET['tipo'] : 'todos',
    'genero' => isset($_GET['genero']) ? (string) $_GET['genero'] : '',
    'ordenar' => isset($_GET['ordenar']) ? (string) $_GET['ordenar'] : 'nota',
];

if (!in_array($filtros['tipo'], ['todos', 'filme', 'serie'], true)) {
    $filtros['tipo'] = 'todos';
}
if (!in_array($filtros['ordenar'], ['nota', 'ano', 'titulo'], true)) {
    $filtros['ordenar'] = 'nota';
}

$resultados = filtrarTitulos($filtros);
$generos = listarGeneros();

$paginaAtual = 'catalogo';
if ($filtros['tipo'] === 'filme') {
    $paginaAtual = 'filmes';
} elseif ($filtros['tipo'] === 'serie') {
    $paginaAtual = 'series';
}

$tituloPagina = 'Catálogo';
$descricaoPagina = 'Busque e filtre filmes e séries do CineGuia.';

require __DIR__ . '/includes/header.php';
?>

<section class="secao secao-catalogo">
    <header class="pagina-cabecalho">
        <h1>Catálogo</h1>
        <p>Use os filtros ou a busca. Sem JavaScript, o formulário ainda funciona via PHP.</p>
    </header>

    <form class="filtros" method="get" action="catalogo.php" data-filtros>
        <div class="campo">
            <label for="filtro-q">Busca</label>
            <input id="filtro-q" type="search" name="q" value="<?php echo e($filtros['q']); ?>" placeholder="Título, diretor, ator...">
        </div>
        <div class="campo">
            <label for="filtro-tipo">Tipo</label>
            <select id="filtro-tipo" name="tipo">
                <option value="todos" <?php echo $filtros['tipo'] === 'todos' ? 'selected' : ''; ?>>Todos</option>
                <option value="filme" <?php echo $filtros['tipo'] === 'filme' ? 'selected' : ''; ?>>Filmes</option>
                <option value="serie" <?php echo $filtros['tipo'] === 'serie' ? 'selected' : ''; ?>>Séries</option>
            </select>
        </div>
        <div class="campo">
            <label for="filtro-genero">Gênero</label>
            <select id="filtro-genero" name="genero">
                <option value="">Todos os gêneros</option>
                <?php foreach ($generos as $genero) : ?>
                    <option value="<?php echo e($genero); ?>" <?php echo $filtros['genero'] === $genero ? 'selected' : ''; ?>>
                        <?php echo e($genero); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="campo">
            <label for="filtro-ordenar">Ordenar</label>
            <select id="filtro-ordenar" name="ordenar">
                <option value="nota" <?php echo $filtros['ordenar'] === 'nota' ? 'selected' : ''; ?>>Melhor nota</option>
                <option value="ano" <?php echo $filtros['ordenar'] === 'ano' ? 'selected' : ''; ?>>Mais recentes</option>
                <option value="titulo" <?php echo $filtros['ordenar'] === 'titulo' ? 'selected' : ''; ?>>A–Z</option>
            </select>
        </div>
        <div class="campo campo-botao">
            <button class="botao botao-principal" type="submit">Aplicar</button>
        </div>
    </form>

    <p class="contador" data-contador>
        <?php echo count($resultados); ?>
        resultado<?php echo count($resultados) === 1 ? '' : 's'; ?>
    </p>

    <div class="grade" data-grade-catalogo>
        <?php if ($resultados === []) : ?>
            <p class="vazio">Nenhum título encontrado. Tente outro termo ou limpe os filtros.</p>
        <?php else : ?>
            <?php foreach ($resultados as $titulo) {
                require __DIR__ . '/includes/cartao.php';
            } ?>
        <?php endif; ?>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
