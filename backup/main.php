<?php
	if (!defined('main_def')) {
		die();
	}
    $account_id = (int)($_SESSION['id'] / 16) - 1;
	$ref_link = GetLink().'register.php?ref='.$account_id;
	if (isset($_GET['r'])) {			
		echo GetErrorTxt($_GET['r']);
	}	
	$post_data = [
		'op' => 'main_page',
		'id' => $_SESSION['id'],
		'ip' => getIP()
	];
    Plugins::pageCurlProcess($post_data);
	$result = CurlPage($post_data, 15);
	$a = UnpackAnswer($result);
?>
<h3>Welcome to the player's personal cabinet</h3>
<?php
Plugins::pageInjectHTML('top', $a);
if (!$a['errorcode'])
{
	if (!$a['roles_count']) {
		echo ErrorMessage('To receive items for voting in top lists and promo codes, you need to create a character in the game.');
	} else {
		echo '<span class="label label-info">TOP & PROMO</span> Select the character to which rewards for voting in the top list and promo codes will be sent.<p><span class="label label-important">Note!</span> If you do not select a character, rewards will go to the character currently online or the one with the most online hours in the game.</p>
		<div class="well">
		';
		foreach ($a['roles'] as $i => $val)
        {
            if ($val['cls'] >= 0) {
                $cls =  '<i class="class_icon class_'.$val['cls'].'"></i> ';
                $hint = 'data-rel="tooltip" data-original-title="'.GetClassName($val['cls']).'"';
            } else {
                $cls = '';
                $hint = '';
            }
			if ($a['cur_reward_role_index'] == $i) {
				printf('<span class="btn btn-success" %s>%s%s</span> ', $hint, $cls, $val['name']);
			} else {
				printf('<a class="btn" href="index.php?op=act&n=800&num=%s&rand=%s' . '" %s>%s%s</a> ', $i, time(), $hint, $cls, $val['name']);
			}
		}
		echo '</div>';
	}
}

if ($promo_enabled) { ?>
<div class="row-fluid">
	<div class="box span12">
		<div class="box-header well"><h2>Enter promo code to get bonus</h2></div>
		<div class="box-content">
			<form name="enterpromo" method="post" action="index.php?op=act&n=73&num=0">
			<input type="text" name="promo_code" maxlength=50> <a href="#" onclick="checkpromo()" class="btn btn-large btn-inverse">Get a bonus</a>
			</form>
		</div>
	</div>
</div>
<script type="text/javascript">
function checkpromo(){
	if (document.enterpromo.promo_code.value.length < 1) alert('Enter promo code'); else
	document.enterpromo.submit();
}
</script>
<?php }
$show_active = false;
if ($shop_enabled && $shop_new_show_main && count($a['shop_new']))
{
    $show_active = true;
    echo '<div class="row-fluid">
	<div class="box span12">
		<div class="box-header well"><h2><div class="menu-shop-new"></div>New in the store</h2></div>
		<div class="box-content">
			<div class="mails-content mt1">';
	foreach ($a['shop_new'] as $item) {
		echo RenderShopItem($item);
	}
	echo	'</div>
		</div>
	</div>
</div>';
}
if ($shop_enabled && $shop_hot_show_main && count($a['shop_hot']))
{
	$show_active = true;
	echo '<div class="row-fluid">
	<div class="box span12">
		<div class="box-header well"><h2><div class="shop-hot"></div>Popular in the store</h2></div>
		<div class="box-content">
			<div class="mails-content mt1">';
	foreach ($a['shop_hot'] as $item) {
		echo RenderShopItem($item);
	}
	echo	'</div>
		</div>
	</div>
</div>';
}
if ($show_active)
{
	echo '<script>'.RoleListJS($a['roles']).PHP_EOL.'InitShopButtons();</script>';
}

