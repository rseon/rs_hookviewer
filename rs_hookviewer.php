<?php
/**
 * 2007-2021 PrestaShop
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
 *
 * @author    Rémi Séon <contact@rseon.com>
 * @copyright 2021 Rémi Séon
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class rs_hookviewer extends Module
{
    // Do not handle these hooks and returns default value
    const HOOK_TO_EXCEPT = [
        'displayOverrideTemplate' => null,
        'displayPaymentEU' => [],
    ];

    const CONFIG_FIELDS = [
        'RS_HOOKVIEWER_DISPLAY_HOOKS',
        'RS_HOOKVIEWER_DISPLAY_HOOKS_BO',
        'RS_HOOKVIEWER_DISPLAY_HOOKS_TYPE',
        'RS_HOOKVIEWER_DISPLAY_HOOKS_IP',
        'RS_HOOKVIEWER_DISPLAY_HOOKS_LOGGEDIN_BO',
        'RS_HOOKVIEWER_DISPLAY_HOOKS_ONLY_DEBUG_MODE',
    ];

    protected $displayHooks = false;
    protected $displayHooksBO = false;
    protected $inAdmin = false;
    protected $restrictedByIP = false;
    protected $restrictedLoggedInBO = false;
    protected $loggedInBO = false;
    protected $listHooks = [];

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->name = 'rs_hookviewer';
        $this->tab = 'administration';
        $this->version = '1.3.0';
        $this->author = 'rseon';
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Hook viewer');
        $this->description = $this->l('Display hook positions in your pages');
        $this->confirmUninstall = $this->l('Do you REALLY want to uninstall this awesome module ?');
        $this->ps_versions_compliancy = ['min' => '1.7', 'max' => _PS_VERSION_];

        $this->displayHooks = (int) Configuration::get('RS_HOOKVIEWER_DISPLAY_HOOKS') === 1;
        if((int) Configuration::get('RS_HOOKVIEWER_DISPLAY_HOOKS_ONLY_DEBUG_MODE') === 1) {
            $this->displayHooks = $this->displayHooks && _PS_MODE_DEV_;
        }

        $this->inAdmin = strpos($_SERVER['REQUEST_URI'], trim(str_replace(_PS_ROOT_DIR_, '', _PS_ADMIN_DIR_), DIRECTORY_SEPARATOR)) !== false;
        $this->displayHooksBO = $this->displayHooks && (int) Configuration::get('RS_HOOKVIEWER_DISPLAY_HOOKS_BO') === 1;
        if(Configuration::get('RS_HOOKVIEWER_DISPLAY_HOOKS_IP')) {
            $this->restrictedByIP = !in_array($_SERVER['REMOTE_ADDR'], explode(',', Configuration::get('RS_HOOKVIEWER_DISPLAY_HOOKS_IP')));
        }
        $this->restrictedLoggedInBO = (int) Configuration::get('RS_HOOKVIEWER_DISPLAY_HOOKS_LOGGEDIN_BO') === 1;
        if($this->restrictedLoggedInBO) {
            $this->loggedInBO = (int) (new Cookie('psAdmin'))->id_employee > 0;
            // Only SuperAdmins
            /*if($id_employee > 0) {
                $this->loggedInBO = (new Employee($id_employee))->isSuperAdmin();
            }*/
        }
        $allHooks = Db::getInstance()->executeS('
            SELECT h.*, GROUP_CONCAT(m.name) as modules
            FROM '._DB_PREFIX_.'hook h
            LEFT JOIN '._DB_PREFIX_.'hook_module hm ON hm.id_hook = h.id_hook
            LEFT JOIN '._DB_PREFIX_.'module m ON m.id_module = hm.id_module
            WHERE m.active = 1
            GROUP BY h.id_hook
            ORDER BY h.name, hm.position
        ');
        foreach($allHooks as $h) {
            $this->listHooks[$h['name']] = $h;
        }
    }

    /**
     * Display hook info
     * 
     * @param string
     * @param array
     * @return string
     */
    protected function showHookInfo($method, $arguments)
    {
        // Ignore action and filter hooks
        if(strpos($method, 'hookAction') !== false || strpos($method, 'hookFilter') !== false) {
            return;
        }

        // Display hooks
        if(!$this->displayHooks) {
            return;
        }

        // Display in BO
        if($this->inAdmin && !$this->displayHooksBO) {
            return;
        }

        // Restrict by IP
        if($this->restrictedByIP) {
            return;
        }

        // Restrict logged in BO
        if($this->restrictedLoggedInBO && !$this->loggedInBO) {
            return;
        }

        // Hook exceptions
        foreach(self::HOOK_TO_EXCEPT as $h => $return) {
            if(strpos(strtolower($method), strtolower($h)) !== false) {
                return $return;
            }
        }

        // Format params for display
        $params = [];
        if(isset($arguments[0])) {
            $params = $arguments[0];
        }

        // Filter unnecessary params
        $paramsFiltered = array_filter($params, function($key) {
            return !in_array($key, ['_ps_version','request','route','cookie','cart','altern','smarty']);
        }, ARRAY_FILTER_USE_KEY);

        // Format for display inline
        $paramList = implode(', ', array_map(function($key) use ($params) {
            $type = gettype($params[$key]);
            if($type === 'string') {
                return "'$key' => '".$params[$key]."'";
            }
            if(in_array($type, ['integer', 'double', 'boolean'])) {
                return "'$key' => ".$params[$key];
            }
            return "'$key' => $type";
        }, array_keys($paramsFiltered)));
        if($paramList !== '') {
            $paramList = "[$paramList]";
        }

        $method = lcfirst(str_replace('hook', '', $method));

        // Format for display informations
        $params = array_map(function($p) {
            $type = gettype($p);
            if($type === 'NULL') {
                return 'null';
            }
            if($type === 'object') {
                $p = get_class($p);
            }
            if($type === 'array') {
                $p = '['.implode(', ', array_map(function($v) use ($p) {
                    return "'$v' => ".gettype($p);
                }, array_keys($p))).']';
            }
            if($type === 'string') {
                $p = "'$p'";
            }
            return "($type) $p";
        }, $params);

        // Add hook info
        $hookInfo = [];
        if(array_key_exists($method, $this->listHooks)) {
            $hookInfo = $this->listHooks[$method];
            if(array_key_exists('modules', $hookInfo)) {
                $hookInfo['modules'] = explode(',', $hookInfo['modules']);
                $hookInfo['modules'] = array_unique($hookInfo['modules']);
                foreach($hookInfo['modules'] as $i => $m) {
                	if($m === 'rs_hookviewer') {
                		unset($hookInfo['modules'][$i]);
                	}
                }
            }
        }

        $this->context->smarty->assign([
            'method' => $method,
            'params' => $params,
            'paramList' => $paramList,
            'hookInfo' => $hookInfo,
            'hash' => md5(json_encode([$method, $paramList] + $hookInfo))
        ]);

        // Display as comment
        if(Configuration::get('RS_HOOKVIEWER_DISPLAY_HOOKS_TYPE') === 'comment') {
            return $this->fetch('module:rs_hookviewer/views/hook_comment.tpl');
        }
        return $this->fetch('module:rs_hookviewer/views/hook_inline.tpl');
    }

    /**
     * Display hook name in page
     * 
     * @param string
     * @param string
     * @return string
     */
    public function __call($method, $arguments)
    {
    	return $this->showHookInfo($method, $arguments);
    }

    /**
     * Shown above the actual content of a Back Office page
     * 
     * @return string
     */
    public function hookDisplayBackOfficeTop($params)
    {
        $this->context->smarty->assign([
            'displayed' => $this->displayHooks,
            'link' => $this->context->link->getAdminLink('AdminModules', true, [], ['configure' => $this->name]),
        ]);
        return $this->showHookInfo(__FUNCTION__, [$params])
            . $this->fetch('module:rs_hookviewer/views/bo-switch.tpl');
    }

    /**
     * Added in the header of every page
     */
    public function hookDisplayHeader($params)
    {
        $this->context->controller->addJS($this->_path.'/public/rs_hookviewer.js');
        if($this->displayHooks) {
            $this->context->controller->registerStylesheet('rs_hookviewer-style', 'modules/'.$this->name.'/public/rs_hookviewer.css');
        }

        return $this->showHookInfo(__FUNCTION__, [$params]);
    }

    /**
     * Use this hook for your modals or any content you want to load at the very end
     */
    public function hookDisplayTop($params)
    {
        $output = $this->showHookInfo(__FUNCTION__, [$params]);
        if($this->displayHooks && Configuration::get('RS_HOOKVIEWER_DISPLAY_HOOKS_TYPE') === 'inline') {
            $output .= $this->fetch('module:rs_hookviewer/views/modal.tpl');
        }
        return $output;
    }

    /**
     * This hook is displayed at the end of the main content, before the footer
     */
    public function hookDisplayAdminEndContent($params)
    {
        $output = $this->showHookInfo(__FUNCTION__, [$params]);
        if($this->displayHooksBO && Configuration::get('RS_HOOKVIEWER_DISPLAY_HOOKS_TYPE') === 'inline') {
            $output .= $this->fetch('module:rs_hookviewer/views/modal.tpl');
        }
        return $output;
    }

    /**
     * Displayed between the <head></head> tags on every Back Office page (when logged in).
     */
    public function hookActionAdminControllerSetMedia($params)
    {
        $this->context->controller->addJS($this->_path.'/public/rs_hookviewer.js');
        if($this->displayHooksBO) {
            $this->context->controller->addCSS($this->_path.'/public/rs_hookviewer.css');
        }
        return $this->showHookInfo(__FUNCTION__, [$params]);
    }

    /**
     * Install on all hooks
     */
    public function install() 
    {
        // Retrieve all hooks
        $hooks = array_map(function($v) {
            return $v['name'];
        }, Db::getInstance()->executeS('
            SELECT `name`
            FROM '._DB_PREFIX_.'hook'
        ));
        return parent::install()
            && $this->registerHook($hooks);
    }

    /**
     * Set this module in first position for all hooks
     * 
     * @return bool
     */
    protected function resetModulePosition()
    {
        $hook_modules = Db::getInstance()->executeS('SELECT * FROM '._DB_PREFIX_.'hook_module');
        $by_hook = [];
        foreach($hook_modules as $hm) {
            if(!array_key_exists($hm['id_hook'], $by_hook)) {
                $by_hook[$hm['id_hook']] = [];
            }
            $by_hook[$hm['id_hook']][] = $hm;
        }
        $by_hook = array_filter($by_hook, function($v) {
            return count($v) > 1;
        });
        $updates = [];
        foreach($by_hook as $modules) {
            foreach($modules as $hm) {
                $new_position = 1;
                if((int) $hm['id_module'] !== (int) $this->id) {
                    $new_position = $hm['position'] + 1;
                }

                $updates[] = sprintf('UPDATE `%shook_module` SET `position` = %d WHERE `id_hook` = %d AND `id_module` = %d AND `id_shop` = %d;'
                    , _DB_PREFIX_
                    , $new_position
                    , $hm['id_hook']
                    , $hm['id_module']
                    , $hm['id_shop']
                );
            }
        }

        if(!empty($updates)) {
            $query = implode(PHP_EOL, $updates);
            return Db::getInstance()->execute($query);
        }
        return true;
    }

    /**
     * Module configuration page
     *
     * @return string
     */
    public function getContent()
    {
        $output = '';

        // Reset module position
        if(Tools::getValue('resetPosition')) {
            if($this->resetModulePosition() === true) {
                Tools::redirect($this->context->link->getAdminLink('AdminModules').'&configure='.$this->name.'&conf=4');
                exit;
            }
            $output .= $this->displayError($this->trans('Error while updating the status.', [], 'Admin.Notifications.Error'));
        }

        $output .= $this->postProcess();
        $output .= $this->renderForm();

        return $output;
    }

    /**
     * Configuration form
     *
     * @return string
     */
    public function renderForm()
    {
        $fieldsForm = [
            [
                'form' => [
                    'legend' => [
                        'title' => $this->trans('Settings', [], 'Admin.Global'),
                        'icon' => 'icon-cogs'
                    ],
                    'input' => [
                        [
                            'type' => 'switch',
                            'label' => $this->l('Display hooks in page'),
                            'name' => 'RS_HOOKVIEWER_DISPLAY_HOOKS',
                            'hint' => $this->l('Can row / crash layout'),
                            'desc' => $this->l('To see where the hooks are in the page'),
                            'is_bool' => true,
                            'values' => [
                                [
                                    'id' => 'display_hooks_on',
                                    'value' => 1,
                                    'label' => $this->trans('Yes', [], 'Admin.Global')
                                ],
                                [
                                    'id' => 'display_hooks_off',
                                    'value' => 0,
                                    'label' => $this->trans('No', [], 'Admin.Global')
                                ]
                            ],
                        ],
                        [
                            'type' => 'switch',
                            'label' => $this->l('Display in BO too'),
                            'name' => 'RS_HOOKVIEWER_DISPLAY_HOOKS_BO',
                            'is_bool' => true,
                            'values' => [
                                [
                                    'id' => 'display_hooks_bo_on',
                                    'value' => 1,
                                    'label' => $this->trans('Yes', [], 'Admin.Global')
                                ],
                                [
                                    'id' => 'display_hooks_bo_off',
                                    'value' => 0,
                                    'label' => $this->trans('No', [], 'Admin.Global')
                                ]
                            ],
                        ],
                        [
                            'type' => 'switch',
                            'label' => $this->l('Display hooks only if logged in BO'),
                            'name' => 'RS_HOOKVIEWER_DISPLAY_HOOKS_LOGGEDIN_BO',
                            'is_bool' => true,
                            'values' => [
                                [
                                    'id' => 'display_hooks_loggedin_bo_on',
                                    'value' => 1,
                                    'label' => $this->trans('Yes', [], 'Admin.Global')
                                ],
                                [
                                    'id' => 'display_hooks_loggedin_bo_off',
                                    'value' => 0,
                                    'label' => $this->trans('No', [], 'Admin.Global')
                                ]
                            ],
                        ],
                        [
                            'type' => 'switch',
                            'label' => $this->l('Display hooks only if debug mode is on'),
                            'name' => 'RS_HOOKVIEWER_DISPLAY_HOOKS_ONLY_DEBUG_MODE',
                            'desc' => sprintf('<a href="%s">%s</a>', $this->context->link->getAdminLink('AdminPerformance'), $this->l('Set debug mode')),
                            'is_bool' => true,
                            'values' => [
                                [
                                    'id' => 'display_hooks_only_debug_mode_on',
                                    'value' => 1,
                                    'label' => $this->trans('Yes', [], 'Admin.Global')
                                ],
                                [
                                    'id' => 'display_hooks_only_debug_mode_off',
                                    'value' => 0,
                                    'label' => $this->trans('No', [], 'Admin.Global')
                                ]
                            ],
                        ],
                        [
                            'type' => 'radio',
                            'label' => $this->l('Type of display'),
                            'name' => 'RS_HOOKVIEWER_DISPLAY_HOOKS_TYPE',
                            'is_bool' => true,
                            'values' => [
                                [
                                    'id' => 'display_hooks_type_inline',
                                    'value' => 'inline',
                                    'label' => $this->l('Inline (can crash layout)'),
                                ],
                                [
                                    'id' => 'display_hooks_type_comment',
                                    'value' => 'comment',
                                    'label' => $this->l('As HTML comment'),
                                ],
                            ],
                        ],
                        [
                            'type' => 'html',
                            'name' => 'tip',
                            'html_content' => sprintf('<p class="alert alert-info">%s</p>'
                            , sprintf($this->l('You can hide hook displays from console using %s and display again using %s'), '<code>hh()</code>', '<code>sh()</code>')),
                        ],
                        [
                            'type' => 'textbutton',
                            'label' => $this->l('Restrict by IP'),
                            'name' => 'RS_HOOKVIEWER_DISPLAY_HOOKS_IP',
                            'desc' => $this->l('Add the IPs that will see the hooks (separate with a comma)'),
                            'button' => [
                                'label' => $this->l('Add my IP'),
                                'attributes' => [
                                    'onclick' => 'addMyIp(\''.$_SERVER['REMOTE_ADDR'].'\')',
                                ]
                            ],
                        ],
                        [
                            'type' => 'html',
                            'name' => 'btn_reset_position',
                            'html_content' => sprintf('<a href="%s" class="btn btn-default">%s</a>'
                                , $this->context->link->getAdminLink('AdminModules', true, [], ['configure' => $this->name, 'resetPosition' => 1])
                                , $this->l('Set module in first position of all hooks')
                            ),
                        ],
                    ],
                    'submit' => [
                        'title' => $this->trans('Save', [], 'Admin.Actions'),
                    ],
                ],
            ],
        ];

        $helper = new HelperForm();
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
        $helper->title = $this->displayName;
        $helper->submit_action = 'submit'.$this->name;
        $helper->fields_value = $this->getConfigFieldsValues();

        return $helper->generateForm($fieldsForm);
    }

    /**
     * Module configuration form values
     *
     * @return array
     */
    protected function getConfigFieldsValues()
    {
        $data = [];
        foreach(self::CONFIG_FIELDS as $f) {
            $data[$f] = Configuration::get($f);
        }
        return $data;
    }

    /**
     * Process module configuration form
     *
     * @return string
     */
    public function postProcess()
    {
        $output = '';
        $errors = [];

        // Settings
        if(Tools::isSubmit('submit'.$this->name)) {
            foreach(self::CONFIG_FIELDS as $f) {
                Configuration::updateValue($f, Tools::getValue($f));
            }

            if(empty($errors)) {
                Tools::redirect($this->context->link->getAdminLink('AdminModules').'&configure='.$this->name.'&conf=6');
            }

            $output = $this->displayError(implode('<br />', $errors));
        }

        return $output;
    }

}
