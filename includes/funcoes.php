<?php
/**
 * Funções compartilhadas do CineGuia.
 *
 * Centraliza leitura de dados, filtros, formatação e persistência
 * das avaliações. As páginas e a API só chamam estas funções.
 */

declare(strict_types=1);

if (!function_exists('mb_strtolower')) {
    function mb_strtolower(string $texto): string
    {
        return strtolower($texto);
    }
}
if (!function_exists('mb_strpos')) {
    function mb_strpos(string $haystack, string $needle)
    {
        $pos = strpos($haystack, $needle);
        return $pos === false ? false : $pos;
    }
}
if (!function_exists('mb_strlen')) {
    function mb_strlen(string $texto): int
    {
        return strlen($texto);
    }
}

const CAMINHO_TITULOS = __DIR__ . '/../data/titulos.json';
const CAMINHO_AVALIACOES = __DIR__ . '/../data/avaliacoes.json';

/**
 * Lê o catálogo completo a partir do JSON.
 *
 * @return list<array<string, mixed>>
 */
function carregarTitulos(): array
{
    if (!is_readable(CAMINHO_TITULOS)) {
        return [];
    }

    $json = file_get_contents(CAMINHO_TITULOS);
    $dados = json_decode($json, true);

    return is_array($dados) ? $dados : [];
}

/**
 * Localiza um título pelo identificador (slug).
 *
 * @return array<string, mixed>|null
 */
function buscarTituloPorId(string $id): ?array
{
    foreach (carregarTitulos() as $titulo) {
        if (($titulo['id'] ?? '') === $id) {
            return $titulo;
        }
    }

    return null;
}

/**
 * Filtra e ordena o catálogo conforme os parâmetros da URL ou da API.
 *
 * Parâmetros aceitos:
 * - q: texto livre (título, diretor, elenco, sinopse)
 * - tipo: filme | serie | todos
 * - genero: nome do gênero
 * - ordenar: nota | ano | titulo
 *
 * @param array<string, mixed> $filtros
 * @return list<array<string, mixed>>
 */
function filtrarTitulos(array $filtros): array
{
    $lista = carregarTitulos();
    $busca = mb_strtolower(trim((string) ($filtros['q'] ?? '')));
    $tipo = (string) ($filtros['tipo'] ?? 'todos');
    $genero = (string) ($filtros['genero'] ?? '');
    $ordenar = (string) ($filtros['ordenar'] ?? 'nota');

    $lista = array_values(array_filter($lista, static function (array $item) use ($busca, $tipo, $genero): bool {
        if ($tipo === 'filme' || $tipo === 'serie') {
            if (($item['tipo'] ?? '') !== $tipo) {
                return false;
            }
        }

        if ($genero !== '') {
            $generos = $item['generos'] ?? [];
            if (!in_array($genero, $generos, true)) {
                return false;
            }
        }

        if ($busca === '') {
            return true;
        }

        $elenco = implode(' ', $item['elenco'] ?? []);
        $generosTexto = implode(' ', $item['generos'] ?? []);
        $haystack = mb_strtolower(
            ($item['titulo'] ?? '') . ' ' .
            ($item['diretor'] ?? '') . ' ' .
            $elenco . ' ' .
            $generosTexto . ' ' .
            ($item['sinopse'] ?? '')
        );

        return mb_strpos($haystack, $busca) !== false;
    }));

    usort($lista, static function (array $a, array $b) use ($ordenar): int {
        if ($ordenar === 'ano') {
            return (int) ($b['ano'] ?? 0) <=> (int) ($a['ano'] ?? 0);
        }
        if ($ordenar === 'titulo') {
            return strcasecmp((string) ($a['titulo'] ?? ''), (string) ($b['titulo'] ?? ''));
        }

        return ((float) ($b['nota'] ?? 0)) <=> ((float) ($a['nota'] ?? 0));
    });

    return $lista;
}

/**
 * Gêneros únicos do catálogo, em ordem alfabética.
 *
 * @return list<string>
 */
function listarGeneros(): array
{
    $generos = [];
    foreach (carregarTitulos() as $titulo) {
        foreach ($titulo['generos'] ?? [] as $genero) {
            $generos[$genero] = true;
        }
    }

    $lista = array_keys($generos);
    sort($lista, SORT_FLAG_CASE | SORT_STRING);

    return $lista;
}

/**
 * Títulos com maior nota, usados na home.
 *
 * @return list<array<string, mixed>>
 */
function titulosEmDestaque(int $quantidade = 8): array
{
    return array_slice(filtrarTitulos(['ordenar' => 'nota']), 0, $quantidade);
}

