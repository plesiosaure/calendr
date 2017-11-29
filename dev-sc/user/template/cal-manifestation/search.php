<div class="block manif-detail preview is-<?php echo $myType ?>">

	<a href="<?php echo $api->manifestationPermalink($e); ?>" class="title medium name">
		<?php
			$useDate = isset($_GET['date']) ? '' : 'DATE';
			echo $this->apiLoad('calendrier')->nameFormat($e, $useDate);
		?>
	</a>

	<div class="resume"><?php

		echo $api->manifestationResume($e, array(
			'searchDate' => $_GET['date']
		));

	?></div>


	<?php
	#	$this->pre($e);
	?>


</div>

