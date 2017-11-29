<?php

class calendrierDepartement extends calendrier {

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public  function build(){
		$file = __DIR__.'/ui/js/dep.js';

		$all['r'] = $this->region();
		$all['d'] = $this->departement();

		$json = $this->helperJsonEncode($all);
		$json = 'var regDep = '.$json;

		echo $json;

		umask(0);
		if(file_exists($file)) unlink($file);
		return file_put_contents($file, $json, 0755);
	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public  function region(){
		return array(
		array('name' => 'Alsace',                       'code' => '42', 'dep' => array(67,68)),
		array('name' => 'Aquitaine',                    'code' => '72', 'dep' => array(24,33,40,47,64)),
		array('name' => 'Auvergne',                     'code' => '83', 'dep' => array('03',15,43,63)),
		array('name' => 'Bourgogne',                    'code' => '26', 'dep' => array(21,58,71,89)),
		array('name' => 'Bretagne',                     'code' => '53', 'dep' => array(22,29,35,56)),
		array('name' => 'Centre',                       'code' => '24', 'dep' => array(18,28,36,37,41,45)),
		array('name' => 'Champagne-Ardenne',            'code' => '21', 'dep' => array('08',10,51,52)),
		array('name' => 'Corse',                        'code' => '94', 'dep' => array('2A','2B')),
		array('name' => 'Franche-Comté',                'code' => '43', 'dep' => array(25,39,70,90)),
		array('name' => 'Guadeloupe',                   'code' => '01', 'dep' => array(971)),
		array('name' => 'Guyane',                       'code' => '03', 'dep' => array(973)),
		array('name' => 'Île-de-France',                'code' => '11', 'dep' => array(75,91,92,93,77,94,95,78)),
		array('name' => 'Languedoc-Roussillon',         'code' => '91', 'dep' => array(11,30,34,48,66)),
		array('name' => 'Limousin',                     'code' => '74', 'dep' => array(74,19,23,87)),
		array('name' => 'Lorraine',                     'code' => '41', 'dep' => array(54,55,57,88)),
		array('name' => 'Martinique',                   'code' => '02', 'dep' => array(972)),
		array('name' => 'Mayotte',                      'code' => '06', 'dep' => array(976)),
		array('name' => 'Midi-Pyrénées',                'code' => '73', 'dep' => array('09',12,31,32,46,65,81,82)),
		array('name' => 'Nord-Pas-de-Calais',           'code' => '31', 'dep' => array(59,62)),
		array('name' => 'Basse-Normandie',              'code' => '25', 'dep' => array(14,50,61)),
		array('name' => 'Haute-Normandie',              'code' => '23', 'dep' => array(27,76)),
		array('name' => 'Pays de la Loire',             'code' => '52', 'dep' => array(44,49,53,72,85)),
		array('name' => 'Picardie',                     'code' => '22', 'dep' => array('02',60,80)),
		array('name' => 'Poitou-Charentes',             'code' => '54', 'dep' => array(16,17,79,86)),
		array('name' => 'Provence-Alpes-Côte d\'Azur',  'code' => '93', 'dep' => array('04','05','06',13,83,84)),
		array('name' => 'La Réunion',                   'code' => '04', 'dep' => array(974)),
		array('name' => 'Rhône-Alpes',                  'code' => '82', 'dep' => array('01','07',26,38,42,69,73,74)));
	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public function regionGet($opt){

		if(!empty($opt['dep'])){
			$region = $this->region();
			foreach($region as $e){
				if(in_array($opt['dep'], $e['dep'])) return $e;
			}
		}else
		if(!empty($opt['code'])){
			$region = $this->region();
			foreach($region as $e){
				if($e['code'] == $opt['code']) return $e;
			}
		}else{
			return $this->region();
		}
	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public  function departement(){
		return array(
		array('code' => '01', 'name' => 'Ain'),
		array('code' => '02', 'name' => 'Aisne'),
		array('code' => '03', 'name' => 'Allier'),
		array('code' => '04', 'name' => 'Alpes-de-Haute-Provence'),
		array('code' => '05', 'name' => 'Hautes-Alpes'),
		array('code' => '06', 'name' => 'Alpes-Maritimes'),
		array('code' => '07', 'name' => 'Ardèche'),
		array('code' => '08', 'name' => 'Ardennes'),
		array('code' => '09', 'name' => 'Ariège'),
		array('code' => '10', 'name' => 'Aube'),
		array('code' => '11', 'name' => 'Aude'),
		array('code' => '12', 'name' => 'Aveyron'),
		array('code' => '13', 'name' => 'Bouches-du-Rhône'),
		array('code' => '14', 'name' => 'Calvados'),
		array('code' => '15', 'name' => 'Cantal'),
		array('code' => '16', 'name' => 'Charente'),
		array('code' => '17', 'name' => 'Charente-Maritime'),
		array('code' => '18', 'name' => 'Cher'),
		array('code' => '19', 'name' => 'Corrèze'),
		array('code' => '2A', 'name' => 'Corse-du-Sud'),
		array('code' => '2B', 'name' => 'Haute-Corse'),
		array('code' => '21', 'name' => 'Côte-d\'Or'),
		array('code' => '22', 'name' => 'Côtes-d\'Armor'),
		array('code' => '23', 'name' => 'Creuse'),
		array('code' => '24', 'name' => 'Dordogne'),
		array('code' => '25', 'name' => 'Doubs'),
		array('code' => '26', 'name' => 'Drôme'),
		array('code' => '27', 'name' => 'Eure'),
		array('code' => '28', 'name' => 'Eure-et-Loir'),
		array('code' => '29', 'name' => 'Finistère'),
		array('code' => '30', 'name' => 'Gard'),
		array('code' => '31', 'name' => 'Haute-Garonne'),
		array('code' => '32', 'name' => 'Gers'),
		array('code' => '33', 'name' => 'Gironde'),
		array('code' => '34', 'name' => 'Hérault'),
		array('code' => '35', 'name' => 'Ille-et-Vilaine'),
		array('code' => '36', 'name' => 'Indre'),
		array('code' => '37', 'name' => 'Indre-et-Loire'),
		array('code' => '38', 'name' => 'Isère'),
		array('code' => '39', 'name' => 'Jura'),
		array('code' => '40', 'name' => 'Landes'),
		array('code' => '41', 'name' => 'Loir-et-Cher'),
		array('code' => '42', 'name' => 'Loire'),
		array('code' => '43', 'name' => 'Haute-Loire'),
		array('code' => '44', 'name' => 'Loire-Atlantique'),
		array('code' => '45', 'name' => 'Loiret'),
		array('code' => '46', 'name' => 'Lot'),
		array('code' => '47', 'name' => 'Lot-et-Garonne'),
		array('code' => '48', 'name' => 'Lozère'),
		array('code' => '49', 'name' => 'Maine-et-Loire'),
		array('code' => '50', 'name' => 'Manche'),
		array('code' => '51', 'name' => 'Marne'),
		array('code' => '52', 'name' => 'Haute-Marne'),
		array('code' => '53', 'name' => 'Mayenne'),
		array('code' => '54', 'name' => 'Meurthe-et-Moselle'),
		array('code' => '55', 'name' => 'Meuse'),
		array('code' => '56', 'name' => 'Morbihan'),
		array('code' => '57', 'name' => 'Moselle'),
		array('code' => '58', 'name' => 'Nièvre'),
		array('code' => '59', 'name' => 'Nord'),
		array('code' => '60', 'name' => 'Oise'),
		array('code' => '61', 'name' => 'Orne'),
		array('code' => '62', 'name' => 'Pas-de-Calais'),
		array('code' => '63', 'name' => 'Puy-de-Dôme'),
		array('code' => '64', 'name' => 'Pyrénées-Atlantiques'),
		array('code' => '65', 'name' => 'Hautes-Pyrénées'),
		array('code' => '66', 'name' => 'Pyrénées-Orientales'),
		array('code' => '67', 'name' => 'Bas-Rhin'),
		array('code' => '68', 'name' => 'Haut-Rhin'),
		array('code' => '69', 'name' => 'Rhône'),
		array('code' => '70', 'name' => 'Haute-Saône'),
		array('code' => '71', 'name' => 'Saône-et-Loire'),
		array('code' => '72', 'name' => 'Sarthe'),
		array('code' => '73', 'name' => 'Savoie'),
		array('code' => '74', 'name' => 'Haute-Savoie'),
		array('code' => '75', 'name' => 'Paris'),
		array('code' => '76', 'name' => 'Seine-Maritime'),
		array('code' => '77', 'name' => 'Seine-et-Marne'),
		array('code' => '78', 'name' => 'Yvelines'),
		array('code' => '79', 'name' => 'Deux-Sèvres'),
		array('code' => '80', 'name' => 'Somme'),
		array('code' => '81', 'name' => 'Tarn'),
		array('code' => '82', 'name' => 'Tarn-et-Garonne'),
		array('code' => '83', 'name' => 'Var'),
		array('code' => '84', 'name' => 'Vaucluse'),
		array('code' => '85', 'name' => 'Vendée'),
		array('code' => '86', 'name' => 'Vienne'),
		array('code' => '87', 'name' => 'Haute-Vienne'),
		array('code' => '88', 'name' => 'Vosges'),
		array('code' => '89', 'name' => 'Yonne'),
		array('code' => '90', 'name' => 'Territoire de Belfort'),
		array('code' => '91', 'name' => 'Essonne'),
		array('code' => '92', 'name' => 'Hauts-de-Seine'),
		array('code' => '93', 'name' => 'Seine-Saint-Denis'),
		array('code' => '94', 'name' => 'Val-de-Marne'),
		array('code' => '95', 'name' => 'Val-d\'Oise'),
		array('code' => '971','name' => 'Guadeloupe'),
		array('code' => '972','name' => 'Martinique'),
		array('code' => '973','name' => 'Guyane'),
		array('code' => '974','name' => 'La Réunion'),
		array('code' => '976','name' => 'Mayotte'));
	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public function departementGet($opt){

		if(!empty($opt['code'])){
			$region = $this->departement();
			foreach($region as $e){
				if($e['code'] == $opt['code']) return $e;
			}
		}else{
			return $this->departement();
		}

	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
// http://www.obs.coe.int/about/iso_3166.html
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public  function country(){
		return array(
			array('code' => 'AD', 'name' => 'Andorre',                                      'lva' => 'ABD'),
		#	array('code' => 'AE', 'name' => 'Emirats Arabes Unis',                          'lva' => ''),
		#	array('code' => 'AF', 'name' => 'Afghanistan',                                  'lva' => ''),
		#	array('code' => 'AL', 'name' => 'Albanie',                                      'lva' => ''),
		#	array('code' => 'AM', 'name' => 'Arménie',                                      'lva' => ''),
		#	array('code' => 'AO', 'name' => 'Angola',                                       'lva' => ''),
		#	array('code' => 'AQ', 'name' => 'Antarctique',                                  'lva' => ''),
			array('code' => 'AR', 'name' => 'Argentine',                                    'lva' => 'ARG'),
			array('code' => 'AT', 'name' => 'Autriche',                                     'lva' => 'A'),
			array('code' => 'AU', 'name' => 'Australie',                                    'lva' => 'AUS'),
		#	array('code' => 'AZ', 'name' => 'Azerbaïdjan',                                  'lva' => ''),
		#	array('code' => 'BA', 'name' => 'Bosnie Herzégovine',                           'lva' => ''),
		#	array('code' => 'BB', 'name' => 'Barbade',                                      'lva' => ''),
		#	array('code' => 'BD', 'name' => 'Bangladesh',                                   'lva' => ''),
			array('code' => 'BE', 'name' => 'Belgique',                                     'lva' => 'B'),
		#	array('code' => 'BF', 'name' => 'Burkina Faso',                                 'lva' => ''),
			array('code' => 'BG', 'name' => 'Bulgarie',                                     'lva' => 'BG'),
		#	array('code' => 'BH', 'name' => 'Bahreïn',                                      'lva' => ''),
		#	array('code' => 'BI', 'name' => 'Burundi',                                      'lva' => ''),
		#	array('code' => 'BJ', 'name' => 'Bénin',                                        'lva' => ''),
		#	array('code' => 'BM', 'name' => 'Bermudes',                                     'lva' => ''),
		#	array('code' => 'BN', 'name' => 'Brunéi Darussalam',                            'lva' => ''),
		#	array('code' => 'BO', 'name' => 'Bolivie',                                      'lva' => ''),
			array('code' => 'BR', 'name' => 'Brésil',                                       'lva' => 'BRE'),
		#	array('code' => 'BS', 'name' => 'Bahamas',                                      'lva' => ''),
		#	array('code' => 'BT', 'name' => 'Bhoutan',                                      'lva' => ''),
		#	array('code' => 'BW', 'name' => 'Botswana',                                     'lva' => ''),
		#	array('code' => 'BY', 'name' => 'Biélorussie',                                  'lva' => ''),
		#	array('code' => 'BZ', 'name' => 'Belize',                                       'lva' => ''),
			array('code' => 'CA', 'name' => 'Canada',                                       'lva' => 'CDN'),
		#	array('code' => 'CF', 'name' => 'République centrafricaine',                    'lva' => ''),
		#	array('code' => 'CG', 'name' => 'Congo',                                        'lva' => ''),
			array('code' => 'CH', 'name' => 'Suisse',                                       'lva' => 'CH'),
		#	array('code' => 'CI', 'name' => 'Côte d\'Ivoire',                               'lva' => ''),
		#	array('code' => 'CL', 'name' => 'Chili',                                        'lva' => ''),
		#	array('code' => 'CM', 'name' => 'Cameroun',                                     'lva' => ''),
			array('code' => 'CN', 'name' => 'Chine',                                        'lva' => 'CHI'),
		#	array('code' => 'CO', 'name' => 'Colombie',                                     'lva' => ''),
		#	array('code' => 'CR', 'name' => 'Costa Rica',                                   'lva' => ''),
		#	array('code' => 'CS', 'name' => 'Serbie-et-Monténégro',                         'lva' => ''),
		#	array('code' => 'CU', 'name' => 'Cuba',                                         'lva' => ''),
		#	array('code' => 'CV', 'name' => 'Cap Vert',                                     'lva' => ''),
			array('code' => 'CY', 'name' => 'Chypre',                                       'lva' => 'CY'),
			array('code' => 'CZ', 'name' => 'République Tchèque',                           'lva' => 'CS'),
			array('code' => 'DE', 'name' => 'Allemagne',                                    'lva' => 'A'),
		#	array('code' => 'DJ', 'name' => 'Djibouti',                                     'lva' => ''),
			array('code' => 'DK', 'name' => 'Danemark',                                     'lva' => 'DK'),
		#	array('code' => 'DO', 'name' => 'République Dominicaine',                       'lva' => ''),
			array('code' => 'DZ', 'name' => 'Algérie',                                      'lva' => 'ALG'),
		#	array('code' => 'EC', 'name' => 'Equateur',                                     'lva' => ''),
		#	array('code' => 'EE', 'name' => 'Estonie',                                      'lva' => ''),
			array('code' => 'EG', 'name' => 'Egypte',                                       'lva' => 'EGY'),
			array('code' => 'ES', 'name' => 'Espagne',                                      'lva' => 'E'),
		#	array('code' => 'ET', 'name' => 'Ethiopie',                                     'lva' => ''),
			array('code' => 'FI', 'name' => 'Finlande',                                     'lva' => 'FIN'),
		#	array('code' => 'FÖ', 'name' => 'Iles Féroé',                                   'lva' => ''),
			array('code' => 'FR', 'name' => 'France',                                       'lva' => 'F'),
		#	array('code' => 'GA', 'name' => 'Gabon',                                        'lva' => ''),
			array('code' => 'GB', 'name' => 'Royaume-Uni',                                  'lva' => 'GB'),
		#	array('code' => 'GE', 'name' => 'Géorgie',                                      'lva' => ''),
		#	array('code' => 'GF', 'name' => 'Guyane française',                             'lva' => ''),
		#	array('code' => 'GH', 'name' => 'Ghana',                                        'lva' => ''),
		#	array('code' => 'GI', 'name' => 'Gibraltar',                                    'lva' => ''),
		#	array('code' => 'GL', 'name' => 'Groenland',                                    'lva' => ''),
		#	array('code' => 'GM', 'name' => 'Gambie',                                       'lva' => ''),
		#	array('code' => 'GN', 'name' => 'Guinée',                                       'lva' => ''),
		#	array('code' => 'GP', 'name' => 'Guadeloupe',                                   'lva' => ''),
		#	array('code' => 'GQ', 'name' => 'Guinée équatoriale',                           'lva' => ''),
			array('code' => 'GR', 'name' => 'Grèce',                                        'lva' => 'GR'),
		#	array('code' => 'GT', 'name' => 'Guatemala',                                    'lva' => ''),
		#	array('code' => 'GW', 'name' => 'Guinée-Bissau',                                'lva' => ''),
		#	array('code' => 'GY', 'name' => 'Guyana',                                       'lva' => ''),
		#	array('code' => 'HK', 'name' => 'Hong-Kong',                                    'lva' => ''),
		#	array('code' => 'HN', 'name' => 'Honduras',                                     'lva' => ''),
			array('code' => 'HR', 'name' => 'Croatie',                                      'lva' => 'CRO'),
		#	array('code' => 'HT', 'name' => 'Haïti',                                        'lva' => ''),
			array('code' => 'HU', 'name' => 'Hongrie',                                      'lva' => 'HON'),
		#	array('code' => 'ID', 'name' => 'Indonésie',                                    'lva' => ''),
			array('code' => 'IE', 'name' => 'Irlande',                                      'lva' => 'IRL'),
		#	array('code' => 'IL', 'name' => 'Israël',                                       'lva' => ''),
			array('code' => 'IN', 'name' => 'Inde',                                         'lva' => 'IND'),
		#	array('code' => 'IQ', 'name' => 'Irak',                                         'lva' => ''),
		#	array('code' => 'IS', 'name' => 'Islande',                                      'lva' => ''),
			array('code' => 'IT', 'name' => 'Italie',                                       'lva' => 'I'),
		#	array('code' => 'JM', 'name' => 'Jamaïque',                                     'lva' => ''),
		#	array('code' => 'JO', 'name' => 'Jordanie',                                     'lva' => ''),
			array('code' => 'JP', 'name' => 'Japon',                                        'lva' => 'J'),
		#	array('code' => 'KE', 'name' => 'Kenya',                                        'lva' => ''),
		#	array('code' => 'KG', 'name' => 'Kirghizistan',                                 'lva' => ''),
		#	array('code' => 'KH', 'name' => 'Cambodge',                                     'lva' => ''),
		#	array('code' => 'KM', 'name' => 'Comores',                                      'lva' => ''),
		#	array('code' => 'KP', 'name' => 'Corée ',                                       'lva' => ''),
		#	array('code' => 'KR', 'name' => 'Corée',                                        'lva' => ''),
		#	array('code' => 'KW', 'name' => 'Koweït',                                       'lva' => ''),
		#	array('code' => 'KZ', 'name' => 'Kazakhstan',                                   'lva' => ''),
		#	array('code' => 'LA', 'name' => 'Laos',                                         'lva' => ''),
		#	array('code' => 'LB', 'name' => 'Liban',                                        'lva' => ''),
			array('code' => 'LI', 'name' => 'Liechtenstein',                                'lva' => 'LIE'),
		#	array('code' => 'LK', 'name' => 'Sri Lanka',                                    'lva' => ''),
		#	array('code' => 'LR', 'name' => 'Liberia',                                      'lva' => ''),
		#	array('code' => 'LS', 'name' => 'Lesotho',                                      'lva' => ''),
		#	array('code' => 'LT', 'name' => 'Lituanie',                                     'lva' => ''),
			array('code' => 'LU', 'name' => 'Luxembourg',                                   'lva' => 'L'),
			array('code' => 'LV', 'name' => 'Lettonie',                                     'lva' => 'LV'),
			array('code' => 'MA', 'name' => 'Maroc',                                        'lva' => 'MA'),
			array('code' => 'MC', 'name' => 'Monaco',                                       'lva' => 'MON'),
		#	array('code' => 'MD', 'name' => 'Moldavie',                                     'lva' => ''),
		#	array('code' => 'ME', 'name' => 'Monténégro',                                   'lva' => ''),
		#	array('code' => 'MG', 'name' => 'Madagascar',                                   'lva' => ''),
		#	array('code' => 'MK', 'name' => 'Ex-République Yougoslave de Macédoine',        'lva' => ''),
		#	array('code' => 'ML', 'name' => 'Mali',                                         'lva' => ''),
		#	array('code' => 'MN', 'name' => 'Mongolie',                                     'lva' => ''),
		#	array('code' => 'MO', 'name' => 'Macao',                                        'lva' => ''),
		#	array('code' => 'MQ', 'name' => 'Martinique',                                   'lva' => ''),
		#	array('code' => 'MR', 'name' => 'Mauritanie',                                   'lva' => ''),
			array('code' => 'MT', 'name' => 'Malte',                                        'lva' => 'MT'),
		#	array('code' => 'MU', 'name' => 'Maurice',                                      'lva' => ''),
		#	array('code' => 'MV', 'name' => 'Maldives',                                     'lva' => ''),
		#	array('code' => 'MW', 'name' => 'Malawi',                                       'lva' => ''),
			array('code' => 'MX', 'name' => 'Mexique',                                      'lva' => 'MEX'),
		#	array('code' => 'MY', 'name' => 'Malaisie',                                     'lva' => ''),
		#	array('code' => 'MZ', 'name' => 'Mozambique',                                   'lva' => ''),
		#	array('code' => 'NA', 'name' => 'Namibie',                                      'lva' => ''),
		#	array('code' => 'NC', 'name' => 'Nouvelle-Calédonie',                           'lva' => ''),
		#	array('code' => 'NE', 'name' => 'Niger',                                        'lva' => ''),
		#	array('code' => 'NG', 'name' => 'Nigéria',                                      'lva' => ''),
		#	array('code' => 'NI', 'name' => 'Nicaragua',                                    'lva' => ''),
			array('code' => 'NL', 'name' => 'Pays-Bas',                                     'lva' => 'NL'),
			array('code' => 'NO', 'name' => 'Norvège',                                      'lva' => 'NO'),
		#	array('code' => 'NP', 'name' => 'Népal',                                        'lva' => ''),
		#	array('code' => 'NZ', 'name' => 'Nouvelle-Zélande',                             'lva' => ''),
		#	array('code' => 'PA', 'name' => 'Panama',                                       'lva' => ''),
		#	array('code' => 'PE', 'name' => 'Pérou',                                        'lva' => ''),
		#	array('code' => 'PF', 'name' => 'Polynésie française',                          'lva' => ''),
		#	array('code' => 'PG', 'name' => 'Papouasie',                                    'lva' => ''),
		#	array('code' => 'PH', 'name' => 'Philippines',                                  'lva' => ''),
		#	array('code' => 'PK', 'name' => 'Pakistan',                                     'lva' => ''),
			array('code' => 'PL', 'name' => 'Pologne',                                      'lva' => 'POL'),
		#	array('code' => 'PR', 'name' => 'Porto Rico',                                   'lva' => ''),
		#	array('code' => 'PS', 'name' => 'Territoires palestiniens',                     'lva' => ''),
			array('code' => 'PT', 'name' => 'Portugal',                                     'lva' => 'P'),
		#	array('code' => 'PY', 'name' => 'Paraguay',                                     'lva' => ''),
			array('code' => 'QC', 'name' => 'Québec ',                                      'lva' => 'QUÉ'),
			array('code' => 'RO', 'name' => 'Roumanie',                                     'lva' => 'RO'),
		#	array('code' => 'RS', 'name' => 'Serbie',                                       'lva' => ''),
			array('code' => 'RU', 'name' => 'Russie',                                       'lva' => 'RUS'),
		#	array('code' => 'RW', 'name' => 'Rwanda',                                       'lva' => ''),
		#	array('code' => 'SA', 'name' => 'Arabie Saoudite',                              'lva' => ''),
		#	array('code' => 'SB', 'name' => 'Salomon (Iles)',                               'lva' => ''),
		#	array('code' => 'SC', 'name' => 'Seychelles',                                   'lva' => ''),
		#	array('code' => 'SD', 'name' => 'Soudan',                                       'lva' => ''),
			array('code' => 'SE', 'name' => 'Suède',                                        'lva' => 'S'),
		#	array('code' => 'SG', 'name' => 'Singapour',                                    'lva' => ''),
			array('code' => 'SI', 'name' => 'Slovénie',                                     'lva' => 'SLO'),
		#	array('code' => 'SK', 'name' => 'Slovaquie',                                    'lva' => ''),
		#	array('code' => 'SL', 'name' => 'Sierra Leone',                                 'lva' => ''),
		#	array('code' => 'SM', 'name' => 'Saint-Marin',                                  'lva' => ''),
		#	array('code' => 'SN', 'name' => 'Sénégal',                                     'lva' => ''),
		#	array('code' => 'SO', 'name' => 'Somalie',                                     'lva' => ''),
		#	array('code' => 'SR', 'name' => 'Suriname',                                    'lva' => ''),
		#	array('code' => 'SU', 'name' => 'Union des Républiques socialistes soviétiques','lva' => ''),
		#	array('code' => 'SV', 'name' => 'El Salvador',                                  'lva' => ''),
		#	array('code' => 'SY', 'name' => 'Syrie',                                        'lva' => ''),
		#	array('code' => 'SZ', 'name' => 'Swaziland',                                    'lva' => ''),
		#	array('code' => 'TD', 'name' => 'Tchad',                                        'lva' => ''),
		#	array('code' => 'TJ', 'name' => 'Tadjikistan',                                  'lva' => ''),
		#	array('code' => 'TG', 'name' => 'Togo',                                         'lva' => ''),
		#	array('code' => 'TH', 'name' => 'Thaïlande',                                    'lva' => ''),
		#	array('code' => 'TM', 'name' => 'Turkmenistan',                                 'lva' => ''),
			array('code' => 'TN', 'name' => 'Tunisie',                                      'lva' => 'TUN'),
			array('code' => 'TR', 'name' => 'Turquie',                                      'lva' => 'TUR'),
		#	array('code' => 'TT', 'name' => 'Trinité-et-Tobago',                            'lva' => ''),
		#	array('code' => 'TW', 'name' => 'Taïwan',                                       'lva' => ''),
		#	array('code' => 'TZ', 'name' => 'Tanzanie',                                     'lva' => ''),
			array('code' => 'UA', 'name' => 'Ukraine',                                      'lva' => 'UKR'),
		#	array('code' => 'UG', 'name' => 'Ouganda',                                      'lva' => ''),
			array('code' => 'US', 'name' => 'Etats-Unis',                                   'lva' => 'USA'),
		#	array('code' => 'UY', 'name' => 'Uruguay',                                      'lva' => ''),
		#	array('code' => 'UZ', 'name' => 'Ouzbékistan',                                  'lva' => ''),
		#	array('code' => 'VA', 'name' => 'Vatican',                                      'lva' => ''),
		#	array('code' => 'VE', 'name' => 'Venezuela',                                    'lva' => ''),
		#	array('code' => 'VN', 'name' => 'Vietnam',                                      'lva' => ''),
		#	array('code' => 'YE', 'name' => 'Yémen',                                        'lva' => ''),
		#	array('code' => 'YU', 'name' => 'Yougoslavie',                                  'lva' => ''),
			array('code' => 'ZA', 'name' => 'Afrique du Sud',                               'lva' => 'ZA'),
		#	array('code' => 'ZM', 'name' => 'Zambie',                                       'lva' => ''),
		#	array('code' => 'ZR', 'name' => 'Zaïre',                                        'lva' => ''),
		#	array('code' => 'ZW', 'name' => 'Zimbabwe',                                     'lva' => '')
		);
	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public function countryGet($opt){

		if(!empty($opt['code'])){
			$region = $this->country();
			foreach($region as $e){
				if($e['code'] == $opt['code']) return $e;
			}
		}else{
			return $this->country();
		}

	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public function countryFromLVA($lva){

		foreach($this->country() as $e){
			if($e['lva'] == $lva) return $e['code'];
		}

		return '';
	}
}