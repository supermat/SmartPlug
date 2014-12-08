	// Exécute une requête HTTP "cross-domain" par appel au proxy PHP
	// Paramètres :
	//	p_params : paramètres de la requête HTTP au format JSON
	//		{url : URL de la page,
	//		 param1, param2  : paramètres à passer à l'URL
	//	p_selector : sélecteur de balise HTML à isoler dans la réponse HTTP
	//	p_regex : expression régulière à appliquer sur la réponse
	//	p_callback : fonction à appeler à la fin de l'exécution de la requête AJAX
	//
	function makeHTTPRequest(p_params, p_selector, p_regex, p_callback) {
		var returnValue = "";
//		alert("p_params="+JSON.stringify(p_params));
		$.ajax({
			async: false,
			type: "POST",
			url: 'ajax_proxy.php',
			contentType: "application/x-www-form-urlencoded; charset=utf-8",                                                        
			data: {params: JSON.stringify(p_params)}, 
			success: function (data) {
				// la syntaxe qui marche
//				alert("data = "+data); //$(data).find(p_selector).html())
				returnValue = data;
//				alert('data='+data);
				if ((p_selector) && ($(data).find(p_selector))) 
					returnValue = $(data).find(p_selector).html();
				var match;
				if (p_regex) 
					match = $(data).find(p_selector).html().match(p_regex);
				if (match)
					returnValue = match[1];
			},
			error: function (xhr, status, error) {
				returnValue = "status="+status+" / error = "+error;
			},
			complete: function (xhr, status) {
				p_callback(returnValue);
			}
		});
		return returnValue;
	}	
	
	// Lit la consommation instantanée de la prise (en Watts)
	//	ip : adresse IP de la prise interrogée
	//	target : id de l'objet HTML qui recevra le résultat
	function getInfoW(ip, target) {
		makeHTTPRequest({url: 'http://'+ip+'/goform/SystemCommand', 
						 command: 'GetInfo W'}, 
						'textarea', 
						/\$..W..\s+(\d*)/, 
						function (result) {
							$(target).html(result*0.01+' W');
						}
		);
	}
	
	// Lit l'intensité de la prise (en Ampères)
	//	ip : adresse IP de la prise interrogée
	//	target : id de l'objet HTML qui recevra le résultat
	function getInfoI(ip, target) {
		makeHTTPRequest({url: 'http://'+ip+'/goform/SystemCommand', 
						 command: 'GetInfo I'}, 
						'textarea', 
						/\$..I..\s+(\d*)/, 
						function (result) {
							$(target).html(result*0.001+' A');
						}
		);
	}
	
	// Lit la tension de la prise (en Volts)
	//	ip : adresse IP de la prise interrogée
	//	target : id de l'objet HTML qui recevra le résultat
	function getInfoV(ip, target) {
		makeHTTPRequest({url: 'http://'+ip+'/goform/SystemCommand', 
						 command: 'GetInfo V'}, 
						'textarea', 
						/\$..V..\s+(\d*)/, 
						function (result) {
							$(target).html(result*0.001+' V');
						}
		);
	}
	
	// Lit la consommation totale de la prise (en Watts)
	//	ip : adresse IP de la prise interrogée
	//	target : id de l'objet HTML qui recevra le résultat
	function getInfoE(ip, target) {
		makeHTTPRequest({url: 'http://'+ip+'/goform/SystemCommand', 
						 command: 'GetInfo E'}, 
						'textarea', 
						/\$..E..\s+(\d*)/, 
						function (result) {
							$(target).html(result*0.01+' W');
						}
		);
	}
	
	// Lit l'état de la prise
	//	ip : adresse IP de la prise interrogée
	//	target : id de l'objet HTML qui recevra le résultat : 1 = allumé, 0 = éteint
	function getStatus(ip, target) {
		makeHTTPRequest({url: 'http://'+ip+'/goform/SystemCommand', 
						 command: 'GetInfo W'}, 
						'textarea', 
						/\$..W..\s+(\d*)/, 
						function (result) {
							alert((result*0.01)>0.2);
							$(target).text((result*0.01)>0.2);
						}
		);
	}

	// Allume/Eteint la prise
	//	ip : adresse IP de la prise interrogée
	//	p_on: 1 pour allumer, 0 pour éteindre
	function setPlugOnOff(ip, p_on) {
		if (p_on!=1 && p_on!=0) p_on = 0;
		makeHTTPRequest({url: 'http://'+ip+'/goform/SystemCommand', 
						 command: 'GpioForCrond '+p_on}, 
						null, 
						null, 
						function (result) {
							// do nothing
						}
		);
	}

	// Planifie un Allumage/Extinction
	//	ip : adresse IP de la prise interrogée
	//	slot : numéro d'emplacement programmable, de 1 à 4
	//	action : action à réaliser : Allumer, éteindre, activer le Wifi...
	//	start_h : heure de début, de 0 à 23
	//	start_m : minute de début, de 0 à 59
	//	end_h : heure de fin, de 0 à 23
	//	end_m : minute de fin, de 0 à 59
	//	target : id de l'objet HTML qui recevra le résultat 
	function setPlugTimerAction(ip, p_slot, p_action, p_start_h, p_start_m, p_end_h, p_end_m, target) {

		var params = {"url":"http://"+ip+"/goform/GreenAP"};
		// construit un tableau de paramètres pour les 4 emplacements
		for (i=1; i<=4; i++) {
			if (i==p_slot) {
				// emplacement à modfier
				params = $.extend(params,
							JSON.parse(
								'{"GAPAction'+p_slot+'":"'+p_action+'",'
								+'"GAPSHour'+p_slot+'":"'+p_start_h+'",'
								+'"GAPSMinute'+p_slot+'":"'+p_start_m+'",'
								+'"GAPEHour'+p_slot+'":"'+p_end_h+'",'
								+'"GAPEMinute'+p_slot+'":"'+p_end_m+'"'
								+'}'
							)
						);
			} else {
				// récupérer les valeurs de l'emplacement existant
				params = $.extend(params,
							JSON.parse(	getPlugTimerAction(ip, i) )
						);
			}
		}
			
		// appelle la requête
		var returnValue = makeHTTPRequest(params,
						null, 
						null, 
						function (result) {
							$(target).html(result);
						}
		);
		
		return returnValue;
	}

	// Lit l'état programmé
	//	Retour : Action et heures planifiées au format JSON/String
	//	ip : adresse IP de la prise interrogée
	//	slot : numéro d'emplacement programmable, de 1 à 4
	//	
	function getPlugTimerAction(ip, p_slot) {
		// lecture de l'action
		var action = makeHTTPRequest({url: 'http://'+ip+'/goform/SystemCommand', 
						 command: 'nvram_get 2860 GreenAPAction'+p_slot}, 
						'textarea', 
						null, 
						function (result) {
							// do nothing
						}
		);
		// lecture de l'heure de début
		var start = makeHTTPRequest({url: 'http://'+ip+'/goform/SystemCommand', 
						 command: 'nvram_get 2860 GreenAPStart'+p_slot}, 
						'textarea', 
						null, 
						function (result) {
							// do nothing
						}
		);
		// lecture de l'heure de fin
		var end = makeHTTPRequest({url: 'http://'+ip+'/goform/SystemCommand', 
						 command: 'nvram_get 2860 GreenAPEnd'+p_slot}, 
						'textarea', 
						null, 
						function (result) {
							// do nothing
						}
		);
		// construction du JSON de retour
		var params = '{';
		params += '"GAPAction'+p_slot+'":"'+action+'",';
		var sTimeArray = start.split(' ');
		params += '"GAPSHour'+p_slot+'":"'+sTimeArray[1]+'",';
		params += '"GAPSMinute'+p_slot+'":"'+sTimeArray[0]+'",';
		var eTimeArray = end.split(' ');
		params += '"GAPEHour'+p_slot+'":"'+eTimeArray[1]+'",';
		params += '"GAPEMinute'+p_slot+'":"'+eTimeArray[0]+'"';
		params += '}';	
		// efface les caractères de contrôle, espaces, retour à la ligne
		return params.replace(/(\s|\r\n|\n|\r)/gm,"");
	}
	
	function getFormGreenAP(ip, target) {
		makeHTTPRequest({url: 'http://'+ip+'/adm/management-user.asp', 
						 command: ''}, 
						null, 
						null, 
						function (result) {
							$(target).html(result);
						}
		);
	}
