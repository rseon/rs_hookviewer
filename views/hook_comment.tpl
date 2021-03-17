{**
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
 *}

<!-- rs_hookviewer-hook : {$method}({$paramList nofilter})
  {if count($hookInfo) > 0}
  {l s='Hook informations' mod='rs_hookviewer'} :
      {l s='ID' mod='rs_hookviewer'} : {$hookInfo.id_hook}
      {l s='Name' mod='rs_hookviewer'} : {$hookInfo.name}
      {l s='Title' mod='rs_hookviewer'} : {$hookInfo.title}
      {l s='Description' mod='rs_hookviewer'} : {$hookInfo.description}
      {l s='Hooked modules' mod='rs_hookviewer'} : {$hookInfo.modules|implode:' '}
      {l s='Parameters' mod='rs_hookviewer'} :
      {foreach from=$params item='value' key='key'}
        '{$key}' => {$value}
      {/foreach}

  {/if}
-->
