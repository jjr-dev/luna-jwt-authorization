# JWT Authorization

Autorização com JsonWebToken em Luna Framework entre PHP e JavaScript.

## Banco de dados

```sql
CREATE TABLE users( id INT NOT NULL AUTO_INCREMENT, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY (id));

INSERT INTO `users` (`id`, `email`, `password`, `created_at`, `updated_at`) VALUES (NULL, 'email@exemplo.com', '$2y$10$CrYJPMYz9WHJo78DdXqGFO80GVbJhiv2SrGI3S0i4VciUhQIMrpW.', '2023-09-11 22:06:37.000000', '2023-09-11 22:06:37.000000');
```

## Como funciona

O acesso pode ser realizado com o email `email@exemplo.com` e senha `12345678`.

Ao clicar em **Acessar** o sistema irá gerar um **JWT** de Autorização que será armazenado na `$_SESSION` do **PHP** e na `sessionStorage` do **JavaScript**.

Para testar se a autorização é válida e obter o token de autorização atual, clique em **Verificar sessão**. O retorno em um `alert()` conterá o token válido de acesso. Caso o token de autorização `authorization_token` esteja expirado, será gerado um novo token a partir do token de recriação (`refresh_token`), desde que o mesmo seja válido.

Todas as requisições que utilizarem o **Middleware** `authorization-web` ou `authorization-api` irão armazenar em `$request->user_id` (ou `$req->user_id`) o ID do usuário que gerou o token de autorização.

Para testar o bloqueio de páginas sem autorização, clique em **Acessar página restrita**.

O tempo de expiração do token de autorização é de `3600` segundos e o token de recriação possui `7` dias (`7 * 24 * 3600`). Ambos podem ser alterados em `app/Services/User.php`
