<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

class oh_configuratorhomeModuleFrontController extends ModuleFrontController
{
    public $configurator;
    public $tabconfig = [];

    public function __construct()
    {
        parent::__construct();
    }

    public function initContent()
    {
        parent::initContent();
        $configurator = Db::getInstance()->getValue('SELECT detail FROM '._DB_PREFIX_.'configurator_page WHERE link_rewrite="'.Tools::getValue('link_rewrite').'"');
        if($configurator!=''){
            $this->configurator = json_decode($configurator);
            $tabsize = [];
            $tabcolorattr = [];
            $tabgroup = [];
            foreach($this->configurator as $material=>$config){
                $attribut = new Attribute((int)$material, $this->context->language->id);
                $this->tabconfig[$material] = [
                    'name' => $attribut->name,
                    'group' => $attribut->id_attribute_group,
                    'popin' => $config->popin,
                    'ancre' => Tools::link_rewrite($attribut->name),
                    'products' => [],
                ];
                foreach($config->products as $prod){
                    $product = new Product($prod->id_product, true, $this->context->language->id);
                    //gestion du nom
                    if(isset($prod->name) && $prod->name!=''){
                        $name_product = $prod->name;
                    }else{
                        $name_product = $product->name;
                    }
                    //gestion du svg
                    $svg = '';
                    if(file_exists(_PS_MODULE_DIR_.$this->module->name.'/views/img/products/'.$material.'_'.$prod->id_product.'.svg')){
                        $svg = file_get_contents(_PS_MODULE_DIR_.$this->module->name.'/views/img/products/'.$material.'_'.$prod->id_product.'.svg');
                    }else{
                        unset($this->tabconfig[$material]['products'][$prod->id_product]);
                        continue;
                    }

                    //gestion declinaison
                    $declinaisons = Db::getInstance()->ExecuteS('SELECT pa.id_product_attribute, a.id_attribute, al.name, a.id_attribute_group, a.color, agl.name as groupe
                                FROM '._DB_PREFIX_.'product_attribute_combination pac
                                INNER JOIN '._DB_PREFIX_.'product_attribute pa ON pa.id_product_attribute = pac.id_product_attribute
                                INNER JOIN '._DB_PREFIX_.'attribute a ON pac.id_attribute = a.id_attribute
                                INNER JOIN '._DB_PREFIX_.'attribute_lang al ON (a.id_attribute = al.id_attribute AND al.id_lang='.$this->context->language->id.')
                                INNER JOIN '._DB_PREFIX_.'attribute_group_lang agl ON (a.id_attribute_group = agl.id_attribute_group AND agl.id_lang='.$this->context->language->id.')
                                WHERE pa.id_product='.$prod->id_product.' ORDER BY a.id_attribute_group, a.position');
                    foreach($declinaisons as $decli){
                        if(!isset($tabgroup[$decli['id_attribute_group']])) $tabgroup[$decli['id_attribute_group']] = $decli['groupe'];
                        if($decli['id_attribute_group']==oh_configurator::CONFIGURATOR_GROUP_SIZE){
                            list($name, $detail) = explode('(', $decli['name']);
                            $tabsize[$decli['id_attribute']] = ['name' => $name, 'detail' => str_replace(['(', ')'], '', $detail),'group'=>$decli['id_attribute_group']];
                        }
                        $tabcolorattr[$decli['id_attribute']] = ['name' => $decli['name'], 'codecolor' => $decli['color'],'group'=>$decli['id_attribute_group']];
                    }
                    $this->tabconfig[$material]['products'][$prod->id_product] = [
                        'id' => $prod->id_product,
                        'name' => $name_product,
                        'ancre' => Tools::link_rewrite($name_product),
                        'svg' => $svg,
                        'couleurAttr' => is_array($prod->couleurAttr) && count($prod->couleurAttr)>0 ? json_encode($prod->couleurAttr) : null,
                        'tailles' => json_encode($prod->taille),
                        'price' => Tools::displayPrice($product->getPrice(true))
                    ];

                }
            }
        }
;
        $this->context->smarty->assign([
           'config' => $this->tabconfig,
           'couleurAttr' => $tabcolorattr,
           'sizes' => $tabsize,
           'tabgroup' => $tabgroup,
           'group_color' => oh_configurator::CONFIGURATOR_GROUP_COLOR_MATERIAL
        ]);

        $this->setTemplate('module:oh_configurator/views/templates/front/home-controller.tpl');
        $this->context->link->getModuleLink('oh_configurator','home');
    }

}
