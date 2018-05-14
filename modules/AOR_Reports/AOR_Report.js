/**
 * Advanced OpenReports, SugarCRM Reporting.
 * @package Advanced OpenReports for SugarCRM
 * @copyright SalesAgility Ltd http://www.salesagility.com
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU AFFERO GENERAL PUBLIC LICENSE as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU AFFERO GENERAL PUBLIC LICENSE
 * along with this program; if not, see http://www.gnu.org/licenses
 * or write to the Free Software Foundation,Inc., 51 Franklin Street,
 * Fifth Floor, Boston, MA 02110-1301  USA
 *
 * @author SalesAgility <info@salesagility.com>
 */

$(document).ready(function () {

  $('#download_pdf_button_old').click(function () {

    var _form = addParametersToForm('DownloadPDF');

    var rGraphs = document.getElementsByClassName('resizableCanvas');
    for (var i = 0; i < rGraphs.length; i++) {
      _form.append('<input type="hidden" id="graphsForPDF" name="graphsForPDF[]" value=' + rGraphs[i].toDataURL() + '>');
    }

    _form.submit();

    $("#formDetailView #graphsForPDF").remove();
  });

  $('#download_csv_button_old').click(function () {

    var _form = addParametersToForm('Export');

    _form.submit();
  });

  $('#updateParametersButton').click(function(){

    var _form = addParametersToForm('DetailView');

    _form.submit();
  });
});


function updateTimeDateFields(fieldInput, ln) {
  // datetime combo fields
  if (typeof fieldInput === 'undefined'
    && $("[name='aor_conditions_value" + ln + "']").val()
    && $("[name='aor_conditions_value" + ln + "']").hasClass('DateTimeCombo')) {
    var datetime = $("[name='aor_conditions_value\\[" + ln + "\\]']").val();
    var date = datetime.substr(0, 10);
    var formatDate = $.datepicker.formatDate('yy-mm-dd', new Date(date));
    fieldInput = datetime.replace(date, formatDate) + ':00';
  }
  return fieldInput;
}

function updateHiddenReportFields(ln, _form) {
// Fix for issue #1272 - AOR_Report module cannot update Date type parameter.
  if ($('#aor_conditions_value' + ln).length) {
    var fieldValue = $('#aor_conditions_value' + ln).val();
    var fieldSign = $('#aor_conditions_value' + ln).val();
    var fieldNumber = $('#aor_conditions_value' + ln).val();
    var fieldTime = $('#aor_conditions_value' + ln).val();

    _form.append('<input type="hidden" name="parameter_date_value[]" value="' + fieldValue + '">');
    _form.append('<input type="hidden" name="parameter_date_sign[]" value="' + fieldSign + '">');
    _form.append('<input type="hidden" name="parameter_date_number[]" value="' + fieldNumber + '">');
    _form.append('<input type="hidden" name="parameter_date_time[]" value="' + fieldTime + '">');
  }
}

function localToDbFormat(index, ln, fieldInput) {
// Fix for issue #1082 - change local date format to db date format
  if ($('#aor_conditions_value' + index + '').hasClass('date_input')) { // only change to DB format if its a date
    if ($('#aor_conditions_value' + ln + '').hasClass('date_input')) {
      fieldInput = $.datepicker.formatDate('yy-mm-dd', new Date(fieldInput));
    }
  }
  return fieldInput;
}

function addParametersToForm(action) {
  var _form = $('#formDetailView');
  _form.find('input[name=action]').val(action);

  $('.aor_conditions_id').each(function(index, elem){
    $elem = $(elem);
    var ln = $elem.attr('id').substr(17);
    var id = $elem.val();
    _form.append('<input type="hidden" name="parameter_id[]" value="'+id+'">');
    var operator = $("#aor_conditions_operator\\["+ln+"\\]").val();
    _form.append('<input type="hidden" name="parameter_operator[]" value="'+operator+'">');
    var fieldType = $('#aor_conditions_value_type\\['+ln+'\\]').val();
    _form.append('<input type="hidden" name="parameter_type[]" value="'+fieldType+'">');
    var fieldInput = $('#aor_conditions_value\\['+ln+'\\]').val();
    updateTimeDateFields(fieldInput, ln);
    updateHiddenReportFields(ln, _form);
  });

  return _form;
}

function openProspectPopup() {

  var popupRequestData = {
    "call_back_function": "setProspectReturn",
    "form_name": "EditView",
    "field_to_name_array": {
      "id": "prospect_id"
    }
  };

  open_popup('ProspectLists', '600', '400', '', true, false, popupRequestData);

}

function setProspectReturn(popup_reply_data) {

  var callback = {
    success: function (result) {
      //report_rel_modules = result.responseText;
      //alert('pass '+result.responseText);
    },
    failure: function (result) {
      //alert('fail '+result.responseText);
    }
  }

  var prospect_id = popup_reply_data.name_to_value_array.prospect_id;
  var record = document.getElementsByName('record')[0].value;

  YAHOO.util.Connect.asyncRequest("GET", "index.php?module=AOR_Reports&action=addToProspectList&record=" + record + "&prospect_id=" + prospect_id, callback);


}

function changeReportPage(record, offset, group_value, table_id) {
  var paginationButtonCaller = $(this);
  var query = "?module=AOR_Reports&action=changeReportPage&record=" + record + "&offset=" + offset + "&group=" + group_value;
  $('.aor_conditions_id').each(function (index, elem) {
    $elem = $(elem);
    var ln = $elem.attr('id').substr(17);
    var id = $elem.val();
    query += "&parameter_id[]=" + id;
    var operator = $("#aor_conditions_operator\\[" + ln + "\\]").val();
    query += "&parameter_operator[]=" + operator;
    var fieldType = $('#aor_conditions_value_type\\[' + ln + '\\]').val();
    query += "&parameter_type[]=" + fieldType;
    var fieldInput = $('#aor_conditions_value\\[' + ln + '\\]').val();
    query += "&parameter_value[]=" + fieldInput;
  });

  $.get(query).done(
    function (data) {
      $('#report_table_' + table_id).replaceWith(data);
    }
  );
}