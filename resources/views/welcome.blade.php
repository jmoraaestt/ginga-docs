<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <x-ginga::styles />
</head>
    <body class="p-4">
    <h1>Teste</h1>
    <x-ginga::input name="nome" label="Nome" required />
    <x-ginga::cpf name="cpf" required />
    <x-ginga::telefone name="celular" />
    <x-ginga::cep name="cep" :preencher="['logradouro' => 'endereco', 'localidade' => 'cidade', 'uf' => 'uf']" />
    <x-ginga::button type="submit">Salvar</x-ginga::button>
</body>
<x-ginga::scripts/>
</html>