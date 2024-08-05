<?php
	if (!defined('main_def')) die();
	if (isset($_GET['r'])) {
		echo GetErrorTxt($_GET['r']);
	}
?>
<p><b>What can I get for coins: </b>various services in the section <a href="index.php?op=pers">Characters</a>, in-game items in the section <a href="index.php?op=shop">Shop</a>, and also the opportunity <a href="index.php?op=klan">to set a clan icon</a>.</p><br>
<?php	
$post_data = [
	'op' => 'page_money',
	'id' => $_SESSION['id'],
	'ip' => getIP()
];
Plugins::pageCurlProcess($post_data);
$result = CurlPage($post_data, 5);
$a = UnpackAnswer($result);
Plugins::pageInjectHTML('top', $a);
if (!is_array($a) && !CheckNum($result)) echo GetErrorTxt($result); else
if ($a['errorcode'] == 81 || (isset($a['bancount']) && $a['bancount']>0)) {
	if ($a['bancount']>0) echo GetErrorTxt(81);
	WriteBanTable($a);
} else 
if ($a['errorcode'] == 0) {
	$captcha = new captcha();
	echo $captcha->GetCaptchaScript();
    ?>

<script>
function checksend(){				
	if ((document.sendform.id.value.length < 1)||(document.sendform.id.value.length > 7)) { alert ("Enter a valid recipient account number");
	} else 
	if (document.sendform.gold.value == 0){ alert ("Cannot send 0 coins");
	} else 
	if ((document.sendform.gold.value.length < 1)||(document.sendform.gold.value.length > 5)){ alert ("Enter a valid coin value");
	} else {
		if (window.confirm("Make sure you have entered the correct recipient account number\r\rTransfer coins to account №"+document.sendform.id.value+"?")) 
			if (window.confirm("This is the last warning, if you made a mistake with the recipient account number, do not write to the administration about the erroneous transfer, such messages will be ignored!\r\n\r\nContinue transfer?")) {document.sendform.submit();}
	}
}
function checkoutform(){
	if ((document.outform.goldcount.value.length < 1)||(document.outform.goldcount.value.length > 7)||(document.outform.goldcount.value < 1)) { alert ("Enter a valid amount of gold");
	} else {	
		if (window.confirm("Do you really want to exchange "+document.outform.goldcount.value+" coins for in-game gold?")) document.outform.submit();
	}
}	
function checkdigit(field)
{
    let val;
    if (field.value === "")
	      return false;
	    val = parseInt(field.value);
	    if (isNaN(val))
    	      return false;
    	if (val != field.value)
	      return false; 		
	    return val > 0;
}
</script>

<div align="center">
	<table border=0 cellpadding=2px cellspacing=0>
	<tr><td>
<?php
AssignPData(Auth::$result['act_key'] ?? '') || die();
if ($lk_transfer_enabled) { ?>
	<form name="sendform" method="post" action="?op=act&n=10&num=0&rand=<?=time()?>">
		<span class="label label-success">Transferring coins to another account</span>
		<table class="table table-bordered table-striped table-condensed">
			<tr>
				<td><div title="Recipient account number" data-rel="tooltip"><input name="id" maxlength="7" type="text" placeholder="Recipient account number in LC"></div></td>
			</tr>
			<tr>
				<td><div title="Transfer amount" data-rel="tooltip"><input name="gold" type="text" maxlength="5" placeholder="Transfer amount"></div></td>
			</tr>
			<tr>
				<td><div title="Comment" data-rel="tooltip"><input name="comment" type="text" maxlength="20" placeholder="Comment (optional)"></div></td>
			</tr>				
			<tr>
				<td><?=$captcha->ShowCaptcha()?></td>
			</tr>
			<tr>
				<td><input type="button" class="btn btn-danger" value="Transfer coins" onclick="checksend();"></td>
			</tr>				
	</table>
	</form><?php } ?></td>
	<td style="vertical-align:top"><?php if ($lk2game_enabled) { ?>
	<form name="outform" method="post" action="?op=act&n=11&num=0&rand=<?=time()?>">
		<span class="label label-success">Withdraw coins into the game</span>
		<table class="table table-bordered table-striped table-condensed" style="width:250px">		
			<tr>
				<td><div title="Number of coins" data-rel="tooltip"><input type="text" name="goldcount" maxlength="7" placeholder="Number of coins" onkeyup="calc_gold()"></div></td>					
			</tr>
			<tr>
				<td><div title="Gold will be credited to the game" data-rel="tooltip">Gold: <span id="plus_gold">0</span></div></td>
			</tr>
			<tr>
				<td><input type="button" class="btn btn-danger" value="Exchange for game gold" onclick="checkoutform();"></td></tr>
		</table>
	</form>
	<script type="text/javascript">
	function calc_gold(){
        let lk2game_exchange_rate = <?=$lk2game_exchange_rate?>;
        let Q1 = document.outform.goldcount;
        let Q2 = document.getElementById('plus_gold');
        let x = Q1.value * lk2game_exchange_rate;
		if (isNaN(x) || x<0) x = 0;
		Q2.innerHTML = parseInt(x);
	}
	</script>
<?php } ?>
</td></tr>
</table>
<?php if ($lk_gold_exchange_enabled || $lk_silver_exchange_enabled) { ?>
<div class="row-fluid">
	<div class="box span12">
		<div class="box-header well" data-original-title>
			<h2>Coin exchange</h2>
		</div>
		<div class="box-content"><?php
		if (count($a['roles']) < 1) echo ErrorMessage('Not enough items found on characters for exchange.'); else {
			echo '
			<table class="table table-bordered table-striped table-condensed" style="width:98%">
			<thead><tr>
				<th>Character</th>';
			if ($lk_gold_exchange_enabled) echo '	<th>'.$a['gold_item_name'].', pcs</th>';
			if ($lk_silver_exchange_enabled) echo '	<th>'.$a['silver_item_name'].', pcs</th>';
			echo '	<th>Actions</th>		
			</tr>
			</thead>
			<tbody>';
			foreach ($a['roles'] as $i => $val){ 
				$t = '';	?>
				<?php if ($lk_gold_exchange_enabled) $t = '
				<script language="javascript"> 
					function changeg'.$i.'(){
						let a=document.goldform'.$i.';
						let c=document.getElementById("gold'.$i.'");
						c.innerHTML = a.i.value*'.$gold_item_exchange_rate.';
					}			
					function checkg'.$i.'(){
						let a=document.goldform'.$i.';
						if (!checkdigit(a.i)) {
							alert("Enter a valid value");
						} else {
							let x=a.i.value*'.$gold_item_exchange_rate.';
							if (x>'.$val['gold_item_cnt'].') {
								alert("Not enough '.$a['gold_item_name'].' items for exchange");	
							} else
							if (window.confirm("Are you sure you want to exchange "+x+" '.$a['gold_item_name'].' for "+a.i.value+" gold coins?")) a.submit();
						}
					}
				</script>
				<form name="goldform'.$i.'" method="get">
				<input type="hidden" name="op" value="act"><input type="hidden" name="n" value="5"><input type="hidden" name="num" value="'.$val['index'].'">
				<table border="0px" cellpadding="0" cellspacing="0" style="border:0; margin:0; padding:0">
				<tr>
					<td style="border:0"><input type="button" class="btn btn-danger" value="Exchange" style="width:80px" onclick="checkg'.$i.'();"></td>
					<td style="border:0; padding:3px"><input style="width:40px" name="i" onkeyup="changeg'.$i.'();">&nbsp;<span class="gold"></span></td>
					<td style="border:0">for <b><span id="gold'.$i.'">0</span></b> '.$a['gold_item_name'].'</td>
				</tr>			
				</table>
				</form>
		';
				if ($lk_silver_exchange_enabled) $t .= '
				<script language="javascript"> 
					function changes'.$i.'(){
						let a=document.silverform'.$i.';
						let c=document.getElementById("silver'.$i.'");
						c.innerHTML=a.i.value*'.$silver_item_exchange_rate.';
					}			
					function checks'.$i.'(){
						let a=document.silverform'.$i.';
						if (!checkdigit(a.i)) {
							alert("Enter a valid value");
						} else {
							let x=a.i.value*'.$silver_item_exchange_rate.';
							if (x>'.$val['silver_item_cnt'].') {
								alert("Not enough '.$a['silver_item_name'].' items for exchange");	
							} else
							if (window.confirm("Are you sure you want to exchange "+x+" '.$a['silver_item_name'].' for "+a.i.value+" gold coins?")) a.submit();
						}
					}
				</script>
				<form name="silverform'.$i.'" method="get">
				<input type="hidden" name="op" value="act"><input type="hidden" name="n" value="6"><input type="hidden" name="num" value="'.$val['index'].'">
				<table border="0px" cellpadding="0" cellspacing="0" style="border:0; margin:0; padding:0">
				<tr>
					<td style="border:0"><input type="button" class="btn btn-danger" value="Exchange" style="width:80px" onclick="checks'.$i.'();"></td>
					<td style="border:0; padding:3px"><input style="width:40px" name="i" onkeyup="changes'.$i.'();">&nbsp;<span class="silver"></span></td>
					<td style="border:0">for <b><span id="silver'.$i.'">0</span></b> '.$a['silver_item_name'].'</td>
				</tr>			
				</table>
				</form>
		';	echo '<tr>';
			printf('<td style="color:#000000"><b>%s</b></td>', $val['name']);
			if ($lk_gold_exchange_enabled) printf('<td>%d</td>', $val['gold_item_cnt']);
			if ($lk_silver_exchange_enabled) printf('<td>%d</td>', $val['silver_item_cnt']);
			printf('<td>%s</td>', $t);
			echo '</tr>';
			}
			echo '</tbody></table>';
		}
		?>
		</div>
	</div>
</div>
<?php }
if (Auth::$is_admin) { ?>
<script>
function GetAccountInfo(){
	sOut = '<div class="userinfo"><center><img src="img/ajax-loaders/ajax-loader-1.gif" border=0> Getting data from server <img src="img/ajax-loaders/ajax-loader-1.gif" border=0></center></div>';
	$('#acc_info').html('');
	$('#acc_info').css('display' , 'none');
	login = document.addlk.acc_login.value.toLowerCase();
	if (login == '') return;
	$('#acc_info').html(sOut);
	$('#acc_info').css({'display' : 'inherit', 'margin-top' : '3px'});
	document.addlk.acc_login.value = login;	
	$.ajax({
	     url: 'ajax.php?login='+login,             // specify the URL and
	     dataType : "text",                     // type of data to be loaded
	     complete: function (data, textStatus) { // attach our handler to complete
		if (textStatus!=='success') {
			$('acc_info').html(textStatus);			
			return;
		}
	        $('#acc_info').html(data.responseText);
		$('[rel="tooltip"],[data-rel="tooltip"]').tooltip({"placement":"bottom",delay: { show: 50, hide: 10 }});
	     }
	});
}	
function CheckAddLkForm(){
	if (document.addlk.acc_login.value.length < <?=$login_min_len?> || document.addlk.acc_login.value.length > <?=$login_max_len?>) alert('Login must be from <?=$login_min_len?> to <?=$login_max_len?> characters'); else
	if (document.addlk.summ_gold.value.length < 1 || isNaN(parseInt(document.addlk.summ_gold.value))) alert('Enter a valid value for gold coins'); else
	if (document.addlk.summ_silver.value.length < 1 || isNaN(parseInt(document.addlk.summ_silver.value))) alert('Enter a valid value for silver coins'); else
	if (document.addlk.summ_gold.value == 0 && document.addlk.summ_silver.value == 0) alert('Cannot give 0 coins'); else
	if (document.addlk.summ_gold.value > 10000000 || document.addlk.summ_silver.value > 10000000) alert('Cannot give more than 10000000 coins'); else
	if (document.addlk.summ_gold.value < -10000000 || document.addlk.summ_silver.value < -10000000) alert('Cannot give less than -10000000 coins'); else
	if (window.confirm('Give coins to account '+document.addlk.acc_login.value+'?')) document.addlk.submit();
}
var timeout_id = 0;
</script>
<div class="row-fluid">
	<div class="box span12">
		<div class="box-header well" data-original-title>
			<h2>Issuance of LC coins <img src="img/admin.png" border=0></h2>
		</div>
		<div class="box-content">
			<form method="get" action="index.php" name="addlk">
			<input type="hidden" name="op" value="act">
			<input type="hidden" name="n" value="52">
			<input type="hidden" name="num" value="0">
			Account login: <input type="text" maxlength="<?=$login_max_len?>" name="acc_login" onKeyUp="clearTimeout(timeout_id); timeout_id = setTimeout(GetAccountInfo, 1000);">
			<span class="gold">&nbsp;</span><input type="text" class="config_cost" name="summ_gold" value="0">
			<span class="silver">&nbsp;</span><input type="text" class="config_cost" name="summ_silver" value="0">
			Reason: <input type="text" name="desc" maxlength="20">
			<input type="button" class="btn btn-inverse" onclick="CheckAddLkForm()" value="Give LC coins">
			</form>					
			<div id="acc_info" class="userinfo money_accinfo" align="center" style="display: none"></div>
		</div>
	</div>
</div>
<?php } ?>
</div><?php
	}
Plugins::pageInjectHTML('bottom', $a);