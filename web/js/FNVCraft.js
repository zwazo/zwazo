if (!FNVCraft) { 
	/* avoid load priority error */
	var FNVCraft = {}; // namespace
}

FNVCraft.addlog = function( msg , type) {
	var d = new Date();
	
	if ('ferr' == type) {
		msg += "\nFNVCraft was forced to stop !";
	}
	msg = msg.replace(/\n/g, "\n               "); // whites spaces are on purpose

	jQuery('#logs').prepend( 
		sprintf('%02d', d.getHours())+':'
		+sprintf('%02d', d.getMinutes())+':'
		+sprintf('%02d', d.getSeconds())+"."
		+sprintf('%03d', d.getMilliseconds())
		+' - '
		+((type) ? '<font class="'+type+'">' : '')
		+msg
		+((type) ? '</font>' : '')+"\n"
	);
};

FNVCraft.indexedDB = {};

FNVCraft.indexedDB.onError = function(event) {
	var err = event.target.error.message;
	if ( empty(err) ) {
		FNVCraft.log("Woops, something went terribly wrong with IndexedDB !", 'ferr');
	} else {
		if ( 'InvalidStateError' == event.target.error.name )
			FNVCraft.addlog('IndexedDB Error: '+err, 'ferr');
		else
			FNVCraft.addlog('IndexedDB Error: '+err, 'err');
	}
};

FNVCraft.indexedDB.drop = function() {
	var request = window.indexedDB.deleteDatabase( FNVCraft.db.name, { storage: FNVCraft.db.storage } );
	
	request.onerror = function(e) {
		FNVCraft.indexedDB.onError( e );
		return false;
	};

	request.onsuccess = function(e) {
		return true;
	};
};

FNVCraft.indexedDB.reload = function( db ) {
	if ('undefined' == typeof(db) ) { 
		db = FNVCraft.indexedDB.db; 
		if ('undefined' == typeof(db) ) {
			FNVCraft.addlog('unable to load datas', 'ferr');
			return false;
		}
	}

	var _i,_j,_tmp = null;
	
	if (db.objectStoreNames.contains("ingredients")) {
		db.deleteObjectStore("ingredients");
	}
	var ing = db.createObjectStore("ingredients", { autoIncrement:true });
	ing.createIndex('I_LABEL', "lbl", { unique: true });
	ing.createIndex('I_DLC', "dlc", { unique: false });
	for ( _i=1; FNVCraft.db.ingredients[_i]; _i++ ) {
		ing.put( FNVCraft.db.ingredients[_i] );
	}

	jQuery('#logs span[class="dbup"]').html('50');
	
	if (db.objectStoreNames.contains("recipe_ingredients")) {
		db.deleteObjectStore("recipe_ingredients");
	}
	var rec_ing = db.createObjectStore("recipe_ingredients", { autoIncrement:true });
	rec_ing.createIndex('RI_RECIPE', "rec", { unique: false });
	rec_ing.createIndex('RI_INGREDIENT', "ing", { unique: false });
	
	if (db.objectStoreNames.contains("recipes")) {
		db.deleteObjectStore("recipes");
	}
	var rec = db.createObjectStore("recipes", { autoIncrement:true });
	rec.createIndex('R_LABEL', "lbl", { unique: true });
	rec.createIndex('R_DLC', "dlc", { unique: false });
	rec.createIndex('R_KIND', "kind", { unique: false });
	for ( _i=1; FNVCraft.db.recipes[_i]; _i++ ) {
		rec.put( FNVCraft.db.recipes[_i] );

		// indexing skill-level relation
		skill_lvl.put({ 
			skill: 
			,lvl: 
			,recipe: _i
		});
		
		for ( _j=0; FNVCraft.db.recipes[_i].need[_j]; _j++ ) {
		// indexing recipe-ingredient relations
			_tmp = FNVCraft.db.recipes[_i].need[_j].split(':');	
			rec_ing.put( { rec: _i, ing: _tmp[0] } );
		}
	}

	jQuery('#logs span[class="dbup"]').html('100');
};

