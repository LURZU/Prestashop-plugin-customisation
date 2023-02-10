<?php

class AdminOhConfiguratorController extends ModuleAdminController
{
   /**
     * Instanciation de la classe
     * Définition des paramètres basiques obligatoires
     */
    public function __construct()
    {
        $this->bootstrap = true; // Gestion de l'affichage en mode bootstrap
        $this->table = 'configurator_page'; // Table de l'objet
        $this->identifier = 'id_configurator_page'; // Clé primaire de l'objet
        $this->className = 'Pages'; // Classe de l'objet
        $this->lang = true; // Flag pour dire si utilisation de langues ou non

        // Appel de la fonction parente pour pouvoir utiliser la traduction ensuite
        parent::__construct();

        // Liste des champs de l'objet à afficher dans la liste
        $this->fields_list = [
            'id_configurator_page' => [ // Nom du champ sql
                'title' => $this->l('Id page'), // Titre
                'align' => 'center', // Alignement
                'class' => 'fixed-width-xs' // Classe css de l'élément
            ],
            'name' => [
                'title' => $this->l('Nom'),
                'type' => 'text',
                'filter_key' => 'a!name',
                'class' => 'fixed-width-m',
            ],
            'link_rewrite' => [
                'title' => $this->l('Url'),
                'type' => 'text',
                'class' => 'fixed-width-m',
            ],
            'active' => [
                'title' => $this->l('Enabled'),
                'align' => 'center',
                'active' => 'status',
                'type' => 'bool',
                'orderby' => false,
                'filter_key' => 'a!active',
                'class' => 'fixed-width-sm',
            ],
        ];

        // Ajout d'actions sur chaque ligne
        $this->addRowAction('edit');
        $this->addRowAction('delete');
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);
        $this->context->controller->addCSS(_PS_MODULE_DIR_.'oh_configurator/views/css/back_configurator.css', 'all');
    }
    public static function checkCustomizedFieldInProduct($id_product)
    {
        $query = new DBQuery();
        $query->select('id_customization_field');
        $query->from('customization_field');
        $query->where('id_product = ' . (int)$id_product);

        return (int)DB::getInstance()->getValue($query);
    }
    public function postProcess(){
        if (Tools::isSubmit("submitAddconfigurator_page")) {
            $json = [];
            foreach (Tools::getValue('material') as $material) {
                $json[$material] = [];
                $json[$material]['popin'] = Tools::getValue('textpopin_'.$material);
                $json[$material]['products'] = [];
                foreach (Tools::getValue($material . '_products') as $product) {
                    if (!self::checkCustomizedFieldInProduct($product)) {
                        $field = new CustomizationField();
                        $field->id_product = $product;
                        $field->type = 1;
                        $field->required = 1;
                        foreach (Language::getLanguages() as $lang) {
                            $field->name[$lang['id_lang']] = 'Configuration';
                        }
                        $res = $field->save();
                    }
                    $json[$material]['products'][$material . '_' . $product] = [
                        'id_product' => $product,
                        'taille' => [],
                        'couleurAttr' => Tools::getValue($material . '_' . $product . '_couleurAttr'),
                    ];
                    if(isset($_FILES[$material.'_'.$product.'_'.$product.'_image']['name'])){
                        move_uploaded_file($_FILES[$material.'_'.$product.'_'.$product.'_image']['tmp_name'], _PS_MODULE_DIR_.$this->module->name.'/views/img/products/'.$material.'_'.$product.'.svg');
                    }
                    if(Tools::getValue($material.'_'.$product.'_'.$product.'_name')!='') {
                        $json[$material]['products'][$material . '_' . $product]['name'] = Tools::getValue($material . '_' . $product . '_' . $product . '_name');
                    }
                    if(Tools::getValue($material . '_' . $product . '_taille')) {
                        foreach (Tools::getValue($material . '_' . $product . '_taille') as $taille) {
                            $json[$material]['products'][$material . '_' . $product]['taille'][$taille] = [];
                            if((int)Tools::getValue($material . '_' . $product . '_' . $taille . '_top')>0) {
                                $json[$material]['products'][$material . '_' . $product]['taille'][$taille]['top'] = (int)Tools::getValue($material . '_' . $product . '_' . $taille . '_top');
                            }
                            if((int)Tools::getValue($material . '_' . $product . '_' . $taille . '_left')>0) {
                                $json[$material]['products'][$material . '_' . $product]['taille'][$taille]['left'] = (int)Tools::getValue($material . '_' . $product . '_' . $taille . '_left');
                            }
                            if((int)Tools::getValue($material . '_' . $product . '_' . $taille . '_height')>0) {
                                $json[$material]['products'][$material . '_' . $product]['taille'][$taille]['height'] = (int)Tools::getValue($material . '_' . $product . '_' . $taille . '_height');
                            }
                            if((int)Tools::getValue($material . '_' . $product . '_' . $taille . '_width')>0) {
                                $json[$material]['products'][$material . '_' . $product]['taille'][$taille]['width'] = (int)Tools::getValue($material . '_' . $product . '_' . $taille . '_width');
                            }
                            if (Tools::getValue($material . '_' . $product . '_' . $taille . '_line1') != '') {
                                $json[$material]['products'][$material . '_' . $product]['taille'][$taille]['ligne_1'] = [];

                                if ((int)Tools::getValue($material . '_' . $product . '_' . $taille . '_line1_recto_nbCaractere')>0) {
                                    $json[$material]['products'][$material . '_' . $product]['taille'][$taille]['ligne_1']['recto'] = Tools::getValue($material . '_' . $product . '_' . $taille . '_line1_recto_nbCaractere', 10);
                                }
                                if ((int)Tools::getValue($material . '_' . $product . '_' . $taille . '_line1_verso_nbCaractere')>0) {
                                    $json[$material]['products'][$material . '_' . $product]['taille'][$taille]['ligne_1']['verso'] = Tools::getValue($material . '_' . $product . '_' . $taille . '_line1_verso_nbCaractere', 10);
                                }
                            }

                            if (Tools::getValue($material . '_' . $product . '_' . $taille . '_line2') != '') {
                                $json[$material]['products'][$material . '_' . $product]['taille'][$taille]['ligne_2'] = [];

                                if ((int)Tools::getValue($material . '_' . $product . '_' . $taille . '_line2_recto_nbCaractere')>0) {
                                    $json[$material]['products'][$material . '_' . $product]['taille'][$taille]['ligne_2']['recto'] = Tools::getValue($material . '_' . $product . '_' . $taille . '_line2_recto_nbCaractere', 10);
                                }
                                if ((int)Tools::getValue($material . '_' . $product . '_' . $taille . '_line2_verso_nbCaractere')>0) {
                                    $json[$material]['products'][$material . '_' . $product]['taille'][$taille]['ligne_2']['verso'] = Tools::getValue($material . '_' . $product . '_' . $taille . '_line2_verso_nbCaractere', 10);
                                }
                            }
                            if (Tools::getValue($material . '_' . $product . '_' . $taille . '_line3') != '') {
                                $json[$material]['products'][$material . '_' . $product]['taille'][$taille]['ligne_3'] = [];

                                if ((int)Tools::getValue($material . '_' . $product . '_' . $taille . '_line3_recto_nbCaractere')>0) {
                                    $json[$material]['products'][$material . '_' . $product]['taille'][$taille]['ligne_3']['recto'] = Tools::getValue($material . '_' . $product . '_' . $taille . '_line3_recto_nbCaractere', 10);
                                }
                                if ((int)Tools::getValue($material . '_' . $product . '_' . $taille . '_line3_verso_nbCaractere')>0) {
                                    $json[$material]['products'][$material . '_' . $product]['taille'][$taille]['ligne_3']['verso'] = Tools::getValue($material . '_' . $product . '_' . $taille . '_line3_verso_nbCaractere', 10);
                                }
                            }
                        };
                    }
                };
            };

            $json = json_encode($json);
            $_POST['detail'] = $json;
        };

        parent::postProcess();
    }

    /**
     * Affichage du formulaire d'ajout / création de l'objet
     * @return string
     * @throws SmartyException
     */
    public function renderForm()
    {
        $id_category = Configuration::get('OH_ID_CATEGORY_PRODUCT');
        $allProducts = Product::getProducts($this->context->language->id, 0, 0, 'id_product', 'ASC', $id_category, true);

        $sqlMaterial = "
        SELECT a.id_attribute, al.name, a.id_attribute_group FROM " . _DB_PREFIX_ . "attribute a
        INNER JOIN " . _DB_PREFIX_ . "attribute_lang al ON a.id_attribute = al.id_attribute
        WHERE al.id_lang = " . $this->context->language->id . " AND a.id_attribute_group = ".oh_configurator::CONFIGURATOR_GROUP_MATERIAL." ORDER BY a.position";
        $allMaterial = Db::getInstance()->executeS($sqlMaterial);

        foreach($allMaterial as &$material) {
            $sqlProduct = "
            SELECT DISTINCT p.id_product, pl.name FROM " . _DB_PREFIX_ . "product p
            INNER JOIN " . _DB_PREFIX_ . "product_lang pl ON p.id_product = pl.id_product
            INNER JOIN " . _DB_PREFIX_ . "product_attribute pa ON p.id_product = pa.id_product
            INNER JOIN " . _DB_PREFIX_ . "product_attribute_combination pac ON pa.id_product_attribute = pac.id_product_attribute
            WHERE pl.id_lang = " . $this->context->language->id . " AND pac.id_attribute = " . $material['id_attribute'];
            $products = Db::getInstance()->executeS($sqlProduct);
            foreach ($products as &$product) {
                $material['products'][$product['id_product']] = [
                    'id_product' => $product['id_product'],
                    'name' => $product['name'],
                    'sizes' => [],
                    'colors' => []
                ];
                $sql = 'SELECT GROUP_CONCAT(pa.id_product_attribute) FROM ' . _DB_PREFIX_ . 'product_attribute_combination pac
                INNER JOIN ' . _DB_PREFIX_ . 'product_attribute pa ON pa.id_product_attribute = pac.id_product_attribute
                WHERE pa.id_product = ' . $product['id_product'].'
                AND pac.id_attribute = '.$material['id_attribute'];
                $combi = Db::getInstance()->getValue($sql);

                $sql = "SELECT DISTINCT a.id_attribute, al.name, a.id_attribute_group FROM " . _DB_PREFIX_ . "attribute a
                INNER JOIN " . _DB_PREFIX_ . "attribute_lang al ON a.id_attribute = al.id_attribute
                INNER JOIN " . _DB_PREFIX_ . "product_attribute_combination pac ON a.id_attribute = pac.id_attribute
                INNER JOIN " . _DB_PREFIX_ . "product_attribute pa ON pa.id_product_attribute = pac.id_product_attribute
                WHERE al.id_lang = " . $this->context->language->id . "
                    AND a.id_attribute_group =" . oh_configurator::CONFIGURATOR_GROUP_SIZE . "
                    AND pa.id_product_attribute IN (" . $combi.")
                ORDER BY a.id_attribute_group, a.position";

                $material['products'][$product['id_product']]['sizes'] = Db::getInstance()->executeS($sql);

                $sql = "SELECT DISTINCT a.id_attribute, al.name, a.id_attribute_group FROM " . _DB_PREFIX_ . "attribute a
                INNER JOIN " . _DB_PREFIX_ . "attribute_lang al ON a.id_attribute = al.id_attribute
                INNER JOIN " . _DB_PREFIX_ . "product_attribute_combination pac ON a.id_attribute = pac.id_attribute
                INNER JOIN " . _DB_PREFIX_ . "product_attribute pa ON pa.id_product_attribute = pac.id_product_attribute
                WHERE al.id_lang = " . $this->context->language->id . "
                    AND a.id_attribute_group =" . oh_configurator::CONFIGURATOR_GROUP_COLOR_MATERIAL . "
                    AND pa.id_product_attribute IN (" . $combi.")
                    ORDER BY a.id_attribute_group, a.position";
                $material['products'][$product['id_product']]['colors'] = Db::getInstance()->executeS($sql);

            }
        }

        $json = $this->loadObject(true);

        $this->context->smarty->assign([
            'allProducts' => $allProducts,
            /*'allColors' => $allColors,*/
            'allMaterial' => $allMaterial,
            'COLOR_MATERIAL' => oh_configurator::CONFIGURATOR_GROUP_COLOR_MATERIAL,
            'SIZE' => oh_configurator::CONFIGURATOR_GROUP_SIZE,
            'data' => $json->detail,
        ]);

        $htmlconfig = $this->context->smarty->fetch(_PS_MODULE_DIR_.'oh_configurator/views/templates/admin/config.tpl');

        // Définition du formulaire d'édition
        $this->fields_form = [
            // Entête
            'legend' => [
                'title' => $this->l('Edition'),
                'icon' => 'icon-cog'
            ],

            // Champs
            'input' => [
                [
                    'type' => 'text',
                    'label' => $this->l('Nom'),
                    'name' => 'name',
                    'required' => true,
                ],
                [
                    'type' => 'text',
                    'label' => $this->l('URL'),
                    'name' => 'link_rewrite',
                    'required' => true,
                ],
                [
                    'type' => 'switch',
                    'label' => $this->l('Enabled'),
                    'name' => 'active',
                    'is_bool' => true,
                    'values' => [
                        [
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Enabled')
                        ],
                        [
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('Disabled')
                        ]
                    ],
                ],
                [
                    'type' => 'free',
                    'label' => '',
                    'name' => 'content',
                    'desc' => $htmlconfig,
                ],
            ],

            // Boutton de soumission
            'submit' => [
                'title' => $this->l('Save'), // On garde volontairement la traduction de l'admin par défaut
            ]
        ];
        return parent::renderForm();
    }

    /**
     * Gestion de la toolbar
     */
    public function initPageHeaderToolbar()
    {
        //Bouton d'ajout
        $this->page_header_toolbar_btn['new'] = array(
            'href' => self::$currentIndex . '&add' . $this->table . '&token=' . $this->token,
            'desc' => $this->module->l('Add new page'),
            'icon' => 'process-icon-new'
        );

        parent::initPageHeaderToolbar();
    }
}
