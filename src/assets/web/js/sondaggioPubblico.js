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

$(document).ready(function () {
    $.fn.setHtmlMessages = function(){
        var notCompilableHtml = $('#sondaggi-text_not_compilable_html').val();
        var endHtml = $('#sondaggi-text_end_html').val();
//        console.log(notCompilableHtml + 'sss');
        if(notCompilableHtml == 1){ console.log('bho');
            $('#text_not_compilable-id-html').attr('disabled', false);
            $('#sondaggi-text_not_compilable').attr('disabled', true);
            $('#htmltext-not-compilable').show();
            $('#areatext-not-compilable').hide();
        } else {
            $('#sondaggi-text_not_compilable').attr('disabled', false);
            $('#text_not_compilable-id-html').attr('disabled', true);
            $('#htmltext-not-compilable').hide();
            $('#areatext-not-compilable').show();
        }
        if(endHtml == 1){
            $('#text_end-id-html').attr('disabled', false);
            $('#sondaggi-text_end').attr('disabled', true);
            $('#htmltext-end').show();
            $('#areatext-end').hide();
        } else {
            $('#text_end-id-html').attr('disabled', true);
            $('#sondaggi-text_end').attr('disabled', false);
            $('#htmltext-end').hide();
            $('#areatext-end').show();
        }
    }
    //console.log(publicPost);
    
    //publicPost è definita nella _form della view sondaggi
    var pubblico = publicPost;  
    if(pubblico == 'null'){
        pubblico = $('#sondaggio-pubblico').val(); 
    }
    if (pubblico == 1 || pubblico == '1' || pubblico == 'true') {
        //$('.field-sondaggi-tipologie_entita').hide();
        $('.field-sondaggi-destinatari_pubblicazione').hide();
        //$('.field-sondaggi-attivita_formativa').show();
        $('#destinatari-pubblicazione-ruolo').attr('disabled', true);
        //$('#tipologie-attivita-pubblicazione').prop('disabled', true);//da usare nel caso si voglia associare un sondaggio ad un singolo corso
        //$('#attivita-formativa-pubblicazione').prop('disabled', false);
    } else {
        $('#destinatari-pubblicazione-ruolo').attr('disabled', false);
        //$('#tipologie-attivita-pubblicazione').prop('disabled', false);
        //$('#attivita-formativa-pubblicazione').prop('disabled', true);
        //$('.field-sondaggi-tipologie_entita').show();
        //$('.field-sondaggi-attivita_formativa').hide();
        $('.field-sondaggi-destinatari_pubblicazione').show();
    }

    $.fn.setHtmlMessages();

    $('#sondaggio-pubblico').change(function (event, messages) {
        $.fn.setHtmlMessages();
        var pubb = $('#sondaggio-pubblico').val();       
        if (pubb == 1 || pubb == '1') {
            //$('.field-sondaggi-tipologie_entita').hide();
            $('.field-sondaggi-destinatari_pubblicazione').hide();
            //$('.field-sondaggi-attivita_formativa').show();
            $('#destinatari-pubblicazione-ruolo').attr('disabled', true);
            //$('#tipologie-attivita-pubblicazione').prop('disabled', true);
            //$('#attivita-formativa-pubblicazione').prop('disabled', false);
        } else {
            $('#destinatari-pubblicazione-ruolo').attr('disabled', false);
            //$('#tipologie-attivita-pubblicazione').prop('disabled', false);
            //$('#attivita-formativa-pubblicazione').prop('disabled', true);
            //$('.field-sondaggi-tipologie_entita').show();
            //$('.field-sondaggi-attivita_formativa').hide();
            $('.field-sondaggi-destinatari_pubblicazione').show();
        }
    });
    
    $('#sondaggi-text_not_compilable_html').change(function(event, messages){
        $.fn.setHtmlMessages();        
    });
    
    $('#sondaggi-text_end_html').change(function(event, messages){
         $.fn.setHtmlMessages();  
    });        
    
});