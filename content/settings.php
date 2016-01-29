CMS | Settings
<?php
$items = [];
$items["General"] = ["<p>content</p>"];
if ($auth > 0) {
	$items["Data"] = ["<p>Data</p>"];
}
if (isset($_GET['panel']) && in_array($_GET['panel'],array_keys($items))){
	array_push($items[$_GET['panel']],true);
}else{
	array_push($items['General'],true);
}
?>
<link rel='stylesheet' type='text/css' href='css/interface.css'/>
<div>
	<ul>
		<?php
		foreach ($items as $key => $value) {
			$selected = (isset($value[1]) && $value[1] === true) ? ' id="selected"' : "";
			?>
			<li<?=$selected?>>
				<a href="/settings?panel=<?php echo urlencode($key);?>">
					<div></div>
					<span><?=$key?></span>
				</a>
			</li>
		<?php
		}
		?>
	</ul>
</div>
<?php
foreach ($items as $key => $value) {
$selected = (isset($value[1]) && $value[1] === true) ? ' id="selected-content"' : "";
?>
	<div<?=$selected?> data-settingpanel-id="<?php echo htmlspecialchars($key);?>">
		<div>
			<?=$value[0]?>
		</div>
	</div>
<?php
}
?>