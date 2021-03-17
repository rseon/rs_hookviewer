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
{* We don't use Bootstrap modal to not be dependent on theme. *}
<div class="rs_hookviewer-modal" id="modalHookInfo">
    <div class="rs_hookviewer-modal--dialog">
        <div class="rs_hookviewer-modal--content">
            <div class="rs_hookviewer-modal--header">
                <h5 class="rs_hookviewer-modal--title">{l s='Hook informations' mod='rs_hookviewer'}</h5>
            </div>
            <div class="rs_hookviewer-modal--body"></div>
            <div class="rs_hookviewer-modal--footer">
                <button type="button" class="rs_hookviewer-modal--button" onclick="rs_hookviewerCloseModal()">
                    {l s='Close' mod='rs_hookviewer'}
                </button>
            </div>
        </div>
    </div>
</div>

