<?php

	$ad = $this->apiLoad('pub')->pubCalendrier(1);

	if(!empty($ad)) echo '<div class="block ad-block">'.$ad.'</div>';
?>
