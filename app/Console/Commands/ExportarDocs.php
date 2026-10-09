<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class ExportarDocs extends Command
{
    protected $signature = 'docs:exportar';
    protected $description = 'Gera a documentação estática na pasta docs/';

    // Toda página nova da documentação entra nesta lista
    protected array $paginas = [
        '/',
    ];

    // Arquivos servidos por rotas (ex.: o tema do Ginga)
    protected array $arquivos = [
        '/_ginga/tema.css',
    ];

    public function handle(Kernel $kernel): int
    {
        $destino = base_path('docs');

        File::deleteDirectory($destino);
        File::copyDirectory(public_path(), $destino);
        File::delete($destino . '/index.php');
        File::put($destino . '/.nojekyll', '');

        foreach ($this->paginas as $uri) {
            $this->salvar($kernel, $uri, rtrim($destino . $uri, '/') . '/index.html');
        }

        foreach ($this->arquivos as $uri) {
            $this->salvar($kernel, $uri, $destino . $uri);
        }

        $this->info('Documentação exportada em docs/');

        return self::SUCCESS;
    }

    protected function salvar(Kernel $kernel, string $uri, string $arquivo): void
    {
        $resposta = $kernel->handle($this->requisicao($uri));

        if (! $resposta->isOk()) {
            $this->error("Falhou: {$uri} ({$resposta->getStatusCode()})");
            return;
        }

        File::ensureDirectoryExists(dirname($arquivo));
        File::put($arquivo, $resposta->getContent());
        $this->line("  {$uri}");
    }

    // Simula uma visita como se o site estivesse em /ginga-docs
    protected function requisicao(string $uri): Request
    {
        $url = rtrim(config('app.url'), '/');
        $base = parse_url($url, PHP_URL_PATH) ?? '';

        return Request::create($url . $uri, server: [
            'SCRIPT_NAME' => $base . '/index.php',
            'SCRIPT_FILENAME' => public_path('index.php'),
        ]);
    }
}