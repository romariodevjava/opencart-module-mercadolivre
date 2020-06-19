[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%207.3.0-blue.svg?style=flat-square)](https://php.net/)[![License MIT](https://img.shields.io/github/license/romariodevjava/opencart-module-mercadolivre?style=flat-square)](LICENSE.md)


# Módulo MercadoLivre Para OpenCart

O desenvolvimento desde módulo é para uso da comunidade, peço a gentileza que contribuam com a solução dos bugs e ajustes para melhorar o código. 

Vamos trabalhar para ter mais módulos OpenSource, já que a Plataforma nasceu com este propósito!

# Instalando
1. Faça o download da ultima versão de release
2. Abra o seu admin do OpenCart -> Extensões -> Instalador -> Faça o upload do arquivo baixado
3. Atualize as modificações. Abra o seu admin do OpenCart -> Extensões -> Modificações -> Atualizar
4. Limpe o Cache do template. Abra o seu admin do OpenCart -> Dashboard -> Atualizar Cache do Tema
5. Configure o Módulo do Mercado Livre em Extensões -> Extensões -> Selecione Módulos -> Instale o módulo e depois edite para configura-lo
6. Abrir o Menu Mercado Livre Extensão -> Fazer Autenticação -> Clique no login para fazer a autenticação do seu usuário do Mercado Livre. Tem que usar o usuário admin da conta, não pode ser conta de colaborador.

**Obs.: Na tela da Autenticação, tem a url para colocar no aplicativo como redirecionamento.**

Contribuições
-------------

Achou e corrigiu um bug ou tem alguma feature em mente e deseja contribuir?

* Faça um fork
* Adicione sua feature ou correção de bug (git checkout -b my-new-feature)
* Commit suas mudanças (git commit -am 'Added some feature')
* Rode um push para o branch (git push origin my-new-feature)
* Envie um Pull Request
* Obs.: Adicione exemplos para sua nova feature. Se seu Pull Request for relacionado a uma versão específica, o Pull Request não deve ser enviado para o branch master e sim para o branch correspondente a versão.

# Autores

* **José Romário** - *Initial work* - [romariodevjava](https://github.com/romariodevjava)

# Contribuem
[![License MIT](https://stc.pagseguro.uol.com.br/public/img/botoes/doacoes/209x48-doar-assina.gif)](https://pagseguro.uol.com.br/checkout/v2/donation.html?receiverEmail=romario2009142009@hotmail.com&currency=BRL)

# Requerimentos para instalar o módulo
* PHP 7.3 ou Superior
* OpenCart 3.0.2 ou superior
* Ocmod OpenCart


# Features do módulo
- [X] Configuração
- [X] Exportar produtos para o Mercado Livre - **Em testes**
- [ ] Atualizar os produtos e variações a cada edição no produto
- [ ] Desativar e excluir produto no Mercado Livre, quando excluído no OpenCart
- [ ] Notificar as perguntas no OpenCart e ter a opção de responder
- [ ] Notificar e exibir pedidos do Mercado Livre no OpenCart
- [X] Mapeamento das categorias do OpenCart x Mercado livre
- [X] Tela de logs para acompanhar quaisquer erros.

## License

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details
