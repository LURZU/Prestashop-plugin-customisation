<?php
/*
* 2007-2016 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2016 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once dirname(__FILE__) . '/classes/pages.php';
require_once dirname(__FILE__) . '/classes/colors.php';

class oh_configurator extends Module
{
    const CONFIGURATOR_GROUP_SIZE = 14;
    const CONFIGURATOR_GROUP_MATERIAL = 15;
    const CONFIGURATOR_GROUP_COLOR_MATERIAL = 16;

    public function __construct()
    {
        $this->bootstrap = true;
        $this->name = 'oh_configurator';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'OHweb';
        $this->need_instance = 0;
        parent::__construct();
        $this->displayName = $this->l('Oh_configurator');
        $this->description = $this->l('Configurateur de médailles personnalisées pour chien et chat');
    }

    public function install() {
        if (parent::install()
            && $this->registerHook('header')
            && $this->registerHook('moduleRoutes')
            && $this->registerHook('actionCartUpdateQuantityBefore')
            && $this->registerHook('displayCustomization')
            && $this->_installSql()
            && $this->_installTab()
        ) {
            return true;
        }
        return false;
    }

    public function uninstall() {
        if (parent::uninstall()
            && $this->_uninstallSql()
            && $this->_uninstallTab()
        ) {
            return true;
        }
        return false;
    }

    /**
     * Création de la base de donnée
     * @return boolean
     */
    protected function _installSql()
    {
        $sqlCreate = "CREATE TABLE IF NOT EXISTS `" . _DB_PREFIX_ . "configurator_page` (
                `id_configurator_page` int(11) unsigned NOT NULL AUTO_INCREMENT,
                `name` varchar(255) NULL,
                `link_rewrite` varchar(255) NULL,
                `active` INT(1) NULL,
                `detail` text NULL,
                `date_add` datetime NOT NULL,
                `date_upd` datetime NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id_configurator_page`)
                ) ENGINE=" . _MYSQL_ENGINE_ . " DEFAULT CHARSET=utf8;";

        $sqlCreateLang = "CREATE TABLE IF NOT EXISTS `" . _DB_PREFIX_ . "configurator_page_lang` (
              `id_configurator_page` int(11) unsigned NOT NULL AUTO_INCREMENT,
              `id_lang` int(11) NOT NULL,
              `title` varchar(255) NULL,
              `description` text,
              PRIMARY KEY (`id_configurator_page`,`id_lang`)
            ) ENGINE=" . _MYSQL_ENGINE_ . "DEFAULT CHARSET=utf8;";

        $sqlCreateColor = "CREATE TABLE IF NOT EXISTS `" . _DB_PREFIX_ . "configurator_color` (
            `id_configurator_color` int(11) unsigned NOT NULL AUTO_INCREMENT,
            `name` varchar(255) NULL,
            `code` varchar(20) NULL,
            `date_add` datetime NOT NULL,
            `date_upd` datetime NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id_configurator_color`)
            ) ENGINE=" . _MYSQL_ENGINE_ . " DEFAULT CHARSET=utf8;";

        $sqlCreateColorLang = "CREATE TABLE IF NOT EXISTS `" . _DB_PREFIX_ . "configurator_color_lang` (
              `id_configurator_color` int(11) unsigned NOT NULL AUTO_INCREMENT,
              `id_lang` int(11) NOT NULL,
              `title` varchar(255) NULL,
              `description` text,
              PRIMARY KEY (`id_configurator_color`,`id_lang`)
            ) ENGINE=" . _MYSQL_ENGINE_ . "DEFAULT CHARSET=utf8;";

        $rst = (bool)Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.'customized_data` CHANGE `value` `value` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL');
        return $rst && Db::getInstance()->execute($sqlCreate) && Db::getInstance()->execute($sqlCreateLang) && Db::getInstance()->execute($sqlCreateColor) && Db::getInstance()->execute($sqlCreateColorLang);
    }

    /**
     * Installation du controller dans le backoffice
     * @return boolean
     */
    protected function _installTab()
    {
        $tab = new Tab();
        $tab->class_name = 'AdminOhConfigurator';
        $tab->module = $this->name;
        $tab->id_parent = (int)Tab::getIdFromClassName('CONFIGURE');
        $tab->icon = 'settings_applications';
        $languages = Language::getLanguages();
        foreach ($languages as $lang) {
            $tab->name[$lang['id_lang']] = $this->l('Configurateur de médaille');
        }
        return $tab->add();
    }

    /**
     * Désinstallation du controller admin
     * @return boolean
     */
    protected function _uninstallTab()
    {
        $idTab = (int)Tab::getIdFromClassName('AdminOhConfigurator');
        if ($idTab) {
            $tab = new Tab($idTab);
            $tab->delete();
        }
        return true;
    }

    /**
     * Suppression de la base de données
     */
    protected function _uninstallSql()
    {
        $sql = "DROP TABLE " . _DB_PREFIX_ . "configurator_page," . _DB_PREFIX_ . "configurator_page_lang";
        return Db::getInstance()->execute($sql);
    }

    public function hookHeader()
    {
        $this->context->controller->addCSS($this->_path . '/views/css/oh_configurator.css', 'all');
        $this->context->controller->addCSS($this->_path . '/views/css/oh_configurator_banner.css', 'all');
        $this->context->controller->addJS($this->_path . '/views/js/oh_configurator.js', 'all');
    }

    public function getContent()
    {
        $output = '';
        $errors = array();

       /* if(Tools::isSubmit('addColoroh_configurator') || Tools::isSubmit('updateconfigurator_color')){
            return $this->renderColorForm();
        }

        if(Tools::isSubmit('deleteconfigurator_color') && (int)Tools::getValue('id_configurator_color')){
            $color = new Colors((int)Tools::getValue('id_configurator_color'));
            $color->delete();
        }*/

        /*if(Tools::isSubmit('submitColorConfigurator')){
            if((int)Tools::getValue('id_configurator_color') > 0) {
                $color = new Colors((int)Tools::getValue('id_configurator_color'));
                $color->name = Tools::getValue('name');
                $color->code = Tools::getValue('code');
                $color->update();
                $output = $this->displayConfirmation($this->trans('The settings have been updated.', array(), 'Admin.Notifications.Success'));
            } else {
                $color = new Colors();
                $color->name = Tools::getValue('name');
                $color->code = Tools::getValue('code');
                $color->add();
                $output = $this->displayConfirmation($this->trans('Successfully created.', array(), 'Admin.Notifications.Success'));
            }
        }*/

        if (Tools::isSubmit('submitConfigurator')) {
            Configuration::updateValue('OH_ID_CATEGORY_PRODUCT', (int)Tools::getValue('OH_ID_CATEGORY_PRODUCT'));
            $output = $this->displayConfirmation($this->trans('The settings have been updated.', array(), 'Admin.Notifications.Success'));
        }

        return $output.$this->renderForm(); //.$this->renderColorList();
    }

    public function renderForm()
    {
        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->trans('Settings', array(), 'Admin.Global'),
                    'icon' => 'icon-cogs'
                ),
                'input' => array(
                    array(
                        'type' => 'text',
                        'required' => 'true',
                        'label' => 'ID de la catégorie',
                        'name' => 'OH_ID_CATEGORY_PRODUCT',
                        'class' => 'fixed-width-xs',
                    ),
                ),
                'submit' => array(
                    'title' => $this->trans('Save', array(), 'Admin.Actions'),
                ),
            ),
        );

        $lang = new Language((int) Configuration::get('PS_LANG_DEFAULT'));

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitConfigurator';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($fields_form));
    }

    public function getConfigFieldsValues()
    {
        return [
            'OH_ID_CATEGORY_PRODUCT' => Tools::getValue('OH_ID_CATEGORY_PRODUCT', Configuration::get('OH_ID_CATEGORY_PRODUCT')),
        ];
    }

    public function renderColorForm()
    {
        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->trans('Colors', array(), 'Admin.Global'),
                    'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'hidden',
                        'name' => 'id_configurator_color'
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Nom'),
                        'name' => 'name',
                        'required' => true
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Code hexadécimal'),
                        'name' => 'code',
                        'required' => true
                    ),
            ),
                'submit' => array(
                    'title' => $this->trans('Save', array(), 'Admin.Actions'),
                ),
            ),
        );

        $lang = new Language((int) Configuration::get('PS_LANG_DEFAULT'));

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitColorConfigurator';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'fields_value' => $this->getColorFormValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($fields_form));
    }

    public function getColorFormValues()
    {
        $fields_value = array();
        $id_configurator_color = (int)Tools::getValue('id_configurator_color');

            if ($id_configurator_color)
            {
                $info = new Colors((int)$id_configurator_color);
                $fields_value['name'] = $info->name;
                $fields_value['code'] = $info->code;
            }
            else {
                $fields_value['name'] = Tools::getValue('name', '');
                $fields_value['code'] = Tools::getValue('code', '');
            }

        $fields_value['id_configurator_color'] = $id_configurator_color;

        return $fields_value;
    }

    protected function renderColorList()
    {
        $this->fields_list = array();

        $this->fields_list['id_configurator_color'] = array(
            'title' => $this->l('ID'),
            'type' => 'text',
            'search' => false,
            'orderby' => false,
        );
        $this->fields_list['name'] = array(
            'title' => $this->l('Color'),
            'type' => 'text',
            'search' => false,
            'orderby' => false,
        );
        $this->fields_list['code'] = array(
            'title' => $this->l('Code hexadécimal'),
            'type' => 'text',
            'search' => false,
            'orderby' => false,
        );

        $helper = new HelperList();
        $helper->shopLinkType = '';
        $helper->simple_header = false;
        $helper->identifier = 'id_configurator_color';
        $helper->actions = array('edit', 'delete');
        $helper->show_toolbar = true;
        $helper->toolbar_btn['new'] = array(
            'href' => AdminController::$currentIndex.'&configure='.$this->name.'&addColor'.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules'),
            'desc' => $this->l('Add new')
        );
        $helper->title = 'Colors';
        $helper->table = 'configurator_color';
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;

        $content = Db::getInstance()->executeS('SELECT * FROM '._DB_PREFIX_.'configurator_color');
        $helper->listTotal = count($content);

        return $helper->generateList($content, $this->fields_list);
    }

    public function hookmoduleRoutes($params)
    {
        $my_routes = [
            'configurator' => [
                'controller' => 'home',
                'rule' => 'medaille-express/',
                'keywords' => [
                    'link_rewrite' => ['regexp' => '[_a-zA-Z0-9-\pL]*', 'param' => 'link_rewrite'],
                ],
                'params' => [
                    'fc' => 'module',
                    'module' => 'oh_configurator',
                ],
            ],
        ];

        return $my_routes;
    }

    public function hookactionCartUpdateQuantityBefore($data){
        $cart = $data['cart']->id;
        $custom = $data['id_customization'];
        if((int)$custom>0){
            Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'customization SET in_cart=1 WHERE id_cart='.$cart.' AND id_customization='.$custom);
        }
    }
    public function hookdisplayCustomization($params)
    {
        if(isset($params['customization'])) {
            $customs = json_decode($params['customization']['value']);
            if (is_object($customs)) {
                $tabcustom = [];
                foreach ($customs as $key=>$custom){
                    if($key=='color'){
                        $tabcustom[] = [
                            'name' => $this->trans('Couleur', array(), 'Modules.oh_configurator.Admin'),
                            'value' => $custom];
                    }
                    if(substr($key,0,5)=='recto'){
                        list($type, $num) = explode('_', $key);
                        $tabcustom[] = [
                            'name' => $this->trans('Texte recto', array(), 'Modules.oh_configurator.Admin').' '.$num,
                            'value' => $custom];
                    }
                    if(substr($key,0,5)=='verso'){
                        list($type, $num) = explode('_', $key);
                        $tabcustom[] = [
                            'name' => $this->trans('Texte verso', array(), 'Modules.oh_configurator.Admin').' '.$num,
                            'value' => $custom];
                    }
                }

                $this->context->smarty->assign([
                    'customs' => $tabcustom
                ]);
                return $this->fetch('module:' . $this->name . '/views/templates/front/displaycustom.tpl');
            }
        }
    }
}
