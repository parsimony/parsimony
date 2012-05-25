    function generateLinks() {
	var tables = [], links = [], tableCount = [];
	$('#recipiant_sql .queryblock').each(function(){
	    var table = $(".table",this).val();
	    if($.inArray(table, tables) == -1){
		tables.push(table);
	    }
	});
	$("#schema_sql .property[link]").each(function(i){
            
	    var table1 = $(this).parent().attr("table");
	    var table2 = $(this).attr('link');
	    if($.inArray(table1, tables) != -1 && $.inArray(table2, tables) != -1){//si ce n'est pas un link de table d'asso'
		if(table1 != table2) {
		    links.unshift(table2 + "=>" + table1);
		    //tableCount[table2] = (tableCount[table2] || 0) + 1;
		}
	    }else if($.inArray(table1, tables) != -1 || $.inArray(table2, tables) != -1){ // si c'est in link de table d'asso'
		var tableProv = Array();
		$('.property[link]', $(this).parent()).each(function(i){
		    var table2 = $(this).attr('link');
		    if($.inArray(table2, tables) != -1) tableProv.push(table1 + "=>" + table2);
		});
		if(tableProv.length >= 2){
		    $.each( tableProv, function(i, val){
			if($.inArray(val, links) == -1){
                            alert(val);
			    links.push(val);
			    var cut = val.split("=>");
			    //tableCount[cut[1]] = (tableCount[cut[1]] || 0) + 1;
			}
		    });
		}
	    }
	});

	var alreadyIncluded = [];
	var cpt = 0;
	boucle:
	while( Object.keys(links).length != 0 ){
	    for(var key in links){
                if(alreadyIncluded.length != tables.length){
                    var myTables = links[key].split("=>");
                    var type="inner join"

                    if(cpt == 0 || ($.inArray(myTables[0], alreadyIncluded) != -1 && $.inArray(myTables[1], alreadyIncluded) == -1)){
                        var linkbefore = $('#links [name="relations[' + myTables[0] + '_' + myTables[1] + '][type]"]');
                        if(linkbefore.length > 0) type = linkbefore.val();
                        putLink(myTables[0],myTables[1],type);
                        delete links[key];
                        if(cpt == 0) alreadyIncluded.push(myTables[0]);
                        alreadyIncluded.push(myTables[1]);
                        cpt++;
                    }else if($.inArray(myTables[1], alreadyIncluded) != -1 && $.inArray(myTables[0], alreadyIncluded) == -1){
                        var linkbefore = $('#links [name="relations[' + myTables[1] + '_' + myTables[0] + '][type]"]');
                        if(linkbefore.length > 0) type = linkbefore.val();
                        putLink(myTables[1],myTables[0],type);
                        delete links[key];
                        alreadyIncluded.push(myTables[0]);
                        cpt++; 
                    } else{
                        delete links[key];
                    }
                }else{
                    break boucle;
                }
	    }
	}
	$("#links").html($("#linkstransit").html());
	$("#linkstransit").empty();
	
	$("#schema_sql .tableCont").hide();
	$('#recipiant_sql .queryblock').each(function(){
	    var table = $(".table",this).val();
	    $('#schema_sql .tableCont[table="' + table + '"]').show();
	    $('.property[link="' + table + '"]').each(function(i){
		$(this).parent().show();
		$('.property[link]',$(this).parent()).each(function(i){
		    $('#schema_sql .tableCont[table="' + $(this).attr("link") + '"]').show();
		});
	    });
	});
    };