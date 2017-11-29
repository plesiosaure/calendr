Dernière mise à jour du document:   

- 24 juillet 2013 à 15h57: intégration mvs[] dans la manifestation
- 24 juillet 2013 à 15h42: l'id_organisateur d'une manifestion passe dans l'objet organisateur:{}
- 24 juillet 2013 à 13h21: le datamodel d'une manifestation indique boolean au lieu de true




----

`Le domaine TEMPORAIRE a interroger est calendrier.kappuccino.org, en HTTP`

Chaque sortie est un objet JSON composé de cette manière
	
	{  
		ok: true|false		si l'action demandé a bien été executé  
		data: []|{}			contenant la réponse, soit un objet soit un array  
		time: float			temps d'execution du script  
	}

## Ville


#### DataModel

	 {
	 	_id: 		MongoId
    	id: 		integer
    	id_dep: 	integer
    	zip: 		string
    	name: 		string
    }

#### Retourne les informations sur une ville ✓

###### URL
> GET /mvs/ville/id/:id  
> GET /mvs/ville/_id/:\_id  

###### ARGUMENTS
> :id est en entier correspondant à l'ID de la ville sur la base MVS  
> :\_id Même fonction de ci-dessus, mais avec l'_ID de l'API

###### SORTIE
<code>data</code> contient un object ville



#### Mise à jour d'une ville ✓
Les champs ne sont pas tous obligatoires, vous pouvez mettre à jour uniquement :zip ou :zip et :name, mais il doit y avoir au minium un chamsp à mettre à jour, sinon l'API retournera une erreur.

###### URL  
> POST /mvs/ville/id/:id  
> POST /mvs/ville/_id/:\_id  

###### ARGUMENTS
> :id est en entier correspondant à l'ID de la ville sur la base MVS  
> :\_id est l'identifiant de la ville coté API  
> :id_dep est l'identifiant du département (entier)  
> :zip est le code postal à changer  
> :name est le nom à changer

###### SORTIE
<code>data</code> contient un object ville mis à jour (renvois un object entier)



#### Création d'une nouvelle ville ✓
Tous les champs (:zip, :name: :id_dep et :id) sont obligatoires

###### URL  
> PUT /mvs/ville  

###### ARGUMENTS  (Tous sont requis)
> :name le nom de la ville  
> :zip le code postal de la ville  
> :id l'identifiant de la base MVS (il sera enregistré en tant qu'entier)  
> :id_dep est l'identifiant du département (entier)  


###### SORTIE  
<code>data</code> contient la nouvelle ville



#### Suppression d'une ville ✓

###### URL  
> DELETE /mvs/ville/id/:id  
> DELETE /mvs/ville/_id/:\_id  

###### ARGUMENTS  
> :id est en entier correspondant à l'ID de la ville sur la base MVS  
> :\_id est l'identifiant de la ville coté API  
> 

###### SORTIE
Un objet de sortie, voir au début du document.  





## Organisateur

#### DataModel

	{
		_id: 			MongoId
    	address: 		string
    	city: {
    	    id: 			integer
    	    _id: 			string
    	    name:			string
    	},
    	commentaire:	string
    	email:			string
    	fax:			string
    	firstname:		string
    	fonction:		string
    	id:				integer
    	lastname: 		string
    	mobile: 		string
    	name: 			string
    	phone: 			string
    	title: 			string
    	web: 			string
    }


#### Retourne les informations sur un organisateur ✓

###### URL
> GET /mvs/organisateur/id/:id  
> GET /mvs/organisateur/_id/:\_id  

###### ARGUMENTS
> :id est en entier correspondant à l'ID de l'organisateur sur la base MVS  
> :\_id même fonction que ci-dessus, mais avec l'_ID de l'API

###### SORTIE
<code>data</code> contient un objet organisateur



#### Mise à jour d'un organisateur ✓
Les champs ne sont pas tous obligatoires, vous pouvez mettre à jour uniquement :name ou :email par exemple, mais il doit y avoir au minium un champ à mettre à jour, sinon l'API retournera une erreur.

###### URL  
> POST /mvs/organisateur/id/:id  
> POST /mvs/organisateur/_id/:\_id  

###### ARGUMENTS
voir le datamodel

###### SORTIE
<code>data</code> contient un objet organisateur mis à jour (renvois un object entier)



#### Création d'un nouvel organisateur ✓
Les champs (:name et :id) sont obligatoires

###### URL  
> PUT /mvs/organisateur  

###### ARGUMENTS  (Tous sont requis)
> :name le nom de l'organisateur  
> :id l'identifiant de la base MVS (il sera enregistré en tant qu'entier)  
Seul :name et :id sont obligatoire, les autres champs indiqués dans la fonction de mise à jour peuvent être utilisés

###### SORTIE  
<code>data</code> contient le nouvel organisateur



#### Suppression d'un organisateur ✓

###### URL  
> DELETE /mvs/organisateur/id/:id  
> DELETE /mvs/organisateur/_id/:\_id  

###### ARGUMENTS  
> :id est en entier correspondant à l'ID de l'organisateur sur la base MVS  
> :\_id est l'identifiant de la ville coté API  

###### SORTIE
Un objet de sortie, voir au début du document.  



## Manifestation

