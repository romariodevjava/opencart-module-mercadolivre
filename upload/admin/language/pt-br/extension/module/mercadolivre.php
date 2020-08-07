<?php
// Heading
$_['heading_title']                          = 'Integração Mercado Livre';
$_['heading_title_authentication']           = 'Autenticação da Aplicação no Mercado Livre';
$_['heading_title_products']                 = 'Gerenciamento de Produtos no Mercado Livre';
$_['heading_title_logs']                     = 'Logs do Mercado Livre';
$_['heading_title_questions']                = 'Perguntas do Mercado Livre';
$_['heading_title_orders']                   = 'Pedidos do Mercado Livre';
$_['heading_title_dashboard']                = 'Dashboard do Mercado Livre';

// Text
$_['text_extension']          = 'Extensões';
$_['text_success']            = 'Módulo modificado com sucesso!';
$_['text_edit']               = 'Editando Módulo';
$_['text_config']             = 'Configurações';

// Entry

$_['entry_status']                     = 'Situação';
$_['entry_app_id']                     = 'App Id do Mercado Livre';
$_['entry_app_secret']                 = 'App Secret do Mercado Livre';
$_['entry_country']                    = 'País de uso do Mercado Livre';
$_['entry_condition']                  = 'Condição do produto';
$_['entry_currency']                   = 'Moeda utilizada';
$_['entry_ad_type']                    = 'Tipo de anúncio para publicar os produtos';
$_['entry_option_value']               = 'Variação do tipo escolhido';
$_['entry_select']                     = 'Selecione uma opção';
$_['entry_select_product']             = 'Selecione pelo menos um produto';
$_['entry_select_left']                = 'Selecione uma opção a esquerda';
$_['entry_select_country']             = 'Selecione um país primeiro';
$_['entry_select_category']            = 'Deseja selecionar uma categoria?';
$_['entry_buying_mode']                = 'Modo de compra';
$_['entry_buy_it_now']                 = 'Compre agora';
$_['entry_classified']                 = 'Classificados';
$_['entry_price_adjustment']           = 'Ajustes do preço antes de publicar';
$_['entry_auction']                    = 'Leilão';
$_['entry_new']                        = 'Novo';
$_['entry_used']                       = 'Usado';
$_['entry_not_specified']              = 'Não especificado';
$_['entry_auto_detect_category']       = 'Identificar automaticamente a categoria do produto?';
$_['entry_consider_special_price']     = 'Considerar preço especial, se disponível?';
$_['entry_feedback_enabled']           = 'Ativar Mensagens Pos Venda?';
$_['entry_feedback_message']           = 'Mensagem';
$_['entry_shipping_type']              = 'Tipo de envio';
$_['entry_with_local_pick_up']         = 'Com retirada no local?';
$_['entry_shipping_free']              = 'Com frete grátis?';
$_['entry_template_title']             = 'Modelo do título da publicação';
$_['entry_template_description']       = 'Modelo da descrição da publicação';
$_['entry_template_image_additional']  = 'Imagem adicional';
$_['entry_product_warranty']           = 'Garantia do produto';
$_['entry_product_warranty_unit']      = 'Unidade de medida da garantia';
$_['entry_product_warranty_type']      = 'Tipo de Garantia do produto';
$_['warranty_of_seller']               = 'Garantia do vendedor';
$_['warranty_of_factory']              = 'Garantia de fábrica';
$_['without_warranty']                 = 'Sem Garantia';
$_['entry_month']                      = 'meses';
$_['entry_days']                       = 'dias';
$_['entry_years']                      = 'anos';
$_['entry_subtract_product']           = 'Subtrair estoque ao realizar uma venda no ML?';
$_['entry_answered']                   = 'Respondidas';
$_['entry_no_reply']                   = 'Sem responder';
$_['entry_expiration_date']            = 'Data de expiração';
$_['entry_creation_date']              = 'Data de criação';
$_['entry_questions']                  = 'Perguntas';
$_['entry_variations']                 = 'Variações';
$_['entry_product_name']               = 'Nome do produto';
$_['entry_product_connected']          = 'Somente produtos conectado?';
$_['entry_without_product_related']    = 'Sem produto relacionado';
$_['entry_subtract_product']           = 'Subtrair estoque ao realizar uma venda no ML?';



