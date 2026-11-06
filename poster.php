<?php
/**
 * Gera um pôster SVG a partir dos dados do título.
 * Assim o projeto funciona sem depender de imagens externas.
 */
declare(strict_types=1);

require_once __DIR__ . '/includes/funcoes.php';

$id = isset($_GET['id']) ? (string) $_GET['id'] : '';
$titulo = $id !== '' ? buscarTituloPorId($id) : null;

if ($titulo === null) {
    http_response_code(404);
    header('Content-Type: image/svg+xml; charset=UTF-8');
    echo '<svg xmlns="http://www.w3.org/2000/svg" width="400" height="600"><rect width="100%" height="100%" fill="#1a1a1a"/><text x="50%" y="50%" fill="#f5c518" text-anchor="middle" font-family="Arial" font-size="22">CineGuia</text></svg>';
    exit;
}

$nome = (string) $titulo['titulo'];
$ano = (string) ($titulo['ano'] ?? '');
$tipo = rotuloTipo((string) ($titulo['tipo'] ?? 'filme'));
$cor = (string) ($titulo['cor'] ?? '#1a1a1a');
$nota = number_format((float) ($titulo['nota'] ?? 0), 1);

$linhas = [];
$palavras = preg_split('/\s+/', $nome) ?: [];
$linhaAtual = '';
foreach ($palavras as $palavra) {
    $teste = trim($linhaAtual . ' ' . $palavra);
    if (mb_strlen($teste) > 16 && $linhaAtual !== '') {
        $linhas[] = $linhaAtual;
        $linhaAtual = $palavra;
    } else {
        $linhaAtual = $teste;
    }
}
if ($linhaAtual !== '') {
    $linhas[] = $linhaAtual;
}
$linhas = array_slice($linhas, 0, 4);

header('Content-Type: image/svg+xml; charset=UTF-8');
header('Cache-Control: public, max-age=86400');

$yTitulo = 250;
?>
<svg xmlns="http://www.w3.org/2000/svg" width="400" height="600" viewBox="0 0 400 600">
    <defs>
        <linearGradient id="fundo" x1="0" y1="0" x2="1" y2="1">
            <stop offset="0%" stop-color="<?php echo e($cor); ?>"/>
            <stop offset="100%" stop-color="#050505"/>
        </linearGradient>
    </defs>
    <rect width="400" height="600" fill="url(#fundo)"/>
    <rect x="18" y="18" width="364" height="564" fill="none" stroke="#f5c518" stroke-opacity="0.55" stroke-width="2"/>
    <text x="32" y="70" fill="#f5c518" font-family="Arial, Helvetica, sans-serif" font-size="18" font-weight="700">CINEGUIA</text>
    <text x="32" y="100" fill="#ffffff" font-family="Arial, Helvetica, sans-serif" font-size="16" opacity="0.8"><?php echo e($tipo); ?> · <?php echo e($ano); ?></text>
<?php foreach ($linhas as $indice => $linha) : ?>
    <text x="32" y="<?php echo (int) ($yTitulo + $indice * 42); ?>" fill="#ffffff" font-family="Arial, Helvetica, sans-serif" font-size="32" font-weight="700"><?php echo e($linha); ?></text>
<?php endforeach; ?>
    <text x="32" y="540" fill="#f5c518" font-family="Arial, Helvetica, sans-serif" font-size="28" font-weight="700">★ <?php echo e($nota); ?></text>
</svg>
