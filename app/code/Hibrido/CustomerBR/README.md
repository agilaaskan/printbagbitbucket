# Hibrido Customer BR

Este módulo realiza várias adaptações para deixar a plataforma Magento Open Source mais condizente com o cenário Brasileiro.

## Features
Todas as features comentadas abaixo estarão disponíveis nas telas a seguir (de acordo com seus encaixes):

* Criar nova conta
* Editar conta (minha conta)
* Adicionar novo endereço (minha conta)
* Editar endereço (minha conta)
* Checkout shipping-step
* Checkout shipping-step (floater)
* Checkout payment-step

Abaixo a lista de features:

* Adiciona o campo "Tipo de Pessoa" no Customer;
* Adiciona o campo "Inscrição Estadual" no Customer;
* Adiciona a máscara em JS no campo de CPF/CNPJ (Taxvat), conforme o "Tipo de Pessoa" escolhido;
* Adiciona a mudança das labels dos nomes conforme tipo de pessoa escolhido (Nome = Razão Social / Sobrenome = Nome Fantasia);
* Adiciona o auto-complete do endereço conforme o CEP;
* Adiciona o preenchimento do campo de Complemento (terceiro campo do street) para "N/A" caso vazio;
* Adiciona o CEP no estilo BR (com hífen no meio);
* Adiciona as labels corretas para os campos de endereço (Rua, Número, Complemento, Bairro);
* Adiciona o campo de CEP acima dos campos de endereço;
* Adiciona validação em todos as linhas do campo street;

## Requerimentos
* (Magento OS) Para que o campo de Tax/VAT apareça corretamente, precisamos habilita-lo em: STORES > SETTINGS > CONFIGURATION > CUSTOMERS > CUSTOMER CONFIGURATION > NAME AND ADDRESS OPTIONS > SHOW TAX/VAT NUMBER;
* (Magento OS) Para que os 4 campos de endereço apareçam, precisamos habilita-los em (colocar 4): STORES > CONFIGURATION > CUSTOMERS > CUSTOMER CONFIGURATION > NAME AND ADDRESS OPTIONS > NUMBER OF LINES IN A STREET ADDRESS
* Versões 2.3.6 e 2.4.1 precisam de um patch para liberar os campos após uma validação ter dado erro. (ver: https://support.magento.com/hc/en-us/articles/360051130212-2-4-1-and-2-3-6-create-an-account-button-disabled-hotfix)

## Escolhas
Durante o desenvolvimento fizemos algumas escolhas técnicas que merecem ser comentadas, segue abaixo:

* Para adicionar os campos "Tipo de Pessoa" e "State Registration", nós utilizamos Plugins que adicionam os campos após o widget do campo Tax/VAT ao invés de substituir o template todo, dessa forma conseguimos adicionar os campos de uma forma menos intrusiva. Quando a instalação do Magento for Commerce que vem com o módulo de edição de atributos dos Customers, nós identificamos se esse módulo está ativo e em caso positivo nós simplesmente não adicionamos os nossos templates, pois, esse módulo nativo do Magento Commerce já realiza isso por nós;
* Para adicionar as labels nos campos de endereço, nós resolvemos mudar as labels (que já existem e são escondidas pelo tema LUMA) por JS e simplesmente mostra-las depois disso;
* Para a sequência dos campos de endereço nós deixamos em: Logradouro, Número, Complemento e Bairro, pois, a maioria dos métodos de pagamentos brasileiros segue essa sequência;
* Para modificar a posição do campo de CEP, nós simpĺesmente modificamos o sort_order do campo postcode, isso mudou a posição do campo no checkout, porém, para mudar a posição do campo no form de endereço do minha conta, tivemos que substituir o template mesmo, visto que o mesmo está hardcoded não respeitando o sort_order do banco;
* Para trocar a posição do campo de CEP e adicionar validação nos campos de street no form de endereço do minha conta, nós realizamos isso por JS, pois, dessa forma além de não precisar substituir o template, o mesmo funciona normalmente para as duas versões do Magento;