//Helpers
$_['help_auto_detect_category']        = 'Faz a atribuição automática da categoria do produto no Mercado Livre pelo título';
$_['help_app_secret']                  = 'App Secret da aplicação criada no Mercado Livre';
$_['help_shipping_free']               = 'Ofereça frete grátis aos seus compradores com o mercado envios, veja as políticas do mercado envio';
$_['help_price_adjustment']            = "Ajuste de preços antes de enviar para o Mercado Livre, poder ser em unidades(moeda) ou porcentagem(%), Exemplo: 20.00 ou 5%. Tambem pode ser usado condições como: '>500:5%;>600:5%+5;<500:5;>=500:200+5%', onde cada ';' é uma condição, '>' valor do produto maior que o valor especificado, '<' valor do produto menor que o valor especificado, '>=' maior ou igual ao valor do produto, '<=' menor ou igual ao valor do produto, '+' soma dois ou mais valores em cima do valor do produto, '-' subtrai dois ou mais valores em cima do valor do produto";
$_['help_template_title']              = "Máximo de 60 caracteres. chaves reservadas disponiveís __TITLE__ , __MODEL__ , __SKU__ , __BRAND__ , __ISBN__ and __MPN__";
$_['help_template_description']        = "chaves reservadas disponiveís __TITLE__ , __DESCRIPTION__ , __MODEL__ , __SKU__ , __BRAND__ , __ISBN__ e __MPN__";
$_['help_image_additional']            = "Imagem a ser adicionada por último no produto";
$_['help_category_for_ads']            = "Deseja aplicar uma categoria para todos os produtos selecionados, basta selecionar, caso contrário, a categoria será escolhida de acordo com as configurações";

$_['authentication_url']                           = '%smodule_mercadolivre';

// Text
$_['text_yes']                                     = 'Sim';
$_['text_no']                                      = 'Não';
$_['text_without_category']                        = 'Sem categoria';
$_['text_verified_configurations']                 = 'Por favor verifique as configurações antes de efetuar esta operação.';
$_['warning_about_application_uri_redirect']       = 'Em sua aplicação você deve configurar a URI de redirecionado de acordo com a URL: ' . $_['authentication_url'];
$_['text_autheticated']                            = 'Parabéns, a sua aplicação já está autenticada.';
$_['text_confirm']                                 = 'Deseja realmente fazer isso?';
$_['text_ml_status_closed']                        = 'Encerrado';
$_['text_ml_status_paused']                        = 'Pausado';
$_['text_ml_status_active']                        = 'Ativo';
$_['text_ml_status_inactive']                      = 'Inativo';
$_['text_ml_without_status']                       = 'Sem situação';
$_['text_without_product_in_ml']                   = 'Sem anúncio no ML';
$_['text_add_products_in_ml']                      = 'Adicionar Produtos no Mercado Livre';
$_['text_order_products']                          = 'Produtos do pedido ML';
$_['text_reply_question_in_ml']                    = 'Responder pergunta do Mercado Livre';
$_['text_products_add_success']                    = 'Produtos adicionados ao Mercado Livre';
$_['text_deleted_with_success']                    = 'Produtos deletados com sucesso.';
$_['text_paused_with_success']                     = 'Produtos pausados com sucesso.';
$_['text_activated_with_success']                  = 'Produtos ativados com sucesso.';
$_['text_no_items_deleted']                        = 'Nenhum produto foi deletado, pois não está vinculado ao ML.';
$_['text_no_items_paused']                         = 'Nenhum produto foi pausado, pois não está vinculado ao ML.';
$_['text_no_items_activated']                      = 'Nenhum produto foi pausado, pois não está vinculado ao ML.';
$_['text_stock_and_price_updated']                 = 'Estoque e preços atualizados com sucesso.';
$_['text_warnig_notification']                     = 'Configure o seu aplicativo com a URL a seguir, para receber as notificações corretamente: ';
$_['text_question_deleted_with_success']           = 'Pergunta deletada com sucesso.';
$_['text_order_deleted_with_success']              = 'Pedido deletado com sucesso.';
$_['text_no_question_deleted']                     = 'Nenhuma pergunta deletada.';
$_['text_no_order_deleted']                        = 'Nenhuma pedido deletado.';
$_['text_question_no_answered']                    = 'Pergunta não respondida.';
$_['text_question_answered']                       = 'Pergunta respondida com sucesso.';
$_['text_no_question_deleted']                     = 'Nenhuma pergunta deletada.';
$_['text_last_questions']                          = 'Ultimas perguntas recebidas.';
$_['text_last_orders']                             = 'Ultimos pedidos recebidos.';
$_['text_perform_purchase']                        = 'realizou uma compra de: ';
$_['text_empty']                                   = 'Vazio';

