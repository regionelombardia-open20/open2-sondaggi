/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 * @package   open20\amos-sondaggi
 * @author    Aria S.p.A. <protocollo@pec.ariaspa.it>
 * @license   GNU General Public License v3.0 <https://www.gnu.org/licenses/gpl-3.0.html>
 * @link      https://www.ariaspa.it/
 *
 * This file is part of OPEN 2.0.
 *
 * OPEN 2.0 is free software: you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OPEN 2.0 is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OPEN 2.0. If not, see <https://www.gnu.org/licenses/>.
 */

var Sondaggi = {
    'domandaSelector': '#ordina_dopo',
    'submitSelector': '#submit-pagina',
    'initCondizione': function () {

        $('#condizione_necessaria-id').prop('disabled', true);
        $('#condizione_necessaria-libera-id').prop('disabled', true);
        //$('#ordina-dopo').prop('disabled', true);
        $('#ordina_dopo').prop('disabled', true);
        $(this.submitSelector).prop('disabled', true);
        $(this.submitSelector).hide();
        $('#sondaggidomande-inline').prop('disabled', true);
        var tipologiaSalv = $('#sondaggidomande-sondaggi_domande_tipologie_id').val();
        if (tipologiaSalv == 1 || tipologiaSalv == 2)
            $('#sondaggidomande-inline').prop('disabled', false);
        if (tipologiaSalv == 1 || tipologiaSalv == 4 || tipologiaSalv == 8) {
            $('#selezioni-minime-massime').show();
            $('#selezioni-minime-massime-label').show();
            $('#selezione-classe-validatrice').hide();
            $('#selezione-modello').hide();
        } else {
            if (tipologiaSalv == 9 || tipologiaSalv == 14) {
                $('#selezione-classe-validatrice').show();
                $('#selezione-modello').show();
            } else {
                $('#selezione-classe-validatrice').hide();
                $('#selezione-modello').hide();
            }
            $('#selezioni-minime-massime').hide();
            $('#selezioni-minime-massime-label').hide();
        }

        $('#sondaggidomande-sondaggi_domande_tipologie_id').change(function (event, messages) {
            var risp = $('#sondaggidomande-sondaggi_domande_tipologie_id').val();
            if (risp == 5 || risp == 6) {
                $('#selezione-classe-validatrice').hide();
                $('#selezione-modello').hide();
                $(this.submitSelector).prop('disabled', false);
                $('#sondaggidomande-inline').prop('disabled', true);
                $(this.submitSelector).show();
                $('#selezioni-minime-massime').hide();
                $('#selezioni-minime-massime-label').hide();
            } else if (risp == 3 || risp == 4 || risp == 15 || risp == 16 || risp == 17) {
                $('#selezione-classe-validatrice').hide();
                $('#selezione-modello').hide();
                $('#sondaggidomande-inline').prop('disabled', true);
                $(this.submitSelector).prop('disabled', true);
                $(this.submitSelector).hide();
                if (risp == 4) {
                    $('#selezioni-minime-massime').show();
                    $('#selezioni-minime-massime-label').show();
                } else {
                    $('#selezioni-minime-massime').hide();
                    $('#selezioni-minime-massime-label').hide();
                }
            } else {
                $('#sondaggidomande-inline').prop('disabled', false);
                $(this.submitSelector).prop('disabled', true);
                $(this.submitSelector).hide();
                $('#selezione-classe-validatrice').hide();
                $('#selezione-modello').hide();
                if (risp == 1) {
                    $('#selezioni-minime-massime').show();
                    $('#selezioni-minime-massime-label').show();
                } else {
                    if (risp == 9 || risp == 14) {
                        $('#selezione-classe-validatrice').show();
                        $('#selezione-modello').show();
                    }
                    $('#selezioni-minime-massime').hide();
                    $('#selezioni-minime-massime-label').hide();
                }
            }
        });
        $('#ordina_dopo').change(function (event, messages) {
            var ordinamento = $('input[type="radio"]:checked').val();
            if (ordinamento == "dopo") {
                //$('#ordina-dopo').prop('disabled', false);
                setTimeout(function () {
                    $('#ordina_dopo').prop('disabled', false);
                }, 500);
            } else {
                $('#ordina_dopo').prop('disabled', true);
            }
        });
        setTimeout(function () {
            var elpre = $('#domcond');
            if (elpre.prop('checked')) {
                $('#condizione_necessaria-id').prop('disabled', false);
                $('#condizione_necessaria-libera-id').prop('disabled', false);
            } else {
                $('#condizione_necessaria-id').prop('disabled', true);
                $('#condizione_necessaria-libera-id').prop('disabled', true);
            }
            var bc = $('#condizione_necessaria-id').prop('disabled');
        }, 500);

        $('#domcond').change(function (event, messages) {
            var el = $('#domcond');
            if (el.prop('checked')) {
                $('#condizione_necessaria-id').prop('disabled', false);
                $('#condizione_necessaria-libera-id').prop('disabled', false);
            } else {
                $('#condizione_necessaria-id').prop('disabled', true);
                $('#condizione_necessaria-libera-id').prop('disabled', true);
            }
        });
        $('#ordinamento-radio').change(function (event, messages) {
            var ord = $('input[type="radio"]:checked').val();
            if (ord == "dopo") {
                //$('#ordina-dopo').prop('disabled', false);
                $('#ordina_dopo').prop('disabled', false);
            } else {
                //$('#ordina-dopo').prop('disabled', true);
                $('#ordina_dopo').prop('disabled', true);
            }
        });
        $('#sondaggi_id-id').change(function (event, messages) {
            var ell = $('#domcond');
            setTimeout(function () {
                if (ell.prop('checked')) {
                    $('#condizione_necessaria-id').prop('disabled', false);
                    $('#condizione_necessaria-libera-id').prop('disabled', false);
                } else {
                    $('#condizione_necessaria-id').prop('disabled', true);
                    $('#condizione_necessaria-libera-id').prop('disabled', true);
                }
            }, 500);
        });
    },
};

$(document).ready(function () {

    Sondaggi.initCondizione();

    $(document).on('change', Sondaggi.domandaSelector, function () {
        Sondaggi.initCondizione();
    });

});