FNVCraft.connect = function() {

	FNVCraft.indexedDB.drop();
	
	// FF:
	var request = window.indexedDB.open( FNVCraft.db.name, { version: FNVCraft.db.version, storage: FNVCraft.db.storage } );
	// Opera: 
	// var request = window.indexedDB.open(FNVCraft.db.name, FNVCraft.db.version );

	request.onupgradeneeded = function(e) {
		var db = e.target.result;
		if ( confirm("We detected a newer version of FNVCraft.db.\nDo you want to upgrade your local database ?") ) {
			FNVCraft.addlog('Upgrading database: <span class="dbup">1</span>%');
			FNVCraft.indexedDB.reload( db );
		} else {
			FNVCraft.addlog('Database upgrade: refused');
		}
	}

	request.onsuccess = function(e) {
		FNVCraft.indexedDB.db = e.target.result;
		FNVCraft.addlog('Database connection : success');
		FNVCraft.render();
	};

	request.onerror = function(e) {
		FNVCraft.addlog('Database connection : failure', 'warn');
		FNVCraft.indexedDB.onError(e);
	}

};

FNVCraft.render = function() {
	
	var index  = null;
	var cursor = null;
	var range  = null;
	
	var trans = FNVCraft.indexedDB.db.transaction(['ingredients','recipes'],'readonly');
	var ingredients = trans.objectStore('ingredients');
	var recipes     = trans.objectStore('recipes');
	
	/* retrieveById : ok
	var request = ingredients.get( 1 );
	request.onerror = FNVCraft.indexedDB.onError;
	request.onsuccess = function(e) {
		if ('undefined' == typeof(e.target.result) ) { return; }
		alert("ingredient[1] = " + e.target.result.lbl);
	}
	*/
	
	/* build #ingredients */
	range = IDBKeyRange.lowerBound(0);
	ingredients.openCursor( range ).onsuccess = function(e) {
		var result = e.target.result;
		if( !result ) { return; }

		jQuery('#ingredients').append( jQuery('<option>', {value: result.key, text: result.value.lbl }) );
		result.continue();
	};
	
	/* search by index */
	// var request = ingredients.index( 'I_LABEL' ).get( 'Butter knife' );
	// var index   = ingredients.index('I_DLC');
	
	/* get whole index
	index.openCursor().onsuccess = function(event) {
		var cursor = event.target.result;
		if (cursor) {
		// cursor.key is a name, like "Bill", and cursor.value is the whole object.
			console.log( cursor.value );
			cursor.continue();
		}
	}; */

	/* get first match 
	var request = index.get( 'DM' );
	request.onerror = FNVCraft.indexedDB.onError;
	request.onsuccess = function(e) {
		if ('undefined' == typeof(e.target.result) ) { return; }
		console.log( e.target.result );
	} */
	
	/* search */
	// var range = IDBKeyRange.only("DM");
	// index.openCursor( range ).onsuccess = function(event) {
		// var cursor = event.target.result;
		// if (cursor) {
			// console.log( cursor.value );
			// cursor.continue();
		// }
	// };
	
	
	/* multi search 
	trans = FNVCraft.indexedDB.db.transaction('recipes','readonly');
	var recipes = trans.objectStore('recipes');
	
	// index = recipes.index('R_DLC');
	// range = IDBKeyRange.only('OWB');
	
	index = recipes.index('R_SKILL');
	//range = IDBKeyRange.only([80,1]);
	range = IDBKeyRange.upperBound([100,1]); // tous ceux avec une recette

	range = IDBKeyRange.upperBound([50,1]);  // tous ceux avec un skill <= 50
	range = IDBKeyRange.upperBound([50,0]);  // tous ceux avec un skill <= 50 et ne nessitant pas de recette

	range = IDBKeyRange.lowerBound([50,1]);  // tous ceux avec un skill >= 50 et necessitant une rectte
	range = IDBKeyRange.lowerBound([50,0]);  // tous ceux avec un skill >= 50

	index = recipes.index('R_S');
	range = IDBKeyRange.bound(0,50,false,false); // [0,50] inclus
	
	index.openCursor( range ).onsuccess = function(event) {
		var cursor = event.target.result;
		if (cursor) {
			console.log( cursor.value );
			cursor.continue();
		}
	};*/
	
};

FNVCraft.sel = function( elm ) {
	var id = jQuery( elm ).val();
	
	var trans = FNVCraft.indexedDB.db.transaction( id ,'readonly');
	var store = trans.objectStore( id );
	
	if ('ingredients' == id) {
		
	}
}

FNVCraft.start = function() {
	if ( 'object' != typeof(FNVCraft.db) ) {
		this.addlog( "Database not found" , 'ferr');
		return;
	}
	
	FNVCraft.connect();
};

jQuery( document ).ready(function() {
	FNVCraft.start();
});