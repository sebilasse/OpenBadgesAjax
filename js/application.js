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
			xhr(verifier, postArgs).then(
				function(data){
					if(data.status>0){
						//console.log( 'Logged in :' ); console.log(data.mail);
						dom.byId('content').innerHTML = '<h2 class="orange">This is just a code example. Please provide REAL lessons or questions here...</h2>'+
														'<h1>1+1 =</h1><input id="question" class="mac" type="text" /><a class="badge answer">Answer</a>';
						query('h2 b.a').addClass('b'); bs.removeClass('a'); css.add(bs[1],'a'); dom.byId('persona').innerHTML = 'Persona';
						
						query('.answer').on('click', function() {
							postArgs.data.type = 'answer';
							postArgs.data.qnumber = '1';
							postArgs.data.answer = dom.byId('question').value;
							xhr(verifier, postArgs).then(
								function(data){
									if(data.status>0){
										dom.byId('content').innerHTML = '<h1>Awarding ...</h1>';
										postArgs.data.type = 'badge';
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
		navigator.id.get(function(assertion) {
			if (assertion) { verify(assertion); } else { console.log( 'PERSONA ERROR 1'); }
		});
	});
	
	query('#loader').orphan();
	css.add(bs[0],'a');
	baseFx.animateProperty({ node: dom.byId('content'), properties: { opacity: { end: 1 } }, duration: 800 }).play();
});