/**
 * Sugestões com pelo menos um gênero em comum, excluindo o próprio título.
 *
 * @param array<string, mixed> $atual
 * @return list<array<string, mixed>>
 */
function titulosSimilares(array $atual, int $limite = 6): array
{
    $generosAtuais = $atual['generos'] ?? [];
    $idAtual = $atual['id'] ?? '';
    $candidatos = [];

    foreach (carregarTitulos() as $item) {
        if (($item['id'] ?? '') === $idAtual) {
            continue;
        }

        $emComum = array_intersect($generosAtuais, $item['generos'] ?? []);
        if ($emComum === []) {
            continue;
        }

        $item['_afinidade'] = count($emComum);
        $candidatos[] = $item;
    }

    usort($candidatos, static function (array $a, array $b): int {
        $porAfinidade = ($b['_afinidade'] ?? 0) <=> ($a['_afinidade'] ?? 0);
        if ($porAfinidade !== 0) {
            return $porAfinidade;
        }

        return ((float) ($b['nota'] ?? 0)) <=> ((float) ($a['nota'] ?? 0));
    });

    return array_slice($candidatos, 0, $limite);
}

/**
 * Avaliações enviadas pelos usuários para um título.
 *
 * @return list<array<string, mixed>>
 */
function carregarAvaliacoes(string $id): array
{
    $todas = lerArquivoAvaliacoes();

    return $todas[$id] ?? [];
}

/**
 * Calcula a média das notas dos usuários (ou null se ainda não houver votos).
 */
function mediaAvaliacoesUsuarios(string $id): ?float
{
    $avaliacoes = carregarAvaliacoes($id);
    if ($avaliacoes === []) {
        return null;
    }

    $soma = 0.0;
    foreach ($avaliacoes as $avaliacao) {
        $soma += (float) ($avaliacao['nota'] ?? 0);
    }

    return round($soma / count($avaliacoes), 1);
}

/**
 * Grava uma avaliação no JSON. Retorna mensagem de erro ou null em caso de sucesso.
 */
function salvarAvaliacao(string $idTitulo, string $nome, int $nota, string $comentario): ?string
{
    if (buscarTituloPorId($idTitulo) === null) {
        return 'Título não encontrado.';
    }

    $nome = sanitizarTexto($nome);
    $comentario = sanitizarTexto($comentario);

    if ($nome === '' || mb_strlen($nome) < 2) {
        return 'Informe um nome com pelo menos 2 caracteres.';
    }

    if ($nota < 1 || $nota > 10) {
        return 'A nota deve ser um número inteiro de 1 a 10.';
    }

    if ($comentario === '' || mb_strlen($comentario) < 8) {
        return 'Escreva um comentário com pelo menos 8 caracteres.';
    }

    $todas = lerArquivoAvaliacoes();
    if (!isset($todas[$idTitulo])) {
        $todas[$idTitulo] = [];
    }

    array_unshift($todas[$idTitulo], [
        'nome' => $nome,
        'nota' => $nota,
        'comentario' => $comentario,
        'data' => date('Y-m-d H:i:s'),
    ]);

    $json = json_encode($todas, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    if ($json === false) {
        return 'Não foi possível gravar a avaliação.';
    }

    if (file_put_contents(CAMINHO_AVALIACOES, $json, LOCK_EX) === false) {
        return 'Sem permissão para gravar em data/avaliacoes.json.';
    }

    return null;
}

/**
 * @return array<string, list<array<string, mixed>>>
 */
function lerArquivoAvaliacoes(): array
{
    if (!is_readable(CAMINHO_AVALIACOES)) {
        return [];
    }

    $json = file_get_contents(CAMINHO_AVALIACOES);
    $dados = json_decode($json, true);

    return is_array($dados) ? $dados : [];
}

function sanitizarTexto(string $texto): string
{
    $texto = trim($texto);
    $texto = str_replace("\0", '', $texto);

    return $texto;
}

function e(string $texto): string
{
    return htmlspecialchars($texto, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function rotuloTipo(string $tipo): string
{
    return $tipo === 'serie' ? 'Série' : 'Filme';
}

function formatarVotos(int $votos): string
{
    if ($votos >= 1000000) {
        return number_format($votos / 1000000, 1, ',', '.') . ' mi';
    }
    if ($votos >= 1000) {
        return number_format($votos / 1000, 1, ',', '.') . ' mil';
    }

    return (string) $votos;
}

function urlPoster(string $id): string
{
    return 'poster.php?id=' . rawurlencode($id);
}
