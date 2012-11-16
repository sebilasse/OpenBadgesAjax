require([ 'dojo/_base/event','dojo/_base/fx','dojo/fx','dojo/dom','dojo/dom-class','dojo/NodeList-dom','dojo/request/xhr','dojo/query','dojo/json','dojo/on','dojo/domReady!' ], 
function( evt,baseFx,fx,dom,css,nl,xhr,query,json,on,ready ){ 
	
	var bs = query('h2 b');
	
	query('.start').on('click', function() {
		// do YOUR OWN error handling
		var personaErr = function(err){  
			console.log( 'PERSONA VERIFY ERROR: ' );
			console.log( err );
		}
		var questionErr = function(err){  
			console.log( 'QUESTION ERROR: ' );
			console.log( err );
		}
		var badgeErr = function(err){  
			console.log( 'BADGE ERROR: ' );
			console.log( err );
		}
		
		// Mozilla Open Badges Issuer API
		var issueBadge = function(assertionUrl){
			OpenBadges.issue([''+assertionUrl+''], function(errors, successes) { 
			//	console.log(errors.toSource()); console.log(successes.toSource()); 
				if (errors.length > 0 ) { 
					badgeErr(errors); 
				}
				if (successes.length > 0) {
					query('h2 b.a').addClass('b'); bs.removeClass('a'); 
					dom.byId('badge').innerHTML = 'Get your badge';
					dom.byId('content').innerHTML = '<h1 class="green">Congratulations, you have earned a badge !!!</h1>';
				}	
			});
		}
		
		
		var verify = function(_assertion) {
			var verifier = 'verify.php';
			var postArgs = {
				data: {
					'badgename':'standard', /* SET THE BADGENAME of the badge you award (defined in settings) */
					'type':		'persona', /* default */
					'qnumber':	'0',
					'answer':	'',
					'a':		_assertion,
					'_A':		_csrf._a, 
					'_B':		_csrf._b
				},
				method: "POST",
				handleAs: "json"
			};
			// verify the Persona BrowserID eMail
			xhr(verifier, postArgs).then(
				function(data){
					if(data.status>0){
						//console.log( 'Logged in :' ); console.log(data.mail);
						// NOTE: Basically there is no need to push the data.mail back to this javascript
						// this is just for demo, we set a php session with the mailadress for issuing the badge
						
						// load questions in the content div here
						dom.byId('content').innerHTML = '<h2 class="orange">This is just a code example. Please provide REAL lessons or questions here...</h2>'+
														'<h1>1+1 =</h1><input id="question" class="mac" type="text" /><a class="badge answer">answer</a>';
						query('h2 b.a').addClass('b'); bs.removeClass('a'); css.add(bs[1],'a'); dom.byId('persona').innerHTML = 'Persona';
						
						// answer button onclick
						query('.answer').on('click', function() {
							postArgs.data.type = 'answer';
							postArgs.data.qnumber = '1';
							postArgs.data.answer = dom.byId('question').value;
							// verify if the answer is correct
							xhr(verifier, postArgs).then(
								function(data){
									if(data.status>0){
										dom.byId('content').innerHTML = '<h1>Awarding ...</h1>';
										postArgs.data.type = 'badge';
										// if it is correct, issue a badge
										xhr(verifier, postArgs).then(
											function(data){
												if(data.status>0){
													// console.log( 'Badge ready :' ); console.log( data.url );
													query('h2 b.a').addClass('b'); bs.removeClass('a'); css.add(bs[2],'a');
													dom.byId('content').innerHTML = '<h1>Your Badge is ready !</h1>';
													dom.byId('badge').innerHTML = '<a href="#badge">Get your badge !</a>';
													on(dom.byId('badge'), 'click', function() {
														issueBadge(data.url);
													});
													issueBadge(data.url);
												} else {
													badgeErr(data.error);	
												}
											},
											function(err){ badgeErr(err); }
										);
										
									} else {
										questionErr(data.error);
									}
								},
								function(err){ questionErr(err); }
							);
						});
					} else { personaErr(data.error); }
				}, 
				function(err){ personaErr(err); }, 
				function(evt){ /*a progress function for xhr2 browsers, not needed here ...*/ }
			);
		}
		
		// Mozilla BrowserID API
		navigator.id.get(function(assertion) {
			if (assertion) { verify(assertion); } else { console.log( 'PERSONA ERROR 1'); }
		});
	});
	
	// kill loader div, activate point 1, fade in page
	query('#loader').orphan();
	css.add(bs[0],'a');
	baseFx.animateProperty({ node: dom.byId('content'), properties: { opacity: { end: 1 } }, duration: 800 }).play();
});