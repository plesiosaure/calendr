<?php

	$ad = $this->apiLoad('pub')->pubCalendrier(array(

	));

	if(!empty($ad)) echo '<div class="block ad-block">'.$ad.'</div>';
?>