#### DataModel

	{
		_id: 						MongoId
		id: 						string 
    	city: {
    		_id							MongoId
    		id							integer
    		name						string
    	}
    	name:						test
    	email: 						string
    	phone:						string
    	fax:						string
    	web:						string
    	mobile:						string
    	paying:						boolean
    	free:						boolean
    	price:						float
    	geo: {
    	    region:						integer
    	    dept:						integer
    	    address:					string,
    	    comment:					string,
    	    gps: [],					float, float
        	zoom:						integer
    	},
    	indoor:						boolean
    	outdoor:					boolean
    	pro: 						boolean
    	individual: 				boolean
    	resident: 					boolean
    	game:						boolean
    	number: 					integer
    	opening: 					string
    	periodicity:				integer
    	schedule: 					string
    	presentation:				string    	
    	resume:						string
    	organisateur:{
    		_id							MongoId,
    		id							entier
    		name						string
    	},
    	type: {
    		auto: [],				string, string, ...
    		moto: [],				string, string, ...
    		brocante: []			string, string, ...
    	},
    	date: [						array
    		{
    			start					timestamp
    			end						timestamp
    			days					integer
    			comment					string
    			canceled				boolean
    			postponed				boolean
    			unsure					boolean
    		}
    	],
    	mvs: {
    		type:							integer,
    		category:						integer
    	}
    }

#### Retourne les informations sur une manifestation ✓

###### URL
> GET /mvs/manifestation/id/:id  
> GET /mvs/manifestation/_id/:\_id  

###### ARGUMENTS
> :id est en string correspondant à l'ID de la manifestation sur la base MVS ainsi que le type sous la forme type_id  
> :\_id même fonction que ci-dessus, mais avec l'_ID de l'API

###### SORTIE
<code>data</code> contient un objet manifestation



#### Mise à jour d'une manifestation ✓
Les champs ne sont pas tous obligatoires, vous pouvez mettre à jour uniquement :name par exemple, mais il doit y avoir au minium un champ à mettre à jour, sinon l'API retournera une erreur.

###### URL  
> POST /mvs/manifestation/id/:id  
> POST /mvs/manifestation/_id/:\_id  

###### ARGUMENTS
> :id est en string correspondant à l'ID de la manifestation sur la base MVS ainsi que le type sous la forme type_id  
> :\_id est l'identifiant de la manifestation coté API  



###### SORTIE
<code>data</code> contient un objet manifestation mis à jour (renvois un object entier)



#### Création d'un nouvel manifestation ✓
Il est possible de créer une manifestation ainsi que les dates rattachées en une seule requête, il faut pour cela envoyer un array de date <code>date[0][start], date[1][start], date[2][start]...</code> en suivant le datamodel pour la structure. Les champs <code>comment, canceled, postponed, unsure</code> sont optionnels

###### URL  
> PUT /mvs/manifestation  

###### ARGUMENTS  (Tous sont requis)
> :name le nom de la manifestation  
> :id l'identifiant de la base MVS (il sera enregistré en tant qu'entier)  
> :city est l'identifiant de la ville sur la base de MVS  
> :organisateur est l'identifiant de l'organisateur de la base MVS

Seul :name :id :city et :organisateur sont obligatoires, les autres champs indiqués dans le datamodel peuvent être utilisés

###### SORTIE  
<code>data</code> contient la nouvelle manifestation



#### Suppression d'une manifestation ✓

###### URL  
> DELETE /mvs/manifestation/id/:id  
> DELETE /mvs/manifestation/_id/:\_id  

###### ARGUMENTS  
> :id est en string correspondant à l'ID de la manifestation sur la base MVS ainsi que le type sous la forme type_id  
> :\_id est l'identifiant de la manifestation coté API  

###### SORTIE
Un objet de sortie, voir au début du document.  




----


#### ★ Ajouter une nouvelle date ✓

###### URL  
> PUT  /mvs/manifestation/:\_id/date

###### ARGUMENTS  (tous obligatoires)
> :\_id est l'identifiant de la manifestation coté API  
> :timestamp est le timestamp de départ de la date à modifier  
> :days le nombre de jour de la période  
> :comment un commentaire sur la date
> :canceled un booleen (1|0) si c'est annulé
> :postponed un booleen (1|0) si c'est reporté
> :unsure un booleen (1|0) si c'est sous-reserve

###### SORTIE
Un objet manifestion complet



#### ★ Suppression d'une date ✓

###### URL  
> DELETE /mvs/manifestation/:\_id/date/:timestamp

###### ARGUMENTS  
> :\_id est l'identifiant de la manifestation coté API  
> :timestamp est le timestamp de départ de la date à supprimer

###### SORTIE
Un objet de sortie, voir au début du document.  



#### ★ Mise à jour d'une date ✓
On ne peut pas changer la date de début, uniquement modifier la durée et rajouter un commentaire

###### URL  
> POST  /mvs/manifestation/:\_id/date/:timestamp

###### ARGUMENTS  (tous obligatoires)
> :\_id est l'identifiant de la manifestation coté API  
> :timestamp est le timestamp de départ de la date à modifier  
> :days le nombre de jour de la période  
> :comment un commentaire sur la date
> :canceled un booleen (1|0) si c'est annulé
> :postponed un booleen (1|0) si c'est reporté
> :unsure un booleen (1|0) si c'est sous-reserve

###### SORTIE
Un objet manifestion complet






