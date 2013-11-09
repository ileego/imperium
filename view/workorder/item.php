<?php
/**
    Work Order Items View

    Shows details about a Work Order Item

    @copyright  2008 Edoceo, Inc
    @package    edoceo-imperium
    @link       http://imperium.edoceo.com
    @since      File available since Release 1013
*/

if (empty($this->WorkOrderItem)) {
    echo '<p class="fail">Failed to load a Work Order Item</p>';
    return;
}

echo '<form action="'. $this->link('/workorder/item?' . http_build_query(array('id'=>$this->WorkOrderItem->id))) . '" method="post">';
echo '<table style="min-width:800px;">';

// Name
echo '<tr><td class="l">Name:</td><td colspan="5">'  . $this->formText('name',$this->WorkOrderItem->name) . '</td></tr>';

// Kind & Date
$time_base = mktime(0,0,0);
$time_list = array();
for ($m=0; $m<=86400; $m+=900) {
    $k = strftime('%H:%M',$time_base + $m);
    $v = strftime('%H:%M',$time_base + $m);
    $time_list[$k] = $v;
}

$d = $this->formText('date',$this->WorkOrderItem->date,array('id'=>'woi_date','size'=>12));
echo '<tr>';
echo '<td class="l">Kind:</td><td>' . $this->formSelect('kind',$this->WorkOrderItem->kind,null,WorkOrderItem::$kind_list) . '</td>';
echo '<td class="l">Date:</td><td>' . $d . '</td>';
echo '<td>' . $this->formSelect('time_alpha',$this->WorkOrderItem->time_alpha,null,$time_list) . '</td>';
echo '<td>' . $this->formSelect('time_omega',$this->WorkOrderItem->time_omega,null,$time_list) . '</td>';
echo '</tr>';

// Estimate: Quantity, Rate, Unit, Tax
$q = $this->formText('e_quantity',$this->WorkOrderItem->e_quantity,array('maxlength'=>8,'onblur'=>'toNumeric(this);','size'=>6));
$r = $this->formText('e_rate',$this->WorkOrderItem->e_rate,array('maxlength'=>12,'onblur'=>'toNumeric(this);','size'=>8));
$u = $this->formSelect('e_unit',$this->WorkOrderItem->e_unit,null,Base_Unit::getList());
$t = $this->formText('e_tax_rate',tax_rate_format($this->WorkOrderItem->e_tax_rate),array('maxlength'=>8,'onblur'=>'toNumeric(this);','size'=>4));
echo "<tr><td class='l'>Estimate:</td><td>$q</td><td><strong>@</strong>$r</td><td><strong>per</strong>&nbsp;$u<td class='b r'>Tax Rate:</td><td>$t&nbsp;%</td></tr>";

// Cost: Quantity, Rate, Unit, Tax
$q = $this->formText('a_quantity',$this->WorkOrderItem->a_quantity,array('maxlength'=>8,'onblur'=>'toNumeric(this);','size'=>6));
$r = $this->formText('a_rate',$this->WorkOrderItem->a_rate,array('maxlength'=>12,'onblur'=>'toNumeric(this);','size'=>8));
$u = $this->formSelect('a_unit',$this->WorkOrderItem->a_unit,null,Base_Unit::getList());
$t = $this->formText('a_tax_rate',tax_rate_format($this->WorkOrderItem->a_tax_rate),array('maxlength'=>8,'onblur'=>'toNumeric(this);','size'=>4));
echo "<tr><td class='l'>Actual:</td><td>$q</td><td><strong>@</strong>$r</td><td><strong>per</strong>&nbsp;$u<td class='b r'>Tax Rate:</td><td>$t&nbsp;%</td></tr>";

//echo "<tr><td class='b r'>Request:</td><td colspan='3'>".$this->formTextarea('request',$this->WorkOrderItem->request,array('cols'=>64,'rows'=>'4'))."</td></tr>";
echo '<tr><td class="l">Note:</td><td colspan="5">' . $this->formTextarea('note',$this->WorkOrderItem->note,array('cols'=>64,'rows'=>4)) . '</td></tr>';
echo "<tr>";
echo "<td class='l'><span title='The Status of this Item, Completed Items will be Billed when creating an Invoice'>Status:</span></td>";
echo '<td colspan="3">';
echo '<input name="status" type="text" value="' . $this->WorkOrderItem->status . '">';
echo $this->formSelect('status',$this->WorkOrderItem->status,null, $this->ItemStatusList);
echo '</td>';
echo '</tr>';

// Notify
echo '<tr><td class="l">';
echo '<span title="Input an email address here and a notification email will be sent">Notify:</span></td>';
echo '<td colspan="5">' . $this->formText('notify',$this->WorkOrderItem->notify) . '</td>';
echo '</tr>';

echo "</table>";

echo '<div class="cmd">';
echo '<input name="workorder_id" type="hidden" value="' . $this->WorkOrderItem->workorder_id . '">';
echo $this->formSubmit('c','Save');
if (!empty($this->WorkOrderItem->id)) {
    echo $this->formSubmit('c','Delete');
}
echo '</div>';

echo '</form>';

// History
$args = array(
    'list' => $this->WorkOrderItem->getHistory()
);
echo $this->partial('../elements/diff-list.phtml',$args);

?>
<script type="text/javascript">
$('#woi_date').datepicker();
$('#name').focus();
$('#notify').autocomplete({
    source:'/imperium/contact/ajax',
    change:function(event, ui) { if (ui.item) {  $("#notify").val(ui.item.id); $("#notify").val(ui.item.contact); } }
});
$('#time_alpha, #time_omega').on('change',function() {

	var alpha = $('#time_alpha').val();
	var omega = $('#time_omega').val();
	var m = null;

	if (m = alpha.match(/^(\d+):(\d+)$/)) {
		h_alpha = parseInt(m[1]);
		m_alpha = parseInt(m[2]) / 60 * 100;
	}

	if (m = omega.match(/^(\d+):(\d+)$/)) {
		h_omega = parseInt(m[1]);
		m_omega = parseInt(m[2]) / 60 * 100;
	}

	var h_delta = h_omega - h_alpha;
	var m_delta = Math.abs(m_omega - m_alpha);

	$('#a_quantity').val(h_delta + '.' + m_delta);
});

</script>