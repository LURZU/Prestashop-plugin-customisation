<?php
/**
 *  Please read the terms of the CLUF license attached to this module(cf "licences" folder)
 *
 * @author    Línea Gráfica E.C.E. S.L.
 * @copyright Lineagrafica.es - Línea Gráfica E.C.E. S.L. all rights reserved.
 * @license   https://www.lineagrafica.es/licenses/license_en.pdf https://www.lineagrafica.es/licenses/license_es.pdf https://www.lineagrafica.es/licenses/license_fr.pdf
 */

class oh_configuratorCartModuleFrontController extends ModuleFrontController
{

    public $urls;
    public $sitemaps;

    public function __construct()
    {
        parent::__construct();
        $this->urls = array();
    }

    public function initContent()
    {
        parent::initContent();
        if (Tools::getValue('action') == 'addCart') {
            $this->displayAjaxAddcart();
        }
        if (Tools::getValue('action') == 'recupPrice') {
            $this->recupPrice();
        }
        die();
    }

    public function recupPrice(){
        if (Tools::getValue('qty', 0) == 0) {
            $this->errors[] = Tools::displayError('Null quantity.', !Tools::getValue('ajax'));
        } elseif (!Tools::getValue('id_product')) {
            $this->errors[] = Tools::displayError('Product not found', !Tools::getValue('ajax'));
        }

        $product = new Product((int)Tools::getValue('id_product'), true, $this->context->language->id);

        if (!$product->id || !$product->active || !$product->checkAccess($this->context->cart->id_customer)) {
            $this->errors[] = Tools::displayError('This product is no longer available.');
            return;
        }

        $groups = explode('&', urldecode(Tools::getValue('group')));
        $tabgroup = [];
        foreach($groups as $group){
            list($name, $value) = explode('=', $group);
            $tabgroup[$name] = $value;
        }
        $id_product_attribute = (int) Product::getIdProductAttributeByIdAttributes(
            $product->id,
            $tabgroup
        );
        if($id_product_attribute>0) {
            $return = array('return' => 'success', 'price' => Tools::displayPrice($product->getPrice(true, $id_product_attribute)));
            die(Tools::jsonEncode($return));
        }
    }

    public function displayAjaxAddcart()
    {
        $mode = 'add';
        $tabcustoms = [];
        if (Tools::getValue('qty', 0) == 0) {
            $this->errors[] = Tools::displayError('Null quantity.', !Tools::getValue('ajax'));
        } elseif (!Tools::getValue('id_product')) {
            $this->errors[] = Tools::displayError('Product not found', !Tools::getValue('ajax'));
        }
        
        $product = new Product((int)Tools::getValue('id_product'), true, $this->context->language->id);

        if (!$product->id || !$product->active || !$product->checkAccess($this->context->cart->id_customer)) {
            $this->errors[] = Tools::displayError('This product is no longer available.');
            return;
        }

        $groups = explode('&', urldecode(Tools::getValue('group')));
        $tabgroup = [];
        foreach($groups as $group){
            list($name, $value) = explode('=', $group);
            $tabgroup[$name] = $value;
        }
        $id_product_attribute = (int) Product::getIdProductAttributeByIdAttributes(
            $product->id,
            $tabgroup
        );
        if(Tools::getValue('recto_1')!='') $tabcustoms['recto_1'] = Tools::getValue('recto_1');
        if(Tools::getValue('recto_2')!='') $tabcustoms['recto_2'] = Tools::getValue('recto_2');
        if(Tools::getValue('recto_3')!='') $tabcustoms['recto_3'] = Tools::getValue('recto_3');
        if(Tools::getValue('verso_1')!='') $tabcustoms['verso_1'] = Tools::getValue('verso_1');
        if(Tools::getValue('verso_2')!='') $tabcustoms['verso_2'] = Tools::getValue('verso_2');
        if(Tools::getValue('verso_3')!='') $tabcustoms['verso_3'] = Tools::getValue('verso_3');
        if(Tools::getValue('sizefont')!='') $tabcustoms['sizefont'] = Tools::getValue('sizefont');

        if (!$this->context->cart->id) {
            if (Context::getContext()->cookie->id_guest) {
                $guest = new Guest(Context::getContext()->cookie->id_guest);
                $this->context->cart->mobile_theme = $guest->mobile_theme;
            }
            $this->context->cart->add();
            if ($this->context->cart->id) {
                $this->context->cookie->id_cart = (int)$this->context->cart->id;
            }
        }
        $supprice = 0;
        $tabcustoms = json_encode($tabcustoms);
        $this->textRecord($product, $tabcustoms,$id_product_attribute,  $this->module->id, $supprice);
        //recupération de id_custom
        $exising_customization = Db::getInstance()->getValue(
            'SELECT cu.`id_customization` FROM `' . _DB_PREFIX_ . 'customization` cu
            LEFT JOIN `' . _DB_PREFIX_ . 'customized_data` cd
            ON cu.`id_customization` = cd.`id_customization`
            WHERE cu.id_cart = ' . (int) $this->context->cart->id . '
            AND cu.id_product = ' . (int) $product->id . '
            AND cu.id_product_attribute = ' . (int) $id_product_attribute . '
            AND in_cart = 0'
        );

        $return = array('return'=>'success', 'id_custom'=>$exising_customization);
        die(Tools::jsonEncode($return));
    }

    protected function textRecord($product, $custom, $id_product_attribute, $id_module, $price)
    {
        $quantity = 0;
            $exising_customization = Db::getInstance()->executeS(
                'SELECT cu.`id_customization`, cd.`index`, cd.`value`, cd.`type` FROM `' . _DB_PREFIX_ . 'customization` cu
            LEFT JOIN `' . _DB_PREFIX_ . 'customized_data` cd
            ON cu.`id_customization` = cd.`id_customization`
            WHERE cu.id_cart = ' . (int) $this->context->cart->id . '
            AND cu.id_product = ' . (int) $product->id . ' AND cu.id_product_attribute IN (0, '.$id_product_attribute.')
            AND in_cart = 0'
            );

            if ($exising_customization) {
                // If the customization field is alreay filled, delete it
                foreach($exising_customization as $id){
                    Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'customized_data WHERE id_customization='.$id['id_customization']);
                    Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'customization WHERE id_customization='.$id['id_customization']);
                }
            }
            Db::getInstance()->execute(
                'INSERT INTO `' . _DB_PREFIX_ . 'customization` (`id_cart`, `id_product`, `id_product_attribute`, `quantity`)
            VALUES (' . (int)$this->context->cart->id . ', ' . (int) $product->id . ', ' . (int) $id_product_attribute . ', ' . (int) $quantity . ')'
            );
            $id_customization = Db::getInstance()->Insert_ID();
            $index = Db::getInstance()->getValue('SELECT `id_customization_field` FROM `'._DB_PREFIX_.'customization_field` WHERE id_product='.(int) $product->id);

            $query = 'INSERT INTO `' . _DB_PREFIX_ . 'customized_data` (`id_customization`, `type`, `index`, `value`, `id_module`, `price`)
            VALUES (' . (int) $id_customization . ', ' . (int) Product::CUSTOMIZE_TEXTFIELD . ', '.$index.', \'' . pSQL($custom) . '\', '.$id_module.', '.$price.')';

            if (!Db::getInstance()->execute($query)) {
                return false;
            }

            return true;


    }

}