?>
<div class="well">		
	<?php		
	if ($a['errorcode'] == 0) {
		$ip = $a['lastip'];
		$lastlogin = $a['lastlogin'];
		if ($ip === '127.0.0.1') $ip = 'System';
		if ($ip != '') printf('<p>Last login date: <span class="label label-info">%s</span></p>', date("d.m.Y H:i:s", $lastlogin));
		if ($lastlogin != '') printf('<p>Last login IP: <span class="label label-inverse">%s</span></p>', $ip);			
		if ($a['bancount'] > 0)
		{
			echo ErrorMessage('The account has the following bans:');
			WriteBanTable($a);
		}
		if ($a['refcount'] == 0) {
			echo ErrorMessage('You currently have no invited players');
		} else {
			echo SuccessMessage('You have invited the following players:');
			echo '<div align="center">
			<table class="table table-bordered table-striped table-condensed" style="max-width:550px">
				<thead><tr>
					<th>Account Number</th>
					<th>Registration Date</th>
					<th>Bonuses Earned</th>
				</tr></thead>
				<tbody>';
			foreach ($a['refdata'] as $i => $val){
				printf('
				<tr>
					<td>%s</td>
					<td>%s</td>
					<td>%s</td>
				</tr>', $val['id'], $val['creatime'], ShowCost($val['ref_bonus'], true, true));
			}
			echo '</tbody></table></div>';
		}
	}
?>
</div>
<?php if ($get_gold_btn && $register_gold > 0) { ?>
<h3>Starter gold</h3>
<div class="well">
	<p>You can get <span class="label label-important"><?=$register_gold?></span> in-game gold once after account registration</p><?php
	if (isset($a['allow_reg_gold'])){
		if (!$a['allow_reg_gold']) echo SuccessMessage('Done'); else
		if ($a['allow_reg_gold'] && $a['roles_count'] < 1) echo ErrorMessage('First, create a character in the game'); else
		if ($a['allow_reg_gold']) echo '<a href="index.php?op=act&n=74&num=0" class="btn btn-large btn-danger">Get the gold</a>';
	}
?>	
</div>
<?php }
Ratings::ShowMainPageInfo($a);
if ($accumulation_system) { ?>
<h3>Accumulation/donation system</h3>
<div class="well">
    <table class="table table-bordered table-striped table-condensed">
        <thead>
        <tr>
            <th>Your bonus</th>
            <th>Sum of all donations</th>
            <th>Bonus</th>
        </tr>
        </thead>
        <?php
        foreach ($acc_param as $i => $val){
            if ($cur_accum_id == $i) {
                $c = ' class="success"';
                $s = 'Amount of donations: <b>'.$cur_user_donate.' USD</b>';
            } else {
                $c = '';
                $s = '';
            }
            printf('
			<tr%s>
				<td>%s</td>
				<td>from %s USD</td>
				<td>+%s%%</td>
			</tr>', $c, $s, $val->summ, ShowCost($val->bonus, true, true));
        }
        ?>
    </table>
    <br>
    <?php
    if ($cur_accum_id < (count($acc_param)-1)) {
        $rest = $acc_param[$cur_accum_id+1]->summ - $cur_user_donate;
        printf('We are down to the next level of bonuses <span class="label label-important">%s</span> USD. (excluding payment system commission)', $rest);
    } else echo 'At this point, youve got <b>maximum discount</b>.';
    echo '</div>';
    }
    ?>
<br>
    <p>Invite your friends to register on the project by referral link and get bonuses!</p>
    <p>Your referral link: <a href="<?=$ref_link?>" target="_blank"><?=$ref_link?></a></p><?php
	Plugins::pageInjectHTML('bottom', $a);
    ?>
<span class="label label-important">Note to self!</span>
<ul align="left" type="disc">
	<li>At the moment LC is under development, and more useful functions will appear here in the future.</li>
	<li>We will be glad to receive your wishes on improving the quality and functionality of this LC, as well as reports about found errors and bugs.</li>
</ul>