//Tabs
$_['tab_general']                            = 'Configs App';
$_['tab_product_settings']                   = 'Configs do Produto';
$_['tab_categories']                         = 'Mapeamento de Categorias';
$_['tab_feedback']                           = 'Configurações de Mensagem Pós Venda';
$_['tab_shipping']                           = 'Configurações de Envio dos produtos';
$_['tab_template']                           = 'Configurações de Template';

//Columns
$_['column_opecart_category']                 = 'Categoria do OpenCart';
$_['column_mercadolivre_category']            = 'Categoria do Mercado Livre';
$_['column_mercadolivre_listed']              = 'Produto no ML';
$_['column_mercadolivre_status']              = 'Situação no ML';
$_['column_mercadolivre_id']                  = 'ID no ML';
$_['column_mercadolivre_quantity_postage']    = 'Quantidade de anúncios no ML';
$_['column_image']           = 'Imagem';
$_['column_name']            = 'Produto';
$_['column_quantity']        = 'Quantidade';
$_['column_status']          = 'Situação';
$_['column_question']        = 'Pergunta';
$_['column_answer']          = 'Resposta';
$_['column_mercadolivre_order_id']          = 'Id pedido ML';
$_['column_customer_name']             = 'Nome do cliente';
$_['column_customer_document']             = 'Documento do cliente';
$_['column_mercadolivre_order_total']             = 'Valor do pedido';
$_['column_price']             = 'Valor';

//Buttons
$_['button_back']                       = 'Voltar';
$_['button_revoke_authentication']      = 'Revogar autenticação';
$_['button_cancel']                     = 'Cancelar';
$_['button_send']                       = 'Enviar';
$_['button_reply']                      = 'Responder';
$_['btn_change_category']               = 'Alterar categoria';
$_['btn_authenticate']                  = 'Autenticar';
$_['button_disconnect']                 = 'Desconectar';
$_['button_active']                     = 'Ativar';
$_['button_pause']                      = 'Pausar';
$_['button_add']                        = 'Adicionar ao Mercado Livre';
$_['button_delete']                     = 'Apagar';
$_['button_order_products']             = 'Ver produtos do pedido';
$_['button_synchronize_stock_price']    = 'Sicronizar preço e estoque';

// Errors
$_['error_permission_message']      = 'Atenção: Você não tem permissão para alterar este módulo.';
$_['message_error_app_id'] = 'App id do Mercado Livre inválido';
$_['message_error_app_secret'] = 'App secret do Mercado Livre inválido';
$_['message_error_app_country'] = 'Selecione um país de funcionamento do Mercado Livre';
$_['message_error_listing_type'] = 'Selecione um tipo de listagem a ser aplicado aos anúncios';
$_['message_error_currency'] = 'Selecione uma moeda local';
$_['message_error_buying_mode'] = 'Selecione um modo de compra';
$_['message_error_condition'] = 'Selecione uma condição do produto';
$_['message_error_price_adjustment'] = 'As regras de ajuste de preço contém caracteres inválidos, por favor verifique';
$_['message_error_extesion_not_configured'] = 'Por favor, configure a extensão primeiro';
$_['message_error_select_category_or_unselect'] = 'Por favor, selecione uma categoria ou desmarque todas';
$_['message_error_configs_not_done'] = 'Por favor, configure tudo antes de entrar aqui!';
$_['message_error_answer']          = 'Por favor, para enviar uma resposta, escreva uma mensagem!';
$_['message_log_error_warning']    = 'Aviso: Seu arquivo de log %s é %s!';
$_['message_send_products_error_title'] = 'Os seguintes erros ocorreram:';

$_['invalid_question'] = 'Não é possível responder à pergunta.';
$_['invalid_post_body'] = 'Parâmetros inválidos.';

//status
$_['question_unanswered'] = 'Não respondida';
$_['question_answered'] = 'Respondida';

$_['order_status_confirmed']                = 'Pedido criado';
$_['order_status_payment_required']         = 'Aguardando pagamento';
$_['order_status_payment_in_process']       = 'Aguardando confirmação do pagamento';
$_['order_status_partially_paid']   = 'Pedido com valor parcialmente pago';
$_['order_status_paid']             = 'Pedido pago';
$_['order_status_cancelled']        = 'Pedido cancelado';
$_['order_status_invalid']          = 'Pedido inválido';