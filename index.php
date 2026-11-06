<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/funcoes.php';

$paginaAtual = 'inicio';
$tituloPagina = 'Início';
$descricaoPagina = 'Descubra filmes e séries bem avaliados no catálogo CineGuia.';

$destaques = titulosEmDestaque(8);
$filmes = array_slice(filtrarTitulos(['tipo' => 'filme', 'ordenar' => 'nota']), 0, 6);
$series = array_slice(filtrarTitulos(['tipo' => 'serie', 'ordenar' => 'nota']), 0, 6);
$hero = $destaques[0] ?? null;
$recentes = array_slice(filtrarTitulos(['ordenar' => 'ano']), 0, 6);

require __DIR__ . '/includes/header.php';
?>

<?php if ($hero) : ?>
<section class="hero" style="--hero-cor: <?php echo e((string) ($hero['cor'] ?? '#1a1a1a')); ?>">
    <div class="hero-conteudo">
        <p class="hero-selo">Em destaque</p>
        <h1><?php echo e((string) $hero['titulo']); ?></h1>
        <p class="hero-meta">
            <?php echo e(rotuloTipo((string) $hero['tipo'])); ?>
            · <?php echo e((string) $hero['ano']); ?>
            · <?php echo e((string) $hero['duracao']); ?>
            · Classificação <?php echo e((string) $hero['classificacao']); ?>
        </p>
        <p class="hero-nota">★ <?php echo e(number_format((float) $hero['nota'], 1, ',', '')); ?>
            <span>(<?php echo e(formatarVotos((int) $hero['votos'])); ?> votos)</span>
        </p>
        <p class="hero-sinopse"><?php echo e((string) $hero['sinopse']); ?></p>
        <div class="hero-acoes">
            <a class="botao botao-principal" href="titulo.php?id=<?php echo e((string) $hero['id']); ?>">Ver detalhes</a>
            <a class="botao botao-secundario" href="catalogo.php">Explorar catálogo</a>
        </div>
    </div>
    <div class="hero-poster">
        <img src="<?php echo e(urlPoster((string) $hero['id'])); ?>" alt="Pôster de <?php echo e((string) $hero['titulo']); ?>">
    </div>
</section>
<?php endif; ?>

<section class="secao">
    <div class="secao-cabecalho">
        <h2>Mais bem avaliados</h2>
        <a href="catalogo.php?ordenar=nota">Ver todos</a>
    </div>
    <div class="grade">
        <?php foreach ($destaques as $titulo) {
            require __DIR__ . '/includes/cartao.php';
        } ?>
    </div>
</section>

<section class="secao">
    <div class="secao-cabecalho">
        <h2>Filmes</h2>
        <a href="catalogo.php?tipo=filme">Ver filmes</a>
    </div>
    <div class="grade">
        <?php foreach ($filmes as $titulo) {
            require __DIR__ . '/includes/cartao.php';
        } ?>
    </div>
</section>

<section class="secao">
    <div class="secao-cabecalho">
        <h2>Séries</h2>
        <a href="catalogo.php?tipo=serie">Ver séries</a>
    </div>
    <div class="grade">
        <?php foreach ($series as $titulo) {
            require __DIR__ . '/includes/cartao.php';
        } ?>
    </div>
</section>

<section class="secao">
    <div class="secao-cabecalho">
        <h2>Lançamentos do catálogo</h2>
        <a href="catalogo.php?ordenar=ano">Ordenar por ano</a>
    </div>
    <div class="grade">
        <?php foreach ($recentes as $titulo) {
            require __DIR__ . '/includes/cartao.php';
        } ?>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
