{extends file='page.tpl'}
{block name='page_content_container'}
    {block name='configurator_banner'}
        <section class="banner-desc row">
            <div class="title-desc">
                <h3>{l s='L\'expert en personnalisation et gravure de médailles' mod='oh_configurator'}</h3>
                <h1>{l s='Pour chiens & chats' mod='oh_configurator'}</h1>
            </div>
            <div class="right-part">
                <div class="framed">
                    <div class="frame-content">
                        <div class="first-desc">
                            <h3>{l s='La médaille personnalisée*' mod='oh_configurator'}</h3>
                        </div>
                        <div class="second-desc">
                            <div class="desc">
                                <span class="desc-1">{l s='À partir de' mod='oh_configurator'}</span>
                                <span class="desc-2">{l s='(anneau inclus)' mod='oh_configurator'}</span>
                                <span class="desc-3">* {l s='Visuel non contractuel' mod='oh_configurator'}</span>
                            </div>
                            <div class="round">
                                <div class="relative-round">
                                    <div class="first-part"><span>{l s='5' mod='oh_configurator'}</span></div>
                                    <div class="second-part">
                                        <div class="second-part-upper"><span>{l s='.50' mod='oh_configurator'}</span></div>
                                        <div class="second-part-lower"><span>{l s='€' mod='oh_configurator'}</span></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="frame-img">
                        <img class="img-fluid" src="{$urls.base_url}modules/oh_configurator/views/img/Medaille-Mia-rose.png" alt="">
                    </div>
                </div>
                <div class="images">
                    <img class="img-fluid img-chien" src="{$urls.base_url}modules/oh_configurator/views/img/illustration_chien.svg" alt="">
                    <img class="img-fluid img-chat" src="{$urls.base_url}modules/oh_configurator/views/img/illustration_chat.svg" alt="">
                </div>
            </div>
        </section>
    {/block}
    {block name='configurator_content'}
        <section id="content" class="configurator-options">
            {* ------------------- *}
            <div class="config-option opt-1">
                <div class="option-header">
                    <div class="round">
                        <div class="relative-round">
                            <span class="first-part">{l s='1' mod='oh_configurator'}</span>
                        </div>
                    </div>
                    <div class="option-title">
                        <p>{l s='Je choisis la ' mod='oh_configurator'}<strong>{l s='matière & la forme...' mod='oh_configurator'}</strong>
                        </p>
                    </div>
                </div>
                <div class="option-content">
                    <div class="material">
                        <div class="middle config-flex">
                            {foreach from=$config name=material key=id item=material}
                            <label id="{$material.ancre}" class="choice-{$smarty.foreach.material.iteration}">
                                <input id="input-{$material.ancre}" data-group="{$material.group}" type="radio" value="{$id}" name="choice-material" />
                                <div class="box">
                                    <div class="box-title config-flex">{$material.name} &nbsp;
                                        {if $material.popin!=''} <span>
                                            <img src="{$urls.base_url}modules/oh_configurator/views/img/info-icon.svg">

                                            <div class="popup-info">
                                                <p>{$material.popin}</p>
                                            </div>

                                        </span>
                                        {/if}
                                    </div>
                                </div>
                            </label>
                            {/foreach}
                        </div>
                    </div>
                    {foreach from=$config name=material item=material}
                    <div class="shapes" id="shape-{$material.ancre}">
                        {foreach from=$material.products name=product item=product}
                            <div class="shape-{$smarty.foreach.product.iteration}">
                                <div class="shape config-flex">
                                    <label id="{$product.ancre}">
                                        <input id="input-{$product.ancre}" data-price="{$product.price}" class="input-shape" value="{$product.id}" type="radio" name="choice-shape"
                                               {if $product.couleurAttr}data-colorattr="{$product.couleurAttr}"{/if}
                                               data-size="{$product.tailles}"
                                        />
                                        <div class="shape-images">
                                            <div class="shape-svg-container">
                                                {$product.svg nofilter}
                                            </div>
                                            <div class="checked">
                                                <img class="choice" src="{$urls.base_url}modules/oh_configurator/views/img/checked.svg" alt="">
                                            </div>
                                        </div>
                                    </label>
                                </div>
                                <div class="shape-name">
                                    <span>{$product.name}</span>
                                </div>
                            </div>
                        {/foreach}
                    </div>
                    {/foreach}
                </div>
            </div>
            {* ------------------- *}
            <div class="config-option opt-2">
                <div class="option-header">
                    <div class="round">
                        <div class="relative-round">
                            <span class="first-part">{l s='2' mod='oh_configurator'}</span>
                        </div>
                    </div>
                    <div class="option-title">
                        <p>{l s='...la ' mod='oh_configurator'}<strong>{l s='taille...' mod='oh_configurator'}</strong></p>
                    </div>
                </div>
                <div class="option-content">
                    <div class="all-size">
                        {foreach from=$sizes key=id item=size name=taille}
                        <div class="displaysize size-{$id} size-{trim($size.name)}">
                            <div class="size-name config-flex">
                                <span>{$size.name}</span>
                            </div>
                            <div class="size">
                                <label>
                                    <input type="radio" data-group="{$size.group}" data-size="{trim($size.name)}" name="choice-size" value="{$id}"/>
                                    <div class="size-images">
                                        <div class="svg-container">
                                        </div>
                                        <div class="checked">
                                            <img class="choice" src="{$urls.base_url}modules/oh_configurator/views/img/checked.svg" alt="">
                                        </div>
                                    </div>
                                </label>
                            </div>
                            <div class="size-dimensions config-flex">
                                <span>{$size.detail}</span>
                            </div>
                        </div>
                        {/foreach}
                        {* ------------------- *}
                    </div>
                </div>
            </div>
            {* ------------------- *}
            <div class="config-option opt-3">
                <div class="option-header">
                    <div class="round">
                        <div class="relative-round">
                            <span class="first-part">{l s='3' mod='oh_configurator'}</span>
                        </div>
                    </div>
                    <div class="option-title">
                        <p>{l s='...et la ' mod='oh_configurator'}<strong>{l s='couleur.' mod='oh_configurator'}</strong></p>
                    </div>
                </div>

                <div class="option-content attr-color">
                    <div class="color-container only-polie">
                        {foreach from=$couleurAttr key=id item=color name=color}
                            <div class="color-choice choice-{$id}">
                                <div class="color-input">
                                    <label>
                                        <input id="input-colorAttr-{$id}" data-group="{$color.group}" data-color="{$color.codecolor}" value="{$id}" type="radio" name="choice-color" />
                                        <div class="color-circle" style="background-color: {$color.codecolor};">
                                            <div class="checked">
                                                <img class="choice" src="{$urls.base_url}modules/oh_configurator/views/img/checked.svg" alt="">
                                            </div>
                                        </div>
                                    </label>
                                </div>
                                <div class="color-name">{$color.name}</div>
                            </div>
                        {/foreach}
                    </div>
                </div>
            </div>
            {* ------------------- *}
            <div class="config-option opt-4">
                <div class="option-header">
                    <div class="round">
                        <div class="relative-round">
                            <span class="first-part">{l s='4' mod='oh_configurator'}</span>
                        </div>
                    </div>
                    <div class="option-title">
                        <p>{l s='Je rentre le ' mod='oh_configurator'}<strong>{l s='texte...' mod='oh_configurator'}</strong>
                        </p>
                    </div>
                </div>
                <div class="option-content">
                    <div class="recto-verso">
                        <div class="middle config-flex">
                            <label id="choose-recto" class="recto">
                                <input id="input-recto" type="radio" name="recto-verso" />
                                <div class="box">
                                    <div class="box-title config-flex">{l s='Recto' mod='oh_configurator'}</div>
                                </div>
                            </label>
                            <label id="choose-verso" class="verso">
                                <input id="input-verso" type="radio" name="recto-verso" />
                                <div class="box">
                                    <div class="box-title config-flex">{l s='Verso' mod='oh_configurator'}</div>
                                </div>
                            </label>
                        </div>
                        <p>{l s='Police de caractère : Roman 3L' mod='oh_configurator'}</p>
                    </div>
                    <div class="recto-inputs">
                        <div class="recto-1">
                            <label>Recto ligne 1 (Obligatoire)</label>
                            <input id="recto-1" type="text" name="recto-1" placeholder="Votre texte ici (12 caractères max.)"
                                maxlength="12">
                        </div>
                        <div class="recto-2">
                            <label>Recto ligne 2 (Optionnel)</label>
                            <input id="recto-2" type="text" name="recto-2" placeholder="Votre texte ici (12 caractères max.)"
                                maxlength="12">
                        </div>
                        <div class="recto-3">
                            <label>Recto ligne 3 (Optionnel)</label>
                            <input id="recto-3" type="text" name="recto-3" placeholder="Votre texte ici (12 caractères max.)"
                                maxlength="12">
                        </div>
                    </div>
                    <div class="verso-inputs">
                        <div class="verso-1">
                            <label>Verso ligne 1 (Obligatoire)</label>
                            <input id="verso-1" type="text" name="verso-1" placeholder="Votre texte ici (12 caractères max.)"
                                maxlength="12">
                        </div>
                        <div class="verso-2">
                            <label>Verso ligne 2 (Optionnel)</label>
                            <input id="verso-2" type="text" name="verso-2" placeholder="Votre texte ici (12 caractères max.)"
                                maxlength="12">
                        </div>
                        <div class="verso-3">
                            <label>Verso ligne 3 (Optionnel)</label>
                            <input id="verso-3" type="text" name="verso-3" placeholder="Votre texte ici (12 caractères max.)"
                                maxlength="12">
                        </div>
                    </div>
                    <div class="size-not-selected config-flex">
                        <p>{l s='Veuillez d\'abord sélectionner une taille.' mod='oh_configurator'}</p>
                    </div>
                    <div class="no-text config-flex">
                        <p>{l s='Pas de texte possible pour cette forme.' mod='oh_configurator'}</p>
                    </div>
                </div>
            </div>
            {* ------------------- *}
            <div class="config-option opt-5">
                <div class="option-header">
                    <div class="round">
                        <div class="relative-round">
                            <span class="first-part">{l s='5' mod='oh_configurator'}</span>
                        </div>
                    </div>
                    <div class="option-title">
                        <p>{l s='...je visualise la ' mod='oh_configurator'}<strong>{l s='médaille...' mod='oh_configurator'}</strong>
                        </p>
                    </div>
                </div>
                <div class="option-content">
                    <div class="recto-verso">
                        <div class="middle config-flex">
                            <label id="view-recto" class="recto">
                                <input id="input-recto-view" type="radio" name="recto-verso-view" />
                                <div class="box">
                                    <div class="box-title config-flex">{l s='Recto' mod='oh_configurator'}</div>
                                </div>
                            </label>
                            <label id="view-verso" class="verso">
                                <input id="input-verso-view" type="radio" name="recto-verso-view" />
                                <div class="box">
                                    <div class="box-title config-flex">{l s='Verso' mod='oh_configurator'}</div>
                                </div>
                            </label>
                        </div>
                        <p>{l s='Aperçu de la médaille' mod='oh_configurator'}<br /><span
                                class="contractuel">{l s='(Visuel non contractuel)' mod='oh_configurator'}</span></p>
                    </div>
                    <div class="preview config-flex">
                        <div class="preview-img">
                            <div class="svg-container">
                            </div>
                            <div id="preview-recto" class="preview-recto">
                                <span></span>
                            </div>
                            <div id="preview-verso" class="preview-verso">
                                <span></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {* ------------------- *}
            <div class="config-option opt-6">
                <div class="option-header">
                    <div class="round">
                        <div class="relative-round">
                            <span class="first-part">{l s='6' mod='oh_configurator'}</span>
                        </div>
                    </div>
                    <div class="option-title">
                        <p>{l s='...et j\'ajoute au ' mod='oh_configurator'}<strong>{l s='panier.' mod='oh_configurator'}</strong>
                        </p>
                    </div>
                </div>
                <div class="option-content">
                    <div class="price-container config-flex">
                        <div class="price-tax config-flex">
                            <div>
                                <span class="price">6,30 €</span>
                                <span class="tax">TTC</span>
                            </div>
                        </div>
                    </div>
                    <div class="add-to-cart-container">
                        <form action="{$urls.pages.cart}" method="post" id="add-to-cart-or-refresh">
                            <input type="hidden" name="token" value="{$static_token}">
                            <input type="hidden" name="id_product" value="" id="product_page_product_id">
                            <input type="hidden" name="id_customization" value="0" id="product_customization_id">
                            {foreach from=$tabgroup key=$id item=group}
                                <input type="hidden" class="group" name="group[{$id}]" value="0" id="group_{$id}">
                            {/foreach}

                            <div class="product-add-to-cart pt-3">
                                <div class="row product-quantity ">
                                    <div class="col-add-qty">
                                        <div class="qty ">
                                            <input
                                                    type="number"
                                                    name="qty"
                                                    id="quantity_wanted"
                                                    value="1"
                                                    class="input-group "
                                                    min="1"
                                            >
                                        </div>
                                    </div>
                                    <div class="col-add-btn">
                                        <div class="add">
                                            <button style="display:none" class="btn btn-primary btn-lg add-to-cart" data-button-action="add-to-cart" type="submit">
                                                <img class="add-to-cart-icon" src="{$urls.base_url}modules/oh_configurator/views/img/basket-icon.svg" />
                                                <i class="fa fa-circle-o-notch fa-spin fa-fw spinner-icon" aria-hidden="true"></i>
                                                Ajouter au panier
                                            </button>
                                            <button class="btn btn-primary btn-lg add-custom" type="button">
                                                <img class="add-to-cart-icon" src="{$urls.base_url}modules/oh_configurator/views/img/basket-icon.svg" />
                                                Ajouter au panier
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            {* ------------------- *}
        </section>
    {/block}
{/block}
