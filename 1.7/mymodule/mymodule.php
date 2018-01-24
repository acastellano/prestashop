<?php
/**
 * @copyright    Copyright (C). All rights reserved.
 * @author Author Name <me@domain.com>
 * @version v1.0
 */
if(!defined('_PS_VERSION_'))
 exit;

class MyModule extends Module
{
 public function __construct()
 {
  $this->name = 'mymodule';
  $this->tab = 'tab';
  $this->version = '1.0.0';
  $this->author = 'author';
  $this->need_instance = 0;
  $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);
  $this->bootstrap = true;

  parent::__construct();

  $this->displayName = $this->l('displayName');
  $this->description = $this->l('Description');

  $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
  if(!Configuration::get('mymodule'))
   $this->warning = $this->l('No name provided');
 }

 public function install()
 {
  if(!parent::install() ||
   !$this->registerHook('header') ||
   !Configuration::updateValue('mymodule_value_1', '1') ||
   !Configuration::updateValue('mymodule_value_2', '1')
  ){
   return false;
  }
  $this->_clearCache('mymodule.tpl');

  return true;
 }

 public function uninstall()
 {
  $this->_clearCache('mymodule.tpl');
  if(!parent::uninstall() ||
   !Configuration::deleteByName('mymodule_value_1') ||
   !Configuration::deleteByName('mymodule_value_2')
  )
   return false;

  return true;
 }

 public function getContent()
 {
  $output = '';

  if(Tools::isSubmit('submit' . $this->name)){
   Configuration::updateValue('mymodule_value_1', Tools::getValue('mymodule_value_1'));
   Configuration::updateValue('mymodule_value_2', Tools::getValue('mymodule_value_2'));
   $this->_clearCache('mymodule.tpl');
   $output .= $this->displayConfirmation($this->l('Settings updated successfully'));
  }

  return $output . $this->renderForm();
 }

 public function renderForm()
 {
  // Get default language
  $default_lang = (int)Configuration::get('PS_LANG_DEFAULT');

  $fields_form = array(
   'form' => array(
    'legend' => array(
     'title' => $this->l('Settings'),
     'icon' => 'icon-cogs'
    ),
    'input' => array(
     array(
      'type' => 'switch',
      'label' => $this->l('same config field'),
      'name' => 'mymodule_value_1',
      'desc' => $this->l('Enable or Disable'),
      'is_bool' => true,
      'values' => array(
       array(
        'id' => 'active_on',
        'value' => 1,
        'label' => $this->l('Enabled')
       ),
       array(
        'id' => 'active_off',
        'value' => 0,
        'label' => $this->l('Disabled')
       )
      ),
     ),
     array(
      'type' => 'switch',
      'label' => $this->l('same config field'),
      'name' => 'mymodule_value_2',
      'desc' => $this->l('Enable or Disable'),
      'is_bool' => true,
      'values' => array(
       array(
        'id' => 'active_on',
        'value' => 1,
        'label' => $this->l('Enabled')
       ),
       array(
        'id' => 'active_off',
        'value' => 0,
        'label' => $this->l('Disabled')
       )
      ),
     ),
     array(
      'type' => 'text',
      'label' => $this->l('another field'),
      'name' => 'mymodule_value_1',
      'class' => 'fixed-width-lg',//fixed-width-xxl,fixed-width-md,fixed-width-xs
      'desc' => $this->l('field description.'),
     ),
    ),
    'submit' => array(
     'title' => $this->l('Save'),
    )
   ),
  );

  $helper = new HelperForm();
  // Module, token and currentIndex
  $helper->module = $this;
  $helper->name_controller = $this->name;
  $helper->token = Tools::getAdminTokenLite('AdminModules');
  $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;

  // Language
  $helper->default_form_language = $default_lang;
  $helper->allow_employee_form_lang = $default_lang;

  $helper->title = $this->displayName;
  $helper->show_toolbar = true;
  $helper->toolbar_scroll = true;
  $helper->table = $this->table;
  $helper->identifier = $this->identifier;
  $helper->submit_action = 'submit' . $this->name;
  $helper->tpl_vars = array(
   'fields_value' => $this->getConfigFieldsValues(),
   'languages' => $this->context->controller->getLanguages(),
   'id_language' => $this->context->language->id
  );
  $helper->toolbar_btn = array(
   'save' =>
    array(
     'desc' => $this->l('Save'),
     'href' => AdminController::$currentIndex . '&configure=' . $this->name . '&save' . $this->name .
      '&token=' . Tools::getAdminTokenLite('AdminModules'),
    ),
   'back' => array(
    'href' => AdminController::$currentIndex . '&token=' . Tools::getAdminTokenLite('AdminModules'),
    'desc' => $this->l('Back to list')
   )
  );

  return $helper->generateForm(array($fields_form));
 }

 public function getConfigFieldsValues()
 {
  return array(
   'mymodule_value_1' => Tools::getValue('mymodule_value_1', Configuration::get('mymodule_value_1')),
   'mymodule_value_2' => Tools::getValue('mymodule_value_2', Configuration::get('mymodule_value_2')),
  );
 }

 public function hookDisplayHeader()
 {
  $this->context->controller->addCSS($this->_path.'css/mymodule.css', 'all');
  $confVars = $this->getConfigFieldsValues();
  $this->smarty->assign('confVars', $confVars);

  $html = $this->display(__FILE__, 'mymodule.tpl');

  return $html;
 }
}

?>