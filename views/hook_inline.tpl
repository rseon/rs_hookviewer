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
<span class="rs_hookviewer-hook" data-hash="{$hash}">{$method}({$paramList})</span>
{if count($hookInfo) > 0}
  <span class="rs_hookviewer-hookInfo" id="hookInfo_{$hash}">
    <p><strong>{l s='ID' mod='rs_hookviewer'} :</strong> <code>{$hookInfo.id_hook}</code></p>
    <p><strong>{l s='Name' mod='rs_hookviewer'} :</strong> <code>{$hookInfo.name}</code></p>
    <p><strong>{l s='Title' mod='rs_hookviewer'} :</strong> {$hookInfo.title}</p>
    <p><strong>{l s='Description' mod='rs_hookviewer'} :</strong> {$hookInfo.description}</p>
    <p>
      <strong>{l s='Hooked modules' mod='rs_hookviewer'} :</strong>
      {if count($hookInfo.modules) > 0}
        {foreach from=$hookInfo.modules item='module'}
          <code>{$module}</code>
        {/foreach}
      {else}
        -
      {/if}
    </p>
    <p><strong>{l s='Parameters' mod='rs_hookviewer'} :</strong></p>
    <pre>
{foreach from=$params item='value' key='key'}
  '{$key}' => {$value}
{/foreach}</pre>
  </span>
{/if}