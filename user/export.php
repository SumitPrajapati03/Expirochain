<?php $export_base = defined('BASE_URL') ? BASE_URL : ''; ?>
<link rel="stylesheet" href="<?php echo $export_base; ?>/user/css/export.css">


<!-- EXPORT POPUP -->

<div id="exportPopup" class="export-popup">

<div class="export-modal">

<h3>Export Table</h3>
<p class="choose-text">Choose format</p>

<form id="exportForm">

<label>
<input type="radio" name="exportType" value="pdf">
PDF (.pdf)
</label>

<label>
<input type="radio" name="exportType" value="csv">
CSV (.csv)
</label>

<label>
<input type="radio" name="exportType" value="excel">
CSV for Excel
</label>

<label>
<input type="radio" name="exportType" value="word">
Word (.doc)
</label>

<label>
<input type="radio" name="exportType" value="json">
JSON (.json)
</label>

<label>
<input type="radio" name="exportType" value="txt">
Plain Text (.txt)
</label>

</form>

<div class="popup-actions">
<button onclick="closeExportPopup()" class="cancel-btn">Cancel</button>
<button onclick="confirmExport()" class="ok-btn">OK</button>
</div>

</div>

</div>