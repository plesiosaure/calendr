<?php

// ----------------------------------------------------------------
/*
Et en Bonus, puisque vous avez été sympa vous trouverez ci-desous
quelques informations qui prennent forcément du temps la première fois
qu'on les cherche !

Si cela vous permet de gagner quelques minutes/heures alors merci de passer
quelques secondes/minutes sur mon site, et de faire un clic ou deux sur la pub ! 
C'est "malheureusement" le seul moyen de juste payer l'hébergement et le domaine !
(Mais non je ne serai jamais riche avec ce site !!!)

|------  HTTP://WWW.KARAMELISE.FR  --------|

C'est sympa ! Merci !

Si par contre vous voyez une erreur, alors merci de me l'indiquer à cette adresse
contact@karamelise.fr

*/
// ----------------------------------------------------------------



// Le "centre" (latitude, longitude) des départements français
$depts_focalpoint = array (
	"67"  => "48.599187, 7.586570",
	"68"  => "47.865798, 7.222762",
	"24"  => "45.142728, 0.703240",
	"33"  => "44.883881, -0.474218",
	"40"  => "44.009922, -0.698237",
	"47"  => "44.369254, 0.468752",
	"64"  => "43.187408, -0.881594",
	"03"  => "46.367877, 3.141527",
	"15"  => "45.049891, 2.717207",
	"43"  => "45.085981, 3.786243",
	"63"  => "45.771938, 3.186703",
	"14"  => "49.092978, -0.356423",
	"50"  => "49.093557, -1.343196",
	"61"  => "48.576332, 0.057986",
	"21"  => "47.465594, 4.792314",
	"58"  => "47.119579, 3.538374",
	"71"  => "46.655721, 4.544038",
	"89"  => "47.855299, 3.594307",
	"22"  => "48.458409, -2.787477",
	"29"  => "48.232757, -4.264126",
	"35"  => "48.172063, -1.652918",
	"56"  => "47.744269, -2.884961",
	"18"  => "47.024891, 2.426636",
	"28"  => "48.447422, 1.374955",
	"36"  => "46.812255, 1.536098",
	"37"  => "47.223410, 0.709267",
	"41"  => "47.659764, 1.414145",
	"45"  => "47.913833, 2.320001",
	"08"  => "49.698015, 4.709221",
	"10"  => "48.320135, 4.124092",
	"51"  => "48.961390, 4.218025",
	"52"  => "48.132939, 5.259089",
	"20"  => "42.056269, 9.150663",
	"25"  => "47.066857, 6.380444",
	"39"  => "46.782985, 5.729471",
	"70"  => "47.638395, 6.096119",
	"90"  => "47.624825, 6.950104",
	"27"  => "49.075729, 1.049821",
	"76"  => "49.661630, 0.928599",
	"75"  => "48.858853, 2.347005",
	"77"  => "48.618909, 2.975640",
	"78"  => "48.761996, 1.837898",
	"91"  => "48.530290, 2.250067",
	"92"  => "48.840203, 2.241248",
	"93"  => "48.910792, 2.445818",
	"94"  => "48.774569, 2.461979",
	"95"  => "49.071363, 2.101589",
	"11"  => "43.054657, 2.464672",
	"30"  => "43.960103, 4.053731",
	"34"  => "43.592608, 3.366918",
	"48"  => "44.542683, 3.490079",
	"66"  => "42.625705, 2.450215",
	"19"  => "45.343267, 1.877855",
	"23"  => "46.059506, 1.992035",
	"87"  => "45.919213, 1.270488",
	"54"  => "48.956121, 6.274396",
	"55"  => "49.013088, 5.371281",
	"57"  => "49.020719, 6.765730",
	"88"  => "48.163513, 6.295955",
	"09"  => "42.943940, 1.501116",
	"12"  => "44.315875, 2.645563",
	"31"  => "43.305402, 1.244674",
	"32"  => "43.695479, 0.460501",
	"46"  => "44.624894, 1.596306",
	"65"  => "43.143377, 0.159571",
	"81"  => "43.791885, 2.234733",
	"82"  => "44.080753, 1.368274",
	"59"  => "50.528903, 3.149132",
	"62"  => "50.513221, 2.371636",
	"44"  => "47.348001, -1.740869",
	"49"  => "47.389406, -0.559479",
	"53"  => "48.150766, -0.644357",
	"72"  => "48.026716, 0.234294",
	"85"  => "46.675889, -1.469182",
	"02"  => "49.453628, 3.607794",
	"60"  => "49.412233, 2.427612",
	"80"  => "49.971149, 2.292245",
	"16"  => "45.664696, 0.242009",
	"17"  => "45.730391, -0.779816",
	"79"  => "46.539156, -0.341337",
	"86"  => "46.612096, 0.554250",
	"04"  => "44.164191, 6.232933",
	"05"  => "44.656398, 6.248195",
	"06"  => "43.920701, 7.177142",
	"13"  => "43.542134, 5.020405",
	"83"  => "43.395516, 6.294573",
	"84"  => "44.045319, 5.202928",
	"01"  => "46.065375, 5.448974",
	"07"  => "44.815342, 4.373790",
	"26"  => "44.729651, 5.238637",
	"38"  => "45.289696, 5.550697",
	"42"  => "45.753789, 4.224511",
	"69"  => "45.880356, 4.702795",
	"73"  => "45.494388, 6.403594",
	"74"  => "46.045179, 6.424750",
	"971" => "16.203622, -61.550668",
	"972" => "14.636899, -60.999009",
	"973" => "4.050577,-53.041992",
	"974" => "-21.149832, 55.531162",
	"975" => "-21.460706, 165.531132",
	"976" => "-12.825192, 45.155182"
);


// le "centre" (latitude, longitude) des régions françaises
// PS : pour les Dom-tom c'est pas simple de trouver le centre... :-)
// Alors j'ai choisi un focal point Martinique/Guadeloupe.... 
$regions_focalpoint = array (
	"alsace"                    => "48.242918,7.543056",
	"aquitaine"                 => "44.274445,-0.167916",
	"auvergne"                  => "45.698750,3.281945",
	"basse-normandie"           => "48.969027,-0.497500",
	"bourgogne"                 => "47.269167,4.190139",
	"bretagne"                  => "48.070000,-3.064861",
	"centre"                    => "47.645832,1.570833",
	"champagne-ardenne"         => "48.875971,4.640417",
	"corse"                     => "42.188096,9.047040",
	"dom-tom"                   => "15.464269,-61.309204",
	"franche-comte"             => "47.122778,6.193472",
	"haute-normandie"           => "49.368471,0.930833",
	"ile-de-france"             => "48.672640,2.479861",
	"languedoc-roussillon"      => "43.654583,3.257500",
	"limousin"                  => "45.675694,1.626528",
	"lorraine"                  => "48.721388,6.250000",
	"midi-pyrenees"             => "43.804026,1.541111",
	"nord-pas-de-calais"        => "50.531250,2.885555",
	"pays-de-la-loire"          => "47.429028,-0.840972",
	"picardie"                  => "49.605694,2.805555",
	"poitou-charentes"          => "46.128332,-0.192083",
	"provence-alpes-cote-dazur" => "44.061390,6.023611",
	"rhone-alpes"               => "45.316250,5.396667"
);

