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
<div class="component hide-mobile-sm">
  <a class="shop-state"
      href="{$link}"
      title="{if !$displayed}{l s='Hidden' mod='rs_hookviewer'}{else}{l s='Displayed' mod='rs_hookviewer'}{/if}"
  >
    <i class="material-icons">extension{if !$displayed}_off{/if}</i>
    <span>{l s='Display hooks' mod='rs_hookviewer'}</span>
  </a>
</div>