# Play GTA 5 — execução local

Este projeto executa uma versão web do jogo em um container Docker. Apache
serve os arquivos estáticos e PHP implementa o endpoint de leitura necessário
pelo frontend.

Os procedimentos abaixo são os mesmos no Windows, Linux e macOS. Você só
precisa ter Docker instalado e executar os comandos a partir da pasta deste
projeto.

## Aviso sobre os assets

A pasta `mirror/playgta5.com` não é distribuída neste projeto. Ela pode conter
arquivos protegidos por direitos autorais, e não sou o responsável por esse
conteúdo: apenas utilizei os arquivos que estavam disponíveis para fazer esta
reescrita. Não conheço a procedência ou o conteúdo detalhado de cada arquivo;
sei apenas que os arquivos presentes funcionam com esta implementação.

Obtenha os assets por sua própria conta e responsabilidade, de uma fonte que
você tenha autorização para usar. Não solicite, redistribua ou inclua essa
pasta em cópias públicas deste projeto. Consulte [`docs/ASSETS.md`](./docs/ASSETS.md)
para identificação e verificação.

## Pré-requisitos

- Docker Desktop no Windows/macOS ou Docker Engine no Linux;
- Docker Compose v2, normalmente disponível como `docker compose`;
- os assets colocados em `mirror/playgta5.com/`;
- porta `8081` disponível no computador.

Verifique a instalação com:

```text
docker --version
docker compose version
```

Se esses dois comandos funcionarem, não é necessário instalar PHP, Apache,
Python, Node.js ou qualquer outra dependência no computador host.

Se os assets não estiverem montados, o container encerra com uma mensagem de
erro em vez de iniciar um servidor vazio.

## Estrutura necessária

Antes de iniciar, a pasta deve estar assim:

```text
rewritten/
├── docker-compose.yml
├── public/
└── mirror/
    └── playgta5.com/
        ├── index.html
        ├── b/
        └── data/
```

O conteúdo de `mirror/playgta5.com/` não faz parte deste repositório e precisa
ser obtido separadamente.

## Iniciar

Abra qualquer terminal — PowerShell, Prompt de Comando, shell Linux ou Terminal
do macOS — e entre na pasta `rewritten` deste projeto. Por exemplo:

```text
cd caminho/para/rewritten
```

Execute:

```text
docker compose up --build
```

O primeiro início pode demorar porque o Docker precisa baixar a imagem
`php:8.4-apache` e construir a aplicação. Depois, abra no navegador:

```text
http://localhost:8081/
```

O servidor fica publicado somente em `127.0.0.1` por padrão.

Para iniciar em segundo plano:

```text
docker compose up --build --detach
```

## Parar o servidor

Se o Compose estiver em primeiro plano, pressione `Ctrl+C`. Caso esteja
executando em segundo plano, use:

```text
docker compose down
```

## Desenvolvimento e reconstrução

As pastas `public/` e `mirror/playgta5.com/` são montadas como volumes
somente leitura. Alterações nos arquivos públicos ficam disponíveis sem
rebuild da imagem. Atualize a página no navegador para carregar as alterações.

Se alterar o `Dockerfile`, o `docker-compose.yml`, o
`apache-vhost.conf` ou o script de entrada, reconstrua e reinicie:

```text
docker compose up --build
```

Para ver os logs do serviço:

```text
docker compose logs --follow
```

Para verificar o estado dos containers:

```text
docker compose ps
```

## Problemas comuns

### Container encerra informando que os assets estão ausentes

Confirme que estes caminhos existem, relativos à pasta do projeto:

```text
mirror/playgta5.com/index.html
mirror/playgta5.com/b/
mirror/playgta5.com/data/
```

O container valida esses itens antes de iniciar o Apache e encerra com uma
mensagem explícita se algum estiver faltando.

### Porta 8081 já está em uso

Pare o processo que está usando a porta ou altere o mapeamento em
`docker-compose.yml`. Por exemplo, para acessar pela porta `8082`:

```yaml
ports:
  - "127.0.0.1:8082:80"
```

Depois, use:

```text
http://localhost:8082/
```

### O navegador mostra uma versão antiga

Faça uma recarga completa da página e confirme que está acessando a porta
correta. O cache persistente dos dados do jogo é explicado em
[`docs/CLIENT-CACHE.md`](./docs/CLIENT-CACHE.md).

## Remover containers e imagem

Para remover os containers e a rede criados pelo Compose:

```text
docker compose down
```

Para remover também a imagem construída localmente, liste as imagens e remova
a correspondente ao projeto usando os comandos do Docker. Não remova a pasta
`mirror/playgta5.com/`, pois ela é um volume local dos assets.

Para a especificação da arquitetura e dos endpoints, consulte a pasta
[`docs/`](./docs/).
