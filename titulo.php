<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/funcoes.php';

$id = isset($_GET['id']) ? (string) $_GET['id'] : '';
$titulo = $id !== '' ? buscarTituloPorId($id) : null;

if ($titulo === null) {
    http_response_code(404);
    $paginaAtual = 'catalogo';
    $tituloPagina = 'Não encontrado';
    require __DIR__ . '/includes/header.php';
    echo '<section class="secao"><h1>Título não encontrado</h1><p>Esse item não existe no catálogo.</p><p><a class="botao botao-principal" href="catalogo.php">Voltar ao catálogo</a></p></section>';
    require __DIR__ . '/includes/footer.php';
    exit;
}

$avaliacoes = carregarAvaliacoes($id);
$mediaUsuarios = mediaAvaliacoesUsuarios($id);
$similares = titulosSimilares($titulo);
$erro = isset($_GET['erro']) ? (string) $_GET['erro'] : '';
$ok = isset($_GET['ok']);

$paginaAtual = ($titulo['tipo'] ?? '') === 'serie' ? 'series' : 'filmes';
$tituloPagina = (string) $titulo['titulo'];
$descricaoPagina = (string) $titulo['sinopse'];

require __DIR__ . '/includes/header.php';
?>

<article class="ficha" style="--hero-cor: <?php echo e((string) ($titulo['cor'] ?? '#1a1a1a')); ?>">
    <div class="ficha-poster">
        <img src="<?php echo e(urlPoster($id)); ?>" alt="Pôster de <?php echo e((string) $titulo['titulo']); ?>">
    </div>
    <div class="ficha-info">
        <p class="hero-selo"><?php echo e(rotuloTipo((string) $titulo['tipo'])); ?></p>
        <h1><?php echo e((string) $titulo['titulo']); ?></h1>
        <p class="hero-meta">
            <?php echo e((string) $titulo['ano']); ?>
            · <?php echo e((string) $titulo['duracao']); ?>
            · Classificação <?php echo e((string) $titulo['classificacao']); ?>
        </p>
        <div class="notas">
            <div>
                <strong>★ <?php echo e(number_format((float) $titulo['nota'], 1, ',', '')); ?></strong>
                <span>Nota editorial</span>
                <small><?php echo e(formatarVotos((int) $titulo['votos'])); ?> votos</small>
            </div>
            <div>
                <strong><?php echo $mediaUsuarios === null ? '—' : '★ ' . e(number_format($mediaUsuarios, 1, ',', '')); ?></strong>
                <span>Nota dos usuários</span>
                <small><?php echo count($avaliacoes); ?> avaliação<?php echo count($avaliacoes) === 1 ? '' : 'ões'; ?></small>
            </div>
        </div>
        <p class="generos-lista">
            <?php foreach ($titulo['generos'] as $genero) : ?>
                <a href="catalogo.php?genero=<?php echo e((string) $genero); ?>"><?php echo e((string) $genero); ?></a>
            <?php endforeach; ?>
        </p>
        <p class="sinopse"><?php echo e((string) $titulo['sinopse']); ?></p>
        <dl class="creditos">
            <div>
                <dt>Direção / criação</dt>
                <dd><?php echo e((string) $titulo['diretor']); ?></dd>
            </div>
            <div>
                <dt>Elenco</dt>
                <dd><?php echo e(implode(', ', $titulo['elenco'] ?? [])); ?></dd>
            </div>
        </dl>
        <button
            type="button"
            class="botao botao-secundario"
            data-lista-id="<?php echo e($id); ?>"
            aria-pressed="false"
        >+ Minha lista</button>
    </div>
</article>

<section class="secao">
    <h2>Avaliações da comunidade</h2>

    <?php if ($ok) : ?>
        <p class="alerta sucesso" role="status">Avaliação publicada com sucesso.</p>
    <?php endif; ?>
    <?php if ($erro !== '') : ?>
        <p class="alerta erro" role="alert"><?php echo e($erro); ?></p>
    <?php endif; ?>

    <form class="form-avaliacao" action="api/avaliacoes.php" method="post">
        <input type="hidden" name="id" value="<?php echo e($id); ?>">
        <div class="campo">
            <label for="nome">Seu nome</label>
            <input id="nome" name="nome" type="text" required minlength="2" maxlength="40">
        </div>
        <div class="campo">
            <label for="nota">Nota (1 a 10)</label>
            <input id="nota" name="nota" type="number" min="1" max="10" step="1" required>
        </div>
        <div class="campo campo-largo">
            <label for="comentario">Comentário</label>
            <textarea id="comentario" name="comentario" rows="4" required minlength="8" maxlength="400" placeholder="O que achou deste título?"></textarea>
        </div>
        <button class="botao botao-principal" type="submit">Publicar avaliação</button>
    </form>

    <?php if ($avaliacoes === []) : ?>
        <p class="vazio">Ainda não há avaliações. Seja o primeiro a comentar.</p>
    <?php else : ?>
        <ul class="lista-avaliacoes">
            <?php foreach ($avaliacoes as $avaliacao) : ?>
                <li>
                    <header>
                        <strong><?php echo e((string) $avaliacao['nome']); ?></strong>
                        <span>★ <?php echo e((string) $avaliacao['nota']); ?>/10</span>
                        <time datetime="<?php echo e((string) $avaliacao['data']); ?>">
                            <?php echo e((string) $avaliacao['data']); ?>
                        </time>
                    </header>
                    <p><?php echo e((string) $avaliacao['comentario']); ?></p>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</section>

<?php if ($similares !== []) : ?>
<section class="secao">
    <div class="secao-cabecalho">
        <h2>Títulos semelhantes</h2>
    </div>
    <div class="grade">
        <?php foreach ($similares as $titulo) {
            include __DIR__ . '/includes/cartao.php';
        } ?>
    </div>
</section>
<?php endif; ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
