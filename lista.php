<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/funcoes.php';

$paginaAtual = 'lista';
$tituloPagina = 'Minha lista';
$descricaoPagina = 'Filmes e séries salvos neste navegador.';

$catalogo = carregarTitulos();

require __DIR__ . '/includes/header.php';
?>

<section class="secao">
    <header class="pagina-cabecalho">
        <h1>Minha lista</h1>
        <p>Os itens ficam salvos apenas neste navegador (localStorage). Nada é enviado ao servidor.</p>
    </header>
    <p class="contador" data-lista-contador>Carregando lista...</p>
    <div class="grade" data-minha-lista></div>
</section>

<script type="application/json" id="catalogo-json"><?php echo json_encode($catalogo, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?></script>

<?php require __DIR__ . '/includes/footer.php'; ?>
