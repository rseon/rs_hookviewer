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
// Add ip
var addMyIp = function addMyIp(ip) {
  var target = $('#RS_HOOKVIEWER_DISPLAY_HOOKS_IP');
  if(target.length > 0) {
      var value = target.val();
      if (value === '') {
        value = ip;
      } else if (value.indexOf(ip) === -1) {
        value += ',' + ip;
      }
      target.val(value);
  }
};

// Event on click
$(function() {
  $('.rs_hookviewer-hook').on('click', function () {
    var $modal = $('#modalHookInfo');
    $modal
      .find('.rs_hookviewer-modal--body')
      .html($('#hookInfo_' + $(this).data('hash')).html());
    $modal.show();
  });
})

// Close modal
var rs_hookviewerCloseModal = function rs_hookviewerCloseModal() {
  $('#modalHookInfo').hide();
};


// Console helpers
/*console.log(
  '%c Rs_Hookviewer : Vous pouvez masquer les hooks avec hh() et les réafficher avec sh() ',
  'background: blue; color: white;'
);*/
var hh = function hh() {
  $('.rs_hookviewer-hook').hide();
  console.info(
    '%c Tous les hooks ont été masqués ',
    'background: green; color: white;'
  );
};
var sh = function sh() {
  $('.rs_hookviewer-hook').show();
  console.info(
    '%c Tous les hooks sont affichés ',
    'background: green; color: white;'
  );
};
