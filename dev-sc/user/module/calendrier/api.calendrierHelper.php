<?php

class calendrierHelper extends calendrier {

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public  function __construct(){
	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public  function pagination($opt=array()){

		# myTotal	= le nombre total d'enregistrement
		# myLimit	= le nombre qu'on demande
		# myOffset	= le nombre a partir duquel on affiche myLimit items
		# myPattern = le lien vers la page - page?p=%s ou %s represente le offset
		# _maxPage  = max pages a afficher avant/apres courant
		
		if($opt['total'] > $opt['Limit']){
		
			$_maxPage   = $opt['size'];
			$totalPages = ceil($opt['total'] / $opt['limit']);
			$myPage		= $opt['offset'] / $opt['limit'];
		
			# start html
			$s_html[] = '<div class="btn-group">';
			$s_html[] = ($myPage != 0)
				? '<a href="'.sprintf($opt['pattern'], ($myPage - 1) ).'" class="btn">&laquo;</a>'
				: '<a href="" class="btn disabled">&laquo;</a>';
		
			# Si on a moins de 5 pages a afficher, petite pagination
			if ($totalPages <= 8) {
		
				for($i=0; $i<$totalPages; $i++){
		
					$v = ($i == $myPage)
						? '<a  class="btn active">'.($i + 1).'</a>'
						: '<a href="'.sprintf($opt['pattern'], $i).'" class="btn">'.($i + 1).'</a>';

					$tmp[] = $v;
				}
			} else {
				# plus de 5 pages, pagination complete
				# Si l'on a au moins 3 elements devant & derriere
		
				# si l'on est au debut ou à la fin de la liste
				if ($myPage > $_maxPage || $myPage < ($totalPages - $_maxPage)) {
					if ($myPage > $_maxPage) {
						# Ajouter lien vers la premiere page et [...]
						$s_html[] = '<a href="'.sprintf($opt['pattern'], 0).'" class="btn">1</a>';
						$s_html[] = '<a  class="btn">...</a>';
					}
					if ($myPage < ($totalPages - $_maxPage)) {
						# Ajouter lien vers la dernière page et [...]	
						$e_html[] = '<a  class="btn ">...</a>';
						$e_html[] = '<a href="'.sprintf($opt['pattern'], ($totalPages - 1)).'" class="btn">'.$totalPages.'</a>';
					}
				}
		
				for($i=($myPage-($_maxPage-1)); $i<($myPage+$_maxPage); $i++){
					if ($i >= 0 && $i < $totalPages) {
						$v = ($i == $myPage)
							? '<a  class="btn active">'.($i+1).'</a>'
							: '<a href="'.sprintf($opt['pattern'], $i).'" class="btn">'.($i+1).'</a>';
						$tmp[] = $v;
					}
				}
			}
		
			# end html
			$e_html[] = ($totalPages > ($myPage+1))
				? '<a href="'.sprintf($opt['pattern'], ($myPage + 1) ).'" class="btn">&raquo;</a>'
				: '<a  class="btn disabled">&raquo;</a>';

			$e_html[] = '</div>';

			echo '<div class="pagination-custom no-mobile">';
			echo implode(' ', $s_html);
			echo implode(' ', $tmp);
			echo implode(' ', $e_html);
			echo '</div>';

			echo '<div class="pagination-custom mobile">';
			if($myPage > 0)             echo '<a href="'.sprintf($opt['pattern'], ($myPage - 1)).'" class="btn">Précédent</a>';
			if($myPage < $totalPages)   echo '<a href="'.sprintf($opt['pattern'], ($myPage + 1)).'" class="btn">Suivant</a>';
			echo '</div>';
		}

	}

}

