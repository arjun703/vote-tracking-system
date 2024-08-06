<?php

use Power\DB;

if (!defined('main_def')) {
	die();
}
if (isset($_GET['r'])) {
	echo GetErrorTxt($_GET['r']);
}
$allowed_merchants = Merchants::getEnabledList();
Plugins::pageInjectHTML('top', $allowed_merchants);
?><center>
    <h3>Donation for project development</h3><?php
if (!$donate_system_enabled || !count($allowed_merchants))
{
	echo ErrorMessage('Accepting donations is temporarily unavailable.').'</center>';
} elseif (isset($_POST['oSum'], $_POST['iSum'], $_POST['sSum'], $_POST['pay_system'])) {	// Формирование счета
	if ( CheckNum($_POST['iSum']) || !is_numeric($_POST['oSum']) || !in_array($_POST['pay_system'],  $allowed_merchants, true)) {
		die(GetErrorTxt(10));
	}
	$out_summ = ceil((float)$_POST['oSum']);
	$res = CalcDonate($out_summ);
	$money_count = $res['moneycount'];
	if ($money_count <= 0) {
		die(GetErrorTxt(10));
	}
	$pay_system = $_POST['pay_system'];
    $class_name = 'Merchants\\'.$pay_system;
	/** @var iMerchant $pay_class */
	$pay_class = new $class_name();
	if ($out_summ < $pay_class->getMinAmount())
	{
		echo '<div class="alert alert-error"><button type="button" class="close" data-dismiss="alert">×</button>
                        <strong>Error!</strong> Minimum payment amount for this payment method - <strong>'.$pay_class->getMinAmount().'</strong> usd.
                    </div>
                    <a class="btn btn-primary" href="#" onclick="window.history.back()">Back</a>';
		die();
	}
	$bonus = $res['bonus'];
	$insert_data = [
		'data' => date('Y-m-d H:i:s'),
		'out_summ' => $out_summ,
		'don_kurs' => $don_kurs,
		'money' => $money_count,
		'act_bonus' => $act_bonus,
		'bonus_money' => $bonus,
		'login' => $_SESSION['login'],
		'userid' => $_SESSION['id'],
		'ip' => getIP(),
		'pay_system' => $pay_system
	];
	DB::insert('donate_client', $insert_data);
	$inv_id = DB::insertId();
	if (!$inv_id) {
		die(GetErrorTxt(32));
	}
	$html = $pay_class->GeneratePayForm($out_summ, $inv_id);
	?><br>
    <div align="center">
        <span class="label label-success">Payment Details</span>
        <table class="table table-bordered table-striped table-condensed" style="width:700px">
            <tr>
                <td>Order #</td>
                <td><?=$inv_id?></td>
            </tr>
            <tr>
                <td>Number of Coins</td>
                <td><span class="gold gold_dark"><?php echo $money_count.'</span>';
					if ($bonus>0) echo ' + <span class="label label-important">bonus <span class="gold">'.$bonus.'</span></span>';?></td>
            </tr>
            <tr>
                <td>Payable to</td>
                <td><b><?=$out_summ?></b> USD <font color="#a0a0a0">(excluding payment system fees)</font></td>
            </tr>
            <tr>
				<?=$html?>
            </tr>
        </table>
    </div>
    </center>
<?php } else {
    ?>
    <img src="img/money.png" border=0 align=left>
    <p>Dear players, to maintain the stable performance of our server, support its improvements and advertising, and cover server costs, we gladly accept voluntary donations. As a token of our gratitude, we offer coins to your account..</p>
    <p><strong>You can get coins for purchasing goods in your personal account under the following conditions <font color="#ff0000">*</font> :</strong></p>
    <p><span class="label label-success"><?=$don_kurs?> usd</span> = <span class="label label-inverse"><span class="gold">1</span></span> <font color="#ff0000">* *</font></p><br><?php if ($bonus_system && count($bonus_param)>0) {
		echo "<p><b>There is also a system of additional bonuses::</b></p>\r\n";
		foreach ($bonus_param as $val){
			printf('<p>With the amount of donations <span class="label label-success">from %d usd</span> - <span class="label label-inverse">bonus <span class="gold">%d%%</span></span> <font color="#ff0000">* *</font></p>', $val->summ, $val->bonus);
		}
	}

	?></center>
    <p><b>Minimum donation amount to receive bonus <span class="label label-important"><?=$don_kurs?> USD</span></b> <font color="#ff0000">* *</font></p>
    <style>
        .pay-btn {
            margin-right: 10px;
        }
    </style>
    <div class="row-fluid">
        <div class="box span12">
            <div class="box-header well">
                <h2><i class="icon-edit"></i> Enter the number of coins and select a payment method</h2>
            </div>
            <div class="box-content">
                <form class="form-horizontal" name="f0" id="f0" method="post" action="">
                    <input id="oSum" name="oSum" type="hidden" value="15">
                    <input id="pay_system" name="pay_system" type="hidden" value="">
                    <fieldset>
                        <label class="control-label" for="iSum">Number of coins</label>
                        <div class="controls">
                            <input id="iSum" name="iSum" type="number" class="w200 coins" onkeyup="curr_change()" onchange="curr_change()" value="1" style="margin-bottom:5px"> <span id="bonus"></span>
                        </div>
                        <div class="clearfix"></div>
                        <label class="control-label" for="sSum">Amount - USD</label>
                        <div class="controls">
                            <input id="sSum" class="input uneditable-input w200" readonly="readonly" name="sSum" type="text" value="0"> <font color="#ff0000">* *</font>
                        </div>

                        <div class="form-actions" style="padding-left: 15px">
							<?php
							foreach ($allowed_merchants as $val)
							{
								echo '<button class="btn btn-primary pay-btn" data-system="'.$val.'">Pay via '.$val.'</button>';
							}
							?>
                        </div>
                    </fieldset>
                </form>
                <?php
                if ($ref_don_bonus_enabled && $ref_don_bonus > 0)
                {
                    $post_data = [
                        'op' => 'donate_page',
                        'id' => $_SESSION['id'],
                        'ip' => getIP()
                    ];
                    $result = CurlPage($post_data, 15);
                    $info = UnpackAnswer($result);
                    if (is_array($info))
                    {
                        if (isset($info['refacc'], $info['time_used']) && $info['refacc'] > 0)
                        {
                            if ($info['time_used'] < $ref_don_bonus_timeused)
                            {
                                echo ErrorMessage('So that the player who invited you gets '.$ref_don_bonus.'% coins from your donation, you need to spend more in the game '.GetTime($ref_don_bonus_timeused - $info['time_used']));
                            } else {
                                echo SuccessMessage('The player who invited you will receive '.$ref_don_bonus.'% of coins from your donation');
                            }
                        }
                    }
                }
                ?>
            </div>
        </div>
		<p><font color="#ff0000">*</font> <i>By transferring funds, you fully consent to the fact that you voluntarily donate to our project. You agree to the project's rules, and in return, we provide you with certain incentives.</i></p>
		<p><font color="#ff0000">**</font> <i>The amounts specified do not include the payment providers fees. The fees will depend on the chosen payment method.</i></p>
		<p><font color="#ff0000">***</font> <i>We do not take fees or commissions into account when providing you with your coins. You receive the full amount you donated for.</i></p>
    </div>
    <script type="text/javascript">
        var don_kurs = <?=PHPToJS::render($don_kurs)?>;
        var actbon = <?=$act_bonus?>;

        $("#IncCurr").chosen().change = function ()
        {
            curr_change();
        }

        $('.pay-btn').click(function (e) {
            e.preventDefault();

            if ($('.coins').val() > 250) {
                alert('Maximum coins allowed per transaction is 250!');
                return;
            }
            $('#pay_system').val($(this).data('system'));
            $('#f0').submit();
        });

        function mround(num){
            let sDo = num.toFixed(2) * 1;
            let sPo = num - sDo;
            let sSs = 0;
            if(sPo*100000000000000 > 0)
                sSs = 0.01;
            let sum = sDo + sSs;
            return sum.toFixed(2);
        }

        function curr_change(){
            let incCurr = document.getElementById('IncCurr');

            let bonus = document.getElementById('bonus');
            let iSum = document.getElementById('iSum');
            if (parseInt(iSum.value)<=0) iSum.value = Math.round(100/don_kurs);
            bonus.innerHTML = '';
            let incSum = document.getElementById('sSum');
            let outSum = document.getElementById('oSum');
            iSum.value = parseInt(iSum.value);
            if (isNaN(iSum.value)) iSum.value = 0;
            if (iSum.value>(20000/don_kurs)) iSum.value = 20000/don_kurs;
            outSum.value = iSum.value*don_kurs;
            let actbon1 = 0;
			<?php
			if ($bonus_system && count($bonus_param)>0) {
				foreach ($bonus_param as $i => $val){
					printf("\t\t\t\tif (outSum.value >= %d) actbon1 = %d;\r\n", $val->summ, $val->bonus);
				}
				echo "\t\t\t\tactbon1 += actbon;\r\n";
			}
			?>
            let sum = outSum.value * 1;
            if (actbon1 > 0) {
                let bonval = Math.round((iSum.value / 100) * actbon1);
                if (bonval > 0)
                {
                    bonus.innerHTML = '+ <span class="label label-inverse">bonus <span class="gold">'+bonval+'</span></span>';
                }
            }
            incSum.value = mround(sum);
        }
        curr_change();
    </script>
<?php }
Plugins::pageInjectHTML('bottom', $allowed_merchants);