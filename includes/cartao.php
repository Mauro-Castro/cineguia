<?php
/**
 * Cartão reutilizável de filme/série.
 *
 * @var array<string, mixed> $titulo
 */
declare(strict_types=1);

if (!isset($titulo) || !is_array($titulo)) {
    return;
}

$id = (string) ($titulo['id'] ?? '');
$nome = (string) ($titulo['titulo'] ?? 'Sem título');
$tipo = rotuloTipo((string) ($titulo['tipo'] ?? 'filme'));
$ano = (int) ($titulo['ano'] ?? 0);
$nota = number_format((float) ($titulo['nota'] ?? 0), 1, ',', '');
$generos = implode(' · ', $titulo['generos'] ?? []);
?>
<article class="cartao">
    <a class="cartao-link" href="titulo.php?id=<?php echo e($id); ?>">
        <div class="cartao-poster">
            <img src="<?php echo e(urlPoster($id)); ?>" alt="Pôster de <?php echo e($nome); ?>" width="300" height="450">
            <span class="selo-nota" title="Nota editorial">★ <?php echo e($nota); ?></span>
        </div>
        <div class="cartao-corpo">
            <h3><?php echo e($nome); ?></h3>
            <p class="cartao-meta"><?php echo e($tipo); ?> · <?php echo e((string) $ano); ?></p>
            <p class="cartao-generos"><?php echo e($generos); ?></p>
        </div>
    </a>
    <button
        type="button"
        class="botao-lista"
        data-lista-id="<?php echo e($id); ?>"
        aria-pressed="false"
    >
        + Lista
    </button>
</article>
