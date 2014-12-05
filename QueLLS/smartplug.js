	// Exécute une requête HTTP "cross-domain" par appel au proxy PHP
	// Paramètres :
	//	p_url : URL de la page
	//	p_command : paramètres à passer dans la requête
	//	p_selector : sélecteur de balise HTML a isoler dans la réponse HTTP
	//	p_regex : expression régulière à appliquer sur la réponse
	//	p_callback : fonction à appeler à la fin de l'exécution de la requête AJAX
	//
	function makeHTTPRequest(p_url, p_command, p_selector, p_regex, p_callback) {
		var returnValue = "";
		$.ajax({
			async: false,
			type: "POST",
			url: 'ajax_proxy.php',
			contentType: "application/x-www-form-urlencoded; charset=utf-8",                                                        
			data: { url: p_url,
					command: p_command },
			success: function (data) {
				// la syntaxe qui marche
//					alert("data = "+$(data).find(p_selector).html())
				var match;
				returnValue = data;
				if (p_selector) returnValue = $(data).find(p_selector).html();
				if (p_regex) match = $(data).find(p_selector).html().match(p_regex);
				if (match)
					returnValue = match[1];
//				alert("data = "+$(data).find(p_selector).html().match(p_regex)[1]);
				// la syntaxe qui marche
		;
			},
			error: function (xhr, status, error) {
				returnValue = "status="+status+" / error = "+error;
			},
			complete: function (xhr, status) {
				p_callback(returnValue);
			}
		});
	}	
	
	// Lit la consommation instantanée de la prise (en Watts)
	//	target : id de l'objet HTML qui recevra le résultat
	function getInfoW(ip, target) {
		makeHTTPRequest('http://'+ip+'/goform/SystemCommand', 
						'GetInfo W', 
						'textarea', 
						/\$..W..\s+(\d*)/, 
						function (result) {
							$(target).html(result*0.01+' W');
						}
		);
	}
	
	// Lit l'intensité de la prise (en Ampères)
	//	target : id de l'objet HTML qui recevra le résultat
	function getInfoI(ip, target) {
		makeHTTPRequest('http://'+ip+'/goform/SystemCommand', 
						'GetInfo I', 
						'textarea', 
						/\$..I..\s+(\d*)/, 
						function (result) {
							$(target).html(result*0.001+' A');
						}
		);
	}
	
	// Lit la tension de la prise (en Volts)
	//	target : id de l'objet HTML qui recevra le résultat
	function getInfoV(ip, target) {
		makeHTTPRequest('http://'+ip+'/goform/SystemCommand', 
						'GetInfo V', 
						'textarea', 
						/\$..V..\s+(\d*)/, 
						function (result) {
							$(target).html(result*0.001+' V');
						}
		);
	}
	
	// Lit la consommation totale de la prise (en Watts)
	//	target : id de l'objet HTML qui recevra le résultat
	function getInfoE(ip, target) {
		makeHTTPRequest('http://'+ip+'/goform/SystemCommand', 
						'GetInfo E', 
						'textarea', 
						/\$..E..\s+(\d*)/, 
						function (result) {
							$(target).html(result*0.01+' W');
						}
		);
	}
	
	// Lit l'état de la prise
	//	target : id de l'objet HTML qui recevra le résultat : 1 = allumé, 0 = éteint
	function getStatus(ip, target) {
		makeHTTPRequest('http://'+ip+'/goform/SystemCommand', 
						'GetInfo W', 
						'textarea', 
						/\$..W..\s+(\d*)/, 
						function (result) {
							$(target).html((result*0.01)>0.2);
						}
		);
	}

	// Allume/Eteint la prise
	//	p_on: 1 pour allumer, 0 pour éteindre
	function setPlugOnOff(ip, p_on) {
		if (p_on!=1 && p_on!=0) p_on = 0;
		makeHTTPRequest('http://'+ip+'/goform/SystemCommand', 
						'GpioForCrond '+p_on, 
						null, 
						null, 
						function (result) {
							// do nothing
						}
		);
	}
	
	function getFormGreenAP(ip, target) {
		makeHTTPRequest('http://'+ip+'/adm/management-user.asp', 
						'', 
						null, 
						null, 
						function (result) {
							$(target).html(result);
						}
		);
	}
