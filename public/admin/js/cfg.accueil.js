/**
 * Copyright (c) 2012, Agence Française Informatique (AFI). All rights reserved.
 *
 * AFI-OPAC 2.0 is free software; you can redistribute it and/or modify
 * it under the terms of the GNU AFFERO GENERAL PUBLIC LICENSE as published by
 * the Free Software Foundation.
 *
 * There are special exceptions to the terms and conditions of the AGPL as it
 * is applied to this software (see README file).
 *
 * AFI-OPAC 2.0 is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU AFFERO GENERAL PUBLIC LICENSE for more details.
 *
 * You should have received a copy of the GNU AFFERO GENERAL PUBLIC LICENSE
 * along with AFI-OPAC 2.0; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301  USA 
 */

function opacBlocksSorting(moduleSortUrl, currentProfilId) {
	opacBlocksSortingInitPositions();

  $('div.layout-division>div').sortable({
    cursor: 'move',
    connectWith: 'div.layout-division>div',
    stop: function(event, ui){
      var newDivision = $(ui.item).parents('div.layout-division').attr('id').split('-').pop();
      var newPosition = $(ui.item).prevAll('div[data-position]').length;
      if (newDivision == $(ui.item).attr('data-division')
          && newPosition == $(ui.item).attr('data-position')) 
        return;

      $.ajax(moduleSortUrl,
             {data: {fromDivision: $(ui.item).attr('data-division'),
                     toDivision: newDivision,
                     fromPosition: $(ui.item).attr('data-position'),
                     toPosition: newPosition,
                     profil: currentProfilId},
              success: function(data, textStatus, jqXHR){
                opacBlocksSortingInitPositions();
              }});
    }});
}


function opacBlocksSortingInitPositions() {
	var currentPositions = [0, 0, 0];
  $("div.layout-division>div>div").each(function(k, item) {
    if ("barre_nav" == $(item).attr("class")) 
      return;
    var division = $(item).parents("div.layout-division").attr("id").split("-").pop();
    $(item).attr("data-position", currentPositions[division-1]);
    currentPositions[division-1]++;
    $(item).attr("data-division", division);
  